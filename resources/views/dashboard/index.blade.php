@extends('layouts.dashboard')

@section('title', __('dashboard.title'))

@section('content')
<div class="space-y-6">

  {{-- ── Top Metrics (EcommerceMetrics style) ──────────────────────────── --}}
  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:gap-6">

    {{-- Metric: Site Status --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 md:p-6 dark:border-gray-800 dark:bg-white/3">
      <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800">
        <svg class="size-6 text-gray-800 dark:text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
        </svg>
      </div>
      <div class="mt-5 flex items-end justify-between">
        <div>
          <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('dashboard.quick_info.public_status') }}</span>
          <h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">{{ __('dashboard.quick_info.public_status_val') }}</h4>
        </div>
        <span class="inline-flex items-center gap-1 rounded-full bg-success-50 px-2.5 py-0.5 text-xs font-medium text-success-700 dark:bg-success-500/15 dark:text-success-400">
          <svg class="size-3" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
          </svg>
          {{ __('dashboard.status_active') }}
        </span>
      </div>
    </div>

    {{-- Metric: Language --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 md:p-6 dark:border-gray-800 dark:bg-white/3">
      <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800">
        <svg class="size-6 text-gray-800 dark:text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
        </svg>
      </div>
      <div class="mt-5 flex items-end justify-between">
        <div>
          <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('dashboard.quick_info.default_lang') }}</span>
          <h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">{{ __('dashboard.quick_info.default_lang_val') }}</h4>
        </div>
        <span class="inline-flex items-center gap-1 rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-medium text-brand-700 dark:bg-brand-500/15 dark:text-brand-400">
          ar · en
        </span>
      </div>
    </div>

  </div>

  {{-- ── Welcome Banner ─────────────────────────────────────────────────── --}}
  <div class="rounded-2xl border border-gray-200 bg-white p-5 md:p-6 dark:border-gray-800 dark:bg-white/3">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700 dark:bg-brand-500/15 dark:text-brand-400">
          <span class="h-2 w-2 rounded-full bg-brand-500"></span>
          {{ __('dashboard.eyebrow') }}
        </span>
        <h3 class="mt-3 text-title-sm font-bold text-gray-800 dark:text-white/90">
          {{ __('dashboard.welcome_title') }}
        </h3>
        <p class="mt-1.5 max-w-2xl text-sm leading-relaxed text-gray-500 dark:text-gray-400">
          {{ __('dashboard.welcome_desc') }}
        </p>
      </div>
      <div class="shrink-0">
        <a
          href="{{ route('home') }}"
          target="_blank"
          class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 transition"
        >
          <span>{{ __('dashboard.view_site') }}</span>
          <svg class="size-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
          </svg>
        </a>
      </div>
    </div>
  </div>

  {{-- ── Modules Grid ────────────────────────────────────────────────────── --}}
  <div>
    <div class="mb-4">
      <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">{{ __('dashboard.modules_title') }}</h3>
      <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('dashboard.modules_subtitle') }}</p>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 md:gap-6">
      @foreach ($modules as $module)
      <div class="flex flex-col justify-between rounded-2xl border border-gray-200 bg-white p-5 md:p-6 shadow-theme-xs transition hover:border-brand-300 dark:border-gray-800 dark:bg-white/3 dark:hover:border-brand-700">
        <div>
          <div class="flex items-center justify-between gap-2">
            <h4 class="font-bold text-gray-800 dark:text-white/90">{{ __('dashboard.module.'.$module['key']) }}</h4>
            <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-semibold text-brand-700 dark:bg-brand-500/15 dark:text-brand-400">
              {{ __('dashboard.status_soon') }}
            </span>
          </div>
          <p class="mt-2.5 text-xs leading-relaxed text-gray-500 dark:text-gray-400">
            {{ __('dashboard.module.'.$module['key'].'_desc') }}
          </p>
        </div>
        <div class="mt-5 flex items-center justify-between border-t border-gray-100 pt-3.5 text-xs text-gray-400 dark:border-gray-800 dark:text-gray-600">
          <span class="inline-flex items-center gap-1.5">
            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <span>{{ __('dashboard.stage') ?? 'المرحلة' }} {{ $module['stage'] }}</span>
          </span>
          <span class="font-medium text-brand-500/70 dark:text-brand-400/60">{{ __('brand.name') }}</span>
        </div>
      </div>
      @endforeach
    </div>
  </div>

</div>
@endsection
