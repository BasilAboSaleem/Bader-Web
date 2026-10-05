<?php

namespace App\Models;

use App\Models\Concerns\HasMediaGallery;
use Database\Factories\CompletedProjectFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A project the organisation has already delivered. Shown on the site and the impact map; it does not collect donations.
 */
class CompletedProject extends Model
{
    /** @use HasFactory<CompletedProjectFactory> */
    use HasFactory, HasMediaGallery;

    protected $fillable = [
        'key',
        'program_id',
        'region_id',
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'content_ar',
        'content_en',
        'completed_at',
        'beneficiaries',
        'cost',
        'image',
        'order',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'completed_at' => 'date',
            'beneficiaries' => 'integer',
            'cost' => 'decimal:2',
            'order' => 'integer',
        ];
    }

    /**
     * Published projects, most recently completed first.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->orderByRaw('completed_at is null')
            ->orderByDesc('completed_at')
            ->orderBy('order')
            ->orderByDesc('id');
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function getTitleAttribute(): string
    {
        return (app()->getLocale() === 'en' && ! empty($this->title_en)) ? $this->title_en : $this->title_ar;
    }

    public function getDescriptionAttribute(): ?string
    {
        return (app()->getLocale() === 'en' && ! empty($this->description_en)) ? $this->description_en : $this->description_ar;
    }

    public function getContentAttribute(): ?string
    {
        return (app()->getLocale() === 'en' && ! empty($this->content_en)) ? $this->content_en : $this->content_ar;
    }
}
