<?php

namespace Database\Factories;

use App\Enums\RuleOperatorEnum;
use App\Enums\RuleTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rule>
 */
class RuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3, true),
            'description' => fake()->paragraphs(random_int(1, 3), true),
            'type' => fake()->randomElement(RuleTypeEnum::values()),
            'operator' => fake()->randomElement(RuleOperatorEnum::values()),
        ];
    }
}
