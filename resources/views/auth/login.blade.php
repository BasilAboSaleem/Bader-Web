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
  <title>{{ __('auth.login_title') }}</title>
  <link rel="icon" href="{{ \App\Support\SiteSettings::brandAsset('favicon') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white font-outfit dark:bg-gray-900">

{{-- Apply saved theme immediately --}}
<script>
  (function () {
    const saved = localStorage.getItem('tailadmin-theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    if (saved === 'dark' || (!saved && prefersDark)) {
      document.documentElement.classList.add('dark');
    }
    const savedDir = localStorage.getItem('tailadmin-dir');
    if (savedDir) document.documentElement.setAttribute('dir', savedDir);
  })();
</script>

<div class="relative flex min-h-screen w-full flex-col lg:flex-row">

  {{-- ── Left: Form Section ── --}}
  <div class="flex flex-1 flex-col">

    {{-- Back link + mobile logo bar --}}
    <div class="mx-auto w-full max-w-md pt-10 px-4">
      <a
        href="{{ route('home') }}"
        class="inline-flex items-center text-sm text-gray-500 transition-colors hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
      >
        <svg class="me-2 size-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        {{ __('auth.back_to_site') }}
      </a>
    </div>

    {{-- Form Centred --}}
    <div class="mx-auto flex w-full max-w-md flex-1 flex-col justify-center px-4 py-12">
      <div>

        {{-- Heading --}}
        <div class="mb-5 sm:mb-8">
          <h1 class="mb-2 text-title-sm font-semibold text-gray-800 sm:text-title-md dark:text-white/90">
            {{ __('auth.login_heading') }}
          </h1>
          <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ __('auth.login_description') }}
          </p>
        </div>

        @if (session('status'))
        <div class="mb-6 rounded-xl border border-success-500/20 bg-success-50 p-4 text-sm text-success-700 dark:bg-success-500/10 dark:text-success-400" role="status">
          {{ session('status') }}
        </div>
        @endif

        {{-- Errors --}}
        @if ($errors->any())
        <div class="mb-6 rounded-xl border border-error-100 bg-error-50 p-4 text-sm text-error-600 dark:border-error-800 dark:bg-error-500/10 dark:text-error-400" role="alert">
          <ul class="list-disc list-inside space-y-1 text-xs">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif

        {{-- Login Form --}}
        <form method="POST" action="{{ route('login.store') }}">
          @csrf
          <div class="space-y-6">

            {{-- Email --}}
            <div>
              <label for="email" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
                {{ __('auth.email') }} <span class="text-error-500">*</span>
              </label>
              <input
                type="email"
                name="email"
                id="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="email"
                placeholder="info@baderhumanitarian.com"
                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-200 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 @error('email') border-error-300 focus:border-error-300 focus:ring-error-500/10 @enderror"
              >
            </div>

            {{-- Password --}}
            <div>
              <label for="password" class="mb-1.5 block text-theme-sm font-medium text-gray-700 dark:text-gray-400">
                {{ __('auth.password') }} <span class="text-error-500">*</span>
              </label>
              <div class="relative">
                <input
                  type="password"
                  name="password"
                  id="password"
                  required
                  autocomplete="current-password"
                  class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-200 bg-transparent py-2.5 ps-4 pe-12 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 @error('password') border-error-300 @enderror"
                >
                <button
                  type="button"
                  onclick="togglePasswordVisibility()"
                  class="absolute inset-e-4 top-1/2 z-30 -translate-y-1/2 cursor-pointer text-gray-500 dark:text-gray-400"
                >
                  <svg id="eye-close" class="size-5 fill-current" viewBox="0 0 24 24">
                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/>
                    <circle cx="12" cy="12" r="3"/>
                    <line x1="2" y1="2" x2="22" y2="22" stroke="currentColor" stroke-width="2" fill="none"/>
                  </svg>
                  <svg id="eye-open" class="hidden size-5 fill-current" viewBox="0 0 24 24">
                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/>
                    <circle cx="12" cy="12" r="3"/>
                  </svg>
                </button>
              </div>
            </div>

            {{-- Remember --}}
            <div class="flex items-center justify-between">
              <label class="flex cursor-pointer items-center gap-3">
                <input
                  type="checkbox"
                  name="remember"
                  id="remember"
                  value="1"
                  {{ old('remember') ? 'checked' : '' }}
                  class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900"
                >
                <span class="block text-theme-sm font-normal text-gray-700 dark:text-gray-400">
                  {{ __('auth.remember_me') }}
                </span>
              </label>
            </div>

            {{-- Submit --}}
            <div>
              <button
                type="submit"
                class="flex w-full items-center justify-center rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 focus:ring-4 focus:ring-brand-500/20 focus:outline-none active:translate-y-0.5 transition"
              >
                {{ __('auth.login_button') }}
              </button>
            </div>
          </div>
        </form>

        {{-- Footer note --}}
        <div class="mt-5 text-center text-xs text-gray-400 dark:text-gray-600">
          {{ __('brand.name') }} · {{ __('brand.locations') }}
        </div>

      </div>
    </div>
  </div>

  {{-- ── Right: Brand Panel ── --}}
  <div class="relative hidden lg:flex lg:w-1/2 items-center justify-center overflow-hidden bg-brand-950 p-12">
    {{-- Dot grid --}}
    <div class="absolute inset-0 pointer-events-none opacity-10"
      style="background-image: radial-gradient(rgba(255,255,255,0.3) 1px, transparent 1px); background-size: 24px 24px;"></div>

    <div class="relative z-10 flex max-w-sm flex-col items-center text-center">
      <div class="mb-8 rounded-2xl bg-white/10 p-5 backdrop-blur-xs border border-white/10 shadow-theme-lg">
        <img
          src="{{ \App\Support\SiteSettings::brandAsset('mark_star') }}"
          alt="{{ __('brand.name') }}"
          width="72"
          height="72"
          class="h-18 w-18"
        >
      </div>

      <h2 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">
        {{ __('brand.name') }}
      </h2>
      <p class="mt-4 text-sm leading-relaxed text-brand-200">
        {{ __('auth.login_description') }}
      </p>

      <div class="mt-8 inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1.5 border border-white/10">
        <span class="h-2 w-2 rounded-full bg-brand-400"></span>
        <span class="text-xs font-semibold text-brand-200">{{ __('brand.name') }}</span>
      </div>
    </div>
  </div>

</div>

<script>
  function togglePasswordVisibility() {
    const input    = document.getElementById('password');
    const eyeClose = document.getElementById('eye-close');
    const eyeOpen  = document.getElementById('eye-open');
    const isText   = input.type === 'text';
    input.type = isText ? 'password' : 'text';
    eyeClose.classList.toggle('hidden', !isText);
    eyeOpen.classList.toggle('hidden', isText);
  }
</script>

</body>
</html>
