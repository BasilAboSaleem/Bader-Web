@extends('layouts.dashboard')

@section('title', ($isEdit ? __('dashboard.edit') : __('dashboard.add_new')) . ' — ' . __('dashboard.module.stories') . ' — ' . __('brand.name'))

@section('content')
<div class="space-y-6">

  {{-- Page Header --}}
  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">
        {{ $isEdit ? __('dashboard.edit') . ': ' . $story->title_ar : __('dashboard.add_new') . ' — ' . __('dashboard.module.stories') }}
      </h1>
      <nav class="mt-1 flex items-center gap-1.5 text-theme-xs text-gray-500 dark:text-gray-400">
        <a href="{{ route('dashboard.stories.index') }}" class="hover:text-brand-500">{{ __('dashboard.module.stories') }}</a>
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
    action="{{ $isEdit ? route('dashboard.stories.update', $story) : route('dashboard.stories.store') }}"
    method="POST"
    class="space-y-6"
  >
    @csrf
    @if ($isEdit) @method('PUT') @endif

    {{-- Basic Info --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
      <div class="mb-5 border-b border-gray-100 pb-4 dark:border-gray-800">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('dashboard.section.basic_info') }}</h2>
      </div>
      <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

        <div>
          <label for="title_ar" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">{{ __('dashboard.field.title_ar') }} <span class="text-error-500">*</span></label>
          <input type="text" id="title_ar" name="title_ar" value="{{ old('title_ar', $story->title_ar) }}"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
          @error('title_ar') <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p> @enderror
        </div>

        <div>
          <label for="title_en" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">{{ __('dashboard.field.title_en') }}</label>
          <input type="text" id="title_en" name="title_en" value="{{ old('title_en', $story->title_en) }}" dir="ltr"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
          @error('title_en') <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p> @enderror
        </div>

        <div>
          <label for="category_ar" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">{{ __('dashboard.field.category_ar') }}</label>
          <input type="text" id="category_ar" name="category_ar" value="{{ old('category_ar', $story->category_ar) }}"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
          @error('category_ar') <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p> @enderror
        </div>

        <div>
          <label for="category_en" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">{{ __('dashboard.field.category_en') }}</label>
          <input type="text" id="category_en" name="category_en" value="{{ old('category_en', $story->category_en) }}" dir="ltr"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
          @error('category_en') <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p> @enderror
        </div>

        <div>
          <label for="key" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">{{ __('dashboard.field.key') }}</label>
          <input type="text" id="key" name="key" value="{{ old('key', $story->key) }}" dir="ltr"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
          @error('key') <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p> @enderror
        </div>

        <div>
          <label for="image" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">{{ __('dashboard.field.image_url') }}</label>
          <input type="text" id="image" name="image" value="{{ old('image', $story->image) }}" dir="ltr" placeholder="https://..."
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
          @error('image') <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p> @enderror
        </div>

      </div>
    </div>

    {{-- Excerpts --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
      <div class="mb-5 border-b border-gray-100 pb-4 dark:border-gray-800">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('dashboard.section.excerpt') }}</h2>
      </div>
      <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div>
          <label for="excerpt_ar" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">{{ __('dashboard.field.excerpt_ar') }}</label>
          <textarea id="excerpt_ar" name="excerpt_ar" rows="3"
            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">{{ old('excerpt_ar', $story->excerpt_ar) }}</textarea>
          @error('excerpt_ar') <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p> @enderror
        </div>
        <div>
          <label for="excerpt_en" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">{{ __('dashboard.field.excerpt_en') }}</label>
          <textarea id="excerpt_en" name="excerpt_en" rows="3" dir="ltr"
            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">{{ old('excerpt_en', $story->excerpt_en) }}</textarea>
          @error('excerpt_en') <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p> @enderror
        </div>
      </div>
    </div>

    {{-- Full Content --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
      <div class="mb-5 border-b border-gray-100 pb-4 dark:border-gray-800">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('dashboard.section.content') }}</h2>
      </div>
      <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div>
          <label for="content_ar" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">{{ __('dashboard.field.content_ar') }}</label>
          <textarea id="content_ar" name="content_ar" rows="8"
            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">{{ old('content_ar', $story->content_ar) }}</textarea>
          @error('content_ar') <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p> @enderror
        </div>
        <div>
          <label for="content_en" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">{{ __('dashboard.field.content_en') }}</label>
          <textarea id="content_en" name="content_en" rows="8" dir="ltr"
            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">{{ old('content_en', $story->content_en) }}</textarea>
          @error('content_en') <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p> @enderror
        </div>
      </div>
    </div>

    {{-- Publication --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
      <div class="mb-5 border-b border-gray-100 pb-4 dark:border-gray-800">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('dashboard.section.publication') }}</h2>
      </div>
      <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

        <div>
          <label for="published_at" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">{{ __('dashboard.field.published_at') }}</label>
          <input type="date" id="published_at" name="published_at"
            value="{{ old('published_at', $story->published_at ? \Carbon\Carbon::parse($story->published_at)->format('Y-m-d') : '') }}"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">
          @error('published_at') <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p> @enderror
        </div>

        <div>
          <label for="status" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">{{ __('dashboard.field.status') }} <span class="text-error-500">*</span></label>
          <select id="status" name="status"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800">
            <option value="draft" {{ old('status', $story->status) === 'draft' ? 'selected' : '' }}>{{ __('dashboard.status.draft') }}</option>
            <option value="under_review" {{ old('status', $story->status) === 'under_review' ? 'selected' : '' }}>{{ __('dashboard.status.under_review') }}</option>
            <option value="published" {{ old('status', $story->status) === 'published' ? 'selected' : '' }}>{{ __('dashboard.status.published') }}</option>
          </select>
          @error('status') <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-3">
          <input type="hidden" name="is_featured" value="0">
          <label class="relative inline-flex cursor-pointer items-center">
            <input type="checkbox" id="is_featured" name="is_featured" value="1"
              {{ old('is_featured', $story->is_featured) ? 'checked' : '' }}
              class="peer sr-only">
            <div class="h-6 w-11 rounded-full bg-gray-200 after:absolute after:start-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-brand-500 peer-checked:after:translate-x-full peer-checked:after:border-white rtl:peer-checked:after:-translate-x-full dark:border-gray-600 dark:bg-gray-700"></div>
          </label>
          <label for="is_featured" class="cursor-pointer text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ __('dashboard.field.featured') }}</label>
        </div>

      </div>
    </div>

    {{-- Form Actions --}}
    <div class="flex items-center justify-end gap-3 pt-2">
      <a href="{{ route('dashboard.stories.index') }}"
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
