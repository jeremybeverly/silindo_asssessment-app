<?php

use App\Actions\Assessment\StartAttempt;
use App\Exceptions\AttemptNotAllowed;
use App\Models\Attempt;
use App\Models\Invitation;
use App\Models\Question;

function addVersion(Question $question, int $version, string $statement = 'Pernyataan'): void
{
    $question->versions()->create([
        'version' => $version,
        'statement' => $statement,
        'language' => 'id',
    ]);
}

it('starts one attempt with the latest active questions in a fixed order', function (): void {
    $first = Question::factory()->create();
    addVersion($first, 1, 'Versi lama');
    addVersion($first, 2, 'Versi baru');

    $second = Question::factory()->create();
    addVersion($second, 1, 'Pernyataan kedua');

    $invitation = Invitation::factory()->create();

    $attempt = app(StartAttempt::class)($invitation);

    expect($attempt->state)->toBe(Attempt::STATE_IN_PROGRESS)
        ->and($attempt->questions)->toHaveCount(2)
        ->and($attempt->questions->pluck('position')->sort()->values()->all())->toBe([1, 2])
        ->and($attempt->questions->pluck('question_version_id')->all())
        ->toContain($first->versions()->where('version', 2)->sole()->id)
        ->and($attempt->questions->pluck('question_version_id')->all())
        ->not->toContain($first->versions()->where('version', 1)->sole()->id);
});

it('excludes archived questions from a new attempt', function (): void {
    $active = Question::factory()->create();
    addVersion($active, 1);

    $archived = Question::factory()->archived()->create();
    addVersion($archived, 1);

    $attempt = app(StartAttempt::class)(Invitation::factory()->create());

    expect($attempt->questions)->toHaveCount(1)
        ->and($attempt->questions->sole()->question_version_id)
        ->toBe($active->versions()->sole()->id);
});

it('refuses a second attempt for the same invitation', function (): void {
    $question = Question::factory()->create();
    addVersion($question, 1);

    $invitation = Invitation::factory()->create();
    app(StartAttempt::class)($invitation);

    try {
        app(StartAttempt::class)($invitation);
        $this->fail('A second attempt should not be allowed.');
    } catch (AttemptNotAllowed $exception) {
        expect($exception->reason)->toBe('already_started');
    }

    expect(Attempt::count())->toBe(1);
});

it('refuses a revoked invitation', function (): void {
    $question = Question::factory()->create();
    addVersion($question, 1);

    $invitation = Invitation::factory()->revoked()->create();

    try {
        app(StartAttempt::class)($invitation);
        $this->fail('A revoked invitation should not be usable.');
    } catch (AttemptNotAllowed $exception) {
        expect($exception->reason)->toBe('revoked');
    }
});

it('refuses an invitation outside its access window', function (): void {
    $question = Question::factory()->create();
    addVersion($question, 1);

    $invitation = Invitation::factory()->outsideWindow()->create();

    try {
        app(StartAttempt::class)($invitation);
        $this->fail('An expired invitation should not be usable.');
    } catch (AttemptNotAllowed $exception) {
        expect($exception->reason)->toBe('outside_window');
    }
});

it('refuses to start when no active questions exist', function (): void {
    $invitation = Invitation::factory()->create();

    try {
        app(StartAttempt::class)($invitation);
        $this->fail('An attempt should not start without questions.');
    } catch (AttemptNotAllowed $exception) {
        expect($exception->reason)->toBe('no_questions');
    }
});
