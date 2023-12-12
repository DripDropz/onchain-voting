<?php

namespace Database\Factories;

use App\Enums\ModelStatusEnum;
use App\Models\Snapshot;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Auth;

/**
 * @extends Factory<Snapshot>
 */
class SnapshotFactory extends Factory
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
            'title' => fake()->sentence(3, true),
            'description' => fake()->paragraphs(random_int(1, 2), true),
            'policy_id' => "lovelace",
            'status' => fake()->randomElement(ModelStatusEnum::values()),
            'type' => "file",
        ];
    }
}
