<?php

namespace Database\Factories;

use App\Enums\ModelStatusEnum;
use App\Models\Policy;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Random\RandomException;

/**
 * @extends Factory<Policy>
 */
class PetitionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws RandomException
     */
    public function definition(): array
    {
        $startedAt = fake()->dateTimeBetween('-1 month', '+1 month');
        $endedAt = fake()->dateTimeBetween($startedAt, '+3 months');

        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3, true),
            'description' => fake()->paragraphs(random_int(1, 2), true),
            'status' => fake()->randomElement(ModelStatusEnum::values()),
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
        ];
        }
}
