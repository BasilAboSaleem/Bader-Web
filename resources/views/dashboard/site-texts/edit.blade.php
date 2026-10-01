@extends('layouts.dashboard')

@section('title', __('dashboard.module.site_texts') . ' — ' . __('brand.name'))

@php
  $fieldClass = 'w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-theme-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90';
  $oldTexts = old('texts', []);
@endphp

@section('content')
<div class="space-y-6">

  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">{{ __('dashboard.module.site_texts') }}</h1>
      <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">{{ __('dashboard.module.site_texts_desc') }}</p>
    </div>
    <span class="rounded-full bg-brand-50 px-3 py-1 text-theme-xs font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">
      {{ __('dashboard.site_texts_customised', ['count' => $customisedCount]) }}
    </span>
  </div>

  @if (session('status'))
    <div class="flex items-center gap-3 rounded-xl border border-success-500/20 bg-success-50 p-4 text-theme-sm font-medium text-success-700 dark:bg-success-500/10 dark:text-success-400">
      <svg class="size-5 shrink-0 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
      <span>{{ session('status') }}</span>
    </div>
  @endif

  @if ($errors->any())
    <div class="rounded-xl border border-error-500/20 bg-error-50 p-4 text-theme-sm text-error-700 dark:bg-error-500/10 dark:text-error-400">
      <p class="font-semibold">{{ __('dashboard.fix_errors') }}</p>
      <ul class="mt-1 list-disc space-y-1 ps-5">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
    <form method="GET" action="{{ route('dashboard.site-texts.edit') }}" class="flex flex-wrap gap-3">
      <input type="search" name="q" value="{{ $search }}" placeholder="{{ __('dashboard.site_texts_search') }}" aria-label="{{ __('dashboard.site_texts_search') }}" class="h-11 min-w-0 flex-1 {{ $fieldClass }}">
      <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-5 text-theme-sm font-medium text-white hover:bg-brand-600">{{ __('dashboard.site_texts_search_button') }}</button>
    </form>
    <nav class="mt-4 flex flex-wrap gap-2" aria-label="{{ __('dashboard.module.site_texts') }}">
      @foreach ($groups as $groupKey)
        <a href="{{ route('dashboard.site-texts.edit', ['group' => $groupKey]) }}"
          @class([
            'rounded-lg px-3.5 py-2 text-theme-sm font-medium transition',
            'bg-brand-500 text-white' => $search === '' && $group === $groupKey,
            'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300' => $search !== '' || $group !== $groupKey,
          ])>
          {{ __('dashboard.site_texts_group.'.$groupKey) }}
        </a>
      @endforeach
    </nav>
    <p class="mt-3 text-theme-xs text-gray-500 dark:text-gray-400">{{ __('dashboard.site_texts_hint') }}</p>
  </div>

  @if ($search !== '')
    <p class="text-theme-sm text-gray-600 dark:text-gray-400">{{ __('dashboard.site_texts_results', ['count' => count($keys), 'query' => $search]) }}</p>
  @endif

  @if ($keys === [])
    <div class="rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center text-theme-sm text-gray-500 dark:border-gray-700 dark:bg-white/[0.03]">{{ __('dashboard.no_records') }}</div>
  @else
    <form action="{{ route('dashboard.site-texts.update') }}" method="POST" class="space-y-3">
      @csrf
      @method('PUT')
      <input type="hidden" name="group" value="{{ $search === '' ? $group : '' }}">
      <input type="hidden" name="q" value="{{ $search }}">

      @foreach ($keys as $key)
        @php $isCustomised = isset($overrides['ar'][$key]) || isset($overrides['en'][$key]); @endphp
        <div @class(['rounded-xl border bg-white p-4 dark:bg-white/[0.03]', 'border-brand-200 dark:border-brand-500/30' => $isCustomised, 'border-gray-200 dark:border-gray-800' => ! $isCustomised])>
          <div class="mb-2 flex flex-wrap items-center gap-2">
            <code class="rounded bg-gray-100 px-2 py-0.5 text-[11px] text-gray-600 dark:bg-gray-800 dark:text-gray-400" dir="ltr">{{ $key }}</code>
            @if ($isCustomised)
              <span class="rounded-full bg-brand-50 px-2 py-0.5 text-[11px] font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">{{ __('dashboard.site_texts_badge') }}</span>
            @endif
          </div>
          <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
            @foreach (config('bader.locales') as $locale)
              @php
                $default = $defaults[$locale][$key] ?? '';
                $value = $oldTexts[$locale][$key] ?? ($overrides[$locale][$key] ?? $default);
              @endphp
              <div>
                <label class="mb-1 block text-theme-xs font-medium text-gray-500 dark:text-gray-400" for="text-{{ $locale }}-{{ $loop->parent->index }}">{{ __('dashboard.site_texts_locale.'.$locale) }}</label>
                <textarea id="text-{{ $locale }}-{{ $loop->parent->index }}" name="texts[{{ $locale }}][{{ $key }}]" rows="{{ mb_strlen($default) > 90 ? 3 : 1 }}" placeholder="{{ $default }}" @if ($locale === 'en') dir="ltr" @endif class="{{ $fieldClass }}">{{ $value }}</textarea>
                @if (isset($overrides[$locale][$key]))
                  <p class="mt-1 text-[11px] leading-5 text-gray-400">{{ __('dashboard.site_texts_original') }}: {{ $default }}</p>
                @endif
              </div>
            @endforeach
          </div>
        </div>
      @endforeach

      <div class="sticky bottom-4 flex justify-end">
        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-6 py-3 text-theme-sm font-medium text-white shadow-theme-lg hover:bg-brand-600 transition">
          <svg class="me-2 size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          {{ __('dashboard.save_changes') }}
        </button>
      </div>
    </form>
  @endif
</div>
@endsection
