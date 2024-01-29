<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Question;
use Illuminate\Database\Seeder;
use App\Models\BallotQuestionChoice;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Question::factory(7)->recycle(User::factory()->count(1), 'user')
        ->has(
            BallotQuestionChoice::factory(4),
            'choices'
        )
        ->create();
    }
}
