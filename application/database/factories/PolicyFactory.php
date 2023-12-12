<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Policy>
 */
class PolicyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::whereRelation('roles', 'name', 'super-admin')->first();
        Auth::login($user);
        return [
            'user_id' => $user->id,
            'model_type' => 'App\Models\Ballot',
            'script' => [
                'type' => 'Native',
                'script' => Str::random(64),
            ],
            'policy_id' => Str::random(50),
        ];
    }
}
