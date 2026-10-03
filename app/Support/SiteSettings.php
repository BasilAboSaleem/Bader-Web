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

        return ! empty($year) ? (string) $year : '2023';
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
        return self::withFoundedYear(self::pageIntroTemplate($page, $locale));
    }

    /**
     * Page intro as the team edits it, with ":founded_year" left in place.
     */
    public static function pageIntroTemplate(string $page, ?string $locale = null): string
    {
        $loc = $locale ?? app()->getLocale();
        $custom = Setting::get("inst_{$page}_intro_{$loc}");

        if (! empty($custom)) {
            return $custom;
        }

        return __("page.{$page}.intro", [], $loc);
    }

    /**
     * Replace ":founded_year" with the founding year from Site Settings, so texts follow it when it changes.
     */
    public static function withFoundedYear(string $text): string
    {
        return str_replace(':founded_year', self::foundedYear(), $text);
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
     * Public URL of a brand asset ("mark_star" or "favicon"); uploads from Site Settings replace the bundled
     * files, and the favicon falls back to an uploaded mark before the bundled icon.
     */
    public static function brandAsset(string $asset): string
    {
        $candidates = $asset === 'favicon' ? ['brand_favicon', 'brand_mark_star'] : ["brand_{$asset}"];

        foreach ($candidates as $settingKey) {
            $path = trim((string) Setting::get($settingKey, ''));

            if ($path !== '') {
                return asset($path);
            }
        }

        return asset((string) config("bader.assets.{$asset}"));
    }

    /**
     * Donation categories offered to donors, keyed by category key, labelled in the given locale.
     * "general" is always present because it is the fallback category.
     *
     * @return array<string, string>
     */
    public static function donationCategories(?string $locale = null): array
    {
        $loc = $locale ?? app()->getLocale();

        return collect(self::donationCategoryDefinitions())
            ->mapWithKeys(fn (array $category): array => [
                $category['key'] => ($loc === 'en' && $category['label_en'] !== '') ? $category['label_en'] : ($category['label_ar'] !== '' ? $category['label_ar'] : __('donation.category.'.$category['key'], [], $loc)),
            ])
            ->all();
    }

    /**
     * Label for any category key, including categories removed after donations were made with them.
     */
    public static function donationCategoryLabel(?string $key, ?string $locale = null): string
    {
        $key = (string) $key;
        $categories = self::donationCategories($locale);

        if (isset($categories[$key])) {
            return $categories[$key];
        }

        $translationKey = "donation.category.{$key}";
        $translated = __($translationKey, [], $locale ?? app()->getLocale());

        return $translated !== $translationKey ? $translated : $key;
    }

    /**
     * Category definitions for the settings form, with both labels.
     *
     * @return list<array{key: string, label_ar: string, label_en: string}>
     */
    public static function donationCategoryDefinitions(): array
    {
        $stored = json_decode((string) Setting::get('donation_categories', ''), true);
        $definitions = is_array($stored) && $stored !== []
            ? collect($stored)
                ->filter(fn ($category) => is_array($category) && filled($category['key'] ?? null))
                ->map(fn (array $category): array => [
                    'key' => (string) $category['key'],
                    'label_ar' => (string) ($category['label_ar'] ?? ''),
                    'label_en' => (string) ($category['label_en'] ?? ''),
                ])
            : collect(config('bader.donation_categories'))->map(fn (string $key): array => [
                'key' => $key,
                'label_ar' => __("donation.category.{$key}", [], 'ar'),
                'label_en' => __("donation.category.{$key}", [], 'en'),
            ]);

        if (! $definitions->contains('key', 'general')) {
            $definitions->prepend(['key' => 'general', 'label_ar' => __('donation.category.general', [], 'ar'), 'label_en' => __('donation.category.general', [], 'en')]);
        }

        return $definitions->values()->all();
    }

    /**
     * Cards shown on an institutional page, in the given locale, falling back to Arabic per field.
     *
     * @return list<array{icon: string, title: string, text: string}>
     */
    public static function institutionalCards(string $page, ?string $locale = null): array
    {
        $loc = $locale ?? app()->getLocale();

        return array_values(array_filter(array_map(fn (array $card): array => [
            'icon' => $card['icon'],
            'title' => $card["title_{$loc}"] !== '' ? $card["title_{$loc}"] : $card['title_ar'],
            'text' => $card["text_{$loc}"] !== '' ? $card["text_{$loc}"] : $card['text_ar'],
        ], self::institutionalCardDefinitions($page)), fn (array $card): bool => $card['title'] !== ''));
    }

    /**
     * Cards of an institutional page with both languages, as saved from Dashboard → Pages, or the
     * default cards (and any older per-card settings) until the team saves its own list.
     *
     * @return list<array{icon: string, title_ar: string, title_en: string, text_ar: string, text_en: string}>
     */
    public static function institutionalCardDefinitions(string $page): array
    {
        $stored = json_decode((string) Setting::get("inst_{$page}_cards", ''), true);

        if (is_array($stored)) {
            return array_values(array_map(fn (array $card): array => [
                'icon' => in_array($card['icon'] ?? null, config('bader.page_cards.icons'), true) ? $card['icon'] : 'sparkle',
                'title_ar' => (string) ($card['title_ar'] ?? ''),
                'title_en' => (string) ($card['title_en'] ?? ''),
                'text_ar' => (string) ($card['text_ar'] ?? ''),
                'text_en' => (string) ($card['text_en'] ?? ''),
            ], array_filter($stored, 'is_array')));
        }

        $cards = [];

        foreach (config("bader.institutional_pages.{$page}.sections", []) as $section => $icon) {
            $cards[] = [
                'icon' => $icon,
                'title_ar' => self::institutionalTitle($page, "{$page}.{$section}", 'ar'),
                'title_en' => self::institutionalTitle($page, "{$page}.{$section}", 'en'),
                'text_ar' => self::institutionalText($page, "{$page}.{$section}", 'ar'),
                'text_en' => self::institutionalText($page, "{$page}.{$section}", 'en'),
            ];
        }

        return $cards;
    }

    /**
     * Whether the hero opens with the organisation's own slide before the featured projects.
     */
    public static function heroIntroEnabled(): bool
    {
        return Setting::get('hero_intro_enabled', '1') === '1';
    }

    /**
     * Content of the organisation's hero slide in the given locale; empty fields fall back to the defaults.
     *
     * @return array{badge: string, title: string, text: string, primary_label: string, primary_url: string, secondary_label: string, secondary_url: string, image: string}
     */
    public static function heroIntro(?string $locale = null): array
    {
        $loc = $locale ?? app()->getLocale();
        $text = function (string $field, string $translationKey) use ($loc): string {
            $custom = trim((string) Setting::get("hero_intro_{$field}_{$loc}", ''));

            return $custom !== '' ? $custom : __($translationKey, [], $loc);
        };
        $url = function (string $field, string $default): string {
            $custom = trim((string) Setting::get("hero_intro_{$field}_url", ''));

            return $custom === '' ? $default : (str_starts_with($custom, '/') ? url($custom) : $custom);
        };

        return [
            'badge' => $text('badge', 'home.hero_kicker'),
            'title' => $text('title', 'home.hero_title'),
            'text' => $text('text', 'home.hero_text'),
            'primary_label' => $text('primary_label', 'home.hero.donate'),
            'primary_url' => $url('primary', route('donate')),
            'secondary_label' => $text('secondary_label', 'home.hero.about'),
            'secondary_url' => $url('secondary', route('about')),
            'image' => self::heroIntroImage(),
        ];
    }

    /**
     * Image of the organisation's hero slide: an upload from Dashboard → Homepage, or the bundled photo.
     */
    public static function heroIntroImage(): string
    {
        $path = trim((string) Setting::get('hero_intro_image', ''));

        return asset($path !== '' ? $path : 'images/programs/education.jpg');
    }

    /**
     * Homepage sections in display order with their visibility; sections added to the config later are
     * appended visible, and "quick_give" always stays first.
     *
     * @return list<array{key: string, visible: bool}>
     */
    public static function homeSections(): array
    {
        $available = config('bader.home_sections');
        $stored = json_decode((string) Setting::get('home_sections', ''), true);
        $sections = collect(is_array($stored) ? $stored : [])
            ->filter(fn ($section) => is_array($section) && in_array($section['key'] ?? null, $available, true))
            ->unique('key')
            ->map(fn (array $section): array => ['key' => $section['key'], 'visible' => (bool) ($section['visible'] ?? true)]);

        foreach ($available as $key) {
            if (! $sections->contains('key', $key)) {
                $sections->push(['key' => $key, 'visible' => true]);
            }
        }

        return $sections->sortBy(fn (array $section): int => $section['key'] === 'quick_give' ? 0 : 1)->values()->all();
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
