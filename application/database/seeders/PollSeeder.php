<?php

namespace Database\Seeders;

use App\Models\Poll;
use App\Models\User;
use App\Models\Question;
use Illuminate\Database\Seeder;
use App\Models\BallotQuestionChoice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PollSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Poll::factory(15)
        ->recycle(User::factory()->count(1), 'user')
        ->has(
            Question::factory(1)
                ->recycle(User::factory()->count(1), 'user')
                ->has(
                    BallotQuestionChoice::factory(4),
                    'choices'
                ),'question'
        )
        ->create();
    }
}
