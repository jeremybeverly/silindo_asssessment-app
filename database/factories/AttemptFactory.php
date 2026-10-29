<?php

namespace Database\Factories;

use App\Models\Attempt;
use App\Models\Invitation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attempt>
 */
class AttemptFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invitation_id' => Invitation::factory(),
            'state' => Attempt::STATE_IN_PROGRESS,
            'started_at' => now(),
            'submitted_at' => null,
        ];
    }
}
