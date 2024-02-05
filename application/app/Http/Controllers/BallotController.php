<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Ballot;
use App\Models\VotingPower;
use Illuminate\Http\Request;
use App\Jobs\SaveRegistration;
use Illuminate\Support\Facades\Auth;
use App\DataTransferObjects\BallotData;
use App\Http\Integrations\Lucid\LucidConnector;
use App\Http\Integrations\Blockfrost\BlockfrostConnector;
use App\Http\Integrations\Blockfrost\Requests\BlockfrostRequest;
use Illuminate\Http\Response as LaravelResponse;
use JetBrains\PhpStorm\NoReturn;
use Saloon\Exceptions\Request\FatalRequestException;
use App\Http\Integrations\Lucid\Requests\GetPolicyId;
use App\Http\Integrations\Lucid\Requests\StartVoting;
use App\Http\Integrations\Lucid\Requests\CompleteVoting;
use App\Http\Integrations\Lucid\Requests\StartRegistration;
use App\Http\Integrations\Lucid\Requests\CompleteRegistration;
use App\Models\Registration;

class BallotController extends Controller
{
    /**
     * Display the ballot list.
     */
    public function index(Request $request): Response
    {
        $ballots = BallotData::collection(
            Ballot::with([
                'questions.choices',
                'user_responses.choices',
            ])->orderBy('started_at')->published()->get()
        );

        $crumbs = [

            [
                'label' => 'Ballots',
                'link' => route('ballots.index')
            ],
        ];

        return Inertia::render('Ballot/Index', [
            'ballots' => $ballots,
            'crumbs' => $crumbs,
        ]);
    }

    /**
     * Display a single ballot.
     */
    public function view(Request $request, Ballot $ballot): Response
    {
        $questions = Question::with('choices')
            ->where([
                'model_id' => $ballot->id,
                'model_type' => $ballot::class,
            ])->get()->append('choices_tally');
        $ballot->load([
            'user_responses.choices'
        ]);
        $ballot->questions = $questions;
        $crumbs = [
            [
                'label' => 'Ballots',
                'link' => route('ballots.index')
            ],
            [
                'label' => 'Ballot Details',
                'link' => route('ballot.view', ['ballot' => $ballot])
            ],
        ];

        return Inertia::render('Ballot/View', [
            'ballot' => BallotData::from($ballot),
            'crumbs' => $crumbs,
        ]);
    }

    /**
     * Display the ballot's form.
     */
    public function viewRegistration(Request $request, Ballot $ballot)
    {
        $ballot->load(['questions.choices', 'user_responses.choice']);

        return Inertia::modal('Ballot/Register')
            ->with([
                'ballot' => BallotData::from($ballot),
            ])
            ->baseRoute('ballot.view', $ballot->hash);
    }

    /**
     * Display the ballot's form.
     */
    public function startRegistration(Request $request, Ballot $ballot): LaravelResponse
    {
        $user = Auth::user();
        $ballot->load(['questions.choices', 'user_responses.choice']);

        $existingRegistrationTx = $this->checkExistingRegistration($request->addr, $ballot);

        if (isset($existingRegistrationTx)) {
            return response([
                'existingTx' => $existingRegistrationTx
            ]);
        }

        // get tx from lucid
        $connector = new LucidConnector;
        $startRegistration = new StartRegistration;
        $startRegistration->body()->merge([
            'seed' => $ballot->registration_policy?->wallet?->passphrase,
            'voterId' => $user->voter_id,
            'assetName' => md5("{$user->voter_id}{$user->id}"),
            'voterAddress' => $request->addr,
            'metadata' => [
                'name' => $ballot->title,
                'image' => $ballot->registration_policy->image_link,
                'Powered by' => config('app.powered_by'),
                'Cast Your Vote At' => config('app.url')
            ],
        ]);

        $response = $connector->sendAndRetry(
            $startRegistration,
            2,
            300,
            fn($exception) => $exception instanceof FatalRequestException
        );

        return response([
            'tx' => $response->body()
        ]);
    }

    public function checkExistingRegistration($address, $ballot)
    {
        $blockFrostRequest = app(BlockfrostRequest::class);
        $blockfrostConn = new BlockfrostConnector();
        $blockFrostRequest->setEndPoint("/addresses/{$address}/total");

        $response = $blockfrostConn->sendAndRetry(
            $blockFrostRequest,
            2,
            300,
            fn($exception) => $exception instanceof FatalRequestException
        );

        if (!isset($response->json()['received_sum'])) {
            return null;
        }

        $txOutputs = $response->json()['received_sum'];
        $policy = $ballot->registration_policy->policy_id;
        $assetName = null;
        foreach ($txOutputs as $asset) {
            if (strstr($asset["unit"], $policy)) {
                $assetName = $asset["unit"];
                break;
            }
        }

        if (!isset($assetName)) {
            return null;
        }

        $blockFrostRequest->setEndPoint("/addresses/{$address}/utxos/{$assetName}");
        $response = $blockfrostConn->sendAndRetry(
            $blockFrostRequest,
            2,
            300,
            fn($exception) => $exception instanceof FatalRequestException
        );


        if (!isset($response->json()[0]['tx_hash'])) {
            return null;
        }
        return $response->json()[0]['tx_hash'];
    }

