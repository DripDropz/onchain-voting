<?php

namespace Database\Factories;

use App\Models\QuestionChoice;
use Illuminate\Database\Eloquent\Factories\Factory;
use Random\RandomException;

/**
 * @extends Factory<QuestionChoice>
 */
class QuestionChoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws RandomException
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3, true),
            'description' => fake()->paragraphs(random_int(1, 2), true),
        ];
    }
}
