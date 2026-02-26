<?php

use App\Enums\QuestionTypeEnum;
use App\Enums\RuleOperatorEnum;
use App\Enums\RuleV1Enum;
use App\Models\Poll;
use App\Models\Question;
use App\Models\Rule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function makePollWithQuestion(User $user, string $status = 'draft'): Poll
{
    $poll = Poll::query()->create([
        'user_id' => $user->id,
        'title' => fake()->sentence(3),
        'description' => fake()->paragraph(),
        'status' => $status,
    ]);

    $question = Question::query()->create([
        'title' => 'Test question',
        'model_type' => Poll::class,
        'model_id' => $poll->id,
        'type' => QuestionTypeEnum::MULTIPLE->value,
        'user_id' => $user->id,
    ]);

    $question->choices()->create([
        'title' => 'Yes',
        'order' => 0,
        'question_id' => $question->id,
        'user_id' => $user->id,
    ]);

    $question->choices()->create([
        'title' => 'No',
        'order' => 1,
        'question_id' => $question->id,
        'user_id' => $user->id,
    ]);

    return $poll->fresh();
}

test('poll store creates draft poll and question choices', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('polls.store'), [
        'question' => 'Should we ship this proposal?',
        'options' => ['Yes', 'No'],
        'publishOnchain' => true,
    ]);

    $poll = Poll::query()->first();
    expect($poll)->not->toBeNull();
    expect($poll->status->value)->toBe('draft');

    $response->assertRedirect(route('polls.create.stepTwo', ['poll' => $poll->hash]));
    $this->assertDatabaseHas('questions', [
        'model_type' => Poll::class,
        'model_id' => $poll->id,
    ]);
    $this->assertDatabaseCount('question_choices', 2);
});

test('poll lifecycle moves from draft to pending approved and published', function () {
    $owner = User::factory()->create();
    $poll = makePollWithQuestion($owner, 'draft');

    $submitResponse = $this->actingAs($owner)->patch(route('polls.submit', ['poll' => $poll->hash]));
    $submitResponse->assertRedirect(route('polls.manage', ['poll' => $poll->hash]));
    expect($poll->fresh()->status->value)->toBe('pending');

    Role::query()->firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin, 'admin')->put(route('admin.polls.update', ['poll' => $poll->hash]), [
        'status' => 'approved',
    ])->assertSuccessful();

    expect($poll->fresh()->status->value)->toBe('approved');

    $this->actingAs($owner)->put(route('polls.publish', ['poll' => $poll->hash]))
        ->assertSuccessful();

    $refreshedPoll = $poll->fresh();
    expect($refreshedPoll->status->value)->toBe('published');
    expect($refreshedPoll->started_at)->not->toBeNull();
});

test('browse polls data only returns published polls when status filter is published', function () {
    $owner = User::factory()->create();
    $published = makePollWithQuestion($owner, 'published');
    makePollWithQuestion($owner, 'pending');

    $response = $this->getJson(route('polls.pollsData', ['status' => 'published']));

    $response->assertOk();
    $response->assertJsonCount(1, 'polls');
    expect($response->json('polls.0.hash'))->toBe($published->hash);
});

test('poll rule save rejects invalid policy id format', function () {
    $owner = User::factory()->create();
    $poll = makePollWithQuestion($owner, 'draft');

    $response = $this->actingAs($owner)->post(route('polls.rules.saveRule', [
        'poll' => $poll->hash,
    ]), [
        'type' => 'nft',
        'title' => 'Holders only',
        'policy' => 'invalid-policy-id',
    ]);

    $response->assertSessionHasErrors('policy');
    $this->assertDatabaseMissing('rules', [
        'title' => 'Holders only',
    ]);
});

test('gated poll voting is blocked when cached asset check fails', function () {
    $owner = User::factory()->create();
    $voter = User::factory()->create([
        'voter_id' => 'stake_test1pollgatefailed00000000000000000000000000000000000',
    ]);
    $poll = makePollWithQuestion($owner, 'published');
    $question = $poll->question;
    $choice = $question->choices()->first();

    $rule = Rule::query()->create([
        'type' => 'ft',
        'title' => 'Token holder required',
        'value1' => RuleV1Enum::POLICY->value,
        'value2' => '7d878696b149b529807aa01b8e20785e0a0d470c32c13f53f08a55e3',
        'operator' => RuleOperatorEnum::EQUALS->value,
    ]);
    $poll->rules()->attach($rule->id);

    $cacheKey = sprintf(
        'poll_asset_gate:%d:%d:%s:%s',
        $poll->id,
        $rule->id,
        md5($voter->voter_id),
        (string) $rule->value2
    );
    Cache::put($cacheKey, false, now()->addMinutes(10));

    $this->actingAs($voter)->post(route('polls.storeQuestionResponse', ['poll' => $poll->hash]), [
        'questionHash' => $question->hash,
        'selectedChoiceHash' => $choice->hash,
    ])->assertSessionHasErrors('selectedChoiceHash');

    $this->assertDatabaseCount('question_responses', 0);
});

test('gated poll voting succeeds when cached asset check passes', function () {
    $owner = User::factory()->create();
    $voter = User::factory()->create([
        'voter_id' => 'stake_test1pollgatepass000000000000000000000000000000000000000',
    ]);
    $poll = makePollWithQuestion($owner, 'published');
    $question = $poll->question;
    $choice = $question->choices()->first();

    $rule = Rule::query()->create([
        'type' => 'nft',
        'title' => 'NFT holder required',
        'value1' => RuleV1Enum::POLICY->value,
        'value2' => '7d878696b149b529807aa01b8e20785e0a0d470c32c13f53f08a55e3',
        'operator' => RuleOperatorEnum::EQUALS->value,
    ]);
    $poll->rules()->attach($rule->id);

    $cacheKey = sprintf(
        'poll_asset_gate:%d:%d:%s:%s',
        $poll->id,
        $rule->id,
        md5($voter->voter_id),
        (string) $rule->value2
    );
    Cache::put($cacheKey, true, now()->addMinutes(10));

    $this->actingAs($voter)->post(route('polls.storeQuestionResponse', ['poll' => $poll->hash]), [
        'questionHash' => $question->hash,
        'selectedChoiceHash' => $choice->hash,
    ])->assertRedirect(route('polls.view', ['poll' => $poll->hash]));

    $this->assertDatabaseHas('question_responses', [
        'model_type' => Poll::class,
        'model_id' => $poll->id,
        'user_id' => $voter->id,
    ]);
});
