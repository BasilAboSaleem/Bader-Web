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
     * Get FAQ question with fallback.
     */
    public static function faqQuestion(string $key, ?string $locale = null): string
    {
        $loc = $locale ?? app()->getLocale();
        $custom = Setting::get("faq_{$key}_q_{$loc}");

        if (! empty($custom)) {
            return $custom;
        }

        return __("faq.{$key}.question", [], $loc);
    }

    /**
     * Get FAQ answer with fallback.
     */
    public static function faqAnswer(string $key, ?string $locale = null): string
    {
        $loc = $locale ?? app()->getLocale();
        $custom = Setting::get("faq_{$key}_a_{$loc}");

        if (! empty($custom)) {
            return $custom;
        }

        return __("faq.{$key}.answer", [], $loc);
    }
}
