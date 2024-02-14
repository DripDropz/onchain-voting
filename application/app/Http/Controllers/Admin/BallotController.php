<?php

namespace App\Http\Controllers\Admin;

use App\DataTransferObjects\BallotData;
use App\DataTransferObjects\QuestionChoiceData;
use App\DataTransferObjects\QuestionData;
use App\Enums\ModelStatusEnum;
use App\Enums\QuestionTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Integrations\Lucid\LucidConnector;
use App\Http\Integrations\Lucid\Requests\GetAddress;
use App\Http\Integrations\Lucid\Requests\GetPolicy;
use App\Http\Integrations\Lucid\Requests\GetPolicyId;
use App\Models\Ballot;
use App\Models\Policy;
use App\Models\Question;
use App\Models\QuestionChoice;
use App\Models\Snapshot;
use App\Models\Wallet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use JetBrains\PhpStorm\NoReturn;
use Momentum\Modal\Modal;
use Saloon\Exceptions\Request\FatalRequestException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Saloon\Exceptions\Request\RequestException;

class BallotController extends Controller
{
    public function index()
    {
        $ballots = Ballot::all();
        $crumbs = [

            [
                'label' => 'Ballots',
                'link' => route('admin.ballots.index')
            ],
        ];

        return Inertia::render(
            'Auth/Ballot/Index',
            [
                'ballots' => $ballots,
                'crumbs' => $crumbs,
            ]
        );
    }

    public function allBallots()
    {
        $ballots = Ballot::query()->with('questions')->get();
        return BallotData::collection($ballots);
    }

    public function ballotsData(Request $request)
    {
        $page = $request->query('page') ?? 1;
        $perPage = $request->query('perPage') ?? 6;

        $ballots = Ballot::paginate($perPage, ['*'], 'page', $page);

        return BallotData::collection($ballots);
    }

    /**
     * Display the new ballot's form.
     */
    public function create(Request $request): Response
    {
        $crumbs = [
            [
                'label' => 'Ballots',
                'link' => route('admin.ballots.index')
            ],
            [
                'label' => 'Create Ballot',
                'link' => route('admin.ballots.create')
            ],
        ];
        return Inertia::render('Auth/Ballot/Create', [
            'crumbs' => $crumbs,
        ]);
    }

    /**
     * Display the ballot's form.
     */
    public function view(Request $request, Ballot $ballot): Response
    {
        $crumbs = [
            [
                'label' => 'Ballots',
                'link' => route('admin.ballots.index')
            ],
            [
                'label' => 'View Ballot',
                'link' => route('admin.ballots.view', ['ballot' => $ballot])
            ],
        ];
        return Inertia::render('Auth/Ballot/View', [
            'ballot' => BallotData::from($ballot->load('snapshot', 'questions.choices', 'policies')),
            'crumbs' => $crumbs,
        ]);
    }

    /**
     * Display the ballot's form.
     */
    public function edit(Request $request, Ballot $ballot): Response
    {
        $ballot->load(['questions.choices', 'snapshot', 'policies']);

        $crumbs = [
            [
                'label' => 'Ballots',
                'link' => route('admin.ballots.index')
            ],
            [
                'label' => 'Edit Ballot',
                'link' => route('admin.ballots.edit', ['ballot' => $ballot])
            ],
        ];
        return Inertia::render('Auth/Ballot/Edit', [
            'ballot' => BallotData::from($ballot),
            'addresses' => $this->policyAddresses($ballot),
            'crumbs' => $crumbs,
        ]);
    }

    /**
     * Store a newly created Ballot in storage.
     */
    public function store(BallotData $ballotData): RedirectResponse
    {
        $response = Gate::inspect('create', Ballot::class);

        if ($response->allowed()) {
            $ballot = new Ballot;
            $ballot->fill($ballotData->all());
            $ballot->save();

            return Redirect::route('admin.ballots.view', ['ballot' => $ballot->hash]);
        } else {
            return Redirect::back()->withErrors(['error' => 'Not authorized to create ballot']);
        }
    }

