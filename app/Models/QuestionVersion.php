<?php

namespace App\Models;

use App\Exceptions\ImmutableQuestionVersion;
use Database\Factories\QuestionVersionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionVersion extends Model
{
    /** @use HasFactory<QuestionVersionFactory> */
    use HasFactory;

    /**
     * Question versions are append-only (D-024). The database enforces the same
     * rule with a PostgreSQL trigger, so direct SQL cannot bypass it either.
     */
    protected static function booted(): void
    {
        static::updating(function (): void {
            throw ImmutableQuestionVersion::cannotUpdate();
        });

        static::deleting(function (): void {
            throw ImmutableQuestionVersion::cannotDelete();
        });
    }

    /**
     * @var list<string>
     */
    protected $fillable = [
        'question_id',
        'version',
        'statement',
        'language',
    ];

    /**
     * @return BelongsTo<Question, $this>
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
