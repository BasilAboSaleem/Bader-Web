<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1f6b38">
    <title>@yield('title', __('brand.name'))</title>
    <link rel="icon" href="{{ asset(config('bader.assets.favicon')) }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-bader-paper text-bader-ink antialiased">
    <a href="#content" class="sr-only focus:not-sr-only focus:absolute focus:start-4 focus:top-4 focus:z-[60] focus:rounded-full focus:bg-white focus:px-4 focus:py-2">{{ __('nav.skip') }}</a>
    @include('partials.urgent-bar')
    @include('partials.site-header')
    <main id="content">
        @yield('content')
    </main>
    @include('partials.site-footer')
</body>
</html>
