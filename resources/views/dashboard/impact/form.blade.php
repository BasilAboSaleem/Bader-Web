@extends('layouts.dashboard')

@section('title', ($isEdit ? __('dashboard.edit_metric') : __('dashboard.add_metric')) . ' — ' . __('brand.name'))

@section('content')
<div class="space-y-6">
  {{-- Header --}}
  <div class="flex items-center gap-3">
    <a href="{{ route('dashboard.impact.index') }}" class="rounded-lg border border-gray-200 bg-white p-2 text-gray-500 hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-800 dark:text-gray-400">
      <svg class="size-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
      </svg>
    </a>
    <div>
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $isEdit ? __('dashboard.edit_metric') : __('dashboard.add_metric') }}</h1>
      <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('dashboard.impact_form_subtitle') }}</p>
    </div>
  </div>

  {{-- Validation Errors --}}
  @if ($errors->any())
    <div class="rounded-xl border border-error-200 bg-error-50 p-4 text-sm text-error-800 dark:border-error-800/40 dark:bg-error-950/20 dark:text-error-300">
      <ul class="list-inside list-disc space-y-1">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Form Card --}}
  <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900">
    <form method="POST" action="{{ $isEdit ? route('dashboard.impact.update', $metric) : route('dashboard.impact.store') }}" class="space-y-6">
      @csrf
      @if ($isEdit)
        @method('PUT')
      @endif

      <div class="grid gap-6 sm:grid-cols-2">
        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.title_ar') }} *</label>
          <input type="text" name="title_ar" value="{{ old('title_ar', $metric->title_ar) }}" required
                 class="mt-1.5 block w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-gray-200">
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.title_en') }}</label>
          <input type="text" name="title_en" value="{{ old('title_en', $metric->title_en) }}" dir="ltr"
                 class="mt-1.5 block w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-gray-200">
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.metric_value') }} *</label>
          <input type="text" name="value" value="{{ old('value', $metric->value) }}" required placeholder="e.g. 150,000+"
                 class="mt-1.5 block w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-gray-200">
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.slug_key') }}</label>
          <input type="text" name="key" value="{{ old('key', $metric->key) }}" placeholder="auto-generated if empty" dir="ltr"
                 class="mt-1.5 block w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-gray-200">
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.unit_ar') }}</label>
          <input type="text" name="unit_ar" value="{{ old('unit_ar', $metric->unit_ar) }}" placeholder="e.g. وجبة ساخنة / لتر ماء"
                 class="mt-1.5 block w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-gray-200">
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.unit_en') }}</label>
          <input type="text" name="unit_en" value="{{ old('unit_en', $metric->unit_en) }}" dir="ltr" placeholder="e.g. Hot Meals / Liters"
                 class="mt-1.5 block w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-gray-200">
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.category') }}</label>
          <select name="category" class="mt-1.5 block w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-gray-200">
            <option value="water" {{ old('category', $metric->category) === 'water' ? 'selected' : '' }}>{{ __('dashboard.category_water') }}</option>
            <option value="food" {{ old('category', $metric->category) === 'food' ? 'selected' : '' }}>{{ __('dashboard.category_food') }}</option>
            <option value="shelter" {{ old('category', $metric->category) === 'shelter' ? 'selected' : '' }}>{{ __('dashboard.category_shelter') }}</option>
            <option value="health" {{ old('category', $metric->category) === 'health' ? 'selected' : '' }}>{{ __('dashboard.category_health') }}</option>
            <option value="education" {{ old('category', $metric->category) === 'education' ? 'selected' : '' }}>{{ __('dashboard.category_education') }}</option>
            <option value="general" {{ old('category', $metric->category) === 'general' ? 'selected' : '' }}>{{ __('dashboard.category_general') }}</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.order') }}</label>
          <input type="number" name="order" value="{{ old('order', $metric->order ?? 0) }}"
                 class="mt-1.5 block w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-gray-200">
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('dashboard.field.status') }}</label>
          <select name="status" class="mt-1.5 block w-full rounded-lg border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-800 dark:bg-gray-800 dark:text-gray-200">
            <option value="draft" {{ old('status', $metric->status) === 'draft' ? 'selected' : '' }}>{{ __('dashboard.status_draft') }}</option>
            <option value="approved" {{ old('status', $metric->status) === 'approved' ? 'selected' : '' }}>{{ __('dashboard.status_approved') }}</option>
            <option value="archived" {{ old('status', $metric->status) === 'archived' ? 'selected' : '' }}>{{ __('dashboard.status_archived') }}</option>
          </select>
        </div>

        <div class="flex items-center gap-3 pt-6">
          <input type="checkbox" id="is_approved" name="is_approved" value="1" {{ old('is_approved', $metric->is_approved) ? 'checked' : '' }}
                 class="size-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800">
          <label for="is_approved" class="text-sm font-semibold text-gray-800 dark:text-gray-200">
            {{ __('dashboard.field.is_approved_label') }}
          </label>
        </div>
      </div>

      <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-6 dark:border-gray-800">
        <a href="{{ route('dashboard.impact.index') }}" class="rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
          {{ __('dashboard.cancel') }}
        </a>
        <button type="submit" class="rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-semibold text-white shadow-theme-xs hover:bg-brand-600 transition">
          {{ __('dashboard.save') }}
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
