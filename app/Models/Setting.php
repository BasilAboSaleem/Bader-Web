<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    /**
     * Retrieve a setting by its unique key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            $setting = static::query()->where('key', $key)->first();

            return $setting !== null ? $setting->value : $default;
        } catch (\Throwable) {
            return $default;
        }
    }

    /**
     * Set a setting value by its unique key.
     */
    public static function set(string $key, mixed $value, ?string $group = null): static
    {
        $setting = static::query()->firstOrNew(['key' => $key]);
        $setting->value = is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : (string) $value;

        if ($group !== null || ! $setting->exists) {
            $setting->group = $group ?? 'general';
        }

        $setting->save();

        return $setting;
    }

    /**
     * Get all settings grouped by their group name.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function getAllGrouped(): array
    {
        try {
            return static::query()
                ->get()
                ->groupBy('group')
                ->map(fn ($group) => $group->pluck('value', 'key')->all())
                ->all();
        } catch (\Throwable) {
            return [];
        }
    }
}
