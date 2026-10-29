<?php

namespace App\Models;

use Database\Factories\QuestionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Question extends Model
{
    /** @use HasFactory<QuestionFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'archived_at' => 'datetime',
        ];
    }

    /**
     * @return HasMany<QuestionVersion, $this>
     */
    public function versions(): HasMany
    {
        return $this->hasMany(QuestionVersion::class);
    }

    /**
     * @return HasOne<QuestionVersion, $this>
     */
    public function latestVersion(): HasOne
    {
        return $this->hasOne(QuestionVersion::class)->latestOfMany('version');
    }

    public function isArchived(): bool
    {
        return $this->archived_at !== null;
    }
}
