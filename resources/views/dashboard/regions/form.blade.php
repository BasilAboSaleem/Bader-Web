@extends('layouts.dashboard')

@section('title', ($isEdit ? __('dashboard.edit') : __('dashboard.add_new')) . ' — ' . __('dashboard.module.regions') . ' — ' . __('brand.name'))

@php
  $inputClass = 'h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800';
  $textareaClass = 'w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800';
  $labelClass = 'mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400';
@endphp

@section('content')
<div class="space-y-6">

  <div>
    <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">
      {{ $isEdit ? __('dashboard.edit') . ': ' . $region->name_ar : __('dashboard.add_new') . ' — ' . __('dashboard.module.regions') }}
    </h1>
    <nav class="mt-1 flex items-center gap-1.5 text-theme-xs text-gray-500 dark:text-gray-400">
      <a href="{{ route('dashboard.regions.index') }}" class="hover:text-brand-500">{{ __('dashboard.module.regions') }}</a>
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

  <form action="{{ $isEdit ? route('dashboard.regions.update', $region) : route('dashboard.regions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
      <div class="mb-5 border-b border-gray-100 pb-4 dark:border-gray-800">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('dashboard.section.basic_info') }}</h2>
      </div>
      <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div>
          <label for="name_ar" class="{{ $labelClass }}">{{ __('dashboard.field.name_ar') }} <span class="text-error-500">*</span></label>
          <input type="text" id="name_ar" name="name_ar" value="{{ old('name_ar', $region->name_ar) }}" required class="{{ $inputClass }}">
        </div>
        <div>
          <label for="name_en" class="{{ $labelClass }}">{{ __('dashboard.field.name_en') }}</label>
          <input type="text" id="name_en" name="name_en" value="{{ old('name_en', $region->name_en) }}" dir="ltr" class="{{ $inputClass }}">
        </div>
        <div>
          <label for="key" class="{{ $labelClass }}">{{ __('dashboard.field.key') }}</label>
          <input type="text" id="key" name="key" value="{{ old('key', $region->key) }}" dir="ltr" placeholder="{{ __('dashboard.field.key_placeholder') }}" class="{{ $inputClass }}">
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label for="order" class="{{ $labelClass }}">{{ __('dashboard.field.order') }}</label>
            <input type="number" id="order" name="order" value="{{ old('order', $region->order ?? 0) }}" min="0" class="{{ $inputClass }}">
          </div>
          <div>
            <label for="status" class="{{ $labelClass }}">{{ __('dashboard.field.status') }} <span class="text-error-500">*</span></label>
            <select id="status" name="status" class="{{ $inputClass }}">
              @foreach (['draft', 'published'] as $status)
                <option value="{{ $status }}" @selected(old('status', $region->status) === $status)>{{ __('dashboard.status.'.$status) }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="md:col-span-2">
          <label for="image_file" class="{{ $labelClass }}">{{ __('dashboard.field.image') }}</label>
          @if ($region->image)
            <img src="{{ asset($region->image) }}" alt="" class="mb-2 h-16 w-24 rounded-md border border-gray-200 object-cover dark:border-gray-700">
          @endif
          <input type="file" id="image_file" name="image_file" accept="image/*"
            class="w-full text-theme-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-xs file:font-semibold file:text-brand-700 hover:file:bg-brand-100 dark:file:bg-brand-500/10 dark:file:text-brand-400">
          <input type="text" name="image" value="{{ old('image', $region->image) }}" dir="ltr" placeholder="images/programs/water.jpg"
            class="mt-2 h-9 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-xs text-gray-700 shadow-theme-xs dark:border-gray-700 dark:bg-gray-900 dark:text-white/80">
        </div>
      </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
      <div class="mb-5 border-b border-gray-100 pb-4 dark:border-gray-800">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('dashboard.section.map_position') }}</h2>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('dashboard.section.map_position_desc') }}</p>
      </div>
      <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div class="md:col-span-2">
          <label for="map_area" class="{{ $labelClass }}">{{ __('dashboard.field.map_area') }}</label>
          <select id="map_area" name="map_area" class="{{ $inputClass }}">
            <option value="">{{ __('dashboard.field.map_area_none') }}</option>
            @foreach (array_keys(config('bader.map_areas')) as $mapArea)
              <option value="{{ $mapArea }}" @selected(old('map_area', $region->map_area) === $mapArea)>{{ __('home.map.area.'.$mapArea) }}</option>
            @endforeach
          </select>
          <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('dashboard.field.map_area_hint') }}</p>
          @error('map_area') <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
        </div>
        <div>
          <label for="map_x" class="{{ $labelClass }}">{{ __('dashboard.field.map_x') }}</label>
          <input type="number" id="map_x" name="map_x" value="{{ old('map_x', $region->map_x ?? 50) }}" min="0" max="100" required class="{{ $inputClass }}">
        </div>
        <div>
          <label for="map_y" class="{{ $labelClass }}">{{ __('dashboard.field.map_y') }}</label>
          <input type="number" id="map_y" name="map_y" value="{{ old('map_y', $region->map_y ?? 50) }}" min="0" max="100" required class="{{ $inputClass }}">
        </div>
      </div>
    </div>

    {{-- Impact figures: existing ones plus empty rows to add more; a row with an empty Arabic label is removed --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
      <div class="mb-5 border-b border-gray-100 pb-4 dark:border-gray-800">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('dashboard.section.region_metrics') }}</h2>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('dashboard.section.region_metrics_desc', ['max' => $maxMetrics]) }}</p>
      </div>
      <div class="space-y-3">
        @for ($row = 0; $row < min(count($region->impact_metrics ?? []) + 2, $maxMetrics); $row++)
          @php
            $metric = ($region->impact_metrics ?? [])[$row] ?? [];
            $metricOld = fn (string $field): string => (string) old("impact_metrics.{$row}.{$field}", $metric[$field] ?? '');
          @endphp
          <div @class(['grid grid-cols-1 gap-3 rounded-xl border p-3 sm:grid-cols-[8rem_9rem_minmax(0,1fr)_minmax(0,1fr)]', 'border-gray-100 bg-gray-50/50 dark:border-gray-800 dark:bg-gray-900/40' => $metric !== [], 'border-dashed border-gray-300 dark:border-gray-700' => $metric === []])>
            <div>
              <label for="metric_{{ $row }}_value" class="{{ $labelClass }}">{{ __('dashboard.field.metric_value') }}</label>
              <input type="text" id="metric_{{ $row }}_value" name="impact_metrics[{{ $row }}][value]" value="{{ $metricOld('value') }}" dir="ltr" placeholder="12,500" class="{{ $inputClass }}">
              @error("impact_metrics.{$row}.value") <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
            </div>
            <div>
              <label for="metric_{{ $row }}_icon" class="{{ $labelClass }}">{{ __('dashboard.pages_card_icon') }}</label>
              <select id="metric_{{ $row }}_icon" name="impact_metrics[{{ $row }}][icon]" class="{{ $inputClass }}">
                @foreach ($metricIcons as $icon)
                  <option value="{{ $icon }}" @selected(($metricOld('icon') ?: 'users') === $icon)>{{ __('dashboard.card_icon.'.$icon) }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label for="metric_{{ $row }}_label_ar" class="{{ $labelClass }}">{{ __('dashboard.field.metric_label_ar') }}</label>
              <input type="text" id="metric_{{ $row }}_label_ar" name="impact_metrics[{{ $row }}][label_ar]" value="{{ $metricOld('label_ar') }}" placeholder="{{ __('dashboard.field.metric_label_placeholder') }}" class="{{ $inputClass }}">
              @error("impact_metrics.{$row}.label_ar") <p class="mt-1 text-xs text-error-500">{{ $message }}</p> @enderror
            </div>
            <div>
              <label for="metric_{{ $row }}_label_en" class="{{ $labelClass }}">{{ __('dashboard.field.metric_label_en') }}</label>
              <input type="text" id="metric_{{ $row }}_label_en" name="impact_metrics[{{ $row }}][label_en]" value="{{ $metricOld('label_en') }}" dir="ltr" class="{{ $inputClass }}">
            </div>
          </div>
        @endfor
      </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
      <div class="mb-5 border-b border-gray-100 pb-4 dark:border-gray-800">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('dashboard.section.description') }}</h2>
      </div>
      <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div>
          <label for="description_ar" class="{{ $labelClass }}">{{ __('dashboard.field.description_ar') }}</label>
          <textarea id="description_ar" name="description_ar" rows="5" class="{{ $textareaClass }}">{{ old('description_ar', $region->description_ar) }}</textarea>
        </div>
        <div>
          <label for="description_en" class="{{ $labelClass }}">{{ __('dashboard.field.description_en') }}</label>
          <textarea id="description_en" name="description_en" rows="5" dir="ltr" class="{{ $textareaClass }}">{{ old('description_en', $region->description_en) }}</textarea>
        </div>
      </div>
    </div>

    <div class="flex items-center justify-end gap-3 pt-2">
      <a href="{{ route('dashboard.regions.index') }}"
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
