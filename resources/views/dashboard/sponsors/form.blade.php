@extends('layouts.dashboard')

@section('title', ($isEdit ? __('sponsors.edit') : __('sponsors.add')) . ' — ' . __('brand.name'))

@php
  $inputClass = 'h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs dark:border-gray-700 dark:bg-gray-900 dark:text-white/90';
  $labelClass = 'mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400';
@endphp

@section('content')
<div class="mx-auto max-w-3xl space-y-6">

  <div>
    <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">{{ $isEdit ? __('sponsors.edit') : __('sponsors.add') }}</h1>
    <nav class="mt-1 flex items-center gap-1.5 text-theme-xs text-gray-500 dark:text-gray-400">
      <a href="{{ route('dashboard.sponsors.index') }}" class="hover:text-brand-500">{{ __('dashboard.module.sponsors') }}</a>
      @if ($isEdit)
        <svg class="size-3 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span dir="ltr">{{ $sponsor->code }}</span>
      @endif
    </nav>
  </div>

  <form method="POST" action="{{ $isEdit ? route('dashboard.sponsors.update', $sponsor) : route('dashboard.sponsors.store') }}"
    class="space-y-5 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
      <div class="sm:col-span-2">
        <label for="name" class="{{ $labelClass }}">{{ __('sponsors.field.name') }} <span class="text-error-500">*</span></label>
        <input type="text" id="name" name="name" value="{{ old('name', $sponsor->name) }}" required maxlength="255" class="{{ $inputClass }}">
        @error('name') <p class="mt-1 text-theme-xs text-error-600">{{ $message }}</p> @enderror
      </div>

      <div class="sm:col-span-2">
        <label for="email" class="{{ $labelClass }}">{{ __('sponsors.field.email') }}</label>
        <input type="email" id="email" name="email" value="{{ old('email', $sponsor->email) }}" maxlength="255" dir="ltr" class="{{ $inputClass }}">
        <p class="mt-1 text-theme-xs text-gray-500 dark:text-gray-400">{{ $isEdit && $sponsor->user_id ? __('sponsors.hint.email_with_account') : __('sponsors.hint.email') }}</p>
        @error('email') <p class="mt-1 text-theme-xs text-error-600">{{ $message }}</p> @enderror
      </div>

      <div>
        <label for="phone" class="{{ $labelClass }}">{{ __('sponsors.field.phone') }}</label>
        <input type="tel" id="phone" name="phone" value="{{ old('phone', $sponsor->phone) }}" maxlength="30" dir="ltr" class="{{ $inputClass }}">
        @error('phone') <p class="mt-1 text-theme-xs text-error-600">{{ $message }}</p> @enderror
      </div>

      <div>
        <label for="country" class="{{ $labelClass }}">{{ __('sponsors.field.country') }}</label>
        <input type="text" id="country" name="country" value="{{ old('country', $sponsor->country) }}" maxlength="100" class="{{ $inputClass }}">
        @error('country') <p class="mt-1 text-theme-xs text-error-600">{{ $message }}</p> @enderror
      </div>

      <div>
        <label for="preferred_currency" class="{{ $labelClass }}">{{ __('sponsors.field.preferred_currency') }} <span class="text-error-500">*</span></label>
        <select id="preferred_currency" name="preferred_currency" class="{{ $inputClass }}">
          @foreach (config('bader.sponsorship_currencies') as $currency)
            <option value="{{ $currency }}" @selected(old('preferred_currency', $sponsor->preferred_currency) === $currency)>{{ __('sponsors.currency.'.$currency) }}</option>
          @endforeach
        </select>
        @error('preferred_currency') <p class="mt-1 text-theme-xs text-error-600">{{ $message }}</p> @enderror
      </div>

      <div class="sm:col-span-2">
        <label for="notes" class="{{ $labelClass }}">{{ __('sponsors.field.notes') }}</label>
        <textarea id="notes" name="notes" rows="3" maxlength="2000" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">{{ old('notes', $sponsor->notes) }}</textarea>
        <p class="mt-1 text-theme-xs text-gray-500 dark:text-gray-400">{{ __('sponsors.hint.notes') }}</p>
        @error('notes') <p class="mt-1 text-theme-xs text-error-600">{{ $message }}</p> @enderror
      </div>
    </div>

    <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-5 dark:border-gray-800">
      <a href="{{ $isEdit ? route('dashboard.sponsors.show', $sponsor) : route('dashboard.sponsors.index') }}"
        class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-theme-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">{{ __('dashboard.cancel') }}</a>
      <button type="submit" class="rounded-lg bg-brand-500 px-5 py-2.5 text-theme-sm font-medium text-white hover:bg-brand-600">{{ __('dashboard.save_changes') }}</button>
    </div>
  </form>
</div>
@endsection
