<?php

namespace App\Jobs;

use App\Models\Snapshot;
use App\Models\User;
use App\Models\VotingPower;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateVotingPowerSnapshotJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected $snapshotHash, protected $voter_id, protected $voting_power)
    {
        //
    }

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
        
        $powerExists = VotingPower::where('user_id', $user->id)->where('snapshot_id', $snapshot->id)->first();
        if (is_null($powerExists)) {
            VotingPower::create([
                'user_id' => $user->id,
                'snapshot_id' => $snapshot->id,
                'voting_power' => $this->voting_power,
            ]);
        }
    }

    protected function createUser($voter_id)
    {
        $newUser = User::create([
            'name' => substr($voter_id, 0, 5) . '...' . substr($voter_id, -5),
            'voter_id' => $voter_id,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        ]);
        
        return $newUser;
    }
}
