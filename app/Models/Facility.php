<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'location_ar',
        'location_en',
        'image',
        'order',
        'status',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->orderBy('order');
    }

    public function getNameAttribute(): string
    {
        $loc = app()->getLocale();

        return ($loc === 'en' && ! empty($this->name_en)) ? $this->name_en : $this->name_ar;
    }

    public function getDescriptionAttribute(): ?string
    {
        $loc = app()->getLocale();

        return ($loc === 'en' && ! empty($this->description_en)) ? $this->description_en : $this->description_ar;
    }

    public function getLocationAttribute(): ?string
    {
        $loc = app()->getLocale();

        return ($loc === 'en' && ! empty($this->location_en)) ? $this->location_en : $this->location_ar;
    }
}
