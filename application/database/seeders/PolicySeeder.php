<?php

namespace Database\Seeders;

use App\Models\Ballot;
use App\Models\Policy;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ballots = Ballot::all();

        foreach ($ballots as $ballot) {
            Policy::factory()->create([
                'model_id' => $ballot->id,
                'context' => 'registration',
            ]);

            Policy::factory()->create([
                'model_id' => $ballot->id,
                'context' => 'voting',
            ]);
        }
    }
}
