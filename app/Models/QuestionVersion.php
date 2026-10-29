<?php

namespace App\Models;

use Database\Factories\QuestionVersionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionVersion extends Model
{
    /** @use HasFactory<QuestionVersionFactory> */
    use HasFactory;

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
