<?php

namespace Database\Factories;

use App\Models\Ballot;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ballot>
 */
class BallotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws \Exception
     */
    public function definition(): array
    {
        $started_at = fake()->dateTimeBetween('-1 year', '+1 year');
        return [
            'title' => fake()->sentence(3, true),
            'version' => fake()->semver(),
            'description' => fake()->sentence(random_int(1, 3), true),
            'status' => fake()->randomElement(['draft', 'pending', 'published']),
            'started_at' => $started_at,
            'ended_at' => fake()->dateTimeBetween($started_at, '+1 year'),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Ballot $ballot) {
            Question::factory()->create([
                'ballot_id' => $ballot->id,
            ]);
        });
    }
}
