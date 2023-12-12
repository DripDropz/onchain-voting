<?php

namespace Database\Seeders;

use App\Models\BallotQuestionChoice;
use App\Models\Question;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Question::factory()->count(25)->create()->each(function ($question) {
            $maxChoices = $question->max_choices;

            $numChoices =  fake()->numberBetween(1, $maxChoices);

            BallotQuestionChoice::factory()->count($numChoices)->create([
                'question_id' => $question->id,
            ]);
        });
    }
}
