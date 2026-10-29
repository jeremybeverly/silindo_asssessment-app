<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\QuestionVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuestionVersion>
 */
class QuestionVersionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question_id' => Question::factory(),
            'version' => 1,
            'statement' => fake()->sentence(),
            'language' => 'id',
        ];
    }
}
