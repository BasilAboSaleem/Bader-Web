@php
    use App\Support\SiteSettings;
    $whatsappNumber = SiteSettings::whatsappNumber();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0A2E2F">
    <title>@yield('title', __('brand.name'))</title>
    @hasSection('meta_description')
        <meta name="description" content="@yield('meta_description')">
    @endif
    <link rel="icon" href="{{ \App\Support\SiteSettings::brandAsset('favicon') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bader-public min-h-screen bg-paper text-ink-900 antialiased selection:bg-gold-500 selection:text-teal-950">
    <div id="scroll-progress-bar" class="pointer-events-none fixed top-0 start-0 z-[70] h-[3px] w-0 bg-gradient-to-r from-forest-600 to-gold-500" aria-hidden="true"></div>

    <a href="#content" class="sr-only focus:not-sr-only focus:absolute focus:start-4 focus:top-4 focus:z-[90] focus:rounded-full focus:bg-gold-500 focus:px-4 focus:py-2 focus:font-semibold focus:text-teal-950">{{ __('nav.skip') }}</a>
    @include('partials.urgent-bar')
    @include('partials.site-header')
    <main id="content">
        @yield('content')
    </main>
    @include('partials.site-footer')

    <div class="fixed bottom-5 end-5 z-40 flex flex-col items-center gap-3 print:hidden">
        <button
            id="back-to-top-btn"
            type="button"
            aria-label="{{ __('common.back_to_top') }}"
            class="floating-action invisible pointer-events-none translate-y-4 border border-hairline bg-white text-forest-700 opacity-0 hover:border-forest-700"
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
            </svg>
        </button>

        @if ($whatsappNumber)
            <a
                href="https://wa.me/{{ $whatsappNumber }}"
                target="_blank"
                rel="noopener"
                aria-label="{{ __('common.whatsapp_contact') }}"
                class="floating-action bg-[#25d366] text-white"
            >
                <x-bader.icon name="whatsapp" class="h-7 w-7" />
            </a>
        @endif
    </div>
</body>
</html>
