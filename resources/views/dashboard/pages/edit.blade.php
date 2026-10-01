@extends('layouts.dashboard')

@section('title', __('dashboard.module.pages') . ' — ' . __('brand.name'))

@php
  $fieldClass = 'w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-theme-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90';
  $labelClass = 'mb-1 block text-theme-xs font-medium text-gray-600 dark:text-gray-400';
@endphp

@section('content')
<div class="space-y-6">

  {{-- Page Header --}}
  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">
        {{ __('dashboard.pages_title') }}
      </h1>
      <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">
        {{ __('dashboard.pages_subtitle') }}
      </p>
    </div>
    <a
      href="{{ route('dashboard.faqs.index') }}"
      class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
    >
      {{ __('dashboard.module.faqs') }}
    </a>
  </div>

  @if (session('status'))
    <div class="flex items-center gap-3 rounded-xl border border-success-500/20 bg-success-50 p-4 text-theme-sm font-medium text-success-700 dark:bg-success-500/10 dark:text-success-400">
      <svg class="size-5 shrink-0 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
      </svg>
      <span>{{ session('status') }}</span>
    </div>
  @endif

  @if ($errors->any())
    <div class="rounded-xl border border-error-500/20 bg-error-50 p-4 text-theme-sm text-error-700 dark:bg-error-500/10 dark:text-error-400">
      {{ __('dashboard.pages_errors') }}
    </div>
  @endif

  <form action="{{ route('dashboard.pages.update') }}" method="POST" class="space-y-4">
    @csrf
    @method('PUT')

    @foreach ($pages as $page => $definition)
      <details class="group rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]" @if ($loop->first) open @endif>
        <summary class="flex cursor-pointer list-none items-center justify-between gap-3 p-5 lg:px-6">
          <span>
            <span class="block text-base font-semibold text-gray-900 dark:text-white">{{ __('page.'.$page.'.title') }}</span>
            <span class="mt-0.5 block text-theme-xs text-gray-500 dark:text-gray-400">{{ __('dashboard.pages_page_hint', ['sections' => count($definition['sections'])]) }}</span>
          </span>
          <span class="flex items-center gap-3">
            <a href="{{ route($page) }}" target="_blank" class="text-theme-xs font-medium text-brand-500 hover:underline">{{ __('dashboard.pages_view') }}</a>
            <svg class="size-5 text-gray-400 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </span>
        </summary>

        <div class="space-y-5 border-t border-gray-100 p-5 dark:border-gray-800 lg:p-6">
          {{-- Intro --}}
          <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            @foreach (config('bader.locales') as $locale)
              @php $name = "inst_{$page}_intro_{$locale}"; @endphp
              <div>
                <label for="{{ $name }}" class="{{ $labelClass }}">{{ __('dashboard.pages_intro_'.$locale) }}</label>
                <textarea id="{{ $name }}" name="{{ $name }}" rows="3" @if ($locale === 'en') dir="ltr" @endif class="{{ $fieldClass }}">{{ old($name, $definition['values'][$name]) }}</textarea>
                @error($name) <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p> @enderror
              </div>
            @endforeach
          </div>

          {{-- Optional blocks (president's speech, vision) --}}
          @foreach ($definition['extras'] as $extra)
            <div class="rounded-xl border border-brand-100 bg-brand-25 p-4 dark:border-brand-500/20 dark:bg-brand-500/5">
              <h3 class="text-theme-sm font-semibold text-gray-800 dark:text-white/90">{{ __('dashboard.pages_extra_'.$extra) }}</h3>
              <p class="mb-3 mt-0.5 text-theme-xs text-gray-500 dark:text-gray-400">{{ __('dashboard.pages_extra_hint') }}</p>
              <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                @foreach (config('bader.locales') as $locale)
                  @php $titleName = "inst_{$page}_{$extra}_title_{$locale}"; $textName = "inst_{$page}_{$extra}_text_{$locale}"; @endphp
                  <div>
                    <label for="{{ $titleName }}" class="{{ $labelClass }}">{{ __('dashboard.title_'.$locale) }}</label>
                    <input type="text" id="{{ $titleName }}" name="{{ $titleName }}" value="{{ old($titleName, $definition['values'][$titleName]) }}" @if ($locale === 'en') dir="ltr" @endif class="h-10 {{ $fieldClass }}">
                    <label for="{{ $textName }}" class="{{ $labelClass }} mt-2.5">{{ __('dashboard.text_'.$locale) }}</label>
                    <textarea id="{{ $textName }}" name="{{ $textName }}" rows="5" @if ($locale === 'en') dir="ltr" @endif class="{{ $fieldClass }}">{{ old($textName, $definition['values'][$textName]) }}</textarea>
                    @error($textName) <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p> @enderror
                  </div>
                @endforeach
              </div>
            </div>
          @endforeach

          {{-- Section cards --}}
          @foreach ($definition['sections'] as $section)
            <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4 dark:border-gray-800 dark:bg-gray-900/40">
              <h3 class="mb-3 text-theme-sm font-semibold text-gray-800 dark:text-white/90">
                {{ __('dashboard.pages_card', ['number' => $loop->iteration]) }}
              </h3>
              <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                @foreach (config('bader.locales') as $locale)
                  @php $titleName = "inst_{$page}_{$section}_title_{$locale}"; $textName = "inst_{$page}_{$section}_text_{$locale}"; @endphp
                  <div>
                    <label for="{{ $titleName }}" class="{{ $labelClass }}">{{ __('dashboard.title_'.$locale) }}</label>
                    <input type="text" id="{{ $titleName }}" name="{{ $titleName }}" value="{{ old($titleName, $definition['values'][$titleName]) }}" @if ($locale === 'en') dir="ltr" @endif class="h-10 {{ $fieldClass }}">
                    @error($titleName) <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p> @enderror
                    <label for="{{ $textName }}" class="{{ $labelClass }} mt-2.5">{{ __('dashboard.text_'.$locale) }}</label>
                    <textarea id="{{ $textName }}" name="{{ $textName }}" rows="3" @if ($locale === 'en') dir="ltr" @endif class="{{ $fieldClass }}">{{ old($textName, $definition['values'][$textName]) }}</textarea>
                    @error($textName) <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p> @enderror
                  </div>
                @endforeach
              </div>
            </div>
          @endforeach
        </div>
      </details>
    @endforeach

    <div class="flex items-center justify-end gap-3 pt-2">
      <a
        href="{{ route('dashboard') }}"
        class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
      >
        {{ __('dashboard.cancel') }}
      </a>
      <button
        type="submit"
        class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-6 py-3 text-theme-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 transition"
      >
        <svg class="me-2 size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <span>{{ __('dashboard.save_changes') }}</span>
      </button>
    </div>
  </form>
</div>
@endsection
