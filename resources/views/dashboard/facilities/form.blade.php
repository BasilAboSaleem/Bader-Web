@extends('layouts.dashboard')

@section('title', ($isEdit ? __('dashboard.edit') : __('dashboard.add_new')) . ' — ' . __('dashboard.module.facilities') . ' — ' . __('brand.name'))

@section('content')
<div class="space-y-6">

  {{-- Page Header --}}
  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">
        {{ $isEdit ? __('dashboard.edit') . ': ' . $facility->name_ar : __('dashboard.add_new') . ' — ' . __('dashboard.module.facilities') }}
      </h1>
      <nav class="mt-1 flex items-center gap-1.5 text-theme-xs text-gray-500 dark:text-gray-400">
        <a href="{{ route('dashboard.facilities.index') }}" class="hover:text-brand-500">{{ __('dashboard.module.facilities') }}</a>
        <svg class="size-3 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span>{{ $isEdit ? __('dashboard.edit') : __('dashboard.add_new') }}</span>
      </nav>
    </div>
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

  <form
    action="{{ $isEdit ? route('dashboard.facilities.update', $facility) : route('dashboard.facilities.store') }}"
    method="POST"
    class="space-y-6"
  >
    @csrf
    @if ($isEdit) @method('PUT') @endif

    {{-- Names, Key & Location --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
      <div class="mb-5 border-b border-gray-100 pb-4 dark:border-gray-800">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('dashboard.section.basic_info') }}</h2>
      </div>

      <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

        <div>
          <label for="name_ar" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('dashboard.field.name_ar') }} <span class="text-error-500">*</span>
          </label>
          <input type="text" id="name_ar" name="name_ar" value="{{ old('name_ar', $facility->name_ar) }}"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
          @error('name_ar') <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p> @enderror
        </div>

        <div>
          <label for="name_en" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('dashboard.field.name_en') }}
          </label>
          <input type="text" id="name_en" name="name_en" value="{{ old('name_en', $facility->name_en) }}" dir="ltr"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
          @error('name_en') <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p> @enderror
        </div>

        <div>
          <label for="location_ar" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('dashboard.field.location_ar') }}
          </label>
          <input type="text" id="location_ar" name="location_ar" value="{{ old('location_ar', $facility->location_ar) }}"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
          @error('location_ar') <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p> @enderror
        </div>

        <div>
          <label for="location_en" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('dashboard.field.location_en') }}
          </label>
          <input type="text" id="location_en" name="location_en" value="{{ old('location_en', $facility->location_en) }}" dir="ltr"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
          @error('location_en') <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p> @enderror
        </div>

        <div>
          <label for="image" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('dashboard.field.image_url') }}
          </label>
          <input type="text" id="image" name="image" value="{{ old('image', $facility->image) }}" dir="ltr"
            placeholder="https://..."
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
          @error('image') <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p> @enderror
        </div>

        <div>
          <label for="key" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('dashboard.field.key') }}
          </label>
          <input type="text" id="key" name="key" value="{{ old('key', $facility->key) }}" dir="ltr"
            placeholder="{{ __('dashboard.field.key_placeholder') }}"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
          @error('key') <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p> @enderror
        </div>

        <div>
          <label for="order" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('dashboard.field.order') }}
          </label>
          <input type="number" id="order" name="order" value="{{ old('order', $facility->order ?? 0) }}" min="0"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
          @error('order') <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p> @enderror
        </div>

        <div>
          <label for="status" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('dashboard.field.status') }} <span class="text-error-500">*</span>
          </label>
          <select id="status" name="status"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800">
            <option value="draft" {{ old('status', $facility->status) === 'draft' ? 'selected' : '' }}>{{ __('dashboard.status.draft') }}</option>
            <option value="under_review" {{ old('status', $facility->status) === 'under_review' ? 'selected' : '' }}>{{ __('dashboard.status.under_review') }}</option>
            <option value="published" {{ old('status', $facility->status) === 'published' ? 'selected' : '' }}>{{ __('dashboard.status.published') }}</option>
          </select>
          @error('status') <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p> @enderror
        </div>

      </div>
    </div>

    {{-- Descriptions --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
      <div class="mb-5 border-b border-gray-100 pb-4 dark:border-gray-800">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('dashboard.section.description') }}</h2>
      </div>
      <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div>
          <label for="description_ar" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">{{ __('dashboard.field.description_ar') }}</label>
          <textarea id="description_ar" name="description_ar" rows="5"
            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">{{ old('description_ar', $facility->description_ar) }}</textarea>
          @error('description_ar') <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p> @enderror
        </div>
        <div>
          <label for="description_en" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">{{ __('dashboard.field.description_en') }}</label>
          <textarea id="description_en" name="description_en" rows="5" dir="ltr"
            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">{{ old('description_en', $facility->description_en) }}</textarea>
          @error('description_en') <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p> @enderror
        </div>
      </div>
    </div>

    {{-- Form Actions --}}
    <div class="flex items-center justify-end gap-3 pt-2">
      <a href="{{ route('dashboard.facilities.index') }}"
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
