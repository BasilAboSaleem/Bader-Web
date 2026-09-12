<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1f6b38">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset(config('bader.assets.favicon')) }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-bader-paper text-bader-ink">
    <div class="pointer-events-none fixed inset-0 flex items-center justify-center opacity-[0.06]" aria-hidden="true">
        <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="" class="h-[min(70vh,36rem)] w-[min(70vh,36rem)]">
    </div>

    <header class="relative z-10 flex items-center justify-between px-6 py-5">
        <img
            src="{{ asset(config('bader.assets.mark_star')) }}"
            alt=""
            class="h-10 w-10"
        >
        <nav class="flex items-center gap-2 text-sm font-semibold" aria-label="Language">
            <a
                href="{{ route('locale.switch', 'ar') }}"
                class="rounded-full px-3 py-1.5 {{ app()->getLocale() === 'ar' ? 'bg-bader-green text-white' : 'text-bader-green' }}"
            >{{ __('phase0.locale_ar') }}</a>
            <a
                href="{{ route('locale.switch', 'en') }}"
                class="rounded-full px-3 py-1.5 {{ app()->getLocale() === 'en' ? 'bg-bader-green text-white' : 'text-bader-green' }}"
            >{{ __('phase0.locale_en') }}</a>
        </nav>
    </header>

    <main class="relative z-10 mx-auto flex min-h-[calc(100vh-5.5rem)] max-w-xl flex-col items-center justify-center px-6 pb-16 text-center">
        <p class="mb-6 text-xs font-semibold tracking-[0.22em] text-bader-green uppercase">
            {{ __('phase0.kicker') }}
        </p>

        <img
            src="{{ asset(config('bader.assets.logo_light')) }}"
            alt="{{ __('phase0.title') }}"
            width="420"
            height="420"
            class="w-full max-w-[22rem] bg-white"
        >

        <p class="mt-8 text-sm font-semibold text-bader-green">
            {{ __('phase0.tagline') }}
        </p>

        <ul class="mt-10 flex items-center gap-3" aria-label="{{ __('phase0.swatches') }}">
            <li class="h-8 w-8 rounded-full bg-bader-green ring-1 ring-black/10" title="Green"></li>
            <li class="h-8 w-8 rounded-full bg-bader-lime ring-1 ring-black/10" title="Lime"></li>
            <li class="h-8 w-8 rounded-full bg-bader-gold ring-1 ring-black/10" title="Gold"></li>
            <li class="h-8 w-8 rounded-full bg-bader-ink ring-1 ring-black/10" title="Ink"></li>
        </ul>
    </main>
</body>
</html>
