@extends('layouts.dashboard')

@section('title', __('dashboard.module.homepage') . ' — ' . __('brand.name'))

@section('content')
<div class="space-y-6">

  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">{{ __('dashboard.module.homepage') }}</h1>
      <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">{{ __('dashboard.homepage_subtitle') }}</p>
    </div>
    <a
      href="{{ route('home') }}"
      target="_blank"
      class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
    >
      {{ __('dashboard.view_live_site') }}
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
      {{ __('dashboard.fix_errors') }}
    </div>
  @endif

  <form action="{{ route('dashboard.homepage.update') }}" method="POST" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
      <div class="border-b border-gray-100 p-5 dark:border-gray-800 lg:px-6">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('dashboard.homepage_sections_title') }}</h2>
        <p class="mt-0.5 text-theme-xs text-gray-500 dark:text-gray-400">{{ __('dashboard.homepage_sections_hint') }}</p>
      </div>

      <div class="flex items-center gap-4 border-b border-gray-100 bg-gray-50/60 px-5 py-3 text-theme-sm text-gray-500 dark:border-gray-800 dark:bg-white/[0.02] lg:px-6">
        <span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-gray-200 text-theme-xs font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-400">—</span>
        <span>{{ __('dashboard.home_section.hero') }}</span>
        <span class="ms-auto text-theme-xs">{{ __('dashboard.homepage_hero_hint') }}</span>
      </div>

      <ul class="divide-y divide-gray-100 dark:divide-gray-800">
        @foreach ($sections as $section)
          @php
            $sectionKey = $section['key'];
            $isPinned = $sectionKey === 'quick_give';
            $isVisible = (bool) old("sections.$sectionKey.visible", $section['visible']);
          @endphp
          <li class="flex flex-wrap items-center gap-4 px-5 py-4 lg:px-6">
            @if ($isPinned)
              <span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-theme-xs font-semibold text-brand-600 dark:bg-brand-500/15 dark:text-brand-400" title="{{ __('dashboard.homepage_pinned') }}">1</span>
            @else
              <label class="sr-only" for="order_{{ $sectionKey }}">{{ __('dashboard.homepage_order') }}</label>
              <input
                type="number"
                id="order_{{ $sectionKey }}"
                name="sections[{{ $sectionKey }}][order]"
                value="{{ old("sections.$sectionKey.order", $loop->iteration) }}"
                min="1"
                max="99"
                dir="ltr"
                class="h-9 w-16 shrink-0 rounded-lg border border-gray-300 bg-transparent px-2 text-center text-theme-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
              >
            @endif

            <div class="min-w-0 flex-1">
              <p class="text-theme-sm font-semibold text-gray-800 dark:text-white/90">{{ __('dashboard.home_section.'.$sectionKey) }}</p>
              <p class="mt-0.5 text-theme-xs text-gray-500 dark:text-gray-400">
                {{ $isPinned ? __('dashboard.homepage_pinned') : __('dashboard.home_section.'.$sectionKey.'_desc') }}
              </p>
            </div>

            <input type="hidden" name="sections[{{ $sectionKey }}][visible]" value="0">
            <label class="flex shrink-0 cursor-pointer items-center gap-3">
              <span class="text-theme-xs text-gray-500 dark:text-gray-400">{{ __('dashboard.homepage_visible') }}</span>
              <span class="relative inline-flex items-center">
                <input type="checkbox" name="sections[{{ $sectionKey }}][visible]" value="1" @checked($isVisible) class="peer sr-only">
                <span class="h-6 w-11 rounded-full bg-gray-200 after:absolute after:start-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-brand-500 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus-visible:ring-3 peer-focus-visible:ring-brand-500/20 rtl:peer-checked:after:-translate-x-full dark:bg-gray-700"></span>
              </span>
            </label>
          </li>
        @endforeach
      </ul>
    </div>

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
