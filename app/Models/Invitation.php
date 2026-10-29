<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\InvitationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property string $candidate_name
 * @property string $candidate_email
 * @property string $candidate_phone
 * @property string $candidate_phone_normalized
 * @property string $position_name
 * @property string|null $position_external_id
 * @property CarbonInterface $access_starts_at
 * @property CarbonInterface $access_ends_at
 * @property string $status
 * @property CarbonInterface|null $revoked_at
 * @property CarbonInterface|null $created_at
 */
class Invitation extends Model
{
    /** @use HasFactory<InvitationFactory> */
    use HasFactory;

    public const STATUS_ISSUED = 'issued';

    public const STATUS_REVOKED = 'revoked';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'token_hash',
        'candidate_name',
        'candidate_email',
        'candidate_phone',
        'candidate_phone_normalized',
        'position_name',
        'position_external_id',
        'access_starts_at',
        'access_ends_at',
        'status',
        'revoked_at',
    ];

    protected function casts(): array
    {
        return [
            'access_starts_at' => 'datetime',
            'access_ends_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    /**
     * @return HasOne<Attempt, $this>
     */
    public function attempt(): HasOne
    {
        return $this->hasOne(Attempt::class);
    }

    public function isRevoked(): bool
    {
        return $this->status === self::STATUS_REVOKED;
    }

    public function isWithinWindow(CarbonInterface $moment): bool
    {
        return $moment->betweenIncluded($this->access_starts_at, $this->access_ends_at);
    }
}
