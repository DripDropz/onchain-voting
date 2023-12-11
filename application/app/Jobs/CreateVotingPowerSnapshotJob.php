<?php

namespace App\Jobs;

use App\Models\Snapshot;
use App\Models\User;
use App\Models\VotingPower;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CreateVotingPowerSnapshotJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected $snapshotHash,
        protected $voter_id,
        protected $voting_power
    ){}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = User::where('voter_id', $this->voter_id)->first();
        $snapshot = Snapshot::byHash($this->snapshotHash);

        if (is_null($user)) {
            $user = $this->createUser($this->voter_id);
        }

        $powerExists = VotingPower::where('user_id', $user->id)
        ->where('snapshot_id', $snapshot?->id)
        ->first();

        if (is_null($powerExists)) {
            VotingPower::create([
                'user_id' => $user->id,
                'snapshot_id' => $snapshot->id,
                'voting_power' => $this->voting_power,
            ]);
        } else {
            Log::info("voting Power {$powerExists} for user {$powerExists->voter_id} already exists for snapshot {$snapshot->id}");
        }
    }

    protected function createUser($voter_id)
    {
        $newUser = User::create([
            'name' => substr($voter_id, 0, 5).'...'.substr($voter_id, -5),
            'voter_id' => $voter_id,
            'password' => Hash::make(Str::random(32)),
        ]);

        return $newUser;
    }
}
