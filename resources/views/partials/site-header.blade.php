@php
    use App\Support\PublicNavigation;
    $primary = PublicNavigation::primary();
    $secondary = PublicNavigation::secondary();
    $dark = $dark ?? true;
@endphp

<header
    data-site-header
    class="sticky top-0 z-50 transition-all duration-300 bg-teal-950/90 backdrop-blur-md shadow-lg border-b border-teal-800 text-sand-50"
>
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
        {{-- Brand Logo & Names --}}
        <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3 group" aria-label="{{ __('brand.name') }}">
            <span class="relative flex h-10 w-10 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-gold-500/20 via-teal-900 to-teal-950 border border-gold-500/30 p-2 shadow-inner group-hover:border-gold-400 group-hover:scale-105 transition-all">
                <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="" class="h-full w-full object-contain">
            </span>
            <span class="leading-tight">
                <span class="block font-display text-base sm:text-lg font-bold tracking-tight text-white group-hover:text-gold-400 transition-colors">{{ __('brand.name') }}</span>
                <span class="block text-[10px] sm:text-[11px] font-sans text-gold-400/90 tracking-wider">{{ __('brand.tagline') }}</span>
            </span>
        </a>

        {{-- Desktop Navigation Links --}}
        <nav class="hidden items-center gap-1.5 lg:flex font-sans" aria-label="{{ __('nav.home') }}">
            @foreach ($primary as $item)
                @php
                    $isActive = request()->routeIs($item['route']);
                @endphp
                <a
                    href="{{ route($item['route']) }}"
                    @if ($isActive) aria-current="page" @endif
                    class="px-3.5 py-2 rounded-xl text-sm font-semibold transition-all duration-200 {{ $isActive ? 'bg-gold-500 text-teal-950 shadow-md shadow-gold-500/20 font-bold' : 'text-sand-100 hover:text-white hover:bg-teal-900/60' }}"
                >{{ __($item['key']) }}</a>
            @endforeach

            {{-- Secondary Pages Dropdown --}}
            <details class="relative group/drop">
                <summary class="cursor-pointer list-none flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-sm font-semibold text-sand-100 hover:text-white hover:bg-teal-900/60 transition-colors">
                    <span>{{ __('nav.more') }}</span>
                    <svg class="h-4 w-4 text-gold-400 transition-transform group-open/drop:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </summary>
                <div class="absolute end-0 top-full z-50 mt-2 w-52 rounded-2xl border border-teal-800 bg-teal-950 p-2 text-sand-100 shadow-2xl backdrop-blur-xl animate-rise">
                    @foreach ($secondary as $item)
                        @php
                            $isSecActive = request()->routeIs($item['route']);
                        @endphp
                        <a
                            href="{{ route($item['route']) }}"
                            class="flex items-center gap-2.5 rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-medium transition-colors {{ $isSecActive ? 'bg-teal-800/80 text-gold-400 font-bold' : 'hover:bg-teal-900/80 hover:text-white' }}"
                        >
                            <span class="h-1.5 w-1.5 rounded-full {{ $isSecActive ? 'bg-gold-400' : 'bg-teal-700' }}"></span>
                            <span>{{ __($item['key']) }}</span>
                        </a>
                    @endforeach
                </div>
            </details>
        </nav>

        {{-- Desktop Action Items (Lang & Donate) --}}
        <div class="hidden items-center gap-3 sm:flex">
            {{-- Language Switcher Pills --}}
            <div class="flex items-center rounded-xl bg-teal-900/80 border border-teal-800/80 p-1 text-xs font-semibold">
                <a
                    href="{{ route('locale.switch', 'ar') }}"
                    class="rounded-lg px-2.5 py-1 transition-all {{ app()->isLocale('ar') ? 'bg-gold-500 text-teal-950 font-bold shadow-sm' : 'text-sand-200 hover:text-white' }}"
                >{{ __('locale.ar') }}</a>
                <a
                    href="{{ route('locale.switch', 'en') }}"
                    class="rounded-lg px-2.5 py-1 transition-all {{ app()->isLocale('en') ? 'bg-gold-500 text-teal-950 font-bold shadow-sm' : 'text-sand-200 hover:text-white' }}"
                >{{ __('locale.en') }}</a>
            </div>

            {{-- Main Header CTA --}}
            <a
                href="{{ route('donate') }}"
                class="btn-primary !px-5 !py-2.5 text-xs sm:text-sm font-bold shadow-lg shadow-gold-500/20 flex items-center gap-2"
            >
                <svg class="h-4 w-4 text-teal-950" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                <span>{{ __('nav.donate') }}</span>
            </a>
        </div>

        {{-- Mobile Hamburger Menu --}}
        <details class="lg:hidden relative group/mobile">
            <summary class="flex h-10 w-10 cursor-pointer list-none items-center justify-center rounded-xl border border-teal-700 bg-teal-900/60 text-sand-50 transition-colors hover:bg-teal-800">
                <span class="sr-only">{{ __('nav.menu') }}</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </summary>
            <div class="absolute inset-x-0 -start-4 end-0 top-full mt-3 w-screen max-w-sm rounded-2xl border border-teal-800 bg-teal-950/95 p-5 shadow-2xl backdrop-blur-xl animate-rise text-sand-50 z-50">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-teal-800/80">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-gold-400">{{ __('brand.name') }}</span>
                    </div>
                    <div class="flex items-center rounded-lg bg-teal-900 border border-teal-800 p-0.5 text-xs font-semibold">
                        <a href="{{ route('locale.switch', 'ar') }}" class="rounded px-2.5 py-1 {{ app()->isLocale('ar') ? 'bg-gold-500 text-teal-950 font-bold' : 'text-sand-300' }}">عربي</a>
                        <a href="{{ route('locale.switch', 'en') }}" class="rounded px-2.5 py-1 {{ app()->isLocale('en') ? 'bg-gold-500 text-teal-950 font-bold' : 'text-sand-300' }}">EN</a>
                    </div>
                </div>

                <nav class="grid gap-1 text-sm font-semibold">
                    @foreach (PublicNavigation::all() as $item)
                        @php
                            $isMobActive = request()->routeIs($item['route']);
                        @endphp
                        <a
                            href="{{ route($item['route']) }}"
                            class="flex items-center justify-between rounded-xl px-3.5 py-2.5 transition-colors {{ $isMobActive ? 'bg-gold-500 text-teal-950 font-bold' : 'text-sand-100 hover:bg-teal-900/60' }}"
                        >
                            <span>{{ __($item['key']) }}</span>
                            @if ($isMobActive)
                                <span class="h-2 w-2 rounded-full bg-teal-950"></span>
                            @endif
                        </a>
                    @endforeach
                </nav>

                <div class="mt-5 pt-4 border-t border-teal-800/80">
                    <a
                        href="{{ route('donate') }}"
                        class="btn-primary w-full text-center !py-3 text-sm font-bold shadow-lg shadow-gold-500/20 flex items-center justify-center gap-2"
                    >
                        <svg class="h-4 w-4 text-teal-950" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        <span>{{ __('nav.donate') }}</span>
                    </a>
                </div>
            </div>
        </details>
    </div>
</header>
