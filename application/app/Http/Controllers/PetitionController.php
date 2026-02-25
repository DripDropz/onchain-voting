<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\PetitionData;
use App\DataTransferObjects\RuleData;
use App\Enums\ModelStatusEnum;
use App\Enums\RuleOperatorEnum;
use App\Enums\RuleV1Enum;
use App\Events\PetitionSigned;
use App\Http\Integrations\Blockfrost\Requests\BlockfrostRequest;
use App\Models\ModelSignature;
use App\Models\Petition;
use App\Models\Rule;
use App\Models\Signature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule as ValidationRule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PetitionController extends Controller
{
    public $perPage;

    public $petitions;

    public ?string $nextCursor = null;

    public int $offset = 4;

    public bool $hasMorePages;

    public ?array $filter;

    public ?bool $hasPending;

    public ?bool $hasSigned;

    public ?string $status = 'draft';

    /**
     * Display the petition list.
     */
    public function index()
    {
        $user = Auth::user();
        $crumbs = [
            [
                'label' => 'Petitions',
                'link' => route('petitions.index'),
            ],
        ];

        $actions = [
            [
                'label' => 'Create Petition',
                'link' => route('petitions.create'),
            ],
        ];

        $counts = $this->petitionsCount(request());

        return Inertia::render('Petition/Index', [
            'counts' => $counts,
            'platformStats' => $this->platformStats(),
            'user' => $user,
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
                'link' => route('petitions.index'),
            ],
            [
                'label' => 'Petition Details',
                'link' => route('petitions.view', ['petition' => $petition]),
            ],
        ];

        $actions = [
            [
                'label' => Auth::check() && Auth::user()?->id === $petition?->user?->id ? 'Manage' : '',
                'link' => Auth::check() && Auth::user()?->id === $petition?->user?->id
                    ? route('petitions.manage', ['petition' => $petition])
                    : '',
                'disabled' => ! Auth::check() || (Auth::check() && Auth::user()?->id !== $petition?->user?->id),
            ],
            [
                'label' => 'Edit Petition',
                'link' => route('petitions.create.stepOne', ['petition' => $petition]),
            ],
            [
                'label' => $petition->closed ? 'Petition closed' : 'Close Petition',
                'clickAction' => 'showModal',
                'disabled' => $petition->closed || $petition->signatures()->count() > 0,

            ],
        ];

        $recentSignatures = $petition->signatures()
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn ($sig) => [
                'hash' => $sig->hash,
                'created_at' => $sig->created_at?->toISOString(),
                'type' => $sig->wallet_signature ? 'wallet' : 'email',
                'masked_address' => $sig->stake_address
                    ? substr($sig->stake_address, 0, 10).'...'.substr($sig->stake_address, -6)
                    : null,
            ]);

        return Inertia::render('Petition/View', [
            'petition' => PetitionData::from($petition->load(['ballot', 'user', 'rules'])),
            'crumbs' => $crumbs,
            'signature' => $this->resolvePetitionSignature($petition, $request->query('stakeAddress')),
            'actions' => $actions,
            'recentSignatures' => $recentSignatures,
        ]);
    }

    public function manage(Petition $petition)
    {
        $crumbs = [
            [
                'label' => 'Petitions',
                'link' => route('petitions.index'),
            ],
            [
                'label' => 'Petition Details',
                'link' => route('petitions.view', ['petition' => $petition]),
            ],
            [
                'label' => 'Manage',
                'link' => route('petitions.manage', ['petition' => $petition]),
            ],
        ];

        $actions = [
            [
                'label' => 'Edit Petition',
                'link' => route('petitions.create.stepOne', ['petition' => $petition]),
            ],
            [
                'label' => 'View Petition',
                'link' => route('petitions.view', ['petition' => $petition]),
            ],
            [
                'label' => $petition->closed ? 'Petition closed' : 'Close Petition',
                'clickAction' => 'showModal',
                'disabled' => $petition->closed || $petition->signatures()->count() > 0,

            ],
        ];

        if ($petition->status->value === 'approved') {
            $firstAction = [
                'label' => 'Publish Petition',
                'clickAction' => 'showPublishModal',
                'disabled' => $petition->status === 'published',
            ];
            array_unshift($actions, $firstAction);
        } elseif ($petition->status->value === 'published') {
            array_slice($actions, 1);
        }

        $response = Gate::inspect('view', $petition);

        if ($response->allowed()) {
            return Inertia::render('Petition/Manage', [
                'petition' => PetitionData::from($petition->load(['user', 'rules', 'ballot'])),
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
        $baseRoute = $this->resolvePetitionRuleBaseRoute($request);

        return Inertia::modal('Petition/Partials/MakeRule', [
            'petition' => PetitionData::from($petition->load(['categories', 'user', 'rules'])),
            'type' => $request->type,
        ])->baseRoute($baseRoute, [
            'petition' => $petition->hash,
        ]);
    }

    public function saveRule(Request $request, Petition $petition)
    {
        $normalizedPolicy = strtolower(trim((string) $request->input('policy', '')));

        $request->merge([
            'policy' => $normalizedPolicy,
        ]);

        $request->validate([
            'type' => ['required', ValidationRule::in(['ft', 'nft'])],
            'title' => ['required', 'string', 'max:255'],
            'policy' => ['required', 'string', 'size:56', 'regex:/^[0-9a-f]{56}$/'],
        ]);

        $rule = new Rule;
        $rule->type = $request->type;
        $rule->title = $request->title;
        $rule->value1 = RuleV1Enum::POLICY->value;
        $rule->value2 = $normalizedPolicy;
        $rule->operator = RuleOperatorEnum::EQUALS->value;
        $rule->save();

        $petition->rules()->attach($rule->id);
    }

    public function deleteRule(Petition $petition, Rule $rule, Request $request)
    {
        $baseRoute = $this->resolvePetitionRuleBaseRoute($request);

        return Inertia::modal('Petition/Partials/DeleteRule', [
            'petition' => PetitionData::from($petition->load(['ballot', 'user'])),
            'rule' => RuleData::from($rule),
        ])->baseRoute($baseRoute, [
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

        if (! $response->allowed()) {
            return to_route('petitions.index');
        }

        $request->validate([
            'email' => ValidationRule::requiredIf(! $request->signature),
            'signature' => ValidationRule::requiredIf(! $request->email),
            'stakeAddress' => ValidationRule::requiredIf((bool) $request->signature && ! $request->email),
        ]);

        $this->ensureWalletMeetsPetitionAssetCriteria($petition, $request);

        $signatureQuery = Signature::query();

        if ($request->filled('email')) {
            $signatureQuery->where('email_signature', $request->email);
        } elseif ($request->filled('stakeAddress')) {
            $signatureQuery->where('stake_address', $request->stakeAddress);
        }

        $signature = $signatureQuery->first();

        if (! $signature instanceof Signature) {
            $signature = new Signature;

            if (! $request->signature) {
                $signature->email_signature = $request->email;
            } else {
                $signature->wallet_signature = $request->signature;
                $signature->stake_address = $request->stakeAddress;
            }
            $signature->user_id = Auth::user()->id;

            $signature->save();
        }

        $petitionId = $petition->getKey();

        $hasSignatureLink = ModelSignature::query()
            ->where('signature_id', $signature->id)
            ->where('model_type', Petition::class)
            ->where('model_id', $petitionId)
            ->exists();

        if (! $hasSignatureLink) {
            ModelSignature::query()->insert([
                'signature_id' => $signature->id,
                'model_type' => Petition::class,
                'model_id' => $petitionId,
            ]);
        }

        PetitionSigned::dispatch($petition);
        Cache::forget('petition_platform_stats');

        $redirectParams = [
            'petition' => $petition->hash,
        ];

        if ($request->filled('stakeAddress')) {
            $redirectParams['stakeAddress'] = $request->stakeAddress;
        }

        return to_route('petitions.view', $redirectParams);
    }

    public function create(Petition $petition)
    {
        return Inertia::render('Petition/Workflows/StepOne', [
            'petition' => $petition,
            'crumbs' => [
                [
                    'label' => 'Petitions',
                    'link' => route('petitions.index'),
                ],
                [
                    'label' => 'Create Petition',
                    'link' => route('petitions.create'),
                ],
            ],
        ]);
    }

    public function stepTwo(Petition $petition)
    {
        return Inertia::render('Petition/Workflows/StepTwo', [
            'petition' => PetitionData::from($petition->load('media')),
            'crumbs' => [
                ['label' => 'Petitions', 'link' => route('petitions.index')],
                ['label' => $petition->title, 'link' => route('petitions.view', ['petition' => $petition])],
                ['label' => 'Edit — Description', 'link' => route('petitions.create.stepTwo', ['petition' => $petition])],
            ],
        ]);
    }

    public function stepThree(Petition $petition)
    {
        return Inertia::render('Petition/Workflows/StepThree', [
            'petition' => PetitionData::from($petition->load(['rules'])),
            'crumbs' => [
                ['label' => 'Petitions', 'link' => route('petitions.index')],
                ['label' => $petition->title, 'link' => route('petitions.view', ['petition' => $petition])],
                ['label' => 'Review & Submit', 'link' => route('petitions.create.stepThree', ['petition' => $petition])],
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string',
        ]);

        $user = Auth::user();
        abort_if(! $user, 401, 'You must be logged in to create a petition.');

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
        abort_if(Auth::user()?->id !== $petition->user_id, 403, 'You are not the owner of this petition.');

        $validatedData = $request->validate([
            'description' => 'required',
            'cover_image' => 'nullable|image|max:4096',
            'remove_cover_image' => 'nullable|boolean',
        ]);

        $petition->description = $validatedData['description'];
        $petition->save();

        if ($request->boolean('remove_cover_image')) {
            $petition->clearMediaCollection('petitions');
        }

        if ($request->hasFile('cover_image')) {
            $petition->clearMediaCollection('petitions');
            $petition->addMediaFromRequest('cover_image')
                ->toMediaCollection('petitions');
        }
    }

    public function close(Petition $petition)
    {
        $petition->update(['ended_at' => now()]);
    }

    /**
     * Submit a petition for admin review.
     */
    public function submitForReview(Petition $petition)
    {
        Gate::authorize('publish', $petition);

        abort_if($petition->status->value !== 'draft', 422, 'Only draft petitions can be submitted for review.');

        $petition->update(['status' => ModelStatusEnum::PENDING->value]);

        return to_route('petitions.manage', $petition->hash);
    }

    /**
     * Publish a petition.
     */
    public function publish(Petition $petition)
    {
        Gate::authorize('publish', $petition);

        abort_if($petition->status->value !== 'approved', 422, 'Only approved petitions can be published.');

        $petition->update([
            'status' => 'published',
            'started_at' => now(),
        ]);
    }

    /**
     * Delete a draft petition (soft delete).
     */
    public function destroy(Petition $petition)
    {
        abort_if(Auth::user()?->id !== $petition->user_id, 403, 'You are not the owner of this petition.');
        abort_if($petition->status->value !== 'draft', 422, 'Only draft petitions can be deleted.');

        $petition->delete();

        return to_route('petitions.index');
    }

    /**
     * Revert an approved petition back to draft so the owner can make changes.
     */
    public function revertToDraft(Petition $petition)
    {
        abort_if(Auth::user()?->id !== $petition->user_id, 403, 'You are not the owner of this petition.');
        abort_if($petition->status->value !== 'approved', 422, 'Only approved petitions can be reverted to draft.');

        $petition->update(['status' => ModelStatusEnum::DRAFT->value]);

        return to_route('petitions.manage', $petition->hash);
    }

    public function petitionsData(Request $request)
    {
        $this->perPage = $request->query('perPage', 4);
        $this->nextCursor = $request->query('nextCursor', null);
        $this->hasMorePages = $request->query('hasMorePages', false);
        $this->filter = $request->query('statusfilter', null);
        $this->hasPending = $request->query('hasPending', false);
        $this->hasSigned = $request->query('hasSigned', false);

        $petitionCursor = Petition::latest()
            ->when($this->hasPending, function ($query) {
                $query->where('user_id', Auth::user()->id)
                    ->whereNotIn('status', ['draft', 'published']);
            })
            ->when(in_array('active', $this->filter ?? []), function ($query) {
                $query->where('user_id', Auth::user()->id)
                    ->whereIn('status', ['published']);
            })
            ->when(in_array('draft', $this->filter ?? []), function ($query) {
                $query->where('user_id', Auth::user()->id)
                    ->whereIn('status', $this->filter);
            })
            ->when(in_array('published', $this->filter ?? []), function ($query) {
                $query->whereIn('status', $this->filter)->where('is_visible', true);
            })
            ->when($this->hasSigned, function ($query) {
                $query->where([
                    'status' => 'published',
                ])->whereRelation('signatures', 'user_id', Auth::user()->id);
            })
            ->when($this->nextCursor, function ($query) {
                $query->cursorPaginate($this->perPage, ['*'], 'cursor', $this->nextCursor);
            })
            ->cursorPaginate($this->perPage);

        return [
            'petitions' => PetitionData::collection(collect($petitionCursor->items())),
            'nextCursor' => $petitionCursor->nextCursor()?->encode(),
            'hasMorePages' => $petitionCursor->hasMorePages(),
        ];
    }

    public function petitionData(Request $request, Petition $petition)
    {
        $petitionData = PetitionData::from($petition->load(['ballot', 'user', 'rules']));
        $stakeAddress = $request->query('stakeAddress');

        if ($stakeAddress) {
            return [
                'petition' => $petitionData,
                'signature' => $this->resolvePetitionSignature($petition, $stakeAddress),
            ];
        }

        return $petitionData;
    }

    private function ensureWalletMeetsPetitionAssetCriteria(Petition $petition, Request $request): void
    {
        $assetRules = $petition->rules()
            ->whereIn('type', ['ft', 'nft'])
            ->whereNotNull('value2')
            ->get();
        $petitionId = $petition->getKey();

        if ($assetRules->isEmpty()) {
            return;
        }

        $stakeAddress = $request->string('stakeAddress')->toString();

        if (empty($stakeAddress)) {
            throw ValidationException::withMessages([
                'signature' => 'A connected wallet is required to sign this gated petition.',
            ]);
        }

        foreach ($assetRules as $rule) {
            $policy = (string) $rule->value2;
            $cacheKey = sprintf(
                'petition_asset_gate:%d:%d:%s:%s',
                $petitionId,
                $rule->id,
                md5($stakeAddress),
                $policy
            );

            $hasPolicyAsset = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($stakeAddress, $policy) {
                return $this->walletHasPolicyAsset($stakeAddress, $policy);
            });

            if (! $hasPolicyAsset) {
                $assetType = strtoupper((string) $rule->type);
                $ruleTitle = $rule->title ?: "{$assetType} gate";

                throw ValidationException::withMessages([
                    'signature' => "Your wallet must hold at least one asset under policy {$policy} to satisfy the {$ruleTitle} requirement.",
                ]);
            }
        }
    }

    private function walletHasPolicyAsset(string $stakeAddress, string $policy): bool
    {
        try {
            $frost = app(BlockfrostRequest::class);
            $frost->setEndPoint("/accounts/{$stakeAddress}/addresses/assets/{$policy}");
            $response = $frost->send();

            if (! $response->successful()) {
                return false;
            }

            $assets = $response->json();

            return is_array($assets) && count($assets) > 0;
        } catch (\Throwable $exception) {
            return false;
        }
    }

    private function resolvePetitionSignature(Petition $petition, ?string $stakeAddress = null): ?Signature
    {
        $signatures = $petition->signatures();
        $user = Auth::user();
        $resolvedStakeAddress = $stakeAddress ?: $user?->voter_id;

        if (! $user?->id && ! $resolvedStakeAddress) {
            return null;
        }

        return $signatures
            ->where(function ($query) use ($user, $resolvedStakeAddress) {
                if ($user?->id) {
                    $query->orWhere('user_id', $user->id);
                }

                if ($resolvedStakeAddress) {
                    $query->orWhere('stake_address', $resolvedStakeAddress);
                }
            })
            ->latest()
            ->first();
    }

    private function resolvePetitionRuleBaseRoute(Request $request): string
    {
        $requestedRoute = (string) $request->query('returnRoute', 'petitions.manage');
        $allowedRoutes = [
            'petitions.manage',
            'petitions.create.stepThree',
        ];

        if (! in_array($requestedRoute, $allowedRoutes, true)) {
            return 'petitions.manage';
        }

        return $requestedRoute;
    }

    private function platformStats(): array
    {
        return Cache::remember('petition_platform_stats', now()->addMinutes(5), function () {
            return [
                'totalPetitions' => Petition::whereNotIn('status', ['draft'])->whereNull('deleted_at')->count(),
                'reviewingCount' => Petition::whereIn('status', ['pending', 'approved'])->whereNull('deleted_at')->count(),
                'publishedCount' => Petition::where('status', 'published')->where('is_visible', true)->whereNull('deleted_at')->count(),
                'collectingCount' => Petition::where('status', 'published')->where('is_visible', false)->whereNull('deleted_at')->count(),
                'totalSignatures' => Signature::count(),
            ];
        });
    }

    private function petitionsCount(Request $request)
    {
        $user = Auth::user();
        $draftCount = Petition::where('user_id', $user?->id)
            ->where('status', 'draft')
            ->count();

        $activeCount = Petition::where('user_id', $user?->id)
            ->where('status', 'published')
            ->count();

        $pendingCount = Petition::where('user_id', $user?->id)
            ->whereNotIn('status', ['draft', 'published'])
            ->count();

        $signedCount = Petition::where('status', 'published')
            ->whereRelation('signatures', 'user_id', $user?->id)
            ->count();
        $allActiveCount = Petition::where('status', 'published')
            ->where('is_visible', true)
            ->count();

        return [
            'draftCount' => $draftCount,
            'activeCount' => $activeCount,
            'pendingCount' => $pendingCount,
            'signedCount' => $signedCount,
            'allCount' => $allActiveCount,
        ];
    }
}
