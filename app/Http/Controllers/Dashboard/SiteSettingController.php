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
        ]);
    }

    /**
     * Update the site settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
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
            'quick_give.*.category' => ['nullable', Rule::in(config('bader.donation_categories'))],
            'quick_give.*.amount' => ['nullable', 'numeric', 'min:1', 'max:100000', 'required_with:quick_give.*.label_ar'],
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

        return redirect()->route('dashboard.settings.edit')
            ->with('status', __('dashboard.settings_saved'));
    }
}
