<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('rejects unauthenticated api requests with the shared envelope', function (): void {
    $this->getJson('/api/v1/ping')
        ->assertUnauthorized()
        ->assertExactJson([
            'success' => false,
            'code' => 401,
            'message' => 'Unauthenticated',
            'data' => null,
        ]);
});

it('responds inside the shared envelope for an authenticated client', function (): void {
    Sanctum::actingAs(User::factory()->create());

    $this->getJson('/api/v1/ping')
        ->assertOk()
        ->assertExactJson([
            'success' => true,
            'code' => 200,
            'message' => 'OK',
            'data' => ['pong' => true],
        ]);
});

it('returns the envelope for unknown api routes', function (): void {
    $this->getJson('/api/v1/missing')
        ->assertNotFound()
        ->assertExactJson([
            'success' => false,
            'code' => 404,
            'message' => 'Not Found',
            'data' => null,
        ]);
});

it('issues a token for a machine client', function (): void {
    $user = User::factory()->create();

    $token = $user->createToken('office-dashboard', ['questions:read'])->plainTextToken;

    expect($token)->toBeString()->not->toBeEmpty();
});
