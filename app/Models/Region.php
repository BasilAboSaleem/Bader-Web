<?php

namespace App\Models;

use Database\Factories\RegionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    /** @use HasFactory<RegionFactory> */
    use HasFactory;

    protected $fillable = [
        'key',
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'image',
        'map_x',
        'map_y',
        'map_area',
        'impact_metrics',
        'order',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'map_x' => 'integer',
            'map_y' => 'integer',
            'impact_metrics' => 'array',
            'order' => 'integer',
        ];
    }

    /**
     * Impact figures in the current locale, falling back to Arabic labels.
     *
     * @return list<array{value: string, label: string, icon: string}>
     */
    public function localizedImpactMetrics(): array
    {
        $isEnglish = app()->getLocale() === 'en';

        return collect($this->impact_metrics ?? [])
            ->map(fn (array $metric): array => [
                'value' => (string) ($metric['value'] ?? ''),
                'label' => (string) (($isEnglish && ! empty($metric['label_en'])) ? $metric['label_en'] : ($metric['label_ar'] ?? '')),
                'icon' => (string) ($metric['icon'] ?? 'sparkle'),
            ])
            ->filter(fn (array $metric): bool => $metric['value'] !== '' && $metric['label'] !== '')
            ->values()
            ->all();
    }

    /**
     * Published regions with the projects and facilities the impact map needs.
     */
    public function scopeForImpactMap(Builder $query): Builder
    {
        return $query->published()
            ->withProjectsCount()
            ->with([
                'facilities' => fn ($facilities) => $facilities->published(),
                'completedProjects' => fn ($completedProjects) => $completedProjects->published(),
            ]);
    }

    /**
     * Adds `projects_count`: the region's published projects.
     */
    public function scopeWithProjectsCount(Builder $query): Builder
    {
        return $query->withCount(['completedProjects as projects_count' => fn (Builder $projects) => $projects->where('status', 'published')]);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->orderBy('order');
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class);
    }

    public function completedProjects(): HasMany
    {
        return $this->hasMany(CompletedProject::class);
    }

    public function sponsorshipCases(): HasMany
    {
        return $this->hasMany(SponsorshipCase::class);
    }

    public function getNameAttribute(): string
    {
        return (app()->getLocale() === 'en' && ! empty($this->name_en)) ? $this->name_en : $this->name_ar;
    }

    public function getDescriptionAttribute(): ?string
    {
        return (app()->getLocale() === 'en' && ! empty($this->description_en)) ? $this->description_en : $this->description_ar;
    }
}
