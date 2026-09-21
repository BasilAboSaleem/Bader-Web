<!DOCTYPE html>
<html
  lang="{{ str_replace('_', '-', app()->getLocale()) }}"
  dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}"
  class="light"
>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="theme-color" content="#465fff">
  <title>@yield('title', __('dashboard.title'))</title>
  <link rel="icon" href="{{ asset(config('bader.assets.favicon')) }}" type="image/svg+xml">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
{{-- body: bg-gray-50 light / bg-gray-900 dark - managed by JS class on <html> --}}
<body class="min-h-screen bg-gray-50 font-outfit text-gray-900 antialiased dark:bg-gray-900 dark:text-white">

{{-- ════════════════════════════════
     MOBILE BACKDROP
════════════════════════════════ --}}
<div
  id="sidebar-backdrop"
  class="fixed inset-0 z-40 hidden bg-gray-900/50 backdrop-blur-xs xl:hidden"
  onclick="toggleMobileSidebar()"
></div>

{{-- ════════════════════════════════
     LAYOUT WRAPPER
════════════════════════════════ --}}
<div class="min-h-screen xl:flex">

  {{-- ────────────────────────────
       SIDEBAR
  ──────────────────────────── --}}
  <aside
    id="app-sidebar"
    class="fixed inset-y-0 start-0 z-50 flex h-screen w-[290px] flex-col border-e border-gray-200 bg-white px-5 text-gray-900 transition-all duration-300 ease-in-out dark:border-gray-800 dark:bg-gray-900 dark:text-white
           -translate-x-full rtl:translate-x-full xl:translate-x-0 xl:rtl:translate-x-0"
  >
    {{-- Sidebar Logo --}}
    <div class="flex items-center py-8">
      <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
        {{-- Light logo --}}
        <img
          class="dark:hidden"
          src="{{ asset(config('bader.assets.mark_star')) }}"
          alt="{{ __('brand.name') }}"
          width="32"
          height="32"
        >
        {{-- Dark logo --}}
        <img
          class="hidden dark:block"
          src="{{ asset(config('bader.assets.mark_star')) }}"
          alt="{{ __('brand.name') }}"
          width="32"
          height="32"
        >
        <div>
          <span class="block text-base font-bold text-gray-900 dark:text-white">{{ __('brand.name') }}</span>
          <span class="block text-[11px] font-medium text-brand-500">{{ __('dashboard.team_area') }}</span>
        </div>
      </a>
    </div>

    {{-- Scrollable Nav --}}
    <div class="no-scrollbar flex flex-col overflow-y-auto duration-300 ease-linear">
      <nav class="mb-6">
        <div class="flex flex-col gap-4">

          {{-- ── Group: MENU ── --}}
          <div>
            <h2 class="mb-4 flex text-xs font-semibold uppercase leading-5 text-gray-400">
              {{ __('dashboard.overview') }}
            </h2>
            <ul class="flex flex-col gap-1">

              {{-- Dashboard --}}
              <li>
                <a
                  href="{{ route('dashboard') }}"
                  class="group menu-item {{ request()->routeIs('dashboard') ? 'menu-item-active' : 'menu-item-inactive' }}"
                >
                  <span class="menu-item-icon-size {{ request()->routeIs('dashboard') ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}">
                    <svg class="size-6" fill="none" viewBox="0 0 24 24">
                      <path fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"
                        d="M3 3h8v8H3V3zm0 10h8v8H3v-8zm10-10h8v8h-8V3zm0 10h8v8h-8v-8z"/>
                    </svg>
                  </span>
                  <span class="menu-item-text">{{ __('dashboard.overview') }}</span>
                </a>
              </li>

            </ul>
          </div>

          {{-- ── Group: OTHERS ── --}}
          <div>
            <h2 class="mb-4 flex text-xs font-semibold uppercase leading-5 text-gray-400">
              {{ __('dashboard.content_group') }}
            </h2>
            @php
              $navModules = $modules ?? [
                [
                  'key' => 'site_settings',
                  'route' => 'dashboard.settings.edit',
                  'stage' => 6,
                  'status' => 'dashboard.status_active',
                ],
                [
                  'key' => 'pages',
                  'route' => 'dashboard.pages.edit',
                  'stage' => 6,
                  'status' => 'dashboard.status_active',
                ],
                [
                  'key' => 'programs',
                  'stage' => 7,
                  'status' => 'dashboard.status_soon',
                ],
                [
                  'key' => 'campaigns',
                  'stage' => 7,
                  'status' => 'dashboard.status_soon',
                ],
                [
                  'key' => 'stories',
                  'stage' => 7,
                  'status' => 'dashboard.status_soon',
                ],
                [
                  'key' => 'inbox',
                  'stage' => 8,
                  'status' => 'dashboard.status_soon',
                ],
              ];
            @endphp

            <ul class="flex flex-col gap-1">

              @foreach ($navModules as $module)
                @php
                  $hasRoute = !empty($module['route']);
                  $isActive = $hasRoute && request()->routeIs($module['route']);
                @endphp
                <li>
                  @if ($hasRoute)
                    <a
                      href="{{ route($module['route']) }}"
                      class="group menu-item {{ $isActive ? 'menu-item-active' : 'menu-item-inactive' }} justify-between"
                    >
                      <div class="flex items-center gap-3">
                        <span class="menu-item-icon-size {{ $isActive ? 'menu-item-icon-active' : 'menu-item-icon-inactive' }}">
                          @if ($module['key'] === 'site_settings')
                            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                          @elseif ($module['key'] === 'pages')
                            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                          @else
                            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                          @endif
                        </span>
                        <span class="menu-item-text truncate">{{ __('dashboard.module.'.$module['key']) }}</span>
                      </div>
                      @if (($module['status'] ?? '') === 'dashboard.status_active')
                        <span class="ms-auto block rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-medium uppercase text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">
                          {{ __('dashboard.status_active') }}
                        </span>
                      @endif
                    </a>
                  @else
                    <div class="group menu-item menu-item-inactive cursor-default justify-between opacity-75">
                      <div class="flex items-center gap-3">
                        <span class="menu-item-icon-size menu-item-icon-inactive">
                          <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                          </svg>
                        </span>
                        <span class="menu-item-text truncate">{{ __('dashboard.module.'.$module['key']) }}</span>
                      </div>
                      <span class="ms-auto block rounded-full bg-success-50 px-2.5 py-0.5 text-xs font-medium uppercase text-success-600 dark:bg-success-500/15 dark:text-success-500">
                        {{ __('dashboard.status_soon') }}
                      </span>
                    </div>
                  @endif
                </li>
              @endforeach

            </ul>
          </div>

        </div>
      </nav>

      {{-- Sidebar Widget --}}
      <div class="pb-20">
        <div class="mx-auto w-full max-w-60 rounded-2xl bg-gray-50 px-4 py-5 text-center dark:bg-white/3">
          <h3 class="mb-2 font-semibold text-gray-900 dark:text-white">
            {{ __('brand.name') }}
          </h3>
          <p class="mb-4 text-theme-sm text-gray-500 dark:text-gray-400">
            {{ __('dashboard.private_notice') }}
          </p>
          <a
            href="{{ route('home') }}"
            target="_blank"
            class="flex items-center justify-center rounded-lg bg-brand-500 p-3 text-theme-sm font-medium text-white hover:bg-brand-600 transition"
          >
            {{ __('dashboard.view_site') }}
          </a>
        </div>
      </div>

    </div>
  </aside>

  {{-- ────────────────────────────
       MAIN CONTENT AREA
  ──────────────────────────── --}}
  <div
    id="main-content"
    class="flex-1 transition-[margin] duration-300 ease-in-out xl:ms-[290px]"
  >

    {{-- ── HEADER ── --}}
    <header class="sticky top-0 z-[99999] flex w-full border-gray-200 bg-white xl:border-b dark:border-gray-800 dark:bg-gray-900">
      <div class="flex grow flex-col items-center justify-between xl:flex-row xl:px-6">

        {{-- Top bar (mobile + desktop left) --}}
        <div class="flex w-full items-center justify-between gap-2 border-b border-gray-200 px-3 py-3 sm:gap-4 xl:justify-normal xl:border-b-0 xl:px-0 xl:py-4 dark:border-gray-800">

          {{-- Sidebar Toggle Button --}}
          <button
            id="sidebar-toggle-btn"
            type="button"
            class="z-[99999] flex h-10 w-10 items-center justify-center rounded-lg border-gray-200 text-gray-500 lg:h-11 lg:w-11 lg:bg-transparent xl:border dark:border-gray-800 dark:text-gray-400"
            onclick="handleSidebarToggle()"
            aria-label="Toggle Sidebar"
          >
            {{-- Hamburger Icon --}}
            <svg class="rtl:-scale-x-100" width="16" height="12" viewBox="0 0 16 12" fill="none">
              <path fill-rule="evenodd" clip-rule="evenodd"
                d="M0.583252 1C0.583252 0.585788 0.919038 0.25 1.33325 0.25H14.6666C15.0808 0.25 15.4166 0.585786 15.4166 1C15.4166 1.41421 15.0808 1.75 14.6666 1.75L1.33325 1.75C0.919038 1.75 0.583252 1.41422 0.583252 1ZM0.583252 11C0.583252 10.5858 0.919038 10.25 1.33325 10.25L14.6666 10.25C15.0808 10.25 15.4166 10.5858 15.4166 11C15.4166 11.4142 15.0808 11.75 14.6666 11.75L1.33325 11.75C0.919038 11.75 0.583252 11.4142 0.583252 11ZM1.33325 5.25C0.919038 5.25 0.583252 5.58579 0.583252 6C0.583252 6.41421 0.919038 6.75 1.33325 6.75L7.99992 6.75C8.41413 6.75 8.74992 6.41421 8.74992 6C8.74992 5.58579 8.41413 5.25 7.99992 5.25L1.33325 5.25Z"
                fill="currentColor"
              />
            </svg>
          </button>

          {{-- Mobile Logo --}}
          <a href="{{ route('dashboard') }}" class="xl:hidden flex items-center gap-2">
            <img class="dark:hidden h-7" src="{{ asset(config('bader.assets.mark_star')) }}" alt="{{ __('brand.name') }}">
            <img class="hidden dark:block h-7" src="{{ asset(config('bader.assets.mark_star')) }}" alt="{{ __('brand.name') }}">
          </a>

          {{-- Mobile: 3-dots / ellipsis menu toggle --}}
          <button
            id="app-menu-toggle"
            onclick="toggleAppMenu()"
            class="z-[99999] flex h-10 w-10 items-center justify-center rounded-lg text-gray-700 hover:bg-gray-100 xl:hidden dark:text-gray-400 dark:hover:bg-gray-800"
          >
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
              <path fill-rule="evenodd" clip-rule="evenodd"
                d="M5.99902 10.4951C6.82745 10.4951 7.49902 11.1667 7.49902 11.9951V12.0051C7.49902 12.8335 6.82745 13.5051 5.99902 13.5051C5.1706 13.5051 4.49902 12.8335 4.49902 12.0051V11.9951C4.49902 11.1667 5.1706 10.4951 5.99902 10.4951ZM17.999 10.4951C18.8275 10.4951 19.499 11.1667 19.499 11.9951V12.0051C19.499 12.8335 18.8275 13.5051 17.999 13.5051C17.1706 13.5051 16.499 12.8335 16.499 12.0051V11.9951C16.499 11.1667 17.1706 10.4951 17.999 10.4951ZM13.499 11.9951C13.499 11.1667 12.8275 10.4951 11.999 10.4951C11.1706 10.4951 10.499 11.1667 10.499 11.9951V12.0051C10.499 12.8335 11.1706 13.5051 11.999 13.5051C12.8275 13.5051 13.499 12.8335 13.499 12.0051V11.9951Z"
                fill="currentColor"
              />
            </svg>
          </button>

          {{-- Desktop Search --}}
          <div class="hidden xl:block">
            <form>
              <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 start-4 flex items-center">
                  <svg class="fill-gray-500 dark:fill-gray-400" width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M3.04175 9.37363C3.04175 5.87693 5.87711 3.04199 9.37508 3.04199C12.8731 3.04199 15.7084 5.87693 15.7084 9.37363C15.7084 12.8703 12.8731 15.7053 9.37508 15.7053C5.87711 15.7053 3.04175 12.8703 3.04175 9.37363ZM9.37508 1.54199C5.04902 1.54199 1.54175 5.04817 1.54175 9.37363C1.54175 13.6991 5.04902 17.2053 9.37508 17.2053C11.2674 17.2053 13.003 16.5344 14.357 15.4176L17.177 18.238C17.4699 18.5309 17.9448 18.5309 18.2377 18.238C18.5306 17.9451 18.5306 17.4703 18.2377 17.1774L15.418 14.3573C16.5365 13.0033 17.2084 11.2669 17.2084 9.37363C17.2084 5.04817 13.7011 1.54199 9.37508 1.54199Z"
                    />
                  </svg>
                </span>
                <input
                  type="text"
                  placeholder="{{ __('dashboard.search') }}"
                  class="h-11 w-full rounded-lg border border-gray-200 bg-transparent py-2.5 ps-12 pe-14 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden xl:w-[430px] dark:border-gray-800 dark:bg-white/3 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                >
                <button type="button" class="absolute inset-e-2.5 top-1/2 inline-flex -translate-y-1/2 items-center gap-0.5 rounded-lg border border-gray-200 bg-gray-50 px-1.75 py-[4.5px] text-xs tracking-[-0.2px] text-gray-500 dark:border-gray-800 dark:bg-white/3 dark:text-gray-400">
                  <span>⌘</span>
                  <span>K</span>
                </button>
              </div>
            </form>
          </div>
        </div>

        {{-- Right Actions (dark/light mode + user) --}}
        <div
          id="app-menu"
          class="hidden w-full items-center justify-between gap-4 px-5 py-4 shadow-theme-md xl:flex xl:justify-end xl:px-0 xl:shadow-none"
        >
          <div class="flex items-center gap-2 2xsm:gap-3">

            {{-- ── RTL/LTR Toggle ── --}}
            <button
              id="dir-toggle"
              onclick="toggleDir()"
              title="Toggle RTL/LTR"
              class="relative flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 shadow-theme-xs hover:bg-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 transition"
            >
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
              </svg>
            </button>

            {{-- ── Dark/Light Mode Toggle ── --}}
            <button
              id="theme-toggle"
              onclick="toggleTheme()"
              title="Toggle Dark / Light Mode"
              class="relative flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 shadow-theme-xs hover:bg-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 transition"
            >
              {{-- Sun icon (shown in dark mode) --}}
              <svg id="icon-sun" class="hidden dark:block" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="5"/>
                <line x1="12" y1="1" x2="12" y2="3"/>
                <line x1="12" y1="21" x2="12" y2="23"/>
                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                <line x1="1" y1="12" x2="3" y2="12"/>
                <line x1="21" y1="12" x2="23" y2="12"/>
                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
              </svg>
              {{-- Moon icon (shown in light mode) --}}
              <svg id="icon-moon" class="block dark:hidden" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
              </svg>
            </button>

            {{-- Notification Bell --}}
            <button
              type="button"
              class="relative flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 shadow-theme-xs hover:bg-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 transition"
            >
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/>
              </svg>
              <span class="absolute end-0 top-0 z-10 flex h-2 w-2 items-center justify-center rounded-full bg-error-500 text-[9px] font-medium text-white"></span>
            </button>

          </div>

          {{-- ── User Dropdown ── --}}
          @auth
          <div class="relative" id="user-dropdown-wrapper">
            <button
              onclick="toggleUserDropdown()"
              class="flex items-center gap-2 rounded-full outline-none"
            >
              <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-brand-50 text-sm font-bold text-brand-600 border border-brand-100 dark:bg-brand-900/30 dark:text-brand-400 dark:border-brand-800">
                {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 2)) }}
              </span>
              <div class="hidden text-start sm:block">
                <p class="text-theme-sm font-semibold text-gray-700 dark:text-gray-200">{{ auth()->user()->name }}</p>
                <p class="text-theme-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->email }}</p>
              </div>
              <svg class="hidden sm:block text-gray-400 dark:text-gray-500" width="16" height="16" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
              </svg>
            </button>

            {{-- Dropdown Menu --}}
            <div
              id="user-dropdown"
              class="hidden absolute end-0 top-full z-[99999] mt-4 w-60 rounded-2xl border border-gray-200 bg-white p-3 shadow-theme-lg dark:border-gray-800 dark:bg-gray-900"
            >
              {{-- User Info Header --}}
              <div class="border-b border-gray-200 pb-3 dark:border-gray-800">
                <p class="text-theme-sm font-semibold text-gray-800 dark:text-white">{{ auth()->user()->name }}</p>
                <p class="text-theme-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->email }}</p>
              </div>
              {{-- Menu Items --}}
              <ul class="pt-3 space-y-1">
                <li>
                  <a href="{{ route('home') }}" target="_blank"
                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-theme-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    {{ __('dashboard.view_site') }}
                  </a>
                </li>
                <li>
                  <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                      class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-theme-sm font-medium text-error-600 hover:bg-error-50 dark:text-error-400 dark:hover:bg-error-500/10">
                      <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                      </svg>
                      {{ __('dashboard.logout') }}
                    </button>
                  </form>
                </li>
              </ul>
            </div>
          </div>
          @endauth
        </div>

      </div>
    </header>

    {{-- ── MAIN CONTENT ── --}}
    <main class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">
      @yield('content')
    </main>

  </div>
