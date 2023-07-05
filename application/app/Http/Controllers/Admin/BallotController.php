<?php

namespace App\Http\Controllers\Admin;

use App\DataTransferObjects\BallotData;
use App\DataTransferObjects\QuestionChoiceData;
use App\DataTransferObjects\QuestionData;
use App\Enums\ModelStatusEnum;
use App\Enums\QuestionTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Ballot;
use App\Models\BallotQuestionChoice;
use App\Models\Question;
use App\Models\Snapshot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use JetBrains\PhpStorm\NoReturn;
use Momentum\Modal\Modal;

class BallotController extends Controller
{
    /**
     * Display the new ballot's form.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('Auth/Ballot/Create', []);
    }

    /**
     * Display the ballot's form.
     */
    public function view(Request $request, Ballot $ballot): Response
    {
        return Inertia::render('Auth/Ballot/View', [
            'ballot' => BallotData::from($ballot->load('snapshot', 'questions.choices')),
        ]);
    }

    /**
     * Display the ballot's form.
     */
    public function edit(Request $request, Ballot $ballot): Response
    {
        $ballot->load(['questions.choices', 'snapshot']);

        return Inertia::render('Auth/Ballot/Edit', [
            'ballot' => BallotData::from($ballot),
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
            $ballot->status = $ballotData->status;
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
    public function destroy(Request $request, $ballot): RedirectResponse
    {
        $response = Gate::inspect('delete', $ballot);

        if ($response->allowed()) {
            $existingBallot = Ballot::byHash($ballot);
            $existingBallot->delete();

            return Redirect::to('/');
        } else {
            return Redirect::route('admin.ballots.view', ['ballot' => $ballot]);
        }
    }

    public function createQuestion(Request $request, Ballot $ballot)
    {
        $response = Gate::inspect('create', Question::class);
        if ($response->allowed()) {
            return Inertia::modal('Auth/Question/Create')
                ->with([
                    'ballot' => BallotData::from($ballot),
                    'questionTypes' => QuestionTypeEnum::values(),
                    'questionsStatuses' => ModelStatusEnum::values(),
                ])
                ->baseRoute('admin.ballots.edit', [
                    'ballot' => $ballot->hash,
                ]);
        } else {
            return Redirect::back()->withErrors(['error' => 'Not authorized to create question']);
        }
    }

    public function editQuestion(Request $request, Ballot $ballot, Question $question)
    {
        $ballot->load(['questions']);

        return Inertia::render('Auth/Question/Edit', [
            'ballot' => BallotData::from($ballot),
            'question' => QuestionData::from($question),
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
    public function storeQuestion(Request $request, $ballot): RedirectResponse
    {
        $response = Gate::inspect('create', Question::class);

        if ($response->allowed()) {
            $question = new Question;
            $question->title = $request->title;
            $question->description = $request->description;
            $question->status = $request->status;
            $question->type = $request->type;
            $question->max_choices = $request->maxChoices;
            $question->supplemental = $request->supplemental;
            $question->user_id = Auth::id();
            $question->ballot_id = decode_model_hash($ballot?->hash, Ballot::class);
            $question->save();

            return Redirect::route('admin.ballots.view', ['ballot' => $ballot?->hash]);
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

    /**
     * Store a newly created Ballot in storage.
     */
    #[NoReturn]
    public function storeQuestionChoice(QuestionChoiceData $choiceData): RedirectResponse
    {
        $choice = new BallotQuestionChoice();
        $choice->fill($choiceData->all());
        $choice->question_id = decode_model_hash($choiceData->question->hash, Question::class);
        $choice->save();

        return Redirect::route('admin.ballots.edit', ['ballot' => $choice?->ballot?->hash]);
    }

    public function updatePosition(Request $request)
    {
        $choice = BallotQuestionChoice::byHash($request->hash);

        $choice->update([
            'order' => round($request->order, 5),
        ]);

        return Redirect::back();
    }
}
