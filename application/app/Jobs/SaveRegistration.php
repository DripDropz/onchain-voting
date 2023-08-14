<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Ballot;
use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Saloon\Exceptions\Request\FatalRequestException;
use App\Http\Integrations\Blockfrost\BlockfrostConnector;
use App\Http\Integrations\Blockfrost\Requests\BlockfrostRequest;

class SaveRegistration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected $user_id, protected $ballot_hash, protected string $txHash)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $ballot = Ballot::byHash($this->ballot_hash);
        $user = User::findOrFail($this->user_id);
        $blockfrostConn = new BlockfrostConnector;
        $blockfrostReq = new BlockfrostRequest('/txs/' . $this->txHash);
        $res = $blockfrostConn->sendAndRetry(
            $blockfrostReq,
            3,
            1000,
            fn ($exception) => $exception instanceof FatalRequestException
        );

        $txOutputs = $res->json()['output_amount'];
        $policy = $ballot->registration_policy->policy_id;
        $assetName = null;
        foreach ($txOutputs as $asset) {
            if (strstr($asset["unit"], $policy)) {
                $assetName = $asset["unit"];
                break;
            }
        }

        if (isset($assetName)) {
            $registration = new Registration;
            $registration->user_id = $user->id;
            $registration->ballot_id = $ballot->id;
            $registration->voting_power_id = $user->voting_power()->first()->id;
            $registration->registration_tx = $this->txHash;
            $registration->asset_name = $assetName;
            $registration->save();
        }
    }
}
