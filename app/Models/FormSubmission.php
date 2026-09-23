<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'organization',
        'location',
        'payload',
        'status',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('status', 'unread');
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }
}
