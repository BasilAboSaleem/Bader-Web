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
     * Display the homepage sections form.
     */
    public function edit(): View
    {
        return view('dashboard.homepage.edit', [
            'sections' => SiteSettings::homeSections(),
        ]);
    }

    /**
     * Save which homepage sections are shown and in which order.
     */
    public function update(Request $request): RedirectResponse
    {
        $available = config('bader.home_sections');
        $rules = ['sections' => ['required', 'array']];

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

        return redirect()->route('dashboard.homepage.edit')
            ->with('status', __('dashboard.homepage_saved'));
    }
}
