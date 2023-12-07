<?php

namespace Database\Seeders;

use App\Models\Ballot;
use Illuminate\Database\Seeder;

class BallotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ballot::factory()->create(4);
    }
}
