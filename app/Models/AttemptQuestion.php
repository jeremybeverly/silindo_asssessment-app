<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttemptQuestion extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'attempt_id',
        'question_version_id',
        'position',
    ];

    /**
     * @return BelongsTo<Attempt, $this>
     */
    public function attempt(): BelongsTo
    {
        return $this->belongsTo(Attempt::class);
    }

    /**
     * @return BelongsTo<QuestionVersion, $this>
     */
    public function questionVersion(): BelongsTo
    {
        return $this->belongsTo(QuestionVersion::class);
    }
}
