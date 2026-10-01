@extends('layouts.dashboard')

@section('title', ($isEdit ? __('dashboard.edit') : __('dashboard.add_new')) . ' — ' . __('dashboard.module.programs') . ' — ' . __('brand.name'))

@section('content')
<div class="space-y-6">

  {{-- Page Header --}}
  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">
        {{ $isEdit ? __('dashboard.edit') . ': ' . $program->title_ar : __('dashboard.add_new') . ' — ' . __('dashboard.module.programs') }}
      </h1>
      <nav class="mt-1 flex items-center gap-1.5 text-theme-xs text-gray-500 dark:text-gray-400">
        <a href="{{ route('dashboard.programs.index') }}" class="hover:text-brand-500">{{ __('dashboard.module.programs') }}</a>
        <svg class="size-3 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span>{{ $isEdit ? __('dashboard.edit') : __('dashboard.add_new') }}</span>
      </nav>
    </div>
  </div>

  {{-- Validation errors --}}
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
    action="{{ $isEdit ? route('dashboard.programs.update', $program) : route('dashboard.programs.store') }}"
    method="POST"
    enctype="multipart/form-data"
    class="space-y-6"
  >
    @csrf
    @if ($isEdit) @method('PUT') @endif

    {{-- Titles & Key --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
      <div class="mb-5 border-b border-gray-100 pb-4 dark:border-gray-800">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('dashboard.section.basic_info') }}</h2>
        <p class="mt-0.5 text-theme-xs text-gray-500 dark:text-gray-400">{{ __('dashboard.section.basic_info_desc') }}</p>
      </div>

      <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

        {{-- Title AR --}}
        <div>
          <label for="title_ar" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('dashboard.field.title_ar') }} <span class="text-error-500">*</span>
          </label>
          <input
            type="text"
            id="title_ar"
            name="title_ar"
            value="{{ old('title_ar', $program->title_ar) }}"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
          >
          @error('title_ar')
            <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
          @enderror
        </div>

        {{-- Title EN --}}
        <div>
          <label for="title_en" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('dashboard.field.title_en') }}
          </label>
          <input
            type="text"
            id="title_en"
            name="title_en"
            value="{{ old('title_en', $program->title_en) }}"
            dir="ltr"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
          >
          @error('title_en')
            <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
          @enderror
        </div>

        {{-- Category AR --}}
        <div>
          <label for="category_ar" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('dashboard.field.category_ar') }} (إغاثي / تنموي / حماية)
          </label>
          <input
            type="text"
            id="category_ar"
            name="category_ar"
            value="{{ old('category_ar', $program->category_ar) }}"
            placeholder="relief أو development أو protection"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
          >
          @error('category_ar')
            <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
          @enderror
        </div>

        {{-- Category EN --}}
        <div>
          <label for="category_en" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('dashboard.field.category_en') }}
          </label>
          <input
            type="text"
            id="category_en"
            name="category_en"
            value="{{ old('category_en', $program->category_en) }}"
            dir="ltr"
            placeholder="Relief, Development, Protection..."
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
          >
          @error('category_en')
            <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
          @enderror
        </div>

        {{-- Badge AR --}}
        <div>
          <label for="badge_ar" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
            الشارة التمييزية (Badge AR)
          </label>
          <input
            type="text"
            id="badge_ar"
            name="badge_ar"
            value="{{ old('badge_ar', $program->badge_ar) }}"
            placeholder="استجابة طارئة، أولوية عاجلة..."
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
          >
          @error('badge_ar')
            <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
          @enderror
        </div>

        {{-- Highlight AR --}}
        <div>
          <label for="highlight_ar" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
            النص البارز (Highlight AR)
          </label>
          <input
            type="text"
            id="highlight_ar"
            name="highlight_ar"
            value="{{ old('highlight_ar', $program->highlight_ar) }}"
            placeholder="مياه نقية للأسر النازحة..."
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
          >
          @error('highlight_ar')
            <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
          @enderror
        </div>

        {{-- Key --}}
        <div>
          <label for="key" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('dashboard.field.key') }}
          </label>
          <input
            type="text"
            id="key"
            name="key"
            value="{{ old('key', $program->key) }}"
            dir="ltr"
            placeholder="{{ __('dashboard.field.key_placeholder') }}"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
          >
          @error('key')
            <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
          @enderror
        </div>

        {{-- Image Upload --}}
        <div>
          <label for="image_file" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('dashboard.field.image') }} (رفع صورة من الجهاز)
          </label>
          <div class="space-y-2">
            @if ($program->image)
              <div class="flex items-center gap-3 p-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800">
                <img src="{{ asset($program->image) }}" alt="Preview" class="h-12 w-16 object-cover rounded-md border border-gray-300 dark:border-gray-600">
                <span class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-xs">{{ $program->image }}</span>
              </div>
            @endif
            <input type="file" id="image_file" name="image_file" accept="image/*"
              class="w-full text-theme-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 dark:file:bg-brand-500/10 dark:file:text-brand-400 text-gray-500">
            @error('image_file') <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p> @enderror
            <input type="text" id="image" name="image" value="{{ old('image', $program->image) }}" dir="ltr" placeholder="أو رابط مسار الصورة..."
              class="h-9 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-xs text-gray-700 shadow-theme-xs dark:border-gray-700 dark:bg-gray-900 dark:text-white/80">
          </div>
        </div>

        {{-- Order --}}
        <div>
          <label for="order" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('dashboard.field.order') }}
          </label>
          <input
            type="number"
            id="order"
            name="order"
            value="{{ old('order', $program->order ?? 0) }}"
            min="0"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
          >
          @error('order')
            <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
          @enderror
        </div>

        {{-- Status --}}
        <div>
          <label for="status" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('dashboard.field.status') }} <span class="text-error-500">*</span>
          </label>
          <select
            id="status"
            name="status"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800"
          >
            <option value="draft" {{ old('status', $program->status) === 'draft' ? 'selected' : '' }}>{{ __('dashboard.status.draft') }}</option>
            <option value="under_review" {{ old('status', $program->status) === 'under_review' ? 'selected' : '' }}>{{ __('dashboard.status.under_review') }}</option>
            <option value="published" {{ old('status', $program->status) === 'published' ? 'selected' : '' }}>{{ __('dashboard.status.published') }}</option>
          </select>
          @error('status')
            <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
          @enderror
        </div>

      </div>
    </div>

    {{-- Descriptions --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
      <div class="mb-5 border-b border-gray-100 pb-4 dark:border-gray-800">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('dashboard.section.description') }}</h2>
      </div>
      <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

        {{-- Description AR --}}
        <div>
          <label for="description_ar" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('dashboard.field.description_ar') }}
          </label>
          <textarea
            id="description_ar"
            name="description_ar"
            rows="5"
            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
          >{{ old('description_ar', $program->description_ar) }}</textarea>
          @error('description_ar')
            <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
          @enderror
        </div>

        {{-- Description EN --}}
        <div>
          <label for="description_en" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('dashboard.field.description_en') }}
          </label>
          <textarea
            id="description_en"
            name="description_en"
            rows="5"
            dir="ltr"
            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
          >{{ old('description_en', $program->description_en) }}</textarea>
          @error('description_en')
            <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
          @enderror
        </div>

      </div>
    </div>

    {{-- Form Actions --}}
    <div class="flex items-center justify-end gap-3 pt-2">
      <a
        href="{{ route('dashboard.programs.index') }}"
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
