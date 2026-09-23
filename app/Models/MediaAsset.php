<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'title_ar',
        'title_en',
        'file_path',
        'category',
        'has_usage_consent',
        'consent_notes',
    ];

    protected $casts = [
        'has_usage_consent' => 'boolean',
    ];

    public function scopeWithConsent(Builder $query): Builder
    {
        return $query->where('has_usage_consent', true);
    }

    public function getTitleAttribute(): string
    {
        $loc = app()->getLocale();

        return ($loc === 'en' && ! empty($this->title_en)) ? $this->title_en : $this->title_ar;
    }
}
