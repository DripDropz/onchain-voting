<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\BallotData;
use App\DataTransferObjects\VoterData;
use App\Models\Ballot;
use App\Models\Question;
use App\Models\QuestionChoice;
use App\Models\QuestionResponse;
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
        $voter = User::where('voter_id', $voterId)->firstOrFail();
        $ballot = Ballot::byHashOrFail($request->ballot);
        $choices = collect($request->choices)
            ->map(
                fn($hash) => QuestionChoice::byHashOrFail($hash)
            );

        $votingPower = VotingPower::where([
            'user_id' => $voter->id,
            'snapshot_id' => $ballot->snapshot?->id,
        ])->firstOrFail();

        $ballotResponse = QuestionResponse::updateOrCreate([
            'model_id' => $ballot->id,
            'model_type' => Ballot::class,
            'question_id' => $choices->first()?->question->id,
            'user_id' => $voter->id,
        ], [
            'model_id' => $ballot->id,
            'model_type' => Ballot::class,
            'question_id' => $choices->first()?->question->id,
            'voting_power_id' => $votingPower->id,
            'user_id' => $voter->id
        ]);

        $ballotResponse->choices()->sync($choices->pluck('id'));

        $ballot->refresh();
        $questions = Question::with('choices')
            ->where([
                'model_id' => $ballot->id,
                'model_type' => Ballot::class
            ])
            ->get()->append('choices_tally');
        $ballot->load([
            'user_responses.user',
            'user_responses.choices',
            'user_responses.voting_power',
        ]);
        $ballot->questions = $questions;
        return BallotData::from($ballot);
    }
}
