<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstitutionalPageController extends Controller
{
    /**
     * Display the institutional pages management form.
     */
    public function edit(): View
    {
        $pages = [];

        foreach (config('bader.institutional_pages') as $page => $definition) {
            $values = [];

            foreach (config('bader.locales') as $locale) {
                $values["inst_{$page}_intro_{$locale}"] = SiteSettings::pageIntro($page, $locale);

                foreach ($definition['sections'] as $section) {
                    $values["inst_{$page}_{$section}_title_{$locale}"] = SiteSettings::institutionalTitle($page, "{$page}.{$section}", $locale);
                    $values["inst_{$page}_{$section}_text_{$locale}"] = SiteSettings::institutionalText($page, "{$page}.{$section}", $locale);
                }

                foreach ($definition['extras'] as $extra) {
                    foreach (['title', 'text'] as $field) {
                        $values["inst_{$page}_{$extra}_{$field}_{$locale}"] = (string) Setting::get("inst_{$page}_{$extra}_{$field}_{$locale}", '');
                    }
                }
            }

            $pages[$page] = $definition + ['values' => $values];
        }

        return view('dashboard.pages.edit', compact('pages'));
    }

    /**
     * Update institutional pages content.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        foreach ($validated as $key => $value) {
            Setting::set($key, (string) ($value ?? ''), 'pages');
        }

        return redirect()->route('dashboard.pages.edit')
            ->with('status', __('dashboard.pages_saved'));
    }

    /**
     * @return array<string, list<string>>
     */
    private function rules(): array
    {
        $rules = [];

        foreach (config('bader.institutional_pages') as $page => $definition) {
            foreach (config('bader.locales') as $locale) {
                $rules["inst_{$page}_intro_{$locale}"] = ['nullable', 'string', 'max:2000'];

                foreach ([...$definition['sections'], ...$definition['extras']] as $section) {
                    $rules["inst_{$page}_{$section}_title_{$locale}"] = ['nullable', 'string', 'max:255'];
                    $rules["inst_{$page}_{$section}_text_{$locale}"] = ['nullable', 'string', 'max:10000'];
                }
            }
        }

        return $rules;
    }
}
