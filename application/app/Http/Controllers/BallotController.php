<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\BallotData;
use App\DataTransferObjects\QuestionData;
use App\Enums\ModelStatusEnum;
use App\Enums\QuestionTypeEnum;
use App\Http\Requests\BallotCreateRequest;
use App\Models\Ballot;
use App\Models\Question;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        return Inertia::render('Ballot/Create', [
        ]);
    }

    /**
     * Display the ballot's form.
     */
    public function view(Request $request, Ballot $ballot): Response
    {
        return Inertia::render('Ballot/View', [
            'ballot' => BallotData::from($ballot)
        ]);
    }

    /**
     * Display the ballot's form.
     */
    public function edit(Request $request, Ballot $ballot): Response
    {
        return Inertia::render('Ballot/Edit', [
            'ballot' => BallotData::from($ballot)
        ]);
    }

    /**
     * Store a newly created Ballot in storage.
     */
    public function store(BallotData $ballotData): RedirectResponse
    {
        $ballot = new Ballot;
        $ballot->fill($ballotData->all());
        $ballot->save();

        return Redirect::route('ballots.view', ['ballot' => $ballot->hash]);
    }

    /**
     * Update the ballot's profile information.
     */
    public function update(BallotData $ballotData, Ballot $ballot): RedirectResponse
    {
        $startedDateTime = $ballot->started_at ?? null;
        $currentDateTime = Carbon::now($tz = 'UTC');

        if (($startedDateTime == null || $startedDateTime > $currentDateTime)) {
            $ballot->update($ballotData->all());

            return Redirect::route('ballots.view', ['ballot' => $ballot->hash]);
        }

        return Redirect::route('ballots.edit', ['ballot' => $ballot]);
    }

    /**
     * Delete the ballot's account.
     */
    public function destroy(Request $request, $ballot): RedirectResponse
    {
        $user = Auth::user();

        if ($user->hasRole('super-admin') == true) {
            $existingBallot = Ballot::byHash($ballot);
            $existingBallot->delete();

            return Redirect::to('/');
        }

        return Redirect::route('ballots.view', ['ballot' => $ballot]);
    }

    public function createQuestion(Request $request, Ballot $ballot): Modal
    {
        return Inertia::modal('Question/Create')
            ->with([
                'ballot' => BallotData::from($ballot),
                'questionTypes' => QuestionTypeEnum::values(),
                'questionsStatuses' => ModelStatusEnum::values(),
            ])
            ->baseRoute('ballots.edit', [
                'ballot' => $ballot->hash
            ]);
    }

    /**
     * Store a newly created Ballot in storage.
     */
    #[NoReturn] public function storeQuestion(QuestionData $questionData): RedirectResponse
    {
        $question = new Question;
        $question->fill($questionData->all());
        $question->ballot_id = decode_model_hash($questionData->ballot->hash, Ballot::class);
        $question->save();

        return Redirect::route('ballots.edit', ['ballot' => $question?->ballot?->hash]);
    }
}
