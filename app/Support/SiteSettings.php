<?php

namespace App\Support;

use App\Models\Setting;

class SiteSettings
{
    /**
     * Get a setting by key with optional fallback.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }

    /**
     * Get the founding year of the foundation.
     */
    public static function foundedYear(): string
    {
        $year = Setting::get('founded_year');

        return ! empty($year) ? (string) $year : '2024';
    }

    /**
     * Get HQ location with bilingual support.
     */
    public static function hqLocation(?string $locale = null): string
    {
        $loc = $locale ?? app()->getLocale();
        $custom = Setting::get("hq_location_{$loc}");

        if (! empty($custom)) {
            return $custom;
        }

        return __('brand.hq', [], $loc);
    }

    /**
     * Get field location with bilingual support.
     */
    public static function fieldLocation(?string $locale = null): string
    {
        $loc = $locale ?? app()->getLocale();
        $custom = Setting::get("field_location_{$loc}");

        if (! empty($custom)) {
            return $custom;
        }

        return __('brand.field', [], $loc);
    }

    /**
     * Get primary contact email.
     */
    public static function contactEmail(): string
    {
        $custom = Setting::get('contact_email');

        if (! empty($custom)) {
            return $custom;
        }

        return __('contact.email.value');
    }

    /**
     * Get primary contact phone / whatsapp.
     */
    public static function contactPhone(): string
    {
        $custom = Setting::get('contact_phone');

        if (! empty($custom)) {
            return $custom;
        }

        return __('contact.phone.value');
    }

    /**
     * Get contact location / address with bilingual support.
     */
    public static function contactAddress(?string $locale = null): string
    {
        $loc = $locale ?? app()->getLocale();
        $custom = Setting::get("contact_address_{$loc}");

        if (! empty($custom)) {
            return $custom;
        }

        return __('contact.location.value', [], $loc);
    }

    /**
     * Check if urgent announcement bar is enabled.
     */
    public static function urgentEnabled(): bool
    {
        $val = Setting::get('urgent_enabled');

        if ($val !== null) {
            return filter_var($val, FILTER_VALIDATE_BOOLEAN);
        }

        return (bool) config('bader.urgent.enabled', false);
    }

    /**
     * Get urgent announcement bar text.
     */
    public static function urgentText(?string $locale = null): string
    {
        $loc = $locale ?? app()->getLocale();
        $custom = Setting::get("urgent_text_{$loc}");

        if (! empty($custom)) {
            return $custom;
        }

        return (string) config('bader.urgent.text', '');
    }

    /**
     * Get urgent announcement URL.
     */
    public static function urgentUrl(): ?string
    {
        $custom = Setting::get('urgent_url');

        if ($custom !== null) {
            return ! empty($custom) ? $custom : null;
        }

        return config('bader.urgent.url');
    }

    /**
     * Get institutional section title with fallback.
     */
    public static function institutionalTitle(string $page, string $section, ?string $locale = null): string
    {
        $loc = $locale ?? app()->getLocale();
        $cleanSec = str_contains($section, '.') ? explode('.', $section, 2)[1] : $section;
        $key = "inst_{$page}_{$cleanSec}_title_{$loc}";
        $custom = Setting::get($key);

        if (! empty($custom)) {
            return $custom;
        }

        $exactKey = "page.{$page}.{$section}.title";
        $translated = __($exactKey, [], $loc);
        if ($translated !== $exactKey) {
            return $translated;
        }

        $altKey = "page.{$section}.title";
        $translatedAlt = __($altKey, [], $loc);
        if ($translatedAlt !== $altKey) {
            return $translatedAlt;
        }

        return __("page.{$page}.{$cleanSec}.title", [], $loc);
    }

    /**
     * Get institutional section text with fallback.
     */
    public static function institutionalText(string $page, string $section, ?string $locale = null): string
    {
        $loc = $locale ?? app()->getLocale();
        $cleanSec = str_contains($section, '.') ? explode('.', $section, 2)[1] : $section;
        $key = "inst_{$page}_{$cleanSec}_text_{$loc}";
        $custom = Setting::get($key);

        if (! empty($custom)) {
            return $custom;
        }

        $exactKey = "page.{$page}.{$section}.text";
        $translated = __($exactKey, [], $loc);
        if ($translated !== $exactKey) {
            return $translated;
        }

        $altKey = "page.{$section}.text";
        $translatedAlt = __($altKey, [], $loc);
        if ($translatedAlt !== $altKey) {
            return $translatedAlt;
        }

        return __("page.{$page}.{$cleanSec}.text", [], $loc);
    }

    /**
     * Optional institutional block field (e.g. the president's speech) that has no translation fallback;
     * falls back to the Arabic value, and returns null when the team has not filled it in.
     */
    public static function optionalInstitutional(string $page, string $section, string $field, ?string $locale = null): ?string
    {
        $loc = $locale ?? app()->getLocale();

        foreach (array_unique([$loc, 'ar']) as $candidate) {
            $value = trim((string) Setting::get("inst_{$page}_{$section}_{$field}_{$candidate}", ''));

            if ($value !== '') {
                return $value;
            }
        }

        return null;
    }

