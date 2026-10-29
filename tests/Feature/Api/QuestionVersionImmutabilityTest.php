<?php

use App\Exceptions\ImmutableQuestionVersion;
use App\Models\Question;
use App\Models\QuestionVersion;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;

beforeEach(function (): void {
    Sanctum::actingAs(User::factory()->create());
});

function versionWithStatement(Question $question, string $statement = 'Pernyataan awal'): QuestionVersion
{
    return $question->versions()->create([
        'version' => 1,
        'statement' => $statement,
        'language' => 'id',
    ]);
}

it('creates and edits questions while preserving every previous version', function (): void {
    $created = $this->postJson('/api/v1/questions', ['statement' => 'Versi pertama'])
        ->assertCreated()
        ->json('data');

    $this->patchJson("/api/v1/questions/{$created['question_id']}", ['statement' => 'Versi kedua'])
        ->assertOk()
        ->assertJsonPath('data.version', 2)
        ->assertJsonPath('data.statement', 'Versi kedua');

    $this->getJson('/api/v1/questions')
        ->assertOk()
        ->assertJsonPath('data.0.version', 2)
        ->assertJsonPath('data.0.statement', 'Versi kedua');

    $this->assertDatabaseHas('question_versions', [
        'id' => $created['id'],
        'question_id' => $created['question_id'],
        'version' => 1,
        'statement' => 'Versi pertama',
        'language' => 'id',
    ]);
});

it('rejects an eloquent update of an existing version', function (): void {
    $version = versionWithStatement(Question::factory()->create());

    $version->statement = 'Diubah';

    expect(fn () => $version->save())->toThrow(ImmutableQuestionVersion::class);
});

it('rejects an eloquent delete of an existing version', function (): void {
    $version = versionWithStatement(Question::factory()->create());

    expect(fn () => $version->delete())->toThrow(ImmutableQuestionVersion::class);
});

it('rejects a direct database update of an existing version', function (): void {
    $version = versionWithStatement(Question::factory()->create());

    expect(fn () => DB::table('question_versions')
        ->where('id', $version->id)
        ->update(['statement' => 'Diubah lewat SQL']))
        ->toThrow(QueryException::class);
});

it('rejects a direct database delete of an existing version', function (): void {
    $version = versionWithStatement(Question::factory()->create());

    expect(fn () => DB::table('question_versions')
        ->where('id', $version->id)
        ->delete())
        ->toThrow(QueryException::class);
});

it('archives a question without touching its versions', function (): void {
    $question = Question::factory()->create();
    $version = versionWithStatement($question, 'Tetap ada');

    $this->postJson("/api/v1/questions/{$question->id}/archive")->assertNoContent();

    $this->assertDatabaseHas('question_versions', [
        'id' => $version->id,
        'statement' => 'Tetap ada',
    ]);

    $this->getJson('/api/v1/questions?include_archived=1')
        ->assertOk()
        ->assertJsonPath('data.0.archived', true)
        ->assertJsonPath('data.0.statement', 'Tetap ada');
});