</div>

{{-- ════════════════════════════════
     JAVASCRIPT (sidebar, theme, dir)
════════════════════════════════ --}}
<script>
  // ─── Persistent Theme (dark/light) ───────────────────────────────────────
  (function () {
    const saved = localStorage.getItem('tailadmin-theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    if (saved === 'dark' || (!saved && prefersDark)) {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }
  })();

  function toggleTheme() {
    const html = document.documentElement;
    const isDark = html.classList.toggle('dark');
    localStorage.setItem('tailadmin-theme', isDark ? 'dark' : 'light');
  }

  // ─── Persistent Dir (rtl/ltr) ────────────────────────────────────────────
  (function () {
    const savedDir = localStorage.getItem('tailadmin-dir');
    if (savedDir) {
      document.documentElement.setAttribute('dir', savedDir);
    }
  })();

  function toggleDir() {
    const html = document.documentElement;
    const current = html.getAttribute('dir') || 'ltr';
    const next = current === 'rtl' ? 'ltr' : 'rtl';
    html.setAttribute('dir', next);
    localStorage.setItem('tailadmin-dir', next);
  }

  // ─── Sidebar State ────────────────────────────────────────────────────────
  let isMobileOpen = false;
  let isDesktopExpanded = true;

  function handleSidebarToggle() {
    if (window.innerWidth >= 1280) {
      toggleDesktopSidebar();
    } else {
      toggleMobileSidebar();
    }
  }

  function toggleDesktopSidebar() {
    const sidebar   = document.getElementById('app-sidebar');
    const main      = document.getElementById('main-content');
    isDesktopExpanded = !isDesktopExpanded;

    if (isDesktopExpanded) {
      sidebar.style.width  = '290px';
      main.style.marginInlineStart = '';
      main.classList.add('xl:ms-[290px]');
      main.classList.remove('xl:ms-[90px]');
    } else {
      sidebar.style.width  = '90px';
      main.style.marginInlineStart = '';
      main.classList.remove('xl:ms-[290px]');
      main.classList.add('xl:ms-[90px]');
    }
  }

  function toggleMobileSidebar() {
    const sidebar  = document.getElementById('app-sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');
    isMobileOpen = !isMobileOpen;

    if (isMobileOpen) {
      // Show
      sidebar.classList.remove('-translate-x-full', 'rtl:translate-x-full');
      sidebar.classList.add('translate-x-0');
      backdrop.classList.remove('hidden');
    } else {
      // Hide
      sidebar.classList.add('-translate-x-full', 'rtl:translate-x-full');
      sidebar.classList.remove('translate-x-0');
      backdrop.classList.add('hidden');
    }
  }

  // ─── App Menu (mobile) ───────────────────────────────────────────────────
  function toggleAppMenu() {
    const menu = document.getElementById('app-menu');
    menu.classList.toggle('hidden');
    menu.classList.toggle('flex');
  }

  // ─── User Dropdown ────────────────────────────────────────────────────────
  function toggleUserDropdown() {
    document.getElementById('user-dropdown').classList.toggle('hidden');
  }

  // Close dropdown when clicking outside
  document.addEventListener('click', function (e) {
    const wrapper  = document.getElementById('user-dropdown-wrapper');
    const dropdown = document.getElementById('user-dropdown');
    if (wrapper && dropdown && !wrapper.contains(e.target)) {
      dropdown.classList.add('hidden');
    }
  });
</script>

</body>
</html>
