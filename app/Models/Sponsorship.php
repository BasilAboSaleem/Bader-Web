<?php

namespace App\Models;

use Database\Factories\SponsorshipFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * One sponsor paying a monthly amount for one beneficiary: an orphan, or the mother of a widow family.
 * Rows are never rewritten: a change of sponsor, amount or currency ends the current row and starts a new one,
 * so the history behind every report stays intact.
 */
class Sponsorship extends Model
{
    /** @use HasFactory<SponsorshipFactory> */
    use HasFactory;

    public const BENEFICIARY_ORPHAN = 'orphan';

    public const BENEFICIARY_MOTHER = 'mother';

    public const RENEWAL_RECURRING = 'recurring';

    public const RENEWAL_ONE_TIME = 'one_time';

    public const RENEWAL_TYPES = [self::RENEWAL_RECURRING, self::RENEWAL_ONE_TIME];

    protected $fillable = [
        'sponsor_id',
        'orphan_family_id',
        'orphan_id',
        'beneficiary_type',
        'monthly_amount',
        'currency',
        'started_at',
        'renewal_type',
        'planned_ended_at',
        'ended_at',
        'created_by',
        'ended_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'monthly_amount' => 'decimal:2',
            'started_at' => 'date',
            'planned_ended_at' => 'date',
            'ended_at' => 'date',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('ended_at');
    }

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(Sponsor::class);
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(OrphanFamily::class, 'orphan_family_id');
    }

    public function orphan(): BelongsTo
    {
        return $this->belongsTo(Orphan::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(SponsorRequest::class);
    }

    public function disbursements(): HasMany
    {
        return $this->hasMany(SponsorshipDisbursement::class);
    }

    public function isActive(): bool
    {
        return $this->ended_at === null;
    }

    public function isForMother(): bool
    {
        return $this->beneficiary_type === self::BENEFICIARY_MOTHER;
    }

    public function isRecurring(): bool
    {
        return $this->renewal_type === self::RENEWAL_RECURRING;
    }

    /**
     * The beneficiary's name as the team and the sponsor know it.
     */
    public function beneficiaryName(): string
    {
        return $this->isForMother() ? (string) $this->family->mother_full_name : (string) $this->orphan?->full_name;
    }
}
