@extends('layouts.dashboard')

@section('title', __('dashboard.module.site_settings') . ' — ' . __('brand.name'))

@section('content')
<div class="space-y-6">

  {{-- Page Header / Breadcrumb --}}
  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">
        {{ __('dashboard.settings_title') }}
      </h1>
      <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">
        {{ __('dashboard.settings_subtitle') }}
      </p>
    </div>
    <div class="flex items-center gap-3">
      <a
        href="{{ route('home') }}"
        target="_blank"
        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
      >
        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
        </svg>
        <span>{{ __('dashboard.view_live_site') }}</span>
      </a>
    </div>
  </div>

  {{-- Flash message --}}
  @if (session('status'))
    <div class="flex items-center gap-3 rounded-xl border border-success-500/20 bg-success-50 p-4 text-theme-sm font-medium text-success-700 dark:bg-success-500/10 dark:text-success-400">
      <svg class="size-5 shrink-0 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
      </svg>
      <span>{{ session('status') }}</span>
    </div>
  @endif

  {{-- Validation errors summary if any --}}
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

  <form action="{{ route('dashboard.settings.update') }}" method="POST" class="space-y-6">
    @csrf
    @method('PUT')

    {{-- 1. General & Identity --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
      <div class="mb-5 flex items-center justify-between border-b border-gray-100 pb-4 dark:border-gray-800">
        <div>
          <h2 class="text-base font-semibold text-gray-900 dark:text-white">
            {{ __('dashboard.section_identity_title') }}
          </h2>
          <p class="mt-0.5 text-theme-xs text-gray-500 dark:text-gray-400">
            {{ __('dashboard.section_identity_desc') }}
          </p>
        </div>
        <span class="rounded-full bg-brand-50 px-3 py-1 text-theme-xs font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">
          {{ __('dashboard.badge_live_sync') }}
        </span>
      </div>

      <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        {{-- Founded Year --}}
        <div>
          <label for="founded_year" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('dashboard.field_founded_year') }}
          </label>
          <input
            type="text"
            id="founded_year"
            name="founded_year"
            value="{{ old('founded_year', $foundedYear) }}"
            placeholder="2024"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
          >
          @error('founded_year')
            <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
          @enderror
        </div>

        <div></div>

        {{-- HQ Location AR --}}
        <div>
          <label for="hq_location_ar" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('dashboard.field_hq_ar') }}
          </label>
          <input
            type="text"
            id="hq_location_ar"
            name="hq_location_ar"
            value="{{ old('hq_location_ar', $hqLocationAr) }}"
            placeholder="سلطنة عُمان، مسقط"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
          >
          @error('hq_location_ar')
            <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
          @enderror
        </div>

        {{-- HQ Location EN --}}
        <div>
          <label for="hq_location_en" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('dashboard.field_hq_en') }}
          </label>
          <input
            type="text"
            id="hq_location_en"
            name="hq_location_en"
            value="{{ old('hq_location_en', $hqLocationEn) }}"
            placeholder="Sultanate of Oman, Muscat"
            dir="ltr"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
          >
          @error('hq_location_en')
            <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
          @enderror
        </div>

        {{-- Field Location AR --}}
        <div>
          <label for="field_location_ar" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('dashboard.field_field_ar') }}
          </label>
          <input
            type="text"
            id="field_location_ar"
            name="field_location_ar"
            value="{{ old('field_location_ar', $fieldLocationAr) }}"
            placeholder="فلسطين، قطاع غزة"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
          >
          @error('field_location_ar')
            <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
          @enderror
        </div>

        {{-- Field Location EN --}}
        <div>
          <label for="field_location_en" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('dashboard.field_field_en') }}
          </label>
          <input
            type="text"
            id="field_location_en"
            name="field_location_en"
            value="{{ old('field_location_en', $fieldLocationEn) }}"
            placeholder="Palestine, Gaza Strip"
            dir="ltr"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
          >
          @error('field_location_en')
            <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
          @enderror
        </div>
      </div>
    </div>

    {{-- 2. Contact Channels --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
      <div class="mb-5 flex items-center justify-between border-b border-gray-100 pb-4 dark:border-gray-800">
        <div>
          <h2 class="text-base font-semibold text-gray-900 dark:text-white">
            {{ __('dashboard.section_contact_title') }}
          </h2>
          <p class="mt-0.5 text-theme-xs text-gray-500 dark:text-gray-400">
            {{ __('dashboard.section_contact_desc') }}
          </p>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        {{-- Contact Email --}}
        <div>
          <label for="contact_email" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('dashboard.field_email') }}
          </label>
          <input
            type="email"
            id="contact_email"
            name="contact_email"
            value="{{ old('contact_email', $contactEmail) }}"
            placeholder="info@baderhumanitarian.com"
            dir="ltr"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
          >
          @error('contact_email')
            <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
          @enderror
        </div>

        {{-- Contact Phone / WhatsApp --}}
        <div>
          <label for="contact_phone" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('dashboard.field_phone') }}
          </label>
          <input
            type="text"
            id="contact_phone"
            name="contact_phone"
            value="{{ old('contact_phone', $contactPhone) }}"
            placeholder="+968 0000 0000"
            dir="ltr"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
          >
          @error('contact_phone')
            <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
          @enderror
        </div>

        {{-- Contact Address AR --}}
        <div>
          <label for="contact_address_ar" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('dashboard.field_address_ar') }}
          </label>
          <input
            type="text"
            id="contact_address_ar"
            name="contact_address_ar"
            value="{{ old('contact_address_ar', $contactAddressAr) }}"
            placeholder="مسقط، سلطنة عُمان / غزة، فلسطين"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
          >
          @error('contact_address_ar')
            <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
          @enderror
        </div>

        {{-- Contact Address EN --}}
        <div>
          <label for="contact_address_en" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('dashboard.field_address_en') }}
          </label>
          <input
            type="text"
            id="contact_address_en"
            name="contact_address_en"
            value="{{ old('contact_address_en', $contactAddressEn) }}"
            placeholder="Muscat, Oman / Gaza, Palestine"
            dir="ltr"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
          >
          @error('contact_address_en')
            <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
          @enderror
        </div>
      </div>
    </div>

    {{-- 3. Urgent Announcement Bar --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
      <div class="mb-5 flex items-center justify-between border-b border-gray-100 pb-4 dark:border-gray-800">
        <div>
          <h2 class="text-base font-semibold text-gray-900 dark:text-white">
            {{ __('dashboard.section_urgent_title') }}
          </h2>
          <p class="mt-0.5 text-theme-xs text-gray-500 dark:text-gray-400">
            {{ __('dashboard.section_urgent_desc') }}
          </p>
        </div>
      </div>

      <div class="space-y-5">
        {{-- Enable Switch --}}
        <div class="flex items-center gap-3">
          <input
            type="hidden"
            name="urgent_enabled"
            value="0"
          >
          <label class="relative inline-flex cursor-pointer items-center">
            <input
              type="checkbox"
              id="urgent_enabled"
              name="urgent_enabled"
              value="1"
              {{ old('urgent_enabled', $urgentEnabled) ? 'checked' : '' }}
              class="peer sr-only"
            >
            <div class="h-6 w-11 rounded-full bg-gray-200 after:absolute after:start-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-brand-500 peer-checked:after:translate-x-full peer-checked:after:border-white rtl:peer-checked:after:-translate-x-full dark:border-gray-600 dark:bg-gray-700"></div>
          </label>
          <label for="urgent_enabled" class="cursor-pointer text-theme-sm font-medium text-gray-800 dark:text-white/90">
            {{ __('dashboard.field_urgent_enable') }}
          </label>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
          {{-- Urgent Text AR --}}
          <div>
            <label for="urgent_text_ar" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
              {{ __('dashboard.field_urgent_text_ar') }}
            </label>
            <textarea
              id="urgent_text_ar"
              name="urgent_text_ar"
              rows="3"
              placeholder="نداء طارئ لمساندة الأسر المتضررة..."
              class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            >{{ old('urgent_text_ar', $urgentTextAr) }}</textarea>
            @error('urgent_text_ar')
              <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
            @enderror
          </div>

          {{-- Urgent Text EN --}}
          <div>
            <label for="urgent_text_en" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
              {{ __('dashboard.field_urgent_text_en') }}
            </label>
            <textarea
              id="urgent_text_en"
              name="urgent_text_en"
              rows="3"
              placeholder="Emergency appeal to support affected families..."
              dir="ltr"
              class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            >{{ old('urgent_text_en', $urgentTextEn) }}</textarea>
            @error('urgent_text_en')
              <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
            @enderror
          </div>
        </div>

        {{-- Urgent URL --}}
        <div>
          <label for="urgent_url" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
            {{ __('dashboard.field_urgent_url') }}
          </label>
          <input
            type="text"
            id="urgent_url"
            name="urgent_url"
            value="{{ old('urgent_url', $urgentUrl) }}"
            placeholder="https://baderhumanitarian.com/donate"
            dir="ltr"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
          >
          @error('urgent_url')
            <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
          @enderror
        </div>
      </div>
    </div>

    {{-- Form Submit Actions --}}
    <div class="flex items-center justify-end gap-3 pt-2">
      <a
        href="{{ route('dashboard') }}"
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
