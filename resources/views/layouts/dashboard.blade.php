<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0f3d22">
    <title>@yield('title', __('dashboard.title'))</title>
    <link rel="icon" href="{{ asset(config('bader.assets.favicon')) }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-bader-admin-paper text-bader-ink antialiased">
    <div class="min-h-screen lg:flex" id="dashboard-root">
        <!-- Mobile Sidebar Backdrop -->
        <div id="sidebar-backdrop" class="fixed inset-0 z-40 bg-black/50 backdrop-blur-xs hidden lg:hidden" onclick="toggleSidebar()"></div>

        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 start-0 z-50 flex w-72 flex-col border-e border-bader-green/15 bg-bader-green-deep text-white transition-transform duration-300 ease-in-out -translate-x-full rtl:translate-x-full lg:static lg:translate-x-0 rtl:lg:translate-x-0">
            <div class="flex items-center justify-between border-b border-white/10 px-6 py-5">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="" width="38" height="38" class="h-9 w-9">
                    <div>
                        <p class="font-bold text-base leading-snug">{{ __('brand.name') }}</p>
                        <p class="text-xs text-white/60">{{ __('dashboard.team_area') }}</p>
                    </div>
                </a>
                <button type="button" class="lg:hidden text-white/70 hover:text-white p-1" onclick="toggleSidebar()" aria-label="Close menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-6 text-sm" aria-label="{{ __('dashboard.navigation') }}">
                <div>
                    <a href="{{ route('dashboard') }}" class="dashboard-nav-item dashboard-nav-item-active">
                        <svg class="w-5 h-5 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <span>{{ __('dashboard.overview') }}</span>
                    </a>
                </div>

                <div>
                    <span class="dashboard-nav-label">{{ __('dashboard.content_group') }}</span>
                    <div class="mt-1 space-y-1">
                        @foreach ($modules ?? [] as $module)
                            @if (($module['group'] ?? 'content') === 'content' || ($module['group'] ?? '') === 'settings')
                                <div class="dashboard-nav-item dashboard-nav-item-disabled justify-between">
                                    <span class="truncate">{{ __('dashboard.module.'.$module['key']) }}</span>
                                    <span class="inline-flex items-center rounded-full bg-white/10 px-2 py-0.5 text-[11px] font-normal text-white/75">
                                        {{ __('dashboard.status_soon') }} ({{ $module['stage'] ?? 6 }})
                                    </span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div>
                    <span class="dashboard-nav-label">{{ __('dashboard.communications_group') }}</span>
                    <div class="mt-1 space-y-1">
                        @foreach ($modules ?? [] as $module)
                            @if (($module['group'] ?? '') === 'inquiries')
                                <div class="dashboard-nav-item dashboard-nav-item-disabled justify-between">
                                    <span class="truncate">{{ __('dashboard.module.'.$module['key']) }}</span>
                                    <span class="inline-flex items-center rounded-full bg-white/10 px-2 py-0.5 text-[11px] font-normal text-white/75">
                                        {{ __('dashboard.status_soon') }} ({{ $module['stage'] ?? 8 }})
                                    </span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </nav>

            <div class="border-t border-white/10 p-4 text-xs text-white/55">
                <div class="flex items-center gap-2 mb-2 text-white/80">
                    <span class="inline-block h-2 w-2 rounded-full bg-bader-lime"></span>
                    <span class="font-medium">{{ __('brand.locations') }}</span>
                </div>
                <p>{{ __('dashboard.private_notice') }}</p>
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div class="flex min-w-0 flex-1 flex-col">
            <!-- Header -->
            <header class="sticky top-0 z-30 flex min-h-18 items-center justify-between border-b border-bader-green/10 bg-white/95 px-4 backdrop-blur-md sm:px-8">
                <div class="flex items-center gap-3">
                    <button type="button" class="lg:hidden rounded-lg p-2 text-bader-ink/70 hover:bg-bader-paper hover:text-bader-ink" onclick="toggleSidebar()" aria-label="Open menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-bader-green">{{ __('dashboard.eyebrow') }}</p>
                        <h1 class="text-lg font-bold text-bader-ink sm:text-xl">@yield('heading', __('dashboard.overview'))</h1>
                    </div>
                </div>

                <div class="flex items-center gap-3 sm:gap-4">
                    <a href="{{ route('home') }}" target="_blank" class="inline-flex items-center gap-1.5 rounded-lg border border-bader-green/20 bg-bader-paper/50 px-3 py-1.5 text-xs font-semibold text-bader-green hover:bg-bader-paper transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        <span>{{ __('dashboard.view_site') }}</span>
                    </a>

                    @auth
                        <div class="hidden items-center gap-2 border-s border-bader-green/10 ps-3 sm:flex">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-bader-green-deep text-xs font-bold text-white">
                                {{ mb_substr(auth()->user()->name, 0, 1) }}
                            </span>
                            <span class="text-sm font-medium text-bader-ink">{{ auth()->user()->name }}</span>
                        </div>

                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="rounded-lg bg-bader-paper px-3 py-1.5 text-xs font-semibold text-bader-ink/80 hover:bg-red-50 hover:text-red-700 transition">
                                {{ __('dashboard.logout') }}
                            </button>
                        </form>
                    @endauth
                </div>
            </header>

            <!-- Main Body -->
            <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-6 sm:px-8 sm:py-8">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            const isRtl = document.documentElement.dir === 'rtl';

            if (isRtl) {
                sidebar.classList.toggle('translate-x-full');
            } else {
                sidebar.classList.toggle('-translate-x-full');
            }
            backdrop.classList.toggle('hidden');
        }
    </script>
</body>
</html>
