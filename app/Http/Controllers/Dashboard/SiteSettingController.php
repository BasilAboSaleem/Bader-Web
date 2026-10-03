<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    private const MAX_GIFT_DESIGNS = 8;

    /**
     * Display the site settings edit form.
     */
    public function edit(): View
    {
        return view('dashboard.settings.edit', [
            'foundedYear' => SiteSettings::foundedYear(),
            'hqLocationAr' => SiteSettings::hqLocation('ar'),
            'hqLocationEn' => SiteSettings::hqLocation('en'),
            'fieldLocationAr' => SiteSettings::fieldLocation('ar'),
            'fieldLocationEn' => SiteSettings::fieldLocation('en'),
            'contactEmail' => SiteSettings::contactEmail(),
            'contactPhone' => SiteSettings::contactPhone(),
            'contactAddressAr' => SiteSettings::contactAddress('ar'),
            'contactAddressEn' => SiteSettings::contactAddress('en'),
            'urgentEnabled' => SiteSettings::urgentEnabled(),
            'urgentTextAr' => SiteSettings::urgentText('ar'),
            'urgentTextEn' => SiteSettings::urgentText('en'),
            'urgentUrl' => SiteSettings::urgentUrl(),
            'whatsappNumber' => Setting::get('whatsapp_number', ''),
            'goldPricePerGram' => SiteSettings::goldPricePerGram(),
            'quickGiveOptions' => SiteSettings::quickGiveOptions(),
            'socialLinks' => SiteSettings::socialLinks(),
            'giftDesigns' => array_values(SiteSettings::giftDesigns()),
            'maxGiftDesigns' => self::MAX_GIFT_DESIGNS,
            'donationCategories' => SiteSettings::donationCategoryDefinitions(),
            'maxDonationCategories' => config('bader.max_donation_categories'),
            'brandMarkUrl' => SiteSettings::brandAsset('mark_star'),
            'brandFaviconUrl' => SiteSettings::brandAsset('favicon'),
            'hasCustomMark' => filled(Setting::get('brand_mark_star')),
            'hasCustomFavicon' => filled(Setting::get('brand_favicon')),
        ]);
    }

    /**
     * Update the site settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $updatesCategories = $request->has('donation_categories');
        $categoryKeys = $updatesCategories
            ? collect($request->input('donation_categories'))->pluck('key')->filter()->push('general')->unique()->values()->all()
            : array_keys(SiteSettings::donationCategories());

        $validated = $request->validate([
            'brand_mark_star_file' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'brand_favicon_file' => ['nullable', 'file', 'mimes:png,ico,webp', 'max:1024'],
            'donation_categories' => ['nullable', 'array', 'max:'.config('bader.max_donation_categories')],
            'donation_categories.*' => ['array'],
            'donation_categories.*.key' => ['nullable', 'string', 'max:40', 'regex:/^[a-z0-9_]+$/', 'distinct', 'required_with:donation_categories.*.label_ar'],
            'donation_categories.*.label_ar' => ['nullable', 'string', 'max:60', 'required_with:donation_categories.*.key'],
            'donation_categories.*.label_en' => ['nullable', 'string', 'max:60'],
            'founded_year' => ['nullable', 'string', 'max:20'],
            'hq_location_ar' => ['nullable', 'string', 'max:255'],
            'hq_location_en' => ['nullable', 'string', 'max:255'],
            'field_location_ar' => ['nullable', 'string', 'max:255'],
            'field_location_en' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:100'],
            'contact_address_ar' => ['nullable', 'string', 'max:255'],
            'contact_address_en' => ['nullable', 'string', 'max:255'],
            'urgent_enabled' => ['nullable', 'boolean'],
            'urgent_text_ar' => ['nullable', 'string', 'max:500'],
            'urgent_text_en' => ['nullable', 'string', 'max:500'],
            'urgent_url' => ['nullable', 'url', 'max:500'],
            'whatsapp_number' => ['nullable', 'string', 'max:30', 'regex:/^\+?[\d\s()-]{6,}$/'],
            'gold_price_per_gram' => ['nullable', 'numeric', 'min:1', 'max:100000'],
            'quick_give' => ['nullable', 'array', 'max:4'],
            'quick_give.*.label_ar' => ['nullable', 'string', 'max:60', 'required_with:quick_give.*.amount'],
            'quick_give.*.label_en' => ['nullable', 'string', 'max:60'],
            'quick_give.*.category' => ['nullable', Rule::in($categoryKeys)],
            'quick_give.*.amount' => ['nullable', 'numeric', 'min:1', 'max:100000', 'required_with:quick_give.*.label_ar'],
            'social' => ['nullable', 'array'],
            'social.*' => ['nullable', 'url:http,https', 'max:500'],
            'gift_designs' => ['nullable', 'array', 'max:'.self::MAX_GIFT_DESIGNS],
            'gift_designs.*.key' => ['nullable', 'string', 'max:40', 'regex:/^[a-z0-9_]+$/', 'distinct', 'required_with:gift_designs.*.label_ar'],
            'gift_designs.*.label_ar' => ['nullable', 'string', 'max:60', 'required_with:gift_designs.*.key'],
            'gift_designs.*.label_en' => ['nullable', 'string', 'max:60'],
            'gift_designs.*.from' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'gift_designs.*.to' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'gift_designs.*.accent' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        $generalFields = [
            'founded_year',
            'hq_location_ar',
            'hq_location_en',
            'field_location_ar',
            'field_location_en',
        ];

        $contactFields = [
            'contact_email',
            'contact_phone',
            'contact_address_ar',
            'contact_address_en',
        ];

        $urgentFields = [
            'urgent_text_ar',
            'urgent_text_en',
            'urgent_url',
        ];

        foreach ($generalFields as $field) {
            Setting::set($field, $validated[$field] ?? '', 'general');
        }

        foreach ($contactFields as $field) {
            Setting::set($field, $validated[$field] ?? '', 'contact');
        }

        Setting::set('urgent_enabled', $request->boolean('urgent_enabled') ? '1' : '0', 'urgent');

        foreach ($urgentFields as $field) {
            Setting::set($field, $validated[$field] ?? '', 'urgent');
        }

        $quickGiveOptions = collect($validated['quick_give'] ?? [])
            ->filter(fn (array $option): bool => filled($option['label_ar'] ?? null) && filled($option['amount'] ?? null))
            ->map(fn (array $option): array => [
                'category' => $option['category'] ?? 'general',
                'amount' => (float) $option['amount'],
                'label_ar' => $option['label_ar'],
                'label_en' => $option['label_en'] ?? '',
            ])
            ->values();

        Setting::set('whatsapp_number', $validated['whatsapp_number'] ?? '', 'giving');
        Setting::set('gold_price_per_gram', $validated['gold_price_per_gram'] ?? '', 'giving');
        Setting::set('quick_give_options', $quickGiveOptions->isEmpty() ? '' : $quickGiveOptions->toJson(JSON_UNESCAPED_UNICODE), 'giving');

        foreach (config('bader.social_platforms') as $platform) {
            Setting::set("social_{$platform}", $validated['social'][$platform] ?? '', 'social');
        }

        $giftDesigns = collect($validated['gift_designs'] ?? [])
            ->filter(fn (array $design): bool => filled($design['key'] ?? null) && filled($design['label_ar'] ?? null))
            ->map(fn (array $design): array => [
                'key' => $design['key'],
                'label_ar' => $design['label_ar'],
                'label_en' => $design['label_en'] ?? '',
                'from' => $design['from'] ?? '#0a2e2f',
                'to' => $design['to'] ?? '#1f6b38',
                'accent' => $design['accent'] ?? '#e1e56b',
            ])
            ->values();

        Setting::set('gift_designs', $giftDesigns->isEmpty() ? '' : $giftDesigns->toJson(JSON_UNESCAPED_UNICODE), 'giving');

        if ($updatesCategories) {
            $donationCategories = collect($validated['donation_categories'] ?? [])
                ->filter(fn (array $category): bool => filled($category['key'] ?? null) && filled($category['label_ar'] ?? null))
                ->map(fn (array $category): array => [
                    'key' => $category['key'],
                    'label_ar' => $category['label_ar'],
                    'label_en' => $category['label_en'] ?? '',
                ])
                ->values();

            if (! $donationCategories->contains('key', 'general')) {
                $donationCategories->prepend(['key' => 'general', 'label_ar' => __('donation.category.general', [], 'ar'), 'label_en' => __('donation.category.general', [], 'en')]);
            }

            Setting::set('donation_categories', $donationCategories->toJson(JSON_UNESCAPED_UNICODE), 'giving');
        }

        foreach (['mark_star', 'favicon'] as $asset) {
            if ($request->hasFile("brand_{$asset}_file")) {
                Setting::set("brand_{$asset}", 'storage/'.$request->file("brand_{$asset}_file")->store('branding', 'public'), 'branding');
            } elseif ($request->boolean("brand_{$asset}_reset")) {
                Setting::set("brand_{$asset}", '', 'branding');
            }
        }

        return redirect()->route('dashboard.settings.edit')
            ->with('status', __('dashboard.settings_saved'));
    }
}
