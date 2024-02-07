<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Ballot;
use App\Models\Question;
use App\Models\Snapshot;
use App\Models\QuestionChoice;
use Illuminate\Database\Seeder;

class BallotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ballot::factory(15)
            ->recycle(User::factory()->count(1), 'user')
            ->has(Snapshot::factory()->count(1), 'snapshot')
            ->has(
                Question::factory(7)
                    ->recycle(
                        User::factory()->count(1),
                        'user'
                    )->state(fn(array $attributes, Ballot $ballot) => [
                        'model_type' => $ballot::class
                    ])
                    ->has(
                        QuestionChoice::factory(4),
                        'choices'
                    ),
                'questions'
            )
            ->create();
    }
}
