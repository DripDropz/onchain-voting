<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'parent_id' => $this->faker->randomDigit(),
            'title' => fake()->randomElement(['NFT','DAap','Token']),
            // 'meta_title' => $this->faker->words(5, true),
            'description' => $this->faker->paragraphs(rand(5, 14), true),
        ];
    }
}
