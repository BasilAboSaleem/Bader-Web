@extends('layouts.dashboard')

@section('title', ($isEdit ? __('dashboard.edit') : __('dashboard.add_new')) . ' — ' . __('dashboard.module.sponsorship_cases') . ' — ' . __('brand.name'))

@php
  use App\Models\SponsorshipCase;

  $inputClass = 'h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800';
  $textareaClass = 'w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800';
  $labelClass = 'mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400';
@endphp

@section('content')
<div class="space-y-6">

  <div>
    <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">
      {{ $isEdit ? __('dashboard.edit') . ': ' . $case->code : __('dashboard.add_new') . ' — ' . __('dashboard.module.sponsorship_cases') }}
    </h1>
    <nav class="mt-1 flex items-center gap-1.5 text-theme-xs text-gray-500 dark:text-gray-400">
      <a href="{{ route('dashboard.sponsorship-cases.index') }}" class="hover:text-brand-500">{{ __('dashboard.module.sponsorship_cases') }}</a>
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

  <div class="rounded-xl border border-warning-500/20 bg-warning-50 p-4 text-theme-sm text-warning-700 dark:bg-warning-500/10 dark:text-warning-400">
    {{ __('dashboard.sponsorship_privacy_notice') }}
  </div>

  <form action="{{ $isEdit ? route('dashboard.sponsorship-cases.update', $case) : route('dashboard.sponsorship-cases.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
      <div class="mb-5 border-b border-gray-100 pb-4 dark:border-gray-800">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('dashboard.section.basic_info') }}</h2>
      </div>
      <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
        <div>
          <label for="code" class="{{ $labelClass }}">{{ __('dashboard.field.code') }} <span class="text-error-500">*</span></label>
          <input type="text" id="code" name="code" value="{{ old('code', $case->code) }}" dir="ltr" placeholder="GZ-101" required class="{{ $inputClass }} font-mono uppercase">
        </div>
        <div>
          <label for="type" class="{{ $labelClass }}">{{ __('dashboard.field.case_type') }} <span class="text-error-500">*</span></label>
          <select id="type" name="type" class="{{ $inputClass }}">
            @foreach (SponsorshipCase::TYPES as $type)
              <option value="{{ $type }}" @selected(old('type', $case->type) === $type)>{{ __('sponsorship.type.'.$type) }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label for="status" class="{{ $labelClass }}">{{ __('dashboard.field.status') }} <span class="text-error-500">*</span></label>
          <select id="status" name="status" class="{{ $inputClass }}">
            @foreach ([SponsorshipCase::STATUS_AVAILABLE, SponsorshipCase::STATUS_SPONSORED, SponsorshipCase::STATUS_HIDDEN] as $status)
              <option value="{{ $status }}" @selected(old('status', $case->status) === $status)>{{ __('sponsorship.status.'.$status) }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label for="name_ar" class="{{ $labelClass }}">{{ __('dashboard.field.display_name_ar') }} <span class="text-error-500">*</span></label>
          <input type="text" id="name_ar" name="name_ar" value="{{ old('name_ar', $case->name_ar) }}" required class="{{ $inputClass }}">
        </div>
        <div>
          <label for="name_en" class="{{ $labelClass }}">{{ __('dashboard.field.display_name_en') }}</label>
          <input type="text" id="name_en" name="name_en" value="{{ old('name_en', $case->name_en) }}" dir="ltr" class="{{ $inputClass }}">
        </div>
        <div>
          <label for="region_id" class="{{ $labelClass }}">{{ __('dashboard.field.region') }}</label>
          <select id="region_id" name="region_id" class="{{ $inputClass }}">
            <option value="">—</option>
            @foreach ($regions as $region)
              <option value="{{ $region->id }}" @selected((string) old('region_id', $case->region_id) === (string) $region->id)>{{ $region->name_ar }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label for="age" class="{{ $labelClass }}">{{ __('dashboard.field.age') }}</label>
          <input type="number" id="age" name="age" value="{{ old('age', $case->age) }}" min="0" max="120" class="{{ $inputClass }}">
        </div>
        <div>
          <label for="gender" class="{{ $labelClass }}">{{ __('dashboard.field.gender') }}</label>
          <select id="gender" name="gender" class="{{ $inputClass }}">
            <option value="">—</option>
            @foreach (['male', 'female'] as $gender)
              <option value="{{ $gender }}" @selected(old('gender', $case->gender) === $gender)>{{ __('sponsorship.gender.'.$gender) }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label for="waiting_since" class="{{ $labelClass }}">{{ __('dashboard.field.waiting_since') }}</label>
          <input type="date" id="waiting_since" name="waiting_since" value="{{ old('waiting_since', $case->waiting_since?->format('Y-m-d')) }}" class="{{ $inputClass }}">
        </div>
      </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
      <div class="mb-5 border-b border-gray-100 pb-4 dark:border-gray-800">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('dashboard.section.financials') }}</h2>
      </div>
      <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div>
          <label for="monthly_amount" class="{{ $labelClass }}">{{ __('dashboard.field.monthly_amount') }} (USD) <span class="text-error-500">*</span></label>
          <input type="number" id="monthly_amount" name="monthly_amount" value="{{ old('monthly_amount', $case->monthly_amount) }}" min="1" step="0.01" required class="{{ $inputClass }}">
        </div>
        <div>
          <label for="duration_months" class="{{ $labelClass }}">{{ __('dashboard.field.duration_months') }} <span class="text-error-500">*</span></label>
          <input type="number" id="duration_months" name="duration_months" value="{{ old('duration_months', $case->duration_months) }}" min="1" max="240" required class="{{ $inputClass }}">
        </div>
      </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
      <div class="mb-5 border-b border-gray-100 pb-4 dark:border-gray-800">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('dashboard.section.description') }}</h2>
      </div>
      <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div>
          <label for="bio_ar" class="{{ $labelClass }}">{{ __('dashboard.field.bio_ar') }}</label>
          <textarea id="bio_ar" name="bio_ar" rows="5" class="{{ $textareaClass }}">{{ old('bio_ar', $case->bio_ar) }}</textarea>
        </div>
        <div>
          <label for="bio_en" class="{{ $labelClass }}">{{ __('dashboard.field.bio_en') }}</label>
          <textarea id="bio_en" name="bio_en" rows="5" dir="ltr" class="{{ $textareaClass }}">{{ old('bio_en', $case->bio_en) }}</textarea>
        </div>
        <div class="md:col-span-2">
          <label for="photo_file" class="{{ $labelClass }}">{{ __('dashboard.field.photo') }}</label>
          @if ($case->photo)
            <img src="{{ asset($case->photo) }}" alt="" class="mb-2 h-16 w-16 rounded-md border border-gray-200 object-cover dark:border-gray-700">
          @endif
          <input type="file" id="photo_file" name="photo_file" accept="image/*"
            class="w-full text-theme-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-xs file:font-semibold file:text-brand-700 hover:file:bg-brand-100 dark:file:bg-brand-500/10 dark:file:text-brand-400">
          <input type="text" name="photo" value="{{ old('photo', $case->photo) }}" dir="ltr"
            class="mt-2 h-9 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-xs text-gray-700 shadow-theme-xs dark:border-gray-700 dark:bg-gray-900 dark:text-white/80">
        </div>
      </div>
    </div>

    <div class="flex items-center justify-end gap-3 pt-2">
      <a href="{{ route('dashboard.sponsorship-cases.index') }}"
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
