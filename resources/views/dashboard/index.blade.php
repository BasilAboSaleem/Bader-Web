@extends('layouts.dashboard')

@section('title', __('dashboard.title'))

@section('content')
<div class="space-y-6">

  {{-- ── Welcome ─────────────────────────────────────────────────────────── --}}
  <div class="rounded-2xl border border-gray-200 bg-white p-5 md:p-6 dark:border-gray-800 dark:bg-white/3">
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
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
      <a
        href="{{ route('home') }}"
        target="_blank"
        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600"
      >
        <span>{{ __('dashboard.view_site') }}</span>
        <svg class="size-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
        </svg>
      </a>
    </div>
  </div>

  {{-- ── Figures that need attention ────────────────────────────────────── --}}
  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @foreach ($stats as $stat)
      <a
        href="{{ route($stat['route']) }}"
        @class([
          'flex items-center gap-4 rounded-2xl border bg-white p-5 transition hover:border-brand-300 dark:bg-white/3 dark:hover:border-brand-700',
          'border-error-200 dark:border-error-500/40' => $stat['alert'],
          'border-gray-200 dark:border-gray-800' => ! $stat['alert'],
        ])
      >
        <span @class([
          'flex h-12 w-12 shrink-0 items-center justify-center rounded-xl',
          'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-400' => $stat['alert'],
          'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-white/90' => ! $stat['alert'],
        ])>
          <x-dashboard.module-icon :name="$stat['icon']" />
        </span>
        <span class="min-w-0">
          <span class="block text-sm text-gray-500 dark:text-gray-400">{{ __('dashboard.stats.'.$stat['key']) }}</span>
          <span class="mt-1 block text-title-sm font-bold text-gray-800 dark:text-white/90" dir="ltr">{{ $stat['value'] }}</span>
        </span>
      </a>
    @endforeach
  </div>

  {{-- ── Sections, grouped like the sidebar ─────────────────────────────── --}}
  @foreach ($groups as $group)
    <section>
      <h3 class="mb-3 text-base font-bold text-gray-800 dark:text-white/90">{{ __('dashboard.nav_group.'.$group['key']) }}</h3>
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($group['modules'] as $module)
          <a
            href="{{ route($module['route']) }}"
            class="group flex items-start gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs transition hover:border-brand-300 dark:border-gray-800 dark:bg-white/3 dark:hover:border-brand-700"
          >
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">
              <x-dashboard.module-icon :name="$module['key']" />
            </span>
            <span class="min-w-0 flex-1">
              <span class="flex items-center justify-between gap-2">
                <span class="font-bold text-gray-800 group-hover:text-brand-600 dark:text-white/90">{{ __('dashboard.module.'.$module['key']) }}</span>
                @if ($module['badge'] > 0)
                  <span class="inline-flex min-w-6 items-center justify-center rounded-full bg-error-500 px-1.5 py-0.5 text-[11px] font-semibold text-white">{{ $module['badge'] }}</span>
                @endif
              </span>
              <span class="mt-1.5 block text-xs leading-relaxed text-gray-500 dark:text-gray-400">{{ __('dashboard.module.'.$module['key'].'_desc') }}</span>
            </span>
          </a>
        @endforeach
      </div>
    </section>
  @endforeach

</div>
@endsection
