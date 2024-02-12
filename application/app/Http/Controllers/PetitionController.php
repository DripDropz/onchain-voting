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
use Illuminate\Support\Carbon;
use App\Enums\RuleOperatorEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\DataTransferObjects\RuleData;
use Illuminate\Support\Facades\Redirect;
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

        $actions = [
            [
                'label' => 'Create Petition',
                'link' => route('petitions.create')
            ],
        ];

        return Inertia::render('Petition/Index', [
            'petitions' => $petitions,
            'signedPetitions' => $signedPetitions,
            'crumbs' => $crumbs,
            'actions' => $actions,
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

        $actions = [
            [
                'label' => Auth::check() ? (Auth::user()->id === $petition->user->id ? 'Manage' : 'You’re not the owner') : 'Not logged in',
                'link' => Auth::check() && Auth::user()->id === $petition->user->id
                    ? route('petitions.manage', ['petition' => $petition])
                    : route('login.email'),
                'disabled' => !Auth::check() || (Auth::check() && Auth::user()->id !== $petition->user->id),
            ]
        ];

        return Inertia::render('Petition/View', [
            'petition' => PetitionData::from($petition->load(['ballot', 'user', 'rules'])),
            'crumbs' => $crumbs,
            'signature' => $petition->signatures()->where('user_id', Auth::user()?->id)->first(),
            'actions' => $actions,
        ]);
    }

    public function manage(Petition $petition)
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
            [
                'label' => 'Manage',
                'link' => route('petitions.manage', ['petition' => $petition])
            ],
        ];

        $actions = [
            [
                'label' => 'Edit Petition',
                'link' => route('petitions.create.stepOne', ['petition' => $petition])
            ],
            [
                'label' => 'View Petition',
                'link' => route('petitions.view', ['petition' => $petition])
            ],
            [
                'label' => $petition->closed ? 'Petition closed' : 'Close Petition',
                "clickAction" => 'showModal',
                'disabled' => $petition->closed,

            ],
        ];
        $response = Gate::inspect('view', $petition);

        if ($response->allowed()) {
            return Inertia::render('Petition/Manage', [
                'petition' => PetitionData::from($petition->load(['user', 'rules','ballot'])),
                'crumbs' => $crumbs,
                'actions' => $actions,
            ]);
        } else {
            abort(401, 'You are not the owner Of this Petition');
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
            'petition' => PetitionData::from($petition->load(['ballot', 'user',])),
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
            'email' => ValidationRule::requiredIf(!$request->signature),
            'signature' => ValidationRule::requiredIf(!$request->email),
            'stakeAddress' => ValidationRule::requiredIf(!!$request->signature && !$request->email)
        ]);

        $signature = new Signature;

        if (!$request->signature) {
            $signature->email_signature = $request->email;
        } else {
            $signature->wallet_signature = $request->signature;
            $signature->stake_address = $request->stakeAddress;
        }
        $signature->user_id = Auth::user()->id;

        $signature->save();
        $petition?->signatures()->syncWithPivotValues($signature->id, [
            'model_type' => Petition::class,
        ], false);
        return to_route('petitions.view', $petition->hash);
    }

    public function create(Petition $petition)
    {
        return Inertia::render('Petition/Workflows/StepOne', [
            'petition' => $petition,
            'crumbs' => [
                [
                    'label' => 'Petitions',
                    'link' => route('petitions.index')
                ],
                [
                    'label' => 'Create Petition',
                    'link' => route('petitions.create')
                ],
            ],
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

        if ($request->petition) {
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

    /**
     * Publish a petition.
     */
    public function publish(Petition $petition)
    {
        if ($petition->status === 'approved') {
            $petition->update([
                'status' => 'published',
                'started_at' => now()
            ]);
        }
    }

}
