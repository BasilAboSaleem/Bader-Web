<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomepageController extends Controller
{
    /**
     * Translatable fields of the organisation's hero slide, with their maximum length.
     *
     * @var array<string, int>
     */
    private const HERO_INTRO_TEXTS = [
        'badge' => 60,
        'title' => 120,
        'text' => 400,
        'primary_label' => 40,
        'secondary_label' => 40,
    ];

    /**
     * Display the homepage sections form.
     */
    public function edit(): View
    {
        return view('dashboard.homepage.edit', [
            'sections' => SiteSettings::homeSections(),
            'heroIntroEnabled' => SiteSettings::heroIntroEnabled(),
            'heroIntroImage' => SiteSettings::heroIntroImage(),
            'hasCustomHeroImage' => filled(Setting::get('hero_intro_image')),
            'heroIntro' => [
                'ar' => SiteSettings::heroIntro('ar'),
                'en' => SiteSettings::heroIntro('en'),
            ],
            'heroIntroUrls' => [
                'primary' => (string) Setting::get('hero_intro_primary_url', ''),
                'secondary' => (string) Setting::get('hero_intro_secondary_url', ''),
            ],
            'heroIntroTexts' => self::HERO_INTRO_TEXTS,
        ]);
    }

    /**
     * Save which homepage sections are shown and in which order.
     */
    public function update(Request $request): RedirectResponse
    {
        $available = config('bader.home_sections');
        $rules = [
            'sections' => ['required', 'array'],
            'hero_intro_enabled' => ['nullable', 'boolean'],
            'hero_intro_image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'hero_intro_primary_url' => ['nullable', 'string', 'max:500', 'regex:#^(https?://|/)#'],
            'hero_intro_secondary_url' => ['nullable', 'string', 'max:500', 'regex:#^(https?://|/)#'],
        ];

        foreach (self::HERO_INTRO_TEXTS as $field => $maxLength) {
            foreach (config('bader.locales') as $locale) {
                $rules["hero_intro_{$field}_{$locale}"] = ['nullable', 'string', "max:{$maxLength}"];
            }
        }

        foreach ($available as $key) {
            $rules["sections.{$key}.visible"] = ['nullable', 'boolean'];
            $rules["sections.{$key}.order"] = ['nullable', 'integer', 'min:1', 'max:99'];
        }

        $validated = $request->validate($rules);

        $sections = collect($available)
            ->map(fn (string $key, int $index): array => [
                'key' => $key,
                'visible' => (bool) ($validated['sections'][$key]['visible'] ?? false),
                'order' => $key === 'quick_give' ? 0 : (int) ($validated['sections'][$key]['order'] ?? $index + 1),
                'index' => $index,
            ])
            ->sortBy([['order', 'asc'], ['index', 'asc']])
            ->map(fn (array $section): array => ['key' => $section['key'], 'visible' => $section['visible']])
            ->values();

        Setting::set('home_sections', $sections->toJson(), 'homepage');

        if ($request->has('hero_intro_enabled')) {
            Setting::set('hero_intro_enabled', $request->boolean('hero_intro_enabled') ? '1' : '0', 'homepage');
        }

        foreach (array_keys($rules) as $field) {
            if (preg_match('/^hero_intro_(?!enabled$|image_file$)/', $field) && $request->has($field)) {
                Setting::set($field, trim((string) ($validated[$field] ?? '')), 'homepage');
            }
        }

        if ($request->hasFile('hero_intro_image_file')) {
            Setting::set('hero_intro_image', 'storage/'.$request->file('hero_intro_image_file')->store('hero', 'public'), 'homepage');
        } elseif ($request->boolean('hero_intro_image_reset')) {
            Setting::set('hero_intro_image', '', 'homepage');
        }

        return redirect()->route('dashboard.homepage.edit')
            ->with('status', __('dashboard.homepage_saved'));
    }
}
