<?php

namespace Database\Factories;

use App\Models\Signature;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Signature>
 */
class SignatureFactory extends Factory
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
            'wallet_signature' => fake()->text,
            'email_signature' => fake()->text,
            'stake_address' => fake()->text,
        ];
    }
}
