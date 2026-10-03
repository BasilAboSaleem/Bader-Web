<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
                $values["inst_{$page}_intro_{$locale}"] = SiteSettings::pageIntroTemplate($page, $locale);

                foreach ($definition['extras'] as $extra) {
                    foreach (['title', 'text'] as $field) {
                        $values["inst_{$page}_{$extra}_{$field}_{$locale}"] = (string) Setting::get("inst_{$page}_{$extra}_{$field}_{$locale}", '');
                    }
                }
            }

            $pages[$page] = $definition + [
                'values' => $values,
                'has_cards' => $definition['sections'] !== [],
                'cards' => SiteSettings::institutionalCardDefinitions($page),
            ];
        }

        return view('dashboard.pages.edit', [
            'pages' => $pages,
            'maxCards' => config('bader.page_cards.max'),
            'cardIcons' => config('bader.page_cards.icons'),
        ]);
    }

    /**
     * Update institutional pages content.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        foreach ($validated as $key => $value) {
            if ($key !== 'cards') {
                Setting::set($key, (string) ($value ?? ''), 'pages');
            }
        }

        foreach ($this->pagesWithCards() as $page) {
            if (! $request->has("cards.{$page}")) {
                continue;
            }

            $cards = collect($validated['cards'][$page] ?? [])
                ->filter(fn (array $card): bool => filled($card['title_ar'] ?? null))
                ->map(fn (array $card): array => [
                    'icon' => $card['icon'] ?? 'sparkle',
                    'title_ar' => $card['title_ar'],
                    'title_en' => $card['title_en'] ?? '',
                    'text_ar' => $card['text_ar'] ?? '',
                    'text_en' => $card['text_en'] ?? '',
                ])
                ->values();

            Setting::set("inst_{$page}_cards", $cards->toJson(JSON_UNESCAPED_UNICODE), 'pages');
        }

        return redirect()->route('dashboard.pages.edit')
            ->with('status', __('dashboard.pages_saved'));
    }

    /**
     * @return array<string, list<mixed>>
     */
    private function rules(): array
    {
        $rules = ['cards' => ['nullable', 'array']];

        foreach (config('bader.institutional_pages') as $page => $definition) {
            foreach (config('bader.locales') as $locale) {
                $rules["inst_{$page}_intro_{$locale}"] = ['nullable', 'string', 'max:2000'];

                foreach ($definition['extras'] as $extra) {
                    $rules["inst_{$page}_{$extra}_title_{$locale}"] = ['nullable', 'string', 'max:255'];
                    $rules["inst_{$page}_{$extra}_text_{$locale}"] = ['nullable', 'string', 'max:10000'];
                }
            }
        }

        foreach ($this->pagesWithCards() as $page) {
            $rules["cards.{$page}"] = ['nullable', 'array', 'max:'.config('bader.page_cards.max')];
            $rules["cards.{$page}.*"] = ['array'];
            $rules["cards.{$page}.*.icon"] = ['nullable', Rule::in(config('bader.page_cards.icons'))];
            $rules["cards.{$page}.*.title_ar"] = ['nullable', 'string', 'max:255', "required_with:cards.{$page}.*.text_ar,cards.{$page}.*.title_en"];
            $rules["cards.{$page}.*.title_en"] = ['nullable', 'string', 'max:255'];
            $rules["cards.{$page}.*.text_ar"] = ['nullable', 'string', 'max:2000'];
            $rules["cards.{$page}.*.text_en"] = ['nullable', 'string', 'max:2000'];
        }

        return $rules;
    }

    /**
     * @return list<string>
     */
    private function pagesWithCards(): array
    {
        return array_keys(array_filter(config('bader.institutional_pages'), fn (array $definition): bool => $definition['sections'] !== []));
    }
}
