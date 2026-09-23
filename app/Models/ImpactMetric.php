<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImpactMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'title_ar',
        'title_en',
        'value',
        'unit_ar',
        'unit_en',
        'category',
        'order',
        'is_approved',
        'approved_at',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_approved' => 'boolean',
            'approved_at' => 'datetime',
            'order' => 'integer',
        ];
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('is_approved', true)->where('status', 'approved');
    }

    public function title(): string
    {
        $locale = app()->getLocale();

        return ($locale === 'en' && ! empty($this->title_en)) ? $this->title_en : $this->title_ar;
    }

    public function unit(): ?string
    {
        $locale = app()->getLocale();

        return ($locale === 'en' && ! empty($this->unit_en)) ? $this->unit_en : $this->unit_ar;
    }
}
