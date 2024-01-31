<?php

namespace Database\Factories;

use App\Models\Poll;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\ModelStatusEnum;
use App\Models\User;

/**
 * @extends Factory<Poll>
 */
class PollFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3, true),
            'description' => fake()->paragraphs(random_int(1, 2), true),
            'status' => fake()->randomElement(ModelStatusEnum::values()),
            'created_at'=>fake()->date(),      
        ];
    }
}
