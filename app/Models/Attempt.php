<?php

namespace App\Models;

use Database\Factories\AttemptFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attempt extends Model
{
    /** @use HasFactory<AttemptFactory> */
    use HasFactory;

    public const STATE_IN_PROGRESS = 'in_progress';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'invitation_id',
        'state',
        'started_at',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Invitation, $this>
     */
    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    /**
     * @return HasMany<AttemptQuestion, $this>
     */
    public function questions(): HasMany
    {
        return $this->hasMany(AttemptQuestion::class);
    }
}
