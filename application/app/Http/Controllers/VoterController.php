<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\BallotResponseData;
use App\DataTransferObjects\VoterData;
use App\Http\Integrations\Lucid\Requests\SubmitVote;
use App\Http\Integrations\Lucid\WalletConnector;
use App\Models\Ballot;
use App\Models\BallotQuestionChoice;
use App\Models\BallotResponse;
use App\Models\User;
use App\Models\VotingPower;
use Illuminate\Http\Request;
use Saloon\Exceptions\Request\FatalRequestException;

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

    public function submitVote(Request $request, string $voterId)
    {
        $user = User::where('voter_id', $voterId)->firstOrFail();
        $ballot = Ballot::byHashOrFail($request->ballot_id);
        $votingPower = VotingPower::where('user_id', $user->id)
            ->where('snapshot_id', $ballot->snapshot?->id)
            ->firstOrFail()?->voting_power;

        $submitVote = new SubmitVote;
        $submitVote->body()->merge([
            'voterId' => $voterId,
            'ballotId' => $request->get('ballot_id'),
            'choices' => $request->get('choices'),
            'votingPower' => $votingPower,
            'seed' => 'finish mammal voice famous ocean spike time emerge gallery area quote dune crater calm month fiscal seminar crime orange pride never danger spirit destroy',
            'voterAddress' => $request->get('address'),
        ]);

        $connector = new WalletConnector;

        $response = $connector->sendAndRetry($submitVote, 3, 100, function ($exception) {
            return $exception instanceof FatalRequestException;
        });

        return $response->body();
    }
}
