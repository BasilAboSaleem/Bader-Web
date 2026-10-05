@extends('layouts.dashboard')

@section('title', ($isEdit ? __('dashboard.edit') : __('dashboard.add_new')) . ' — ' . __('dashboard.module.campaigns') . ' — ' . __('brand.name'))

@php
  $inputClass = 'h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800';
  $textareaClass = 'w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800';
  $labelClass = 'mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400';
  $toggleClass = "h-6 w-11 rounded-full bg-gray-200 after:absolute after:start-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-brand-500 peer-checked:after:translate-x-full peer-checked:after:border-white rtl:peer-checked:after:-translate-x-full dark:border-gray-600 dark:bg-gray-700";
  $presetAmounts = old('preset_amounts', implode(', ', $campaign->preset_amounts ?? []));
@endphp

@section('content')
<div class="space-y-6">

  <div>
    <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">
      {{ $isEdit ? __('dashboard.edit') . ': ' . $campaign->title_ar : __('dashboard.add_new') . ' — ' . __('dashboard.module.campaigns') }}
    </h1>
    <nav class="mt-1 flex items-center gap-1.5 text-theme-xs text-gray-500 dark:text-gray-400">
      <a href="{{ route('dashboard.campaigns.index') }}" class="hover:text-brand-500">{{ __('dashboard.module.campaigns') }}</a>
      <svg class="size-3 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
      <span>{{ $isEdit ? __('dashboard.edit') : __('dashboard.add_new') }}</span>
    </nav>
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

  <form action="{{ $isEdit ? route('dashboard.campaigns.update', $campaign) : route('dashboard.campaigns.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    {{-- Basic info --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
      <div class="mb-5 border-b border-gray-100 pb-4 dark:border-gray-800">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('dashboard.section.basic_info') }}</h2>
      </div>
      <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div>
          <label for="title_ar" class="{{ $labelClass }}">{{ __('dashboard.field.title_ar') }} <span class="text-error-500">*</span></label>
          <input type="text" id="title_ar" name="title_ar" value="{{ old('title_ar', $campaign->title_ar) }}" required class="{{ $inputClass }}">
        </div>
        <div>
          <label for="title_en" class="{{ $labelClass }}">{{ __('dashboard.field.title_en') }}</label>
          <input type="text" id="title_en" name="title_en" value="{{ old('title_en', $campaign->title_en) }}" dir="ltr" class="{{ $inputClass }}">
        </div>
        <div>
          <label for="program_id" class="{{ $labelClass }}">{{ __('dashboard.field.program') }}</label>
          <select id="program_id" name="program_id" class="{{ $inputClass }}">
            <option value="">—</option>
            @foreach ($programs as $program)
              <option value="{{ $program->id }}" @selected((string) old('program_id', $campaign->program_id) === (string) $program->id)>{{ $program->title_ar }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label for="region_id" class="{{ $labelClass }}">{{ __('dashboard.field.region') }}</label>
          <select id="region_id" name="region_id" class="{{ $inputClass }}">
            <option value="">—</option>
            @foreach ($regions as $region)
              <option value="{{ $region->id }}" @selected((string) old('region_id', $campaign->region_id) === (string) $region->id)>{{ $region->name_ar }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label for="key" class="{{ $labelClass }}">{{ __('dashboard.field.key') }}</label>
          <input type="text" id="key" name="key" value="{{ old('key', $campaign->key) }}" dir="ltr" placeholder="{{ __('dashboard.field.key_placeholder') }}" class="{{ $inputClass }}">
        </div>
        <div>
          <label for="image_file" class="{{ $labelClass }}">{{ __('dashboard.field.image') }}</label>
          <div class="space-y-2">
            @if ($campaign->image)
              <div class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 p-2 dark:border-gray-700 dark:bg-gray-800">
                <img src="{{ asset($campaign->image) }}" alt="" class="h-12 w-16 rounded-md border border-gray-300 object-cover dark:border-gray-600">
                <span class="max-w-xs truncate text-xs text-gray-500 dark:text-gray-400">{{ $campaign->image }}</span>
              </div>
            @endif
            <input type="file" id="image_file" name="image_file" accept="image/*"
              class="w-full text-theme-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-xs file:font-semibold file:text-brand-700 hover:file:bg-brand-100 dark:file:bg-brand-500/10 dark:file:text-brand-400">
            <input type="text" id="image" name="image" value="{{ old('image', $campaign->image) }}" dir="ltr" placeholder="images/programs/water.jpg"
              class="h-9 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-xs text-gray-700 shadow-theme-xs dark:border-gray-700 dark:bg-gray-900 dark:text-white/80">
          </div>
        </div>
      </div>
    </div>

    {{-- Financials --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
      <div class="mb-5 border-b border-gray-100 pb-4 dark:border-gray-800">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('dashboard.section.financials') }}</h2>
      </div>
      <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div>
          <label for="goal_amount" class="{{ $labelClass }}">{{ __('dashboard.field.goal_amount') }}</label>
          <input type="number" id="goal_amount" name="goal_amount" value="{{ old('goal_amount', $campaign->goal_amount) }}" min="0" step="0.01" class="{{ $inputClass }}">
          <p class="mt-1 text-theme-xs text-gray-400">{{ __('dashboard.hint.ongoing_campaign') }}</p>
        </div>
        <div>
          <label for="raised_amount" class="{{ $labelClass }}">{{ __('dashboard.field.raised_amount') }}</label>
          <input type="number" id="raised_amount" name="raised_amount" value="{{ old('raised_amount', $campaign->raised_amount) }}" min="0" step="0.01" class="{{ $inputClass }}">
        </div>
        <div>
          <label for="currency_ar" class="{{ $labelClass }}">{{ __('dashboard.field.currency_ar') }}</label>
          <input type="text" id="currency_ar" name="currency_ar" value="{{ old('currency_ar', $campaign->currency_ar) }}" class="{{ $inputClass }}">
        </div>
        <div>
          <label for="currency_en" class="{{ $labelClass }}">{{ __('dashboard.field.currency_en') }}</label>
          <input type="text" id="currency_en" name="currency_en" value="{{ old('currency_en', $campaign->currency_en) }}" dir="ltr" class="{{ $inputClass }}">
        </div>
        <div>
          <label for="preset_amounts" class="{{ $labelClass }}">{{ __('dashboard.field.preset_amounts') }}</label>
          <input type="text" id="preset_amounts" name="preset_amounts" value="{{ $presetAmounts }}" dir="ltr" placeholder="10, 25, 50" class="{{ $inputClass }}">
          <p class="mt-1 text-theme-xs text-gray-400">{{ __('dashboard.hint.preset_amounts') }}</p>
        </div>
        <div class="flex items-center gap-3 pt-6">
          <input type="hidden" name="allows_monthly" value="0">
          <label class="relative inline-flex cursor-pointer items-center">
            <input type="checkbox" id="allows_monthly" name="allows_monthly" value="1" @checked(old('allows_monthly', $campaign->allows_monthly)) class="peer sr-only">
            <div class="{{ $toggleClass }}"></div>
          </label>
          <label for="allows_monthly" class="cursor-pointer text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ __('dashboard.field.allows_monthly') }}</label>
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
          <label for="description_ar" class="{{ $labelClass }}">{{ __('dashboard.field.description_ar') }}</label>
          <textarea id="description_ar" name="description_ar" rows="4" class="{{ $textareaClass }}">{{ old('description_ar', $campaign->description_ar) }}</textarea>
        </div>
        <div>
          <label for="description_en" class="{{ $labelClass }}">{{ __('dashboard.field.description_en') }}</label>
          <textarea id="description_en" name="description_en" rows="4" dir="ltr" class="{{ $textareaClass }}">{{ old('description_en', $campaign->description_en) }}</textarea>
        </div>
        <div>
          <label for="content_ar" class="{{ $labelClass }}">{{ __('dashboard.field.content_ar') }}</label>
          <textarea id="content_ar" name="content_ar" rows="8" class="{{ $textareaClass }}">{{ old('content_ar', $campaign->content_ar) }}</textarea>
        </div>
        <div>
          <label for="content_en" class="{{ $labelClass }}">{{ __('dashboard.field.content_en') }}</label>
          <textarea id="content_en" name="content_en" rows="8" dir="ltr" class="{{ $textareaClass }}">{{ old('content_en', $campaign->content_en) }}</textarea>
        </div>
      </div>
    </div>

    <x-dashboard.media-fields :model="$campaign" />

    {{-- Publication --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
      <div class="mb-5 border-b border-gray-100 pb-4 dark:border-gray-800">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('dashboard.section.publication') }}</h2>
      </div>
      <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
        <div>
          <label for="status" class="{{ $labelClass }}">{{ __('dashboard.field.status') }} <span class="text-error-500">*</span></label>
          <select id="status" name="status" class="{{ $inputClass }}">
            @foreach (['draft', 'under_review', 'published'] as $status)
              <option value="{{ $status }}" @selected(old('status', $campaign->status) === $status)>{{ __('dashboard.status.'.$status) }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label for="order" class="{{ $labelClass }}">{{ __('dashboard.field.order') }}</label>
          <input type="number" id="order" name="order" value="{{ old('order', $campaign->order ?? 0) }}" min="0" class="{{ $inputClass }}">
        </div>
        <div class="flex items-center gap-3 pt-6">
          <input type="hidden" name="is_featured" value="0">
          <label class="relative inline-flex cursor-pointer items-center">
            <input type="checkbox" id="is_featured" name="is_featured" value="1" @checked(old('is_featured', $campaign->is_featured)) class="peer sr-only">
            <div class="{{ $toggleClass }}"></div>
          </label>
          <label for="is_featured" class="cursor-pointer text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ __('dashboard.field.featured_hero') }}</label>
        </div>
      </div>
    </div>

    <div class="flex items-center justify-end gap-3 pt-2">
      <a href="{{ route('dashboard.campaigns.index') }}"
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
