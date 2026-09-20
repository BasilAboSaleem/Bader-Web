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
  <link rel="icon" href="{{ asset(config('bader.assets.favicon')) }}" type="image/svg+xml">
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

        {{-- Social Buttons --}}
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-5">
          <button type="button"
            class="inline-flex items-center justify-center gap-3 rounded-lg bg-gray-100 px-7 py-3 text-sm font-normal text-gray-700 transition-colors hover:bg-gray-200 hover:text-gray-800 dark:bg-white/5 dark:text-white/90 dark:hover:bg-white/10">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
              <path d="M18.7511 10.1944C18.7511 9.47495 18.6915 8.94995 18.5626 8.40552H10.1797V11.6527H15.1003C15.0011 12.4597 14.4654 13.675 13.2749 14.4916L13.2582 14.6003L15.9087 16.6126L16.0924 16.6305C17.7788 15.1041 18.7511 12.8583 18.7511 10.1944Z" fill="#4285F4"/>
              <path d="M10.1788 18.75C12.5895 18.75 14.6133 17.9722 16.0915 16.6305L13.274 14.4916C12.5201 15.0068 11.5081 15.3666 10.1788 15.3666C7.81773 15.3666 5.81379 13.8402 5.09944 11.7305L4.99473 11.7392L2.23868 13.8295L2.20264 13.9277C3.67087 16.786 6.68674 18.75 10.1788 18.75Z" fill="#34A853"/>
              <path d="M5.10014 11.7305C4.91165 11.186 4.80257 10.6027 4.80257 9.99992C4.80257 9.3971 4.91165 8.81379 5.09022 8.26935L5.08523 8.1534L2.29464 6.02954L2.20333 6.0721C1.5982 7.25823 1.25098 8.5902 1.25098 9.99992C1.25098 11.4096 1.5982 12.7415 2.20333 13.9277L5.10014 11.7305Z" fill="#FBBC05"/>
              <path d="M10.1789 4.63331C11.8554 4.63331 12.9864 5.34303 13.6312 5.93612L16.1511 3.525C14.6035 2.11528 12.5895 1.25 10.1789 1.25C6.68676 1.25 3.67088 3.21387 2.20264 6.07218L5.08953 8.26943C5.81381 6.15972 7.81776 4.63331 10.1789 4.63331Z" fill="#EB4335"/>
            </svg>
            Sign in with Google
          </button>
          <button type="button"
            class="inline-flex items-center justify-center gap-3 rounded-lg bg-gray-100 px-7 py-3 text-sm font-normal text-gray-700 transition-colors hover:bg-gray-200 hover:text-gray-800 dark:bg-white/5 dark:text-white/90 dark:hover:bg-white/10">
            <svg width="21" class="fill-current" height="20" viewBox="0 0 21 20" fill="none">
              <path d="M15.6705 1.875H18.4272L12.4047 8.75833L19.4897 18.125H13.9422L9.59717 12.4442L4.62554 18.125H1.86721L8.30887 10.7625L1.51221 1.875H7.20054L11.128 7.0675L15.6705 1.875ZM14.703 16.475H16.2305L6.37054 3.43833H4.73137L14.703 16.475Z"/>
            </svg>
            Sign in with X
          </button>
        </div>

        {{-- OR divider --}}
        <div class="relative py-3 sm:py-5">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-200 dark:border-gray-800"></div>
          </div>
          <div class="relative flex justify-center text-sm">
            <span class="bg-white px-2 text-gray-400 sm:px-5 sm:py-2 dark:bg-gray-900">Or</span>
          </div>
        </div>

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
                  placeholder="Enter your password"
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

            {{-- Remember + Forgot --}}
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
              <a href="#" class="text-sm text-brand-500 hover:text-brand-600 dark:text-brand-400">
                {{ __('auth.forgot_password') ?? 'Forgot password?' }}
              </a>
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
          src="{{ asset(config('bader.assets.mark_star')) }}"
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