    /**
     * Update the ballot's profile information.
     */
    public function update(BallotData $ballotData)
    {

        $ballot = Ballot::byHash($ballotData->hash);
        $response = $ballotData->status == 'published' ? Gate::inspect('publish', $ballot) : Gate::inspect('update', $ballot);

        if ($response->allowed()) {
            $ballot->title = $ballotData->title;
            $ballot->description = $ballotData->description;
            $ballot->version = $ballotData->version;
            $ballot->type = $ballotData->type;
            $ballot->started_at = $ballotData->started_at;
            $ballot->ended_at = $ballotData->ended_at;
            $ballot->update();

            return Redirect::back();
        } else {
            return Redirect::back()->withErrors(['error' => 'Not authorized']);
        }
    }

    /**
     * Delete the ballot's account.
     */
    public function destroy(Request $request, Ballot $ballot): RedirectResponse
    {
        $password = $request->input('password');
        if (!$password) {
            return Redirect::back()->withErrors(['password' => 'Password is required.']);
        }

        if (Hash::check($password, Auth::user()->password)) {
            $response = Gate::inspect('delete', $ballot);

            if ($response->allowed()) {
                $existingBallot = Ballot::byHash($ballot->hash);
                $existingBallot->delete();

                return Redirect::route('admin.dashboard');
            } else {
                return Redirect::route('admin.ballots.view', ['ballot' => $ballot]);
            }
        } else {
            return Redirect::back()->withErrors(['password' => 'Invalid password for current user.']);
        }
    }

    /**
     * Update the ballot's status.
     */
    public function statusUpdate(Ballot $ballot)
    {
        $ballot = Ballot::byHash($ballot->hash);
        $response = $ballot->status == 'published' ? Gate::inspect('publish', $ballot) : Gate::inspect('update', $ballot);

        if ($response->allowed()) {
            $ballot->status = ModelStatusEnum::PUBLISHED;
            $ballot->update();
            return Redirect::back();
        } else {
            return Redirect::back()->withErrors(['error' => 'Not authorized']);
        }
    }


    /**
     * Create a ballot's question.
     * Checks if the user is authorized to create resource.
     * @param Request $request
     * @param Ballot $ballot
     */
    public function createQuestion(Request $request, Ballot $ballot)
    {
        $response = Gate::inspect('create', Question::class);
        if ($response->allowed()) {
            $crumbs = [
                ['label' => 'Create Question', 'link' => route('admin.ballots.questions.create', ['ballot' => $ballot->hash])],
            ];
            return Inertia::modal('Auth/Question/Create')
                ->with([
                    'ballot' => BallotData::from($ballot),
                    'questionTypes' => QuestionTypeEnum::values(),
                    'questionsStatuses' => ModelStatusEnum::values(),
                    'crumbs' => $crumbs,
                ])
                ->baseRoute('admin.ballots.edit', [
                    'ballot' => $ballot->hash,
                ]);
        } else {
            return Redirect::back()->withErrors(['error' => 'Not authorized to create question']);
        }
    }

    /**
     * Load Ballot's edit question form.
     * @param Request $request
     * @param Ballot $ballot
     * @param Question $question
     * @return Response
     */
    public function editQuestion(Request $request, Ballot $ballot, Question $question)
    {
        $ballot->load(['questions']);
        $crumbs = [
            [
                'label' => 'Edit Question',
                'link' => route('admin.ballots.questions.edit', [
                    'ballot' => $ballot->hash,
                    'question' => $question->hash
                ])
            ],
        ];

        return Inertia::render('Auth/Question/Edit', [
            'ballot' => BallotData::from($ballot),
            'question' => QuestionData::from($question),
            'crumbs' => $crumbs,
        ]);
    }

    /**
     * Store a newly created Question in storage.
     */
    #[NoReturn]
    public function updateQuestion(Request $request, $ballot, $question): RedirectResponse
    {
        $response = Gate::inspect('update', $question);

        if ($response->allowed()) {
            $question = Question::byHash($question->hash);
            $question->title = $request->title;
            $question->description = $request->description;
            $question->status = $request->status;
            $question->type = $request->type;
            $question->max_choices = $request->maxChoices;
            $question->supplemental = $request->supplemental;
            $question->update();

            return Redirect::route('admin.ballots.view', ['ballot' => $ballot->hash]);
        } else {
            return redirect()->route('admin.ballots.view', ['ballot' => $ballot->hash])->withErrors([
                'error' => 'Not authorized to update this question!',
            ]);
        }
    }

