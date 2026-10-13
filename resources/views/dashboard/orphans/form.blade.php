@extends('layouts.dashboard')

@section('title', ($isEdit ? __('dashboard.edit') : __('orphans.add_manually')) . ' — ' . __('dashboard.module.orphans') . ' — ' . __('brand.name'))

@php
  $ui = [
    'section' => 'min-w-0 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6',
    'sectionTitle' => 'text-base font-semibold text-gray-900 dark:text-white',
    'sectionHint' => 'mt-1 text-theme-xs text-gray-500 dark:text-gray-400',
    'grid' => 'mt-5 grid grid-cols-1 gap-5 md:grid-cols-2',
    'label' => 'mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400',
    'input' => 'h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800',
    'choice' => 'flex cursor-pointer items-center gap-3 rounded-xl border border-gray-200 p-4 text-theme-sm font-medium text-gray-800 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50 dark:border-gray-700 dark:text-white/90 dark:has-[:checked]:bg-brand-500/10',
    'item' => 'rounded-xl border border-gray-200 p-4 dark:border-gray-700',
    'itemTitle' => 'text-theme-sm font-semibold text-gray-900 dark:text-white',
    'removeButton' => 'text-theme-xs font-medium text-error-600 hover:underline',
    'file' => 'w-full text-theme-sm text-gray-500 file:me-4 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-xs file:font-semibold file:text-brand-700 hover:file:bg-brand-100 dark:file:bg-brand-500/10 dark:file:text-brand-400',
    'hint' => 'mt-1 block text-theme-xs text-gray-500 dark:text-gray-400',
    'link' => 'mt-1 inline-block text-theme-xs font-medium text-brand-500 hover:underline',
  ];
@endphp

@section('content')
<div class="space-y-6">

  <div>
    <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">
      {{ $isEdit ? __('dashboard.edit') . ': ' . $family->code : __('orphans.add_manually') }}
    </h1>
    <nav class="mt-1 flex items-center gap-1.5 text-theme-xs text-gray-500 dark:text-gray-400">
      <a href="{{ route('dashboard.orphans.index') }}" class="hover:text-brand-500">{{ __('dashboard.module.orphans') }}</a>
      <svg class="size-3 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
      <span>{{ $isEdit ? __('dashboard.edit') : __('dashboard.add_new') }}</span>
    </nav>
    @unless ($isEdit)
      <p class="mt-2 text-theme-sm text-gray-500 dark:text-gray-400">{{ __('orphans.manual_hint') }}</p>
    @endunless
  </div>

  @if ($errors->any())
    <div class="rounded-xl border border-error-500/20 bg-error-50 p-4 text-theme-sm text-error-700 dark:bg-error-500/10 dark:text-error-400">
      <p class="font-semibold">{{ __('dashboard.fix_errors') }}</p>
      <ul class="mt-1 list-disc ps-5 space-y-1">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ $isEdit ? route('dashboard.orphans.update', $family) : route('dashboard.orphans.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    @include('partials.orphan-family-fields', ['family' => $family, 'documentsRequired' => false, 'ui' => $ui])

    <div class="flex items-center justify-end gap-3 pt-2">
      <a href="{{ $isEdit ? route('dashboard.orphans.show', $family) : route('dashboard.orphans.index') }}"
        class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
        {{ __('dashboard.cancel') }}
      </a>
      <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-6 py-3 text-theme-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 transition">
        {{ __('dashboard.save_changes') }}
      </button>
    </div>
  </form>
</div>
@endsection