    /**
     * Get institutional page intro text with fallback.
     */
    public static function pageIntro(string $page, ?string $locale = null): string
    {
        $loc = $locale ?? app()->getLocale();
        $key = "inst_{$page}_intro_{$loc}";
        $custom = Setting::get($key);

        if (! empty($custom)) {
            return $custom;
        }

        return __("page.{$page}.intro", [], $loc);
    }

    /**
     * WhatsApp number in international format (digits only), falling back to the contact phone.
     */
    public static function whatsappNumber(): ?string
    {
        $number = Setting::get('whatsapp_number');

        if (empty($number)) {
            $number = Setting::get('contact_phone');
        }

        $digits = preg_replace('/\D+/', '', (string) $number);

        return $digits !== '' ? $digits : null;
    }

    /**
     * Gold price per gram in USD, used to compute the zakat nisab.
     */
    public static function goldPricePerGram(): float
    {
        $price = Setting::get('gold_price_per_gram');

        return is_numeric($price) && (float) $price > 0
            ? (float) $price
            : (float) config('bader.zakat.default_gold_price_per_gram');
    }

    /**
     * Quick-give options shown under the homepage hero.
     *
     * @return list<array{category: string, amount: float, label: string, label_ar: string, label_en: string}>
     */
    public static function quickGiveOptions(?string $locale = null): array
    {
        $loc = $locale ?? app()->getLocale();
        $stored = json_decode((string) Setting::get('quick_give_options', ''), true);
        $options = is_array($stored) && $stored !== [] ? $stored : config('bader.quick_give', []);

        return collect($options)
            ->filter(fn ($option) => is_array($option) && is_numeric($option['amount'] ?? null) && (float) $option['amount'] > 0)
            ->map(fn (array $option): array => [
                'category' => (string) ($option['category'] ?? 'general'),
                'amount' => (float) $option['amount'],
                'label_ar' => (string) ($option['label_ar'] ?? ''),
                'label_en' => (string) ($option['label_en'] ?? ''),
                'label' => (string) (($loc === 'en' && ! empty($option['label_en'])) ? $option['label_en'] : ($option['label_ar'] ?? '')),
            ])
            ->values()
            ->all();
    }

    /**
     * Translation strings overridden from Dashboard → Site texts for the given locale.
     *
     * @return array<string, string>
     */
    public static function textOverrides(string $locale): array
    {
        $stored = json_decode((string) Setting::get("site_texts_{$locale}", ''), true);

        return is_array($stored)
            ? array_filter($stored, fn ($value, $key): bool => is_string($key) && is_string($value) && $value !== '', ARRAY_FILTER_USE_BOTH)
            : [];
    }

    /**
     * Social profile URLs saved in Site Settings, keyed by platform, in the configured display order.
     *
     * @return array<string, string>
     */
    public static function socialLinks(): array
    {
        $links = [];

        foreach (config('bader.social_platforms', []) as $platform) {
            $url = trim((string) Setting::get("social_{$platform}", ''));

            if ($url !== '') {
                $links[$platform] = $url;
            }
        }

        return $links;
    }

    /**
     * Gift card designs keyed by design key; custom designs from Site Settings replace the config defaults.
     *
     * @return array<string, array{key: string, label_ar: string, label_en: string, from: string, to: string, accent: string}>
     */
    public static function giftDesigns(): array
    {
        $stored = json_decode((string) Setting::get('gift_designs', ''), true);
        $designs = is_array($stored) && $stored !== [] ? $stored : config('bader.gift_designs', []);

        return collect($designs)
            ->filter(fn ($design) => is_array($design) && filled($design['key'] ?? null))
            ->mapWithKeys(fn (array $design): array => [(string) $design['key'] => [
                'key' => (string) $design['key'],
                'label_ar' => (string) ($design['label_ar'] ?? ''),
                'label_en' => (string) ($design['label_en'] ?? ''),
                'from' => (string) ($design['from'] ?? '#0a2e2f'),
                'to' => (string) ($design['to'] ?? '#1f6b38'),
                'accent' => (string) ($design['accent'] ?? '#e1e56b'),
            ]])
            ->all();
    }

    /**
     * Get dynamic site content text with key and fallback.
     */
    public static function content(string $key, ?string $locale = null): string
    {
        $loc = $locale ?? app()->getLocale();
        $cleanKey = str_replace('.', '_', $key);
        $custom = Setting::get("content_{$cleanKey}_{$loc}");

        if (! empty($custom)) {
            return $custom;
        }

        $translated = __($key, [], $loc);
        if ($translated !== $key) {
            return $translated;
        }

        return __($key);
    }
}
