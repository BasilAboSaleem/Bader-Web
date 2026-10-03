<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    private const CACHE_KEY = 'bader.settings';

    protected static function booted(): void
    {
        static::saved(fn () => Cache::store('array')->forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::store('array')->forget(self::CACHE_KEY));
    }

    /**
     * Retrieve a setting by its unique key. All settings are loaded once per request, since a single page
     * reads dozens of them.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            $settings = Cache::store('array')->rememberForever(self::CACHE_KEY, fn (): array => static::query()->pluck('value', 'key')->all());

            return array_key_exists($key, $settings) ? $settings[$key] : $default;
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
