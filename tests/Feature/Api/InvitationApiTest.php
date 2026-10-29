<?php

use App\Models\Invitation;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function (): void {
    Sanctum::actingAs(User::factory()->create());
});

function invitationPayload(array $overrides = []): array
{
    return array_merge([
        'candidate_name' => 'Budi Santoso',
        'candidate_email' => 'budi@example.test',
        'candidate_phone' => '0812-3456-7890',
        'candidate_position' => 'Backend Developer',
        'position_external_id' => 'pos-42',
        'access_starts_at' => now()->toIso8601String(),
        'access_ends_at' => now()->addDays(7)->toIso8601String(),
    ], $overrides);
}

it('requires authentication to manage invitations', function (): void {
    $this->app['auth']->forgetGuards();

    $this->getJson('/api/v1/invitations')->assertUnauthorized();
});

it('creates an invitation and returns the raw token once', function (): void {
    $response = $this->postJson('/api/v1/invitations', invitationPayload())
        ->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.invitation.candidate_email', 'budi@example.test')
        ->assertJsonPath('data.invitation.status', 'issued');

    $token = $response->json('data.token');

    expect($token)->toBeString()->toHaveLength(64);

    $invitation = Invitation::sole();

    expect($invitation->token_hash)->toBe(hash('sha256', $token))
        ->and($invitation->token_hash)->not->toBe($token);
});

it('normalises the phone number to the 62 prefix', function (): void {
    $this->postJson('/api/v1/invitations', invitationPayload([
        'candidate_phone' => '0812-3456-7890',
    ]))
        ->assertCreated()
        ->assertJsonPath('data.invitation.candidate_phone', '0812-3456-7890')
        ->assertJsonPath('data.invitation.candidate_phone_normalized', '6281234567890');
});

it('allows the same email to be invited twice', function (): void {
    $this->postJson('/api/v1/invitations', invitationPayload())->assertCreated();
    $this->postJson('/api/v1/invitations', invitationPayload())->assertCreated();

    expect(Invitation::count())->toBe(2);
});

it('rejects an access window that ends before it starts', function (): void {
    $this->postJson('/api/v1/invitations', invitationPayload([
        'access_starts_at' => now()->addDay()->toIso8601String(),
        'access_ends_at' => now()->toIso8601String(),
    ]))
        ->assertUnprocessable()
        ->assertJsonPath('success', false)
        ->assertJsonStructure(['data' => ['access_ends_at']]);
});

it('revokes an invitation and keeps the record', function (): void {
    $invitation = Invitation::factory()->create();

    $this->postJson("/api/v1/invitations/{$invitation->id}/revoke")
        ->assertOk()
        ->assertJsonPath('data.status', 'revoked');

    $invitation->refresh();

    expect($invitation->status)->toBe(Invitation::STATUS_REVOKED)
        ->and($invitation->revoked_at)->not->toBeNull();
});

it('filters invitations by status and search term', function (): void {
    Invitation::factory()->create(['candidate_name' => 'Siti Aminah']);
    Invitation::factory()->revoked()->create(['candidate_name' => 'Andi Wijaya']);

    $this->getJson('/api/v1/invitations?status=revoked')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.candidate_name', 'Andi Wijaya');

    $this->getJson('/api/v1/invitations?search=Siti')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.candidate_name', 'Siti Aminah');
});