    /**
     * Store a newly created Question in storage.
     */
    #[NoReturn]
    public function storeQuestion(Request $request, $ballot)
    {
        $response = Gate::inspect('create', Question::class);

        if ($response->allowed()) {
            $question = $ballot->questions()->create([
                'title' => $request->title,
                'description' => $request->description,
                'status' => $request->status,
                'type' => $request->type,
                'maxChoices' => $request->maxChoices,
                'supplemental' => $request->supplemental,
                'user_id' => Auth::id(),
                'model_type' => Ballot::class,
            ]);
        } else {
            return redirect()->route('admin.ballots.view', ['ballot' => $ballot?->hash])->withErrors([
                'error' => 'Not authorized to create question!',
            ]);
        }
    }

    /**
     * Delete the ballot's account.
     */
    public function destroyQuestion(Request $request, Ballot $ballot, Question $question)
    {
        $response = Gate::inspect('delete', $question);

        if ($response->allowed()) {
            $queestion = Question::where('id', $question->id)->first();
            $queestion->choices()->delete();
            $queestion->delete();
        } else {
            return Redirect::back()->withErrors(['error' => 'Not authorized to delete question']);
        }
    }

    public function createQuestionChoice(Request $request, Ballot $ballot, Question $question): Modal
    {
        $question->load('ballot');

        return Inertia::modal('Auth/Question/QuestionChoice/Create')
            ->with([
                'question' => QuestionData::from($question),
                'ballot' => $question?->ballot,
            ])
            ->baseRoute('admin.ballots.edit', [
                'ballot' => $question?->ballot->hash,
            ]);
    }

    public function editQuestionChoice(Request $request, Ballot $ballot, Question $question): Modal
    {
        $question->load('ballot');
        $choice = QuestionChoice::byHash($request->choice);

        return Inertia::modal('Auth/Question/QuestionChoice/Edit')
            ->with([
                'question' => QuestionData::from($question),
                'ballot' => $question?->ballot,
                'choice' => $choice,
            ])
            ->baseRoute('admin.ballots.edit', [
                'ballot' => $question?->ballot->hash,
            ]);
    }

    public function viewLinkSnapshot(Ballot $ballot)
    {
        return Inertia::modal('Auth/Snapshot/Partials/SnapshotPicker')
            ->with([
                'ballot' => $ballot,
            ])
            ->baseRoute(previous_route_name(), [
                'ballot' => $ballot->hash,
            ]);
    }

