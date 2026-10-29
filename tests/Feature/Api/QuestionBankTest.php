<?php

use App\Models\Question;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function (): void {
    Sanctum::actingAs(User::factory()->create());
});

it('requires authentication to read the question bank', function (): void {
    $this->app['auth']->forgetGuards();

    $this->getJson('/api/v1/questions')
        ->assertUnauthorized()
        ->assertJsonPath('success', false)
        ->assertJsonPath('code', 401);
});

it('creates a question with its first version', function (): void {
    $this->postJson('/api/v1/questions', [
        'statement' => 'Gembira/Antusias',
        'language' => 'id',
    ])
        ->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.version', 1)
        ->assertJsonPath('data.statement', 'Gembira/Antusias');

    $this->assertDatabaseHas('question_versions', [
        'version' => 1,
        'statement' => 'Gembira/Antusias',
    ]);
});

it('appends a version on edit and leaves the previous version intact', function (): void {
    $question = Question::factory()->create();
    $question->versions()->create([
        'version' => 1,
        'statement' => 'Original statement',
        'language' => 'id',
    ]);

    $this->patchJson("/api/v1/questions/{$question->id}", [
        'statement' => 'Revised statement',
    ])
        ->assertOk()
        ->assertJsonPath('data.version', 2)
        ->assertJsonPath('data.statement', 'Revised statement')
        ->assertJsonPath('data.language', 'id');

    $this->assertDatabaseHas('question_versions', [
        'question_id' => $question->id,
        'version' => 1,
        'statement' => 'Original statement',
    ]);
    $this->assertDatabaseHas('question_versions', [
        'question_id' => $question->id,
        'version' => 2,
        'statement' => 'Revised statement',
    ]);
});

it('lists only the latest version of each question', function (): void {
    $question = Question::factory()->create();
    $question->versions()->create(['version' => 1, 'statement' => 'First', 'language' => 'id']);
    $question->versions()->create(['version' => 2, 'statement' => 'Second', 'language' => 'id']);

    $this->getJson('/api/v1/questions')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.version', 2)
        ->assertJsonPath('data.0.statement', 'Second');
});

it('archives a question instead of deleting its history', function (): void {
    $question = Question::factory()->create();
    $question->versions()->create(['version' => 1, 'statement' => 'Keep me', 'language' => 'id']);

    $this->postJson("/api/v1/questions/{$question->id}/archive")->assertNoContent();

    $this->getJson('/api/v1/questions')->assertOk()->assertJsonCount(0, 'data');
    $this->getJson('/api/v1/questions?include_archived=1')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.archived', true);

    $this->assertDatabaseHas('question_versions', [
        'question_id' => $question->id,
        'statement' => 'Keep me',
    ]);
});

it('rejects a question without a statement', function (): void {
    $this->postJson('/api/v1/questions', [])
        ->assertUnprocessable()
        ->assertJsonPath('success', false)
        ->assertJsonPath('code', 422);
});
