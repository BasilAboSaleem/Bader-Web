<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'icon',
        'order',
        'status',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->orderBy('order');
    }

    public function getTitleAttribute(): string
    {
        $loc = app()->getLocale();

        return ($loc === 'en' && ! empty($this->title_en)) ? $this->title_en : $this->title_ar;
    }

    public function getDescriptionAttribute(): ?string
    {
        $loc = app()->getLocale();

        return ($loc === 'en' && ! empty($this->description_en)) ? $this->description_en : $this->description_ar;
    }
}
