<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0A2E2F">
    <title>@yield('title', __('brand.name'))</title>
    <link rel="icon" href="{{ asset(config('bader.assets.favicon')) }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bader-public min-h-screen bg-sand-50 text-ink-900 antialiased selection:bg-gold-500 selection:text-teal-950">
    {{-- Global Scroll Progress Bar --}}
    <div id="scroll-progress-bar" class="fixed top-0 start-0 z-[70] h-[3px] w-0 bg-gradient-to-r from-gold-500 via-amber-300 to-gold-400 pointer-events-none transition-all duration-75 ease-out shadow-[0_0_8px_rgba(234,179,8,0.6)]" aria-hidden="true"></div>

    <a href="#content" class="sr-only focus:not-sr-only focus:absolute focus:start-4 focus:top-4 focus:z-[60] focus:rounded-full focus:bg-gold-500 focus:text-teal-950 focus:px-4 focus:py-2 focus:font-semibold">{{ __('nav.skip') }}</a>
    @include('partials.urgent-bar')
    @include('partials.site-header', ['dark' => trim($__env->yieldContent('headerTheme')) !== 'light'])
    <main id="content">
        @yield('content')
    </main>
    @include('partials.site-footer')

    {{-- Floating Back to Top Button --}}
    <button
        id="back-to-top-btn"
        type="button"
        aria-label="{{ app()->isLocale('ar') ? 'العودة إلى الأعلى' : 'Back to top' }}"
        class="fixed bottom-6 end-6 z-40 flex h-12 w-12 items-center justify-center rounded-full bg-teal-950/90 text-gold-400 border border-teal-700/80 shadow-2xl backdrop-blur-md opacity-0 invisible translate-y-4 pointer-events-none transition-all duration-300 hover:bg-gold-500 hover:text-teal-950 hover:border-gold-400 hover:scale-110 hover:shadow-gold-500/25 active:scale-95 focus:outline-none focus:ring-2 focus:ring-gold-400"
    >
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </button>
</body>
</html>
