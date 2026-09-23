@extends('layouts.dashboard')

@section('title', ($isEdit ? __('dashboard.edit') : __('dashboard.add_new')) . ' — ' . __('dashboard.module.media') . ' — ' . __('brand.name'))

@section('content')
<div class="space-y-6">

  {{-- Page Header --}}
  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">
        {{ $isEdit ? __('dashboard.edit') . ': ' . $asset->title_ar : __('dashboard.add_new') . ' — ' . __('dashboard.module.media') }}
      </h1>
      <nav class="mt-1 flex items-center gap-1.5 text-theme-xs text-gray-500 dark:text-gray-400">
        <a href="{{ route('dashboard.media.index') }}" class="hover:text-brand-500">{{ __('dashboard.module.media') }}</a>
        <svg class="size-3 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span>{{ $isEdit ? __('dashboard.edit') : __('dashboard.add_new') }}</span>
      </nav>
    </div>
  </div>

  @if ($errors->any())
    <div class="rounded-xl border border-error-500/20 bg-error-50 p-4 text-theme-sm text-error-700 dark:bg-error-500/10 dark:text-error-400">
      <p class="font-semibold">{{ __('dashboard.fix_errors') }}</p>
      <ul class="mt-1 list-disc ps-5 space-y-1">
        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
      </ul>
    </div>
  @endif

  <form
    action="{{ $isEdit ? route('dashboard.media.update', $asset) : route('dashboard.media.store') }}"
    method="POST"
    class="space-y-6"
  >
    @csrf
    @if ($isEdit) @method('PUT') @endif

    {{-- Titles & File --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
      <div class="mb-5 border-b border-gray-100 pb-4 dark:border-gray-800">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('dashboard.section.basic_info') }}</h2>
      </div>
      <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

        <div>
          <label for="title_ar" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">{{ __('dashboard.field.title_ar') }} <span class="text-error-500">*</span></label>
          <input type="text" id="title_ar" name="title_ar" value="{{ old('title_ar', $asset->title_ar) }}"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
          @error('title_ar') <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p> @enderror
        </div>

        <div>
          <label for="title_en" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">{{ __('dashboard.field.title_en') }}</label>
          <input type="text" id="title_en" name="title_en" value="{{ old('title_en', $asset->title_en) }}" dir="ltr"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
          @error('title_en') <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p> @enderror
        </div>

        <div class="md:col-span-2">
          <label for="file_path" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">{{ __('dashboard.field.file_path') }} <span class="text-error-500">*</span></label>
          <input type="text" id="file_path" name="file_path" value="{{ old('file_path', $asset->file_path) }}" dir="ltr"
            placeholder="https://..."
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
          @error('file_path') <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p> @enderror
        </div>

        <div>
          <label for="category" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">{{ __('dashboard.field.category') }}</label>
          <input type="text" id="category" name="category" value="{{ old('category', $asset->category) }}" dir="ltr"
            placeholder="image, video, document..."
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
          @error('category') <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p> @enderror
        </div>

      </div>
    </div>

    {{-- Consent --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
      <div class="mb-5 border-b border-gray-100 pb-4 dark:border-gray-800">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('dashboard.section.consent') }}</h2>
        <p class="mt-0.5 text-theme-xs text-gray-500 dark:text-gray-400">{{ __('dashboard.section.consent_desc') }}</p>
      </div>

      <div class="space-y-5">
        {{-- Consent Toggle --}}
        <div class="flex items-center gap-3">
          <input type="hidden" name="has_usage_consent" value="0">
          <label class="relative inline-flex cursor-pointer items-center">
            <input
              type="checkbox"
              id="has_usage_consent"
              name="has_usage_consent"
              value="1"
              {{ old('has_usage_consent', $asset->has_usage_consent) ? 'checked' : '' }}
              class="peer sr-only"
            >
            <div class="h-6 w-11 rounded-full bg-gray-200 after:absolute after:start-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-brand-500 peer-checked:after:translate-x-full peer-checked:after:border-white rtl:peer-checked:after:-translate-x-full dark:border-gray-600 dark:bg-gray-700"></div>
          </label>
          <label for="has_usage_consent" class="cursor-pointer text-theme-sm font-medium text-gray-800 dark:text-white/90">
            {{ __('dashboard.field.has_usage_consent') }}
          </label>
        </div>

        {{-- Consent Notes --}}
        <div>
          <label for="consent_notes" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">{{ __('dashboard.field.consent_notes') }}</label>
          <textarea id="consent_notes" name="consent_notes" rows="3"
            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">{{ old('consent_notes', $asset->consent_notes) }}</textarea>
          @error('consent_notes') <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p> @enderror
        </div>
      </div>
    </div>

    {{-- Form Actions --}}
    <div class="flex items-center justify-end gap-3 pt-2">
      <a href="{{ route('dashboard.media.index') }}"
        class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
        {{ __('dashboard.cancel') }}
      </a>
      <button type="submit"
        class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-6 py-3 text-theme-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 transition">
        <svg class="me-2 size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <span>{{ __('dashboard.save_changes') }}</span>
      </button>
    </div>

  </form>
</div>
@endsection
