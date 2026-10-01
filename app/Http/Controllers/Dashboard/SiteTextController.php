<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SiteTextController extends Controller
{
    private const SEARCH_LIMIT = 100;

    /** @var array<string, array<string, string>>|null */
    private ?array $defaults = null;

    /**
     * Show the editable public texts of one group, or the search results across all groups.
     */
    public function edit(Request $request): View
    {
        $groups = config('bader.editable_text_groups');
        $search = trim((string) $request->query('q', ''));
        $group = array_key_exists((string) $request->query('group'), $groups) ? (string) $request->query('group') : array_key_first($groups);

        $defaults = $this->defaults();
        $overrides = $this->overrides();

        $keys = $search !== ''
            ? array_slice(array_values(array_filter(
                $this->editableKeys(),
                fn (string $key): bool => Str::contains(
                    implode(' ', [$key, $defaults['ar'][$key] ?? '', $defaults['en'][$key] ?? '', $overrides['ar'][$key] ?? '', $overrides['en'][$key] ?? '']),
                    $search,
                    ignoreCase: true,
                ),
            )), 0, self::SEARCH_LIMIT)
            : $this->keysForPrefixes($groups[$group]);

        return view('dashboard.site-texts.edit', [
            'groups' => array_keys($groups),
            'group' => $group,
            'search' => $search,
            'keys' => $keys,
            'defaults' => $defaults,
            'overrides' => $overrides,
            'customisedCount' => count($overrides['ar']) + count($overrides['en']),
        ]);
    }

    /**
     * Save overrides for the submitted keys; an empty value or the original text restores the default.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'texts' => ['required', 'array'],
            'texts.*' => ['array'],
        ]);

        $editableKeys = array_flip($this->editableKeys());
        $defaults = $this->defaults();
        $overrides = $this->overrides();
        $submitted = $request->input('texts');
        $errors = [];

        foreach (config('bader.locales') as $locale) {
            foreach ((array) ($submitted[$locale] ?? []) as $key => $value) {
                if (! is_string($key) || ! isset($editableKeys[$key])) {
                    continue;
                }

                $value = trim((string) $value);
                $default = (string) ($defaults[$locale][$key] ?? '');

                if ($value === '' || $value === $default) {
                    unset($overrides[$locale][$key]);

                    continue;
                }

                if (mb_strlen($value) > 2000) {
                    $errors[] = __('dashboard.site_texts_too_long', ['key' => $key]);

                    continue;
                }

                $missing = $this->missingPlaceholders($default, $value);

                if ($missing !== []) {
                    $errors[] = __('dashboard.site_texts_placeholders', ['key' => $key, 'placeholders' => implode('، ', $missing)]);

                    continue;
                }

                $overrides[$locale][$key] = $value;
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages(['texts' => $errors]);
        }

        foreach ($overrides as $locale => $lines) {
            ksort($lines);
            Setting::set("site_texts_{$locale}", $lines === [] ? '' : json_encode($lines, JSON_UNESCAPED_UNICODE), 'texts');
        }

        app('translator')->setLoaded([]);

        return redirect()->route('dashboard.site-texts.edit', array_filter([
            'group' => $request->input('group'),
            'q' => $request->input('q'),
        ]))->with('status', __('dashboard.site_texts_saved'));
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function defaults(): array
    {
        if ($this->defaults !== null) {
            return $this->defaults;
        }

        $this->defaults = [];

        foreach (config('bader.locales') as $locale) {
            $this->defaults[$locale] = json_decode((string) file_get_contents(lang_path("{$locale}.json")), true) ?: [];
        }

        return $this->defaults;
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function overrides(): array
    {
        $overrides = [];

        foreach (config('bader.locales') as $locale) {
            $overrides[$locale] = SiteSettings::textOverrides($locale);
        }

        return $overrides;
    }

    /**
     * @return list<string>
     */
    private function editableKeys(): array
    {
        return $this->keysForPrefixes(array_merge(...array_values(config('bader.editable_text_groups'))));
    }

    /**
     * Keys starting with one of the prefixes, excluding plural strings whose "|" segments must stay intact.
     *
     * @param  list<string>  $prefixes
     * @return list<string>
     */
    private function keysForPrefixes(array $prefixes): array
    {
        $defaults = $this->defaults()['ar'];

        return array_values(array_filter(
            array_keys($defaults),
            fn (string $key): bool => Str::startsWith($key, $prefixes) && ! str_contains((string) $defaults[$key], '|'),
        ));
    }

    /**
     * @return list<string>
     */
    private function missingPlaceholders(string $default, string $value): array
    {
        preg_match_all('/:([A-Za-z_]+)/', $default, $matches);

        return array_values(array_filter(
            array_unique($matches[0]),
            fn (string $placeholder): bool => ! Str::contains($value, $placeholder, ignoreCase: true),
        ));
    }
}
