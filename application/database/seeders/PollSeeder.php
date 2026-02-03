<?php

namespace Database\Seeders;

use App\Models\Poll;
use App\Models\User;
use App\Models\Question;
use App\Models\QuestionChoice;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PollSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Poll::factory(40)
            ->recycle(User::factory()->count(1), 'user')
            ->has(
                Question::factory(7)
                    ->recycle(
                        User::factory()->count(1),
                        'user'
                    )->state(fn (array $attributes, Poll $poll) => [
                        'model_type' => $poll::class
                    ])
                    ->has(
                        QuestionChoice::factory(4)->recycle(
                            User::factory()->count(1),
                            'user'
                        ),
                        'choices'
                    ),
                'question'
            )
            ->create();
    }
}
