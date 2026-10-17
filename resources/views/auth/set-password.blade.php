<!DOCTYPE html>
<html
  lang="{{ str_replace('_', '-', app()->getLocale()) }}"
  dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}"
  class="light"
>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#465fff">
  <meta name="robots" content="noindex">
  <title>{{ __('sponsors.set_password_title') }} — {{ __('brand.name') }}</title>
  <link rel="icon" href="{{ \App\Support\SiteSettings::brandAsset('favicon') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-outfit dark:bg-gray-900">
@php
  $inputClass = 'h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-800 dark:bg-white/3 dark:text-white/90';
@endphp

<main class="flex min-h-screen items-center justify-center px-4 py-12">
  <div class="w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-sm dark:border-gray-800 dark:bg-white/[0.03] sm:p-8">
    <div class="mb-6 flex items-center gap-3">
      <img src="{{ \App\Support\SiteSettings::brandAsset('mark_star') }}" alt="" width="40" height="40" class="size-10">
      <div>
        <p class="text-base font-bold text-gray-900 dark:text-white">{{ __('brand.name') }}</p>
        <p class="text-theme-xs font-medium text-brand-500">{{ __('sponsors.portal_name') }}</p>
      </div>
    </div>

    <h1 class="mb-2 text-xl font-semibold text-gray-800 dark:text-white/90">{{ __('sponsors.set_password_title') }}</h1>
    <p class="mb-6 text-sm text-gray-500 dark:text-gray-400">{{ __('sponsors.set_password_intro') }}</p>

    @if ($errors->any())
      <div class="mb-6 rounded-xl border border-error-100 bg-error-50 p-4 text-sm text-error-600 dark:border-error-800 dark:bg-error-500/10 dark:text-error-400" role="alert">
        <ul class="list-inside list-disc space-y-1 text-xs">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">

      <div>
        <label for="email" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">{{ __('auth.email') }}</label>
        <input type="email" id="email" name="email" value="{{ old('email', $email) }}" required autocomplete="email" dir="ltr" class="{{ $inputClass }}">
      </div>

      <div>
        <label for="password" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">{{ __('sponsors.new_password') }}</label>
        <input type="password" id="password" name="password" required minlength="10" autocomplete="new-password" class="{{ $inputClass }}">
        <p class="mt-1 text-theme-xs text-gray-500 dark:text-gray-400">{{ __('sponsors.password_rule') }}</p>
      </div>

      <div>
        <label for="password_confirmation" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">{{ __('sponsors.confirm_password') }}</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required minlength="10" autocomplete="new-password" class="{{ $inputClass }}">
      </div>

      <button type="submit" class="flex w-full items-center justify-center rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
        {{ __('sponsors.save_password') }}
      </button>
    </form>
  </div>
</main>
</body>
</html>
