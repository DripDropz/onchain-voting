<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\PollData;
use App\DataTransferObjects\RuleData;
use App\Enums\ModelStatusEnum;
use App\Enums\QuestionTypeEnum;
use App\Enums\RuleOperatorEnum;
use App\Enums\RuleV1Enum;
use App\Http\Integrations\Blockfrost\Requests\BlockfrostRequest;
use App\Models\Poll;
use App\Models\Question;
use App\Models\QuestionChoice;
use App\Models\QuestionResponse;
use App\Models\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule as ValidationRule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PollController extends Controller
{
    public $perPage;

    public $polls;

    public ?string $nextCursor = null;

    public int $offset = 4;

    public bool $hasMorePages;

    public ?bool $hasPending;

    public ?bool $hasAnswered;

    public ?bool $hasActive;

    public ?string $status = 'pending';

    /**
     * Display the poll list.
     */
    public function index(): Response
    {
        $user = Auth::user();
        $crumbs = [
            [
                'label' => 'Polls',
                'link' => route('polls.index'),
            ],
        ];

        $counts = $this->pollsCount(request());

        return Inertia::render('Poll/Index', [
            'counts' => $counts,
            'platformStats' => $this->platformStats(),
            'user' => $user,
            'crumbs' => $crumbs,
        ]);
    }

    /**
     * Display the poll creation form.
     */
    public function create(?Poll $poll = null): Response
    {
        return Inertia::render('Poll/Workflows/StepOne', [
            'poll' => $poll?->exists ? PollData::from($poll->load('question.choices')) : null,
            'crumbs' => [
                [
                    'label' => 'Polls',
                    'link' => route('polls.index'),
                ],
                [
                    'label' => 'Create Poll',
                    'link' => route('polls.create'),
                ],
            ],
        ]);
    }

    public function stepTwo(Poll $poll): Response
    {
        abort_if(Auth::id() !== $poll->user_id, 403, 'You are not the owner of this poll.');

        return Inertia::render('Poll/Workflows/StepTwo', [
            'poll' => PollData::from($poll->load('question.choices')),
            'crumbs' => [
                ['label' => 'Polls', 'link' => route('polls.index')],
                ['label' => $poll->title, 'link' => route('polls.view', ['poll' => $poll])],
                ['label' => 'Edit - Details', 'link' => route('polls.create.stepTwo', ['poll' => $poll])],
            ],
        ]);
    }

    public function stepThree(Poll $poll): Response
    {
        abort_if(Auth::id() !== $poll->user_id, 403, 'You are not the owner of this poll.');

        return Inertia::render('Poll/Workflows/StepThree', [
            'poll' => PollData::from($poll->load(['rules', 'question.choices'])),
            'crumbs' => [
                ['label' => 'Polls', 'link' => route('polls.index')],
                ['label' => $poll->title, 'link' => route('polls.view', ['poll' => $poll])],
                ['label' => 'Review & Submit', 'link' => route('polls.create.stepThree', ['poll' => $poll])],
            ],
        ]);
    }

    public function view(Poll $poll): Response
    {
        abort_if(
            $poll->status->value !== 'published' && Auth::id() !== $poll->user_id,
            403,
            'You are not authorized to view this poll.'
        );

        $crumbs = [
            [
                'label' => 'Polls',
                'link' => route('polls.index'),
            ],
            [
                'label' => 'Poll Details',
                'link' => route('polls.view', ['poll' => $poll]),
            ],
        ];

        $recentVotes = $poll->responses()
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn ($response) => [
                'hash' => $response->hash,
                'created_at' => $response->created_at?->toISOString(),
                'masked_address' => $response->user?->voter_id
                    ? substr($response->user->voter_id, 0, 10).'...'.substr($response->user->voter_id, -6)
                    : null,
            ]);

        return Inertia::render('Poll/View', [
            'poll' => PollData::from($poll->load(['question.choices', 'user_responses', 'rules', 'user'])),
            'crumbs' => $crumbs,
            'recentVotes' => $recentVotes,
        ]);
    }

    public function manage(Poll $poll): Response
    {
        abort_if(Auth::id() !== $poll->user_id, 403, 'You are not the owner of this poll.');

        return Inertia::render('Poll/Manage', [
            'poll' => PollData::from($poll->load(['user', 'rules', 'question.choices'])),
            'crumbs' => [
                ['label' => 'Polls', 'link' => route('polls.index')],
                ['label' => 'Poll Details', 'link' => route('polls.view', ['poll' => $poll])],
                ['label' => 'Manage', 'link' => route('polls.manage', ['poll' => $poll])],
            ],
        ]);
    }

    public function edit(Poll $poll)
    {
        abort_if(Auth::id() !== $poll->user_id, 403, 'You are not the owner of this poll.');

        return to_route('polls.create.stepOne', ['poll' => $poll->hash]);
    }

    /**
     * Store a newly created poll in storage.
     */
    public function pollsData(Request $request): array
    {
        $this->perPage = $request->query('perPage', 4);
        $this->nextCursor = $request->query('nextCursor', null);
        $this->hasMorePages = $request->query('hasMorePages', false);
        $this->status = $request->query('status', null);
        $this->hasPending = $request->query('hasPending', false);
        $this->hasAnswered = $request->query('hasAnswered', false);
        $this->hasActive = $request->query('hasActive', false);
        $hasDraft = $request->query('hasDraft', false);

        $pollCursor = Poll::latest()
            ->when($hasDraft, function ($query) {
                $query->where('user_id', Auth::user()->id)
                    ->where('status', 'draft');
            })
            ->when($this->hasPending, function ($query) {
                $query->where('user_id', Auth::user()->id)
                    ->whereIn('status', ['pending', 'approved']);
            })->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })->when($this->hasAnswered, function ($query) {
                $query->where([
                    'status' => 'published',
                ])->whereRelation('user_responses', 'user_id', Auth::user()->id);
            })->when($this->hasActive, function ($query) {
                $query->where('user_id', Auth::user()->id)
                    ->whereIn('status', ['published']);
            })->when($this->nextCursor, function ($query) {
                $query->cursorPaginate($this->perPage, ['*'], 'cursor', $this->nextCursor);
            })->cursorPaginate($this->perPage);

        return [
            'polls' => PollData::collection(
                collect($pollCursor->items())
                    ->each(fn ($p) => $p->load('question.choices', 'user_responses', 'rules'))
            ),
            'nextCursor' => $pollCursor->nextCursor()?->encode(),
            'hasMorePages' => $pollCursor->hasMorePages(),
        ];
    }

    public function userPollsData(Request $request): array
    {
        abort_if(! Auth::check(), 401, 'You must be logged in to view your polls.');

        $this->perPage = $request->query('perPage', 4);
        $this->nextCursor = $request->query('nextCursor', null);

        $pollCursor = Poll::query()
            ->latest()
            ->where('user_id', Auth::id())
            ->when($this->nextCursor, function ($query) {
                $query->cursorPaginate($this->perPage, ['*'], 'cursor', $this->nextCursor);
            })
            ->cursorPaginate($this->perPage);

        return [
            'polls' => PollData::collection(
                collect($pollCursor->items())
                    ->each(fn ($p) => $p->load('question.choices', 'user_responses', 'rules'))
            ),
            'nextCursor' => $pollCursor->nextCursor()?->encode(),
            'hasMorePages' => $pollCursor->hasMorePages(),
        ];
    }

    public function pollData(Request $request): PollData
    {
        $poll = $request->route('poll');
        abort_if(
            $poll->status->value !== 'published' && Auth::id() !== $poll->user_id,
            403,
            'You are not authorized to view this poll.'
        );

        $poll->load('question.choices', 'user_responses', 'rules');

        $pollData = PollData::from($poll);

        return $pollData;
    }

    private function pollsCount(Request $request): array
    {
        $user = Auth::user();

        $draftCount = Poll::where('user_id', $user?->id)
            ->where('status', 'draft')
            ->count();

        $activeCount = Poll::where('user_id', $user?->id)
            ->where('status', 'published')
            ->count();

        $pendingCount = Poll::where('user_id', $user?->id)
            ->whereIn('status', ['pending', 'approved'])
            ->count();

        $answeredCount = Poll::where('status', 'published')
            ->whereRelation('user_responses', 'user_id', $user?->id)
            ->count();

        return [
            'draftCount' => $draftCount,
            'activeCount' => $activeCount,
            'pendingCount' => $pendingCount,
            'answeredCount' => $answeredCount,
            'allCount' => Poll::where('status', 'published')->count(),
        ];
    }

    private function platformStats(): array
    {
        return Cache::remember('poll_platform_stats', now()->addMinutes(5), function () {
            return [
                'totalPolls' => Poll::whereNotIn('status', ['draft'])->whereNull('deleted_at')->count(),
                'reviewingCount' => Poll::whereIn('status', ['pending', 'approved'])->whereNull('deleted_at')->count(),
                'publishedCount' => Poll::where('status', 'published')->whereNull('deleted_at')->count(),
                'totalVotes' => QuestionResponse::where('model_type', Poll::class)->count(),
            ];
        });
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        abort_if(! $user, 401, 'You must be logged in to create a poll.');

        $validatedData = $request->validate([
            'question' => 'required|string|max:255',
            'options' => 'required|array|min:1',
            'options.*' => 'required|string|max:255',
            'publishOnchain' => 'boolean',
        ]);

        if ($request->filled('poll')) {
            $poll = Poll::byHash($request->poll);
            abort_if($poll->user_id !== $user->id, 403, 'You are not the owner of this poll.');
            abort_if(! in_array($poll->status->value, ['draft', 'rejected']), 422, 'Only draft or rejected polls can be edited.');

            $poll->update([
                'title' => $validatedData['question'],
                'publish_on_chain' => $validatedData['publishOnchain'] ?? false,
            ]);

            $question = $poll->question ?: new Question([
                'model_type' => Poll::class,
                'model_id' => $poll->id,
                'type' => QuestionTypeEnum::MULTIPLE->value,
            ]);

            $question->fill([
                'title' => $validatedData['question'],
                'user_id' => $user->id,
                'model_id' => $poll->id,
                'model_type' => Poll::class,
                'type' => QuestionTypeEnum::MULTIPLE->value,
            ]);
            $question->save();

            $question->choices()->delete();
            foreach ($validatedData['options'] as $key => $choice) {
                $question->choices()->create([
                    'title' => $choice,
                    'order' => $key,
                    'question_id' => $question->id,
                ]);
            }

            return to_route('polls.create.stepTwo', $poll->hash);
        }

        $poll = new Poll([
            'user_id' => $user->id,
            'title' => $validatedData['question'],
            'publish_on_chain' => $validatedData['publishOnchain'] ?? false,
            'status' => ModelStatusEnum::DRAFT->value,
        ]);

        $poll->save();

        $question = new Question([
            'title' => $validatedData['question'],
            'model_type' => Poll::class,
            'model_id' => $poll->id,
            'type' => QuestionTypeEnum::MULTIPLE->value,
        ]);

        $question->user_id = $user->id;
        $question->model_id = $poll->id;

        $question->save();

        foreach ($validatedData['options'] as $key => $choice) {
            $question->choices()->create([
                'title' => $choice,
                'order' => $key,
                'question_id' => $question->id,
            ]);
        }

        return to_route('polls.create.stepTwo', $poll->hash);
    }

    public function update(Request $request, Poll $poll): void
    {
        abort_if(Auth::id() !== $poll->user_id, 403, 'You are not the owner of this poll.');
        abort_if(! in_array($poll->status->value, ['draft', 'rejected']), 422, 'Only draft or rejected polls can be edited.');

        $validatedData = $request->validate([
            'description' => ['required', 'string'],
            'cover_image' => ['nullable', 'image', 'max:4096'],
            'remove_cover_image' => ['nullable', 'boolean'],
        ]);

        $poll->update([
            'description' => $validatedData['description'],
        ]);

        if ($request->boolean('remove_cover_image')) {
            $poll->clearMediaCollection('polls');
        }

        if ($request->hasFile('cover_image')) {
            $poll->clearMediaCollection('polls');
            $poll->addMediaFromRequest('cover_image')
                ->toMediaCollection('polls');
        }
    }

    public function submitForReview(Poll $poll)
    {
        Gate::authorize('publish', $poll);

        abort_if($poll->status->value !== 'draft', 422, 'Only draft polls can be submitted for review.');

        $poll->update(['status' => ModelStatusEnum::PENDING->value]);

        return to_route('polls.manage', $poll->hash);
    }

    public function publish(Poll $poll): void
    {
        Gate::authorize('publish', $poll);

        abort_if($poll->status->value !== 'approved', 422, 'Only approved polls can be published.');

        $poll->update([
            'status' => ModelStatusEnum::PUBLISHED->value,
            'started_at' => now(),
        ]);
    }

    public function close(Poll $poll): void
    {
        abort_if(Auth::id() !== $poll->user_id, 403, 'You are not the owner of this poll.');
        abort_if($poll->status->value !== 'published', 422, 'Only published polls can be closed.');

        $poll->update([
            'ended_at' => now(),
            'status' => ModelStatusEnum::CLOSED->value,
        ]);
    }

    public function destroy(Poll $poll)
    {
        abort_if(Auth::id() !== $poll->user_id, 403, 'You are not the owner of this poll.');
        abort_if($poll->status->value !== 'draft', 422, 'Only draft polls can be deleted.');

        $poll->delete();

        return to_route('polls.index');
    }

    public function makeRule(Poll $poll, Request $request)
    {
        $baseRoute = $this->resolvePollRuleBaseRoute($request);

        return Inertia::modal('Poll/Partials/MakeRule', [
            'poll' => PollData::from($poll->load(['user', 'rules'])),
            'type' => $request->type,
        ])->baseRoute($baseRoute, [
            'poll' => $poll->hash,
        ]);
    }

    public function saveRule(Request $request, Poll $poll): void
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

        $poll->rules()->attach($rule->id);
    }

    public function deleteRule(Poll $poll, Rule $rule, Request $request)
    {
        $baseRoute = $this->resolvePollRuleBaseRoute($request);

        return Inertia::modal('Poll/Partials/DeleteRule', [
            'poll' => PollData::from($poll->load(['user', 'rules'])),
            'rule' => RuleData::from($rule),
        ])->baseRoute($baseRoute, [
            'poll' => $poll->hash,
        ]);
    }

    public function removeRule(Poll $poll, Rule $rule)
    {
        $poll->rules()->detach($rule->id);
        $rule->delete();

        return to_route('polls.manage', [
            'poll' => $poll->hash,
        ]);
    }

    public function storeQuestionResponse(Request $request)
    {
        $poll = $request->route('poll');
        $user = Auth::user();
        abort_if(! $user, 401, 'You must be logged in to vote on this poll.');
        abort_if($poll->status->value !== 'published', 422, 'Only published polls can receive votes.');

        $request->validate([
            'questionHash' => ['required', 'string'],
            'selectedChoiceHash' => ['required', 'string'],
            'signature' => ['required', 'string'],
            'stakeAddress' => ['required', 'string'],
        ]);

        // Verify the stake address matches the user's voter_id
        $userStakeAddress = $user->voter_id;
        if ($request->stakeAddress !== $userStakeAddress) {
            throw ValidationException::withMessages([
                'stakeAddress' => 'The signing wallet does not match your connected wallet.',
            ]);
        }

        $this->ensureWalletMeetsPollAssetCriteria($poll, $request);

        $question = Question::byHash($request->questionHash);
        $choice = QuestionChoice::byHash($request->selectedChoiceHash);

        abort_if((int) $question->model_id !== (int) $poll->id, 422, 'This question does not belong to the selected poll.');

        $existingResponse = QuestionResponse::query()
            ->where('user_id', $user->id)
            ->where('model_type', Poll::class)
            ->where('model_id', $poll->id)
            ->exists();

        if ($existingResponse) {
            return to_route('polls.view', ['poll' => $poll->hash]);
        }

        $questionResponse = new QuestionResponse([
            'user_id' => $user->id,
            'model_type' => Poll::class,
            'model_id' => $poll->id,
            'question_id' => $question->id,
        ]);

        $questionResponse->save();

        $questionResponse->choices()->attach($choice->id);

        // Clear cache to ensure fresh stats
        Cache::forget('poll_platform_stats');

        return to_route('polls.view', ['poll' => $poll->hash]);
    }

    private function ensureWalletMeetsPollAssetCriteria(Poll $poll, Request $request): void
    {
        $assetRules = $poll->rules()
            ->whereIn('type', ['ft', 'nft'])
            ->whereNotNull('value2')
            ->get();

        if ($assetRules->isEmpty()) {
            return;
        }

        $stakeAddress = (string) (Auth::user()?->voter_id ?? '');

        if (empty($stakeAddress)) {
            throw ValidationException::withMessages([
                'selectedChoiceHash' => 'A connected wallet is required to vote on this gated poll.',
            ]);
        }

        foreach ($assetRules as $rule) {
            $policy = (string) $rule->value2;
            $cacheKey = sprintf(
                'poll_asset_gate:%d:%d:%s:%s',
                $poll->id,
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
                    'selectedChoiceHash' => "Your wallet must hold at least one asset under policy {$policy} to satisfy the {$ruleTitle} requirement.",
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

    private function resolvePollRuleBaseRoute(Request $request): string
    {
        $requestedRoute = (string) $request->query('returnRoute', 'polls.manage');
        $allowedRoutes = [
            'polls.manage',
            'polls.create.stepThree',
        ];

        if (! in_array($requestedRoute, $allowedRoutes, true)) {
            return 'polls.manage';
        }

        return $requestedRoute;
    }
}
