<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\BallotResponseData;
use App\DataTransferObjects\VoterData;
use App\Models\Ballot;
use App\Models\BallotQuestionChoice;
use App\Models\BallotResponse;
use App\Models\User;
use App\Models\VotingPower;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

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
        ->firstOrFail()?->voting_power;
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
        $ballot = Ballot::byHashOrFail($request->ballot_hash);
        $choice = BallotQuestionChoice::byHashOrFail($request->choice_hash);
        $votingPower = VotingPower::where([
            'user_id' => $voter->id,
            'snapshot_id' => $ballot->snapshot?->id
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

        return BallotResponseData::from($ballotResponse);
    }
}
