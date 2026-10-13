<?php

namespace App\Models;

use Database\Factories\OrphanFamilyFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * One sponsorship file: the deceased father, the orphans and, for a widow's family, the mother.
 * Files registered from the website stay private until the team approves them.
 */
class OrphanFamily extends Model
{
    /** @use HasFactory<OrphanFamilyFactory> */
    use HasFactory;

    public const TYPE_ORPHAN = 'orphan';

    public const TYPE_WIDOW_FAMILY = 'widow_family';

    public const TYPES = [self::TYPE_ORPHAN, self::TYPE_WIDOW_FAMILY];

    public const SOURCE_WEBSITE = 'website';

    public const SOURCE_DASHBOARD = 'dashboard';

    public const REVIEW_PENDING = 'pending';

    public const REVIEW_APPROVED = 'approved';

    public const REVIEW_REJECTED = 'rejected';

    public const REVIEW_STATUSES = [self::REVIEW_PENDING, self::REVIEW_APPROVED, self::REVIEW_REJECTED];

    public const SPONSORSHIP_AVAILABLE = 'available';

    public const SPONSORSHIP_SPONSORED = 'sponsored';

    public const SPONSORSHIP_STATUSES = [self::SPONSORSHIP_AVAILABLE, self::SPONSORSHIP_SPONSORED];

    /** Attachments every registration must carry. */
    public const DOCUMENTS = ['father_death_certificate', 'martyr_certificate', 'mother_id'];

    public const MAX_ORPHANS = 15;

    protected $fillable = [
        'type',
        'source',
        'review_status',
        'sponsorship_status',
        'contact_name',
        'contact_relation',
        'contact_phone',
        'father_full_name',
        'father_death_date',
        'father_death_cause',
        'mother_full_name',
        'mother_national_id',
        'mother_birth_date',
        'mother_passport_number',
        'mother_residence_area',
        'mother_neighborhood',
        'mother_nearest_landmark',
        'mother_birth_country',
        'mother_birth_place',
        'mother_nationality',
        'mother_phone',
        'documents',
        'review_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'father_death_date' => 'date',
            'mother_birth_date' => 'date',
            'documents' => 'array',
            'reviewed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (OrphanFamily $family): void {
            $family->forceFill(['code' => 'ORP-'.str_pad((string) $family->id, 4, '0', STR_PAD_LEFT)])->saveQuietly();
        });
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('review_status', self::REVIEW_APPROVED);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('review_status', self::REVIEW_PENDING);
    }

    public function orphans(): HasMany
    {
        return $this->hasMany(Orphan::class)->orderBy('birth_date');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isWidowFamily(): bool
    {
        return $this->type === self::TYPE_WIDOW_FAMILY;
    }

    public function isApproved(): bool
    {
        return $this->review_status === self::REVIEW_APPROVED;
    }

    public function isAvailable(): bool
    {
        return $this->sponsorship_status === self::SPONSORSHIP_AVAILABLE;
    }

    /**
     * Name the team uses to tell files apart: the mother for a widow's family, otherwise the father.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->isWidowFamily() && $this->mother_full_name ? $this->mother_full_name : $this->father_full_name;
    }

    /**
     * The only part of the mother's name shown to visitors.
     */
    public function getMotherFirstNameAttribute(): string
    {
        return Orphan::firstNameOf((string) $this->mother_full_name);
    }

    public function hasDocument(string $document): bool
    {
        return ! empty($this->documents[$document] ?? null);
    }
}
