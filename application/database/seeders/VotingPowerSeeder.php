<?php

namespace Database\Seeders;

use App\Models\Snapshot;
use App\Models\User;
use App\Models\VotingPower;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VotingPowerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        $snapshots = Snapshot::all();

        foreach ($users as $user) {
            foreach ($snapshots as $snapshot) {
                VotingPower::factory()->count(1)->create([
                    'user_id' => $user->id,
                    'snapshot_id' => $snapshot->id,
                    'voting_power' => rand(50000, 10000000),
                ]);
            }
        }
    }
}
