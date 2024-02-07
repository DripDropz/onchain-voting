<?php

namespace Database\Seeders;

use App\Models\QuestionChoice;
use App\Models\User;
use App\Models\Question;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Question::factory(7)->recycle(User::factory()->count(1), 'user')
        ->has(
            QuestionChoice::factory(4),
            'choices'
        )
        ->create();
    }
}
