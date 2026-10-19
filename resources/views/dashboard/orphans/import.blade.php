@extends('layouts.dashboard')

@section('title', __('orphans.import.title') . ' — ' . __('brand.name'))

@php
  $card = 'rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6';
  $cardTitle = 'mb-3 text-base font-semibold text-gray-900 dark:text-white';
@endphp

@section('content')
<div class="space-y-6">

  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">{{ __('orphans.import.title') }}</h1>
      <nav class="mt-1 flex items-center gap-1.5 text-theme-xs text-gray-500 dark:text-gray-400">
        <a href="{{ route('dashboard.orphans.index') }}" class="hover:text-brand-500">{{ __('dashboard.module.orphans') }}</a>
        <svg class="size-3 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span>{{ __('orphans.import.title') }}</span>
      </nav>
    </div>
    <a href="{{ route('dashboard.orphans.import.template') }}"
      class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-theme-sm font-medium text-white hover:bg-brand-600">
      <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
      {{ __('orphans.import.download_template') }}
    </a>
  </div>

  @if ($errors->any())
    <div class="rounded-xl border border-error-500/20 bg-error-50 p-4 text-theme-sm text-error-700 dark:bg-error-500/10 dark:text-error-400" role="alert">
      <p class="font-semibold">{{ __('orphans.import.failed') }}</p>
      <ul class="mt-2 list-disc space-y-1 ps-5">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_24rem]">
    <div class="space-y-6">
      <div class="{{ $card }}">
        <h2 class="{{ $cardTitle }}">{{ __('orphans.import.upload_title') }}</h2>
        <p class="mb-4 text-theme-sm text-gray-500 dark:text-gray-400">{{ __('orphans.import.upload_intro') }}</p>
        <form method="POST" action="{{ route('dashboard.orphans.import.store') }}" enctype="multipart/form-data" class="space-y-4">
          @csrf
          <label class="block">
            <span class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">{{ __('orphans.import.file') }}</span>
            <input type="file" name="file" accept=".xlsx,.csv,text/csv" required
              class="block w-full text-theme-sm text-gray-700 file:me-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-theme-sm file:font-medium file:text-brand-600 dark:text-gray-300 dark:file:bg-brand-500/10 dark:file:text-brand-400">
          </label>
          <button type="submit" class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2.5 text-theme-sm font-medium text-white hover:bg-brand-600">{{ __('orphans.import.submit') }}</button>
        </form>
      </div>

      <div class="{{ $card }}">
        <h2 class="{{ $cardTitle }}">{{ __('orphans.import.rules_title') }}</h2>
        <ul class="list-disc space-y-2 ps-5 text-theme-sm text-gray-600 dark:text-gray-300">
          <li>{{ __('orphans.import.rule_group') }}</li>
          <li>{{ __('orphans.import.rule_row') }}</li>
          <li>{{ __('orphans.import.rule_type') }}</li>
          <li>{{ __('orphans.import.rule_dates') }}</li>
          <li>{{ __('orphans.import.rule_ids') }}</li>
          <li>{{ __('orphans.import.rule_docs') }}</li>
          <li>{{ __('orphans.import.rule_examples') }}</li>
        </ul>
      </div>
    </div>

    <aside class="space-y-6">
      <div class="{{ $card }}">
        <h2 class="{{ $cardTitle }}">{{ __('orphans.import.values_type') }}</h2>
        <p class="text-theme-sm text-gray-600 dark:text-gray-300" dir="ltr">{{ implode(' | ', $types) }}</p>
      </div>
      <div class="{{ $card }}">
        <h2 class="{{ $cardTitle }}">{{ __('orphans.import.values_education') }}</h2>
        <p class="text-theme-sm leading-7 text-gray-600 dark:text-gray-300">{{ implode(' · ', $educationStages) }}</p>
      </div>
      <div class="{{ $card }}">
        <h2 class="{{ $cardTitle }}">{{ __('orphans.import.values_health') }}</h2>
        <p class="text-theme-sm leading-7 text-gray-600 dark:text-gray-300">{{ implode(' · ', $healthStatuses) }}</p>
      </div>
      <div class="{{ $card }}">
        <h2 class="{{ $cardTitle }}">{{ __('orphans.import.values_area') }}</h2>
        <p class="max-h-64 overflow-y-auto text-theme-xs leading-6 text-gray-600 dark:text-gray-300">{{ implode(' · ', $residenceAreas) }}</p>
      </div>
    </aside>
  </div>
</div>
@endsection
