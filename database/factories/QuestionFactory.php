<?php

namespace Database\Factories;

use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Question>
 */
class QuestionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [];
    }

    public function archived(): static
    {
        return $this->state(fn (): array => [
            'archived_at' => now(),
        ]);
    }
}