    public function linkSnapshot(Ballot $ballot, Snapshot $snapshot)
    {
        try {
            $ballot->snapshot()->save($snapshot);

            return Redirect::route('admin.ballots.edit', ['ballot' => $ballot?->hash]);
        } catch (\Exception $e) {
            return Redirect::back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Store a newly created Ballot in storage.
     */
    #[NoReturn]
    public function storeQuestionChoice(QuestionChoiceData $choiceData)
    {
        $choice = new QuestionChoice();
        $choice->fill($choiceData->all());
        $choice->question_id = decode_model_hash($choiceData->question->hash, Question::class);
        $choice->save();
    }

    public function storeEditQuestionChoice(QuestionChoiceData $choiceData, Request $request)
    {
        $choice = QuestionChoice::byHash($request->choice);

        $choice->update([
            'title' => $choiceData->title,
            'description' => $choiceData->description,
        ]);
    }

    public function deleteQuestionChoice(Request $request)
    {
        $choice = QuestionChoice::byHash($request->choice);
        $choice->delete();
    }

    public function updatePosition(Request $request)
    {
        $choice = QuestionChoice::byHash($request->hash);

        $choice->update([
            'order' => round($request->order, 5),
        ]);

        return Redirect::back();
    }

    public function createPolicy(Request $request, Ballot $ballot)
    {

        $ballot->load(['policies']);
        // @todo add similar gate for policies
        // $response = Gate::inspect('create', Policy::class);
        // if ($response->allowed()) {
        return Inertia::modal('Auth/Policies/Create')
            ->with([
                'ballot' => BallotData::from($ballot),
            ])
            ->baseRoute('admin.ballots.edit', [
                'ballot' => $ballot->hash,
            ]);
        // } else {
        //     return Redirect::back()->withErrors(['error' => 'Not authorized to create question']);
        // }
    }

    public function editPolicy(Request $request, Ballot $ballot)
    {
    }

    /**
     * Store a wallet and generate Ballot policy.
     * Calls Lucid API to generate policy.
     * @param Request $request
     * @param Ballot $ballot
     * @return RedirectResponse
     * @throws FatalRequestException
     * @throws \JsonException
     * @throws RequestException
     * @todo add gate for confirming user create/update policies
     */
    public function storePolicy(Request $request, Ballot $ballot)
    {
        $data = $request->validate([
            'context' => 'required|string',
            'seedphrase' => 'required|string',
        ]);

        try {
            DB::beginTransaction();
            // create policy
            $policyRequest = new GetPolicy;
            $policyRequest->body()->merge([
                'seed' => $data['seedphrase']
            ]);
            $lucid = new LucidConnector;
            $policyResponse = $lucid->send($policyRequest);
            if ($policyResponse->failed()) {
                DB::rollBack();
                return Redirect::back()
                    ->withErrors(['error' => $policyResponse->object()?->message]);
            }

            $policy = new Policy;
            $policy->script = $policyResponse->json();
            $policy->user_id = Auth::id();
            $policy->model_id = $ballot->id;
            $policy->model_type = Ballot::class;
            $policy->context = $data['context'];
            $policy->save();

            // creaet wallet that will be the signer on policy
            $wallet = new Wallet();
            $wallet->user_id = Auth::id();
            $wallet->model_id = $ballot->id;
            $wallet->model_type = Ballot::class;
            $wallet->context_id = $policy->id;
            $wallet->context_type = Policy::class;
            $wallet->passphrase = $data['seedphrase'];

            $wallet->save();

            DB::commit();

            // generate policy id
            $connector = new LucidConnector;
            $getPolicyIdRequest = new GetPolicyId;

            $getPolicyIdRequest->body()->merge([
                'seed' => $policy?->wallet?->passphrase,
            ]);
            $response = $connector->sendAndRetry(
                $getPolicyIdRequest,
                2,
                300,
                fn ($exception) => $exception instanceof FatalRequestException
            );
            $policy->policy_id = $response->body();
            $policy->save();


            return Redirect::back();
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->withErrors(['error' => 'An error occurred while creating the policy. Please try again later.']);
        }
    }

    public function policyAddresses($ballot)
    {
        $firstPolicySeed = null;
        $secondPolicySeed = null;
        $registrationPolicyAddress = null;
        $votingPolicyAddress = null;

        foreach ($ballot->policies as $index => $policy) {
            $seed = $policy->wallet->passphrase ?? null;

            if ($index === 0) {
                $firstPolicySeed = $seed;
            } elseif ($index === 1) {
                $secondPolicySeed = $seed;
            }

            if ($index >= 1) {
                break;
            }
        }
        if ($firstPolicySeed) {
            $policyAddress = new GetAddress;
            $policyAddress->body()->merge([
                'seed' => $firstPolicySeed
            ]);
            $lucid = new LucidConnector;
            $policyResponse = $lucid->send($policyAddress);

            $registrationPolicyAddress = $policyResponse->body() ?? null;
        }

        if ($secondPolicySeed) {
            $policyAddress = new GetAddress;
            $policyAddress->body()->merge([
                'seed' => $secondPolicySeed
            ]);
            $lucid = new LucidConnector;
            $policyResponse = $lucid->send($policyAddress);

            $votingPolicyAddress = $policyResponse->body();
        }

        return [
            'registrationPolicyAddress' => $registrationPolicyAddress,
            'votingPolicyAddress' => $votingPolicyAddress,
        ];
    }

    public function addImageLink(Request $request, Ballot $ballot, Policy $policy)
    {
        $response = Gate::inspect('update', $ballot);
        if ($response->allowed()) {
            $policy->image_link = $request->link;
            $policy->save();
            return true;
        } else {
            $this->unauthenticated($request);
        }
        return false;
    }

    public function unLinkSnapShot(Ballot $ballot, Snapshot $snapshot)
    {
        $currentDate = Carbon::now();
        $startDate = Carbon::parse($ballot->started_at);

        if ($currentDate->greaterThanOrEqualTo($startDate)) {
            throw new \Exception('Cannot remove snapshot, the ballot has already started!');
        }

        try {
            $snapshot->ballot_id = null;
            $snapshot->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
