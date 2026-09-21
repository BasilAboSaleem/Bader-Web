<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        return redirect()->route('dashboard.settings.edit')
            ->with('status', __('dashboard.settings_saved'));
    }
}
