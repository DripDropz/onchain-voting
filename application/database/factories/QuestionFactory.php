<?php

namespace Database\Factories;

use App\Enums\ModelStatusEnum;
use App\Enums\QuestionTypeEnum;
use App\Models\Ballot;
use App\Models\Poll;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Question>
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
        $user_ids = User::pluck('id')->toArray();
        $ballot_ids = Ballot::pluck('id')->toArray();

        return [
            'title' => fake()->sentence(3, true),
            'description' => fake()->paragraphs(random_int(1, 2), true),
            'status' => fake()->randomElement(ModelStatusEnum::values()),
            'type' => fake()->randomElement(QuestionTypeEnum::values()),
            'supplemental' => fake()->url,
            'max_choices' => fake()->numberBetween(1, 20),
            'model_id' => random_int(1, 15),
            'model_type' => fake()->randomElement([Poll::class,Ballot::class]),
            'user_id' => fake()->randomElement($user_ids)
        ];
    }
}
