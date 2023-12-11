<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\BallotResponseData;
use App\DataTransferObjects\QuestionChoiceData;
use App\DataTransferObjects\VoterData;
use App\Models\Ballot;
use App\Models\BallotQuestionChoice;
use App\Models\BallotResponse;
use App\Models\User;
use App\Models\VotingPower;
use Illuminate\Http\Request;

class VoterController extends Controller
{
    /**
     * Display the ballot's form.
     */
    public function power(Request $request, string $voterId, Ballot $ballot)
    {
        $user = User::where('voter_id', $voterId)->firstOrFail();

        return VotingPower::where('user_id', $user->id)
            ->where('snapshot_id', $ballot->snapshot?->id)
            ->firstOrFail()?->voting_power / 1000000;
    }

    /**
     * Display the ballot's form.
     */
    public function voter(Request $request, string $voterId)
    {
        return VoterData::from(User::where('voter_id', $voterId)->firstOrFail());
    }

    public function saveBallotResponse(Request $request, string $voterId)
    {
        if (isset($request->choices)) {
            return $this->saveRankedChoiceBallotResponse($request->input(), $voterId);
        }


        $voter = User::where('voter_id', $voterId)->firstOrFail();
        $ballot = Ballot::byHashOrFail($request->ballot_hash);
        $choice = BallotQuestionChoice::byHashOrFail($request->choice_hash);
        $votingPower = VotingPower::where([
            'user_id' => $voter->id,
            'snapshot_id' => $ballot->snapshot?->id,
        ])->firstOrFail();

        $ballotResponse = BallotResponse::updateOrCreate([
            'ballot_id' => $ballot->id,
            'question_id' => $choice?->question->id,
            'user_id' => $voter->id,
        ], [
            'ballot_id' => $ballot->id,
            'question_id' => $choice?->question->id,
            'voting_power_id' => $votingPower->id,
            'user_id' => $voter->id,
            'ballot_question_choice_id' => $choice->id,
        ]);

        return BallotResponseData::from($ballotResponse->load(['user', 'ballot', 'question', 'choice', 'voting_power']));
    }

    public function saveRankedChoiceBallotResponse($rankedChoiceData, $voterId)
    {
        $voter = User::where('voter_id', $voterId)->firstOrFail();
        $ballot = Ballot::byHashOrFail($rankedChoiceData['ballot_hash']);
        $votingPower = VotingPower::where([
            'user_id' => $voter->id,
            'snapshot_id' => $ballot->snapshot?->id,
        ])->firstOrFail();

        $responses = collect([]);
        // normal updateOr create
        foreach ($rankedChoiceData['choices'] as $choiceData) {
            $choice = BallotQuestionChoice::byHashOrFail($choiceData['hash']);
            $response = BallotResponse::updateOrCreate([
                'ballot_id' => $ballot->id,
                'question_id' => $choice?->question->id,
                'user_id' => $voter->id,
                'ballot_question_choice_id' => $choice->id,
            ], [
                'ballot_id' => $ballot->id,
                'question_id' => $choice?->question->id,
                'voting_power_id' => $votingPower->id,
                'user_id' => $voter->id,
                'ballot_question_choice_id' => $choice->id,
                'rank' => $choiceData['index'],
            ]);

            $responses->push($response->load(['user', 'ballot', 'question', 'choice', 'voting_power']));
        }

        // delete diselected
        $currentChoiceIds = $responses->pluck('ballot_question_choice_id');
        $questionId = BallotQuestionChoice::find($currentChoiceIds[0])->question->id;
        BallotResponse::where('ballot_id', $ballot->id)
            ->where(['question_id' => $questionId, 'user_id' => $voter->id])
            ->whereNotIn('ballot_question_choice_id', $currentChoiceIds)
            ->delete();

        return BallotResponseData::collection($responses);
    }
}
