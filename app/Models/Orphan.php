<?php

namespace App\Models;

use Database\Factories\OrphanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Orphan extends Model
{
    /** @use HasFactory<OrphanFactory> */
    use HasFactory;

    public const EDUCATION_STAGES = ['under_school_age', 'kindergarten', 'primary', 'preparatory', 'secondary', 'university', 'not_enrolled'];

    public const HEALTH_STATUSES = ['healthy', 'chronic_illness', 'disability', 'injured'];

    protected $fillable = [
        'full_name',
        'birth_date',
        'national_id',
        'education_stage',
        'health_status',
        'health_notes',
        'sponsorship_status',
        'sponsor_name',
        'sponsorship_monthly_amount',
        'sponsorship_started_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'sponsorship_monthly_amount' => 'decimal:2',
            'sponsorship_started_at' => 'date',
        ];
    }

    public static function firstNameOf(string $fullName): string
    {
        return Str::before(Str::squish($fullName), ' ');
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(OrphanFamily::class, 'orphan_family_id');
    }

    /**
     * The only part of the name shown to visitors.
     */
    public function getFirstNameAttribute(): string
    {
        return self::firstNameOf($this->full_name);
    }

    public function getAgeAttribute(): int
    {
        return (int) $this->birth_date->age;
    }

    public function isAvailable(): bool
    {
        return $this->sponsorship_status === OrphanFamily::SPONSORSHIP_AVAILABLE;
    }
}
