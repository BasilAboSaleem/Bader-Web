@php
    use App\Support\PublicNavigation;
    $primary = PublicNavigation::primary();
    $secondary = PublicNavigation::secondary();
@endphp

<header class="relative sticky top-0 z-50 border-b border-bader-green/10 bg-bader-paper">
    <div class="border-b border-bader-green/10 bg-white/70">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-3 px-4 py-1.5 text-[11px] font-semibold text-bader-green sm:text-xs">
            <p>{{ __('brand.hq') }}</p>
            <p>{{ __('brand.field') }}</p>
        </div>
    </div>

    <div class="mx-auto flex max-w-6xl items-center gap-3 px-4 py-3">
        <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-2" aria-label="{{ __('brand.name') }}">
            <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="" width="40" height="40" class="h-10 w-10">
            <span class="leading-tight">
                <span class="block text-sm font-semibold text-bader-green">{{ __('brand.name') }}</span>
                <span class="hidden text-[11px] text-bader-ink/55 sm:block">{{ __('brand.name_en') }}</span>
            </span>
        </a>

        <nav class="ms-auto hidden items-center gap-5 text-sm font-semibold text-bader-ink/80 lg:flex" aria-label="{{ __('nav.home') }}">
            @foreach ($primary as $item)
                <a
                    href="{{ route($item['route']) }}"
                    @class(['text-bader-green' => request()->routeIs($item['route'])])
                >{{ __($item['key']) }}</a>
            @endforeach

            <details class="relative">
                <summary class="cursor-pointer list-none text-bader-ink/80">{{ __('nav.more') }}</summary>
                <div class="absolute end-0 top-full z-20 mt-2 w-48 rounded-xl border border-bader-green/10 bg-white py-2 shadow-sm">
                    @foreach ($secondary as $item)
                        <a href="{{ route($item['route']) }}" class="block px-4 py-2 text-sm hover:bg-bader-paper">{{ __($item['key']) }}</a>
                    @endforeach
                </div>
            </details>
        </nav>

        <div class="ms-auto flex items-center gap-2 lg:ms-3">
            <div class="hidden items-center text-xs font-semibold sm:flex">
                <a href="{{ route('locale.switch', 'ar') }}" class="rounded-full px-2 py-1 {{ app()->isLocale('ar') ? 'bg-bader-green text-white' : 'text-bader-green' }}">{{ __('locale.ar') }}</a>
                <a href="{{ route('locale.switch', 'en') }}" class="rounded-full px-2 py-1 {{ app()->isLocale('en') ? 'bg-bader-green text-white' : 'text-bader-green' }}">{{ __('locale.en') }}</a>
            </div>
            <x-bader.button :href="route('donate')">{{ __('nav.donate') }}</x-bader.button>
        </div>

        <details class="lg:hidden">
            <summary class="flex h-11 w-11 cursor-pointer list-none items-center justify-center rounded-full border border-bader-green/20 text-bader-green">
                <span class="sr-only">{{ __('nav.menu') }}</span>
                <span aria-hidden="true">☰</span>
            </summary>
            <div class="absolute inset-x-0 top-full border-b border-bader-green/10 bg-bader-paper px-4 py-4">
                <nav class="grid gap-2 text-sm font-semibold">
                    @foreach (PublicNavigation::all() as $item)
                        <a href="{{ route($item['route']) }}" class="py-2">{{ __($item['key']) }}</a>
                    @endforeach
                    <div class="flex gap-2 pt-2 sm:hidden">
                        <a href="{{ route('locale.switch', 'ar') }}" class="rounded-full px-3 py-1 {{ app()->isLocale('ar') ? 'bg-bader-green text-white' : 'border border-bader-green text-bader-green' }}">{{ __('locale.ar') }}</a>
                        <a href="{{ route('locale.switch', 'en') }}" class="rounded-full px-3 py-1 {{ app()->isLocale('en') ? 'bg-bader-green text-white' : 'border border-bader-green text-bader-green' }}">{{ __('locale.en') }}</a>
                    </div>
                </nav>
            </div>
        </details>
    </div>
</header>
