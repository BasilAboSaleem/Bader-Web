@php
    use App\Support\PublicNavigation;
    $primary = PublicNavigation::primary();
    $secondary = PublicNavigation::secondary();
    $dark = $dark ?? false;
@endphp

<header
    data-site-header
    @class([
        'relative sticky top-0 z-50 border-b transition-colors duration-200',
        'border-white/10 bg-bader-green-deep text-bader-on-dark' => $dark,
        'border-bader-green/10 bg-bader-paper text-bader-ink' => ! $dark,
    ])
>
    <div class="mx-auto flex max-w-6xl items-center gap-3 px-4 py-3">
        <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-2" aria-label="{{ __('brand.name') }}">
            <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="" width="40" height="40" class="h-10 w-10">
            <span class="leading-tight">
                <span @class(['block text-sm font-semibold', 'text-white' => $dark, 'text-bader-green' => ! $dark])>{{ __('brand.name') }}</span>
                <span @class(['hidden text-[11px] sm:block', 'text-white/55' => $dark, 'text-bader-ink/55' => ! $dark])>{{ __('brand.name_en') }}</span>
            </span>
        </a>

        <nav class="ms-auto hidden items-center gap-5 text-sm font-semibold lg:flex" aria-label="{{ __('nav.home') }}">
            @foreach ($primary as $item)
                <a
                    href="{{ route($item['route']) }}"
                    @class([
                        'hover:text-bader-lime' => $dark,
                        request()->routeIs($item['route']) ? ($dark ? 'text-bader-lime' : 'text-bader-green') : ($dark ? 'text-white/80' : 'text-bader-ink/80'),
                    ])
                >{{ __($item['key']) }}</a>
            @endforeach

            <details class="relative">
                <summary @class(['cursor-pointer list-none', 'text-white/80 hover:text-bader-lime' => $dark, 'text-bader-ink/80' => ! $dark])>{{ __('nav.more') }}</summary>
                <div class="absolute end-0 top-full z-20 mt-2 w-48 rounded-xl border border-bader-green/10 bg-white py-2 text-bader-ink shadow-sm">
                    @foreach ($secondary as $item)
                        <a href="{{ route($item['route']) }}" class="block px-4 py-2 text-sm hover:bg-bader-paper">{{ __($item['key']) }}</a>
                    @endforeach
                </div>
            </details>
        </nav>

        <div class="ms-auto flex items-center gap-2 lg:ms-3">
            <div class="hidden items-center text-xs font-semibold sm:flex">
                <a href="{{ route('locale.switch', 'ar') }}" @class(['rounded-full px-2 py-1', app()->isLocale('ar') ? ($dark ? 'bg-bader-lime text-bader-green-deep' : 'bg-bader-green text-white') : ($dark ? 'text-white' : 'text-bader-green')])>{{ __('locale.ar') }}</a>
                <a href="{{ route('locale.switch', 'en') }}" @class(['rounded-full px-2 py-1', app()->isLocale('en') ? ($dark ? 'bg-bader-lime text-bader-green-deep' : 'bg-bader-green text-white') : ($dark ? 'text-white' : 'text-bader-green')])>{{ __('locale.en') }}</a>
            </div>
            <x-bader.button :href="route('donate')" :variant="$dark ? 'lime' : 'primary'">{{ __('nav.donate') }}</x-bader.button>
        </div>

        <details class="lg:hidden">
            <summary @class([
                'flex h-11 w-11 cursor-pointer list-none items-center justify-center rounded-full border',
                'border-white/25 text-white' => $dark,
                'border-bader-green/20 text-bader-green' => ! $dark,
            ])>
                <span class="sr-only">{{ __('nav.menu') }}</span>
                <span aria-hidden="true">☰</span>
            </summary>
            <div @class([
                'absolute inset-x-0 top-full border-b px-4 py-4',
                'border-white/10 bg-bader-green-deep' => $dark,
                'border-bader-green/10 bg-bader-paper' => ! $dark,
            ])>
                <nav class="grid gap-2 text-sm font-semibold">
                    @foreach (PublicNavigation::all() as $item)
                        <a href="{{ route($item['route']) }}" class="py-2">{{ __($item['key']) }}</a>
                    @endforeach
                    <div class="flex gap-2 pt-2 sm:hidden">
                        <a href="{{ route('locale.switch', 'ar') }}" class="rounded-full px-3 py-1 {{ app()->isLocale('ar') ? 'bg-bader-lime text-bader-green-deep' : 'border border-current' }}">{{ __('locale.ar') }}</a>
                        <a href="{{ route('locale.switch', 'en') }}" class="rounded-full px-3 py-1 {{ app()->isLocale('en') ? 'bg-bader-lime text-bader-green-deep' : 'border border-current' }}">{{ __('locale.en') }}</a>
                    </div>
                </nav>
            </div>
        </details>
    </div>
</header>
