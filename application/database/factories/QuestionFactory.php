<?php

namespace Database\Factories;

use App\Enums\ModelStatusEnum;
use App\Enums\QuestionTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->name(),
            'status' => ModelStatusEnum::PUBLISHED,
            'type' => QuestionTypeEnum::SINGLE,
        ];
    }
}
