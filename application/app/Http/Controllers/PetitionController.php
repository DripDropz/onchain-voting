<?php

namespace App\Http\Controllers;

use App\Models\Rule;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Petition;
use App\Enums\RuleV1Enum;
use App\Models\Signature;
use App\Enums\RuleTypeEnum;
use Illuminate\Http\Request;
use App\Enums\ModelStatusEnum;
use App\Enums\RuleOperatorEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\DataTransferObjects\RuleData;
use App\DataTransferObjects\PetitionData;
use Illuminate\Validation\Rule as ValidationRule;

class PetitionController extends Controller
{
    /**
     * Display the petition list.
     */

    public function index(Request $request): Response
    {
        $petitions = Petition::query()->get();
        $signedPetitions = Petition::whereRelation('signatures', 'user_id', $request->user()?->id)
            ->get();
            $crumbs = [
                [
                    'label' => 'Petitions',
                    'link' => route('petitions.index')
                ],
            ];

        return Inertia::render('Petition/Index', [
            'petitions' => $petitions,
            'signedPetitions' => $signedPetitions,
            'crumbs' => $crumbs,
        ]);
    }

    /**
     * Display a single petition.
     */
    public function view(Request $request, Petition $petition): Response
    {
        $crumbs = [
            [
                'label' => 'Petitions',
                'link' => route('petitions.index')
            ],
            [
                'label' => 'Petition Details',
                'link' => route('petitions.view', ['petition' => $petition])
            ],
        ];
        return Inertia::render('Petition/View', [
            'petition' => PetitionData::from($petition->load(['categories', 'user', 'rules'])),
            'crumbs' => $crumbs,
            'signature' => $petition->signatures()->where('user_id',Auth::user()->id)->first()
        ]);
    }

    public function manage(Petition $petition)
    {
        $response = Gate::inspect('view', $petition);

        if ($response->allowed()) {
            return Inertia::render('Petition/Manage', [
                'petition' => PetitionData::from($petition->load(['categories', 'user', 'rules'])),
            ]);
        } else {
            return to_route('petitions.index');
        }
    }

    public function share(Petition $petition)
    {
        return Inertia::render('Petition/Share', [
            'petition' => PetitionData::from($petition->load(['categories', 'user', 'rules'])),
        ]);
    }

    public function makeRule(Petition $petition, Request $request)
    {
        return Inertia::modal('Petition/Partials/MakeRule', [
            'petition' => PetitionData::from($petition->load(['categories', 'user', 'rules'])),
            'type' => $request->type
        ])->baseRoute('petitions.manage', [
            'petition' => $petition->hash,
        ]);
    }

    public function saveRule(Request $request, Petition $petition)
    {
        $request->validate([
            'type' => 'required',
            'title' => 'required',
            'policy' => 'required',
        ]);

        $rule = new Rule;
        $rule->type = $request->type;
        $rule->title = $request->title;
        $rule->value1 = RuleV1Enum::POLICY->value;
        $rule->value2 = $request->policy;
        $rule->operator = RuleOperatorEnum::EQUALS->value;
        $rule->save();

        $petition->rules()->attach($rule->id);
    }

    public function deleteRule(Petition $petition, Rule $rule)
    {
        return Inertia::modal('Petition/Partials/DeleteRule', [
            'petition' => PetitionData::from($petition->load(['categories', 'user',])),
            'rule' => RuleData::from($rule)
        ])->baseRoute('petitions.manage', [
            'petition' => $petition->hash,
        ]);
    }

    public function removeRule(Petition $petition, Rule $rule)
    {
        $petition->rules()->detach($rule->id);
        $rule->delete();
        return to_route('petitions.manage', [
            'petition' => $petition->hash,
        ]);
    }

    public function signPetition(Petition $petition, Request $request)
    {
        $response = Gate::inspect('sign', $petition);

        if (!$response->allowed()) {
            return to_route('petitions.index');
        }

        $request->validate([
            'email'=> ValidationRule::requiredIf(!$request->signature),
            'signature' => ValidationRule::requiredIf(!$request->email),
            'stakeAddress' => ValidationRule::requiredIf(!!$request->signature && !$request->email)
        ]);

        $signature = new Signature;

        if (!$request->signature) {

            $signature->email_signature  = $request->email;
            $signature->user_id  = Auth::user()->id;

        }else{
            $signature->wallet_signature  = $request->signature;
            $signature->stake_address  = $request->stakeAddress;
            $signature->user_id  = Auth::user()->id;
        }

        $signature->save();
        $petition?->signatures()->syncWithPivotValues($signature->id, [
            'model_type' => Petition::class,
        ], false);
        return to_route('petitions.view',$petition->hash);

    }

    public function create(Petition $petition)
    {
        return Inertia::render('Petition/Workflows/StepOne', [
            'petition' => $petition,
        ]);
    }

    public function stepTwo(Petition $petition)
    {
        return Inertia::render('Petition/Workflows/StepTwo', [
            'petition' => $petition,
        ]);
    }

    public function stepThree(Petition $petition)
    {
        return Inertia::render('Petition/Workflows/StepThree', [
            'petition' => $petition,
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string',
        ]);

        $user = Auth::user();

        if($request->petition){
            $petition = Petition::byHash($request->petition);
            $petition->update(['title' => $validatedData['title']]);
            return to_route('petitions.create.stepTwo', $petition->hash);
        }

        $petition = new Petition([
            'title' => $validatedData['title'],
            'user_id' => $user->id,
            'description' => '',
            'status' => ModelStatusEnum::DRAFT->value,
        ]);

        $petition->save();

        return to_route('petitions.create.stepTwo', $petition->hash);
    }


    public function update(Request $request, Petition $petition)
    {
        $validatedData = $request->validate([
            'description' => 'required',
        ]);

        $petition->description = $validatedData['description'];
        $petition->save();
    }

    public function close(Petition $petition)
    {
        $petition->update(['ended_at' => now()]);
    }
}
