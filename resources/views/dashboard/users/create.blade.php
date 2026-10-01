@extends('layouts.dashboard')

@section('title', __('dashboard.users_add') . ' — ' . __('brand.name'))

@php
  $inputClass = 'h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-theme-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800';
  $labelClass = 'mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400';
@endphp

@section('content')
<div class="space-y-6">

  <div>
    <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">{{ __('dashboard.users_add') }}</h1>
    <nav class="mt-1 flex items-center gap-1.5 text-theme-xs text-gray-500 dark:text-gray-400">
      <a href="{{ route('dashboard.users.index') }}" class="hover:text-brand-500">{{ __('dashboard.module.users') }}</a>
      <svg class="size-3 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
      <span>{{ __('dashboard.users_add') }}</span>
    </nav>
  </div>

  @if ($errors->any())
    <div class="rounded-xl border border-error-500/20 bg-error-50 p-4 text-theme-sm text-error-700 dark:bg-error-500/10 dark:text-error-400">
      <p class="font-semibold">{{ __('dashboard.fix_errors') }}</p>
      <ul class="mt-1 list-disc space-y-1 ps-5">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('dashboard.users.store') }}" method="POST" class="space-y-6">
    @csrf

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
      <p class="mb-5 border-b border-gray-100 pb-4 text-theme-sm text-gray-500 dark:border-gray-800 dark:text-gray-400">{{ __('dashboard.users_create_hint') }}</p>
      <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div>
          <label for="name" class="{{ $labelClass }}">{{ __('dashboard.users_name') }} <span class="text-error-500">*</span></label>
          <input type="text" id="name" name="name" value="{{ old('name') }}" required autocomplete="off" class="{{ $inputClass }}">
        </div>
        <div>
          <label for="email" class="{{ $labelClass }}">{{ __('dashboard.users_email') }} <span class="text-error-500">*</span></label>
          <input type="email" id="email" name="email" value="{{ old('email') }}" required dir="ltr" autocomplete="off" class="{{ $inputClass }}">
        </div>
        <div>
          <label for="password" class="{{ $labelClass }}">{{ __('dashboard.users_password') }} <span class="text-error-500">*</span></label>
          <input type="password" id="password" name="password" required minlength="12" dir="ltr" autocomplete="new-password" class="{{ $inputClass }}">
          <p class="mt-1 text-theme-xs text-gray-400">{{ __('dashboard.users_password_hint') }}</p>
        </div>
        <div>
          <label for="password_confirmation" class="{{ $labelClass }}">{{ __('dashboard.users_password_confirmation') }} <span class="text-error-500">*</span></label>
          <input type="password" id="password_confirmation" name="password_confirmation" required minlength="12" dir="ltr" autocomplete="new-password" class="{{ $inputClass }}">
        </div>
      </div>
    </div>

    <div class="flex items-center justify-end gap-3 pt-2">
      <a href="{{ route('dashboard.users.index') }}"
        class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
        {{ __('dashboard.cancel') }}
      </a>
      <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-6 py-3 text-theme-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 transition">
        {{ __('dashboard.users_add') }}
      </button>
    </div>
  </form>
</div>
@endsection
