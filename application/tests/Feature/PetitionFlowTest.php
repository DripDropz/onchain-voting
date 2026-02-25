<?php

use App\Enums\RuleOperatorEnum;
use App\Enums\RuleV1Enum;
use App\Http\Controllers\PetitionController;
use App\Models\Petition;
use App\Models\Rule;
use App\Models\Signature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

function makePublishedPetition(User $user, bool $visible = true): Petition
{
    return Petition::query()->create([
        'user_id' => $user->id,
        'title' => fake()->sentence(3),
        'description' => fake()->paragraph(),
        'status' => 'published',
        'is_visible' => $visible,
        'is_featured' => false,
        'started_at' => now()->subDay(),
    ]);
}

test('browse petition data only returns visible published petitions', function () {
    $owner = User::factory()->create();
    $visiblePetition = makePublishedPetition($owner, true);
    makePublishedPetition($owner, false);

    $response = $this->getJson('/petitions/petitionsData?statusfilter[0]=published');

    $response->assertOk();
    $response->assertJsonCount(1, 'petitions');
    expect($response->json('petitions.0.hash'))->toBe($visiblePetition->hash);
});

test('save rule rejects invalid policy id format', function () {
    $user = User::factory()->create();
    $petition = makePublishedPetition($user, true);

    $response = $this->actingAs($user)->post(route('petitions.rules.saveRule', [
        'petition' => $petition->hash,
    ]), [
        'type' => 'nft',
        'title' => 'Dev Cards',
        'policy' => 'invalid-policy-id',
    ]);

    $response->assertSessionHasErrors('policy');
    $this->assertDatabaseMissing('rules', [
        'title' => 'Dev Cards',
    ]);
});

test('save rule stores normalized policy id for petition gate', function () {
    $user = User::factory()->create();
    $petition = makePublishedPetition($user, true);
    $policy = strtoupper('7d878696b149b529807aa01b8e20785e0a0d470c32c13f53f08a55e3');

    $response = $this->actingAs($user)->post(route('petitions.rules.saveRule', [
        'petition' => $petition->hash,
    ]), [
        'type' => 'nft',
        'title' => 'Dev Cards',
        'policy' => $policy,
    ]);

    $response->assertSuccessful();
    $this->assertDatabaseHas('rules', [
        'type' => 'nft',
        'title' => 'Dev Cards',
        'value2' => strtolower($policy),
    ]);

    $ruleId = Rule::query()
        ->where('type', 'nft')
        ->where('title', 'Dev Cards')
        ->value('id');

    expect($ruleId)->not->toBeNull();

    $this->assertDatabaseHas('petition_rule', [
        'petition_id' => $petition->id,
        'rule_id' => $ruleId,
    ]);
});

test('gated petition signing is blocked when cached asset check fails', function () {
    $user = User::factory()->create([
        'voter_id' => 'stake_test1failedassetcheck00000000000000000000000000000000000',
    ]);
    $petition = makePublishedPetition($user, true);

    $rule = new Rule;
    $rule->type = 'ft';
    $rule->title = 'Token holder required';
    $rule->value1 = RuleV1Enum::POLICY->value;
    $rule->value2 = 'policyassetmissing123';
    $rule->operator = RuleOperatorEnum::EQUALS->value;
    $rule->save();
    $petition->rules()->attach($rule->id);

    $stakeAddress = 'stake_test1failedassetcheck00000000000000000000000000000000000';
    $cacheKey = sprintf(
        'petition_asset_gate:%d:%d:%s:%s',
        $petition->id,
        $rule->id,
        md5($stakeAddress),
        (string) $rule->value2
    );
    Cache::put($cacheKey, false, now()->addMinutes(10));

    $this->actingAs($user);
    $request = Request::create('/petitions/sign', 'POST', [
        'signature' => 'wallet_signature_for_failure',
        'stakeAddress' => $stakeAddress,
    ]);

    expect(fn () => app(PetitionController::class)->signPetition($petition, $request))
        ->toThrow(ValidationException::class);

    expect($petition->fresh()->signatures()->count())->toBe(0);
});

test('gated petition signing succeeds when cached asset check passes', function () {
    $user = User::factory()->create([
        'voter_id' => 'stake_test1passassetcheck0000000000000000000000000000000000000',
    ]);
    $petition = makePublishedPetition($user, true);

    $rule = new Rule;
    $rule->type = 'ft';
    $rule->title = 'Token holder required';
    $rule->value1 = RuleV1Enum::POLICY->value;
    $rule->value2 = 'policyassetpresent123';
    $rule->operator = RuleOperatorEnum::EQUALS->value;
    $rule->save();
    $petition->rules()->attach($rule->id);

    $stakeAddress = 'stake_test1passassetcheck0000000000000000000000000000000000000';
    $cacheKey = sprintf(
        'petition_asset_gate:%d:%d:%s:%s',
        $petition->id,
        $rule->id,
        md5($stakeAddress),
        (string) $rule->value2
    );
    Cache::put($cacheKey, true, now()->addMinutes(10));

    $this->actingAs($user);
    $request = Request::create('/petitions/sign', 'POST', [
        'signature' => 'wallet_signature_for_success',
        'stakeAddress' => $stakeAddress,
    ]);

    $response = app(PetitionController::class)->signPetition($petition, $request);

    expect($response->status())->toBe(302);
    $this->assertDatabaseHas('signatures', [
        'user_id' => $user->id,
        'stake_address' => $stakeAddress,
    ]);
    $this->assertDatabaseHas('model_signatures', [
        'model_type' => Petition::class,
        'model_id' => $petition->id,
    ]);
});

test('petition data returns existing signature by stake address context', function () {
    $owner = User::factory()->create();
    $viewer = User::factory()->create();
    $petition = makePublishedPetition($owner, true);

    $stakeAddress = 'stake_test1existingwalletsignature0000000000000000000000000000';

    $signature = new Signature;
    $signature->user_id = $owner->id;
    $signature->wallet_signature = 'wallet_signature_for_lookup';
    $signature->stake_address = $stakeAddress;
    $signature->save();

    $petition->signatures()->attach($signature->id, [
        'model_type' => Petition::class,
    ]);

    $response = $this->actingAs($viewer)->getJson(
        route('petitions.petitionData', ['petition' => $petition->hash]).'?stakeAddress='.$stakeAddress
    );

    $response->assertOk();
    expect($response->json('signature.hash'))->toBe($signature->hash);
});