    public function policyId(Request $request, Ballot $ballot, string $policyType)
    {
        $policyRequest = new GetPolicyId;
        $policy = "{$policyType}_policy";
        $policyRequest->body()->merge([
            'seed' => $ballot->{$policy}?->wallet?->passphrase,
        ]);
        $lucid = new LucidConnector;
        $response = $lucid->sendAndRetry(
            $policyRequest,
            2,
            300,
            fn($exception) => $exception instanceof FatalRequestException
        );
        return $response->body();
    }

    public function submitRegistration(Request $request, Ballot $ballot): LaravelResponse
    {
        $user = Auth::user();

        // get tx from lucid
        $connector = new LucidConnector;
        $completeRegistration = new CompleteRegistration;
        $completeRegistration->body()->merge([
            'seed' => $ballot->registration_policy?->wallet?->passphrase,
            'voterId' => $user->voter_id,
            'assetName' => md5("{$user->voter_id}{$user->id}"),
            'voterAddress' => $request->addr,
            'tx' => $request->tx,
            'witnesses' => $request->witnesses,
            'voterStakekey' => $user->voter_id
        ]);

        $response = $connector->sendAndRetry(
            $completeRegistration,
            2,
            300,
            fn($exception) => $exception instanceof FatalRequestException
        );


        if ($response->body() != null) {
            SaveRegistration::dispatch($user->id, $ballot->hash, $response->body())->delay(now()->addSeconds(40));
        }
        return response([
            'tx' => $response->body()
        ]);
    }

    #[NoReturn]
    public function startVoting(Request $request, Ballot $ballot)
    {
        $data = $request->validate([
            'registration' => 'required',
            'utxos' => 'required',
        ]);
        $user = Auth::user();
        $votingPower = VotingPower::where('user_id', $user->id)
            ->where('snapshot_id', $ballot->snapshot?->id)
            ->firstOrFail()?->voting_power;

        // get voter ballot response choices map
        $choices = $ballot->user_responses
            ->map(
                function ($response) {
                    return [
                        $response->question_hash => $response->choices->map(fn($choice) => $choice->hash)->toArray()
                    ];
                }
            )->collapse()->toArray();

        $submitVote = new StartVoting;
        $submitVote->body()->merge([
            'assetName' => md5("{$user->voter_id}{$user->id}"),
            'voterId' => $user->voter_id,
            'ballotHash' => $ballot->hash,
            'choices' => $choices,
            'utxos' => $data['utxos'],
            'votingPower' => $votingPower,
            'registration' => $data['registration'],
            'votingSeed' => $ballot->voting_policy?->wallet?->passphrase,
            'registrationSeed' => $ballot->registration_policy?->wallet?->passphrase,
        ]);

        $connector = new LucidConnector;
        $response = $connector->sendAndRetry($submitVote, 3, 100, function ($exception) {
            return $exception instanceof FatalRequestException;
        });

        return $response->body();
    }

    public function saveUpdateRegistration(Request $request, Ballot $ballot)
    {
        $user = Auth::user();
        $registration = Registration::where('user_id', $user->id)
            ->where('ballot_id', $ballot->id)
            ->first();

        if ($registration instanceof Registration) {
            $registration->registration_tx = $request->registration_tx;

            $registration->save();
        } else {
            $reg = new Registration();
            $reg->user_id = $user->id;
            $reg->ballot_id = $ballot->id;
            $reg->voting_power_id = $user->voting_power->id;
            $reg->asset_name = md5("{$user->voter_id}{$user->id}");
            $reg->registration_tx = $request->registration_tx;

            $reg->save();
        }
    }

    public function completeVoting(Request $request, Ballot $ballot): LaravelResponse
    {
        $user = Auth::user();

        // get tx from lucid
        $connector = new LucidConnector;
        $completeRegistration = new CompleteVoting;
        $completeRegistration->body()->merge([
            'votingSeed' => $ballot->voting_policy?->wallet?->passphrase,
            'registrationSeed' => $ballot->registration_policy?->wallet?->passphrase,
            'voterId' => $user->voter_id,
            'assetName' => md5("{$user->voter_id}{$user->id}"),
            'voterAddress' => $request->addr,
            'tx' => $request->tx,
            'witnesses' => $request->witnesses,
            'voterStakekey' => $user->voter_id
        ]);

        $response = $connector->sendAndRetry(
            $completeRegistration,
            2,
            300,
            fn($exception) => $exception instanceof FatalRequestException
        );

        if (!$response->successful()) {
            //@todo handle failure
        }

        // update ballot response
        $ballot->user_responses()->update([
            'submit_tx' => $response->body(),
        ]);

        return response([
            'tx' => $response->body()
        ]);
    }

    /**
     * Display missing snapshot modal.
     */

    public function missingSnapshot(Ballot $ballot)
    {
        return Inertia::modal('Ballot/MissingSnapshot')
            ->baseRoute('ballot.view', $ballot->hash);
    }
}
