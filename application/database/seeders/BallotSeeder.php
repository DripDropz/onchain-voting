<?php

namespace Database\Seeders;

use App\Models\Ballot;
use App\Models\Snapshot;
use Illuminate\Database\Seeder;

class BallotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ballot::factory(15)
            ->has(Snapshot::factory()->count(1), 'snapshot')
            ->create();
    }
}
