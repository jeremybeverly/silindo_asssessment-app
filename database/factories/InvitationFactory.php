<?php

namespace Database\Factories;

use App\Models\Invitation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invitation>
 */
class InvitationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'token_hash' => hash('sha256', fake()->uuid()),
            'candidate_name' => fake()->name(),
            'candidate_email' => fake()->safeEmail(),
            'candidate_phone' => '081234567890',
            'candidate_phone_normalized' => '6281234567890',
            'position_name' => 'Backend Developer',
            'position_external_id' => null,
            'access_starts_at' => now()->subHour(),
            'access_ends_at' => now()->addDays(7),
            'status' => Invitation::STATUS_ISSUED,
            'revoked_at' => null,
        ];
    }

    public function revoked(): static
    {
        return $this->state(fn (): array => [
            'status' => Invitation::STATUS_REVOKED,
            'revoked_at' => now(),
        ]);
    }

    public function outsideWindow(): static
    {
        return $this->state(fn (): array => [
            'access_starts_at' => now()->subDays(14),
            'access_ends_at' => now()->subDays(7),
        ]);
    }
}
