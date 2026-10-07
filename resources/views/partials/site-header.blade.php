@php
    use App\Support\PublicNavigation;

    $menus = $navMenus ?? PublicNavigation::menus();
    $aboutLinks = PublicNavigation::aboutLinks();
    $otherLocale = app()->isLocale('ar') ? 'en' : 'ar';
    $megaMenus = [
        'about' => ['label' => __('nav.about'), 'active' => request()->routeIs('about', 'impact', 'partners', 'volunteer', 'faq', 'contact')],
        'regions' => ['label' => __('nav.regions'), 'active' => request()->routeIs('regions.*', 'impact-map')],
        'programs' => ['label' => __('nav.programs'), 'active' => request()->routeIs('programs', 'programs.*')],
        'projects' => ['label' => __('nav.projects'), 'active' => request()->routeIs('campaigns', 'campaigns.*', 'projects', 'projects.*')],
        'sponsorship' => ['label' => __('nav.sponsorship'), 'active' => request()->routeIs('sponsorship', 'sponsorship.*')],
    ];
@endphp

{{-- Utility bar --}}
<div class="relative z-[51] bg-teal-950 text-sand-100 print:hidden">
    <div class="container-bader flex h-10 items-center justify-between gap-4 text-xs">
        <p class="hidden items-center gap-2 font-medium text-sand-100/85 sm:flex">
            <span class="h-1.5 w-1.5 rounded-full bg-gold-500 animate-pulse-dot" aria-hidden="true"></span>
            {{ __('header.topbar_text') }}
        </p>
        <nav class="flex items-center gap-1 ms-auto" aria-label="{{ __('header.utility_nav') }}">
            <a href="{{ route('gift') }}" class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 font-semibold transition-colors hover:bg-white/10 hover:text-white">
                <x-bader.icon name="gift" class="h-3.5 w-3.5 text-gold-400" />
                <span>{{ __('nav.gift') }}</span>
            </a>
            <a href="{{ route('zakat') }}" class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 font-semibold transition-colors hover:bg-white/10 hover:text-white">
                <x-bader.icon name="calculator" class="h-3.5 w-3.5 text-gold-400" />
                <span>{{ __('nav.zakat') }}</span>
            </a>
            <span class="mx-1 h-4 w-px bg-white/15" aria-hidden="true"></span>
            <a href="{{ route('locale.switch', $otherLocale) }}" lang="{{ $otherLocale }}" class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 font-bold transition-colors hover:bg-white/10 hover:text-white">
                <x-bader.icon name="globe" class="h-3.5 w-3.5 text-gold-400" />
                <span>{{ __('locale.'.$otherLocale) }}</span>
            </a>
        </nav>
    </div>
</div>

<header data-site-header class="site-header sticky top-0 z-50">
    <div class="container-bader flex h-[4.5rem] items-center gap-4">
        <a href="{{ route('home') }}" class="group flex shrink-0 items-center gap-3" aria-label="{{ __('brand.name') }}">
            <img src="{{ \App\Support\SiteSettings::brandAsset('mark_star') }}" alt="" width="56" height="56" class="h-14 w-14 shrink-0 object-contain transition-transform duration-300 ease-bader group-hover:scale-105">
            <span class="leading-tight">
                <span class="block text-lg font-extrabold text-ink-900">{{ __('brand.name') }}</span>
                <span class="block text-[11px] font-semibold text-forest-700">{{ __('brand.name_en') }}</span>
            </span>
        </a>

        <nav class="hidden h-full items-stretch gap-0 ms-1 lg:flex xl:gap-0.5 xl:ms-4" aria-label="{{ __('header.main_nav') }}">
            <div class="flex items-center">
                <a href="{{ route('home') }}" @class(['nav-link', 'is-current' => request()->routeIs('home')]) @if (request()->routeIs('home')) aria-current="page" @endif>{{ __('nav.home') }}</a>
            </div>

            @foreach ($megaMenus as $menuKey => $menu)
                <div data-mega class="flex items-center">
                    <button
                        type="button"
                        data-mega-trigger
                        aria-expanded="false"
                        aria-controls="mega-{{ $menuKey }}"
                        @class(['nav-link', 'is-current' => $menu['active']])
                    >
                        <span>{{ $menu['label'] }}</span>
                        <x-bader.icon name="chevron-down" class="h-4 w-4 transition-transform duration-200" />
                    </button>

                    <div id="mega-{{ $menuKey }}" data-mega-panel hidden class="mega-panel">
                        <div class="container-bader py-8">
                            @switch($menuKey)
                                @case('about')
                                    <div class="grid gap-8 lg:grid-cols-[1fr_20rem]">
                                        <ul class="grid gap-2 sm:grid-cols-2 xl:grid-cols-3">
                                            @foreach ($aboutLinks as $link)
                                                <li>
                                                    <a href="{{ route($link['route']) }}" class="mega-link">
                                                        <span class="mega-link-icon"><x-bader.nav-icon :route="$link['route']" class="h-5 w-5" /></span>
                                                        <span>
                                                            <span class="block font-bold text-ink-900">{{ __($link['key']) }}</span>
                                                            <span class="mt-0.5 block text-xs leading-relaxed text-muted">{{ __('header.about_desc.'.$link['route']) }}</span>
                                                        </span>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                        <div class="rounded-2xl bg-teal-950 p-6 text-sand-50">
                                            <p class="text-xs font-bold text-gold-400">{{ __('brand.name') }}</p>
                                            <p class="mt-2 text-lg font-extrabold leading-snug">{{ __('brand.tagline') }}</p>
                                            <a href="{{ route('donate') }}" class="btn-primary mt-5">{{ __('nav.donate') }}</a>
                                        </div>
                                    </div>
                                    @break

                                @case('regions')
                                    <div class="flex items-end justify-between gap-4">
                                        <div>
                                            <p class="kicker">{{ __('nav.regions') }}</p>
                                            <p class="mt-2 max-w-xl text-sm text-muted">{{ __('header.regions_intro') }}</p>
                                        </div>
                                        <a href="{{ route('impact-map') }}" class="mega-more">{{ __('header.view_map') }} <x-bader.icon name="arrow" class="h-4 w-4" /></a>
                                    </div>
                                    <ul class="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                                        @forelse ($menus['regions'] as $region)
                                            <li>
                                                <a href="{{ route('regions.show', $region->key) }}" class="mega-tile">
                                                    <x-bader.icon name="map-pin" class="h-5 w-5 text-forest-700" />
                                                    <span class="mt-3 block font-bold text-ink-900">{{ $region->name }}</span>
                                                    <span class="mt-1 block text-xs text-muted">{{ trans_choice('region.projects_count', $region->projects_count, ['count' => $region->projects_count]) }}</span>
                                                </a>
                                            </li>
                                        @empty
                                            <li class="text-sm text-muted">{{ __('header.coming_soon') }}</li>
                                        @endforelse
                                    </ul>
                                    @break

                                @case('programs')
                                    <div class="flex items-end justify-between gap-4">
                                        <p class="kicker">{{ __('nav.programs') }}</p>
                                        <a href="{{ route('programs') }}" class="mega-more">{{ __('header.all_programs') }} <x-bader.icon name="arrow" class="h-4 w-4" /></a>
                                    </div>
                                    <ul class="mt-6 grid gap-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                                        @foreach ($menus['programs'] as $program)
                                            <li>
                                                <a href="{{ route('programs.show', $program->key) }}" class="mega-link !items-center">
                                                    <span class="h-11 w-11 shrink-0 overflow-hidden rounded-xl bg-paper-2">
                                                        @if ($program->image)
                                                            <img src="{{ asset($program->image) }}" alt="" class="h-full w-full object-cover" loading="lazy">
                                                        @else
                                                            <span class="flex h-full w-full items-center justify-center text-forest-700"><x-bader.icon name="sprout" class="h-5 w-5" /></span>
                                                        @endif
                                                    </span>
                                                    <span class="min-w-0">
                                                        <span class="block truncate font-bold text-ink-900">{{ $program->title }}</span>
                                                        <span class="block text-xs text-muted">{{ trans_choice('region.projects_count', $program->projects_count, ['count' => $program->projects_count]) }}</span>
                                                    </span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                    @break

                                @case('projects')
                                    <div class="flex items-end justify-between gap-4">
                                        <p class="kicker">{{ __('header.featured_projects') }}</p>
                                        <div class="flex flex-wrap items-center gap-x-6 gap-y-2">
                                            <a href="{{ route('projects') }}" class="mega-more">{{ __('header.all_projects') }} <x-bader.icon name="arrow" class="h-4 w-4" /></a>
                                            <a href="{{ route('campaigns') }}" class="mega-more">{{ __('header.all_campaigns') }} <x-bader.icon name="arrow" class="h-4 w-4" /></a>
                                        </div>
                                    </div>
                                    <ul class="mt-6 grid gap-4 md:grid-cols-3">
                                        @forelse ($menus['projects'] as $project)
                                            <li>
                                                <a href="{{ route('projects.show', $project->key) }}" class="group block overflow-hidden rounded-2xl border border-hairline bg-white transition hover:shadow-card-md">
                                                    <span class="block aspect-[16/9] overflow-hidden bg-paper-2">
                                                        <img src="{{ asset($project->image ?: 'images/programs/water.jpg') }}" alt="" class="h-full w-full object-cover transition duration-500 ease-bader group-hover:scale-105" loading="lazy">
                                                    </span>
                                                    <span class="block p-4">
                                                        @if ($project->region)
                                                            <span class="flex items-center gap-1 text-xs font-semibold text-forest-700"><x-bader.icon name="map-pin" class="h-3.5 w-3.5" />{{ $project->region->name }}</span>
                                                        @endif
                                                        <span class="mt-1 block font-bold text-ink-900">{{ $project->title }}</span>
                                                    </span>
                                                </a>
                                            </li>
                                        @empty
                                            <li class="text-sm text-muted">{{ __('header.coming_soon') }}</li>
                                        @endforelse
                                    </ul>
                                    @break

                                @case('sponsorship')
                                    <div class="grid gap-8 lg:grid-cols-[18rem_1fr]">
                                        <div>
                                            <p class="kicker">{{ __('nav.sponsorship') }}</p>
                                            <p class="mt-3 text-lg font-extrabold leading-snug text-ink-900">{{ __('header.sponsorship_title') }}</p>
                                            <p class="mt-2 text-sm leading-relaxed text-muted">{{ __('header.sponsorship_intro') }}</p>
                                            <a href="{{ route('sponsorship') }}" class="btn-brand mt-5">{{ __('header.all_cases') }}</a>
                                        </div>
                                        <ul class="grid gap-3 md:grid-cols-3">
                                            @forelse ($menus['waitingCases'] as $case)
                                                <li>
                                                    <a href="{{ route('sponsorship.show', $case->code) }}" class="mega-tile h-full">
                                                        <span class="flex items-center justify-between gap-2">
                                                            <span class="rounded-full bg-paper-2 px-2.5 py-0.5 text-[11px] font-bold text-forest-700">{{ $case->type_label }}</span>
                                                            <span class="font-mono text-[11px] text-subtle" dir="ltr">{{ $case->code }}</span>
                                                        </span>
                                                        <span class="mt-3 block font-bold text-ink-900">{{ $case->name }}</span>
                                                        <span class="mt-1 block text-xs text-muted">
                                                            @if ($case->age){{ __('sponsorship.age_years', ['age' => $case->age]) }} · @endif{{ $case->region?->name }}
                                                        </span>
                                                        <span class="mt-3 block text-sm font-extrabold text-forest-700">{{ __('sponsorship.per_month', ['amount' => '$'.number_format((float) $case->monthly_amount)]) }}</span>
                                                    </a>
                                                </li>
                                            @empty
                                                <li class="text-sm text-muted md:col-span-3">{{ __('header.no_waiting_cases') }}</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                    @break
                            @endswitch
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="flex items-center">
                <a href="{{ route('news') }}" @class(['nav-link', 'is-current' => request()->routeIs('news', 'news.*')]) @if (request()->routeIs('news', 'news.*')) aria-current="page" @endif>{{ __('nav.news') }}</a>
            </div>
        </nav>

        <div class="flex items-center gap-2 ms-auto">
            <a href="{{ route('donate') }}" class="btn-primary hidden !min-h-11 sm:inline-flex">
                <x-bader.icon name="heart" class="h-4 w-4" />
                <span>{{ __('nav.donate') }}</span>
            </a>
            <button type="button" data-drawer-open aria-expanded="false" aria-controls="site-drawer" class="flex h-11 w-11 items-center justify-center rounded-xl border border-hairline-strong bg-white text-ink-900 transition hover:border-forest-700 hover:text-forest-700 lg:hidden">
                <span class="sr-only">{{ __('nav.menu') }}</span>
                <x-bader.icon name="menu" class="h-6 w-6" />
            </button>
        </div>
    </div>
</header>

{{-- Mobile drawer --}}
<div id="site-drawer" data-drawer hidden class="drawer lg:hidden" role="dialog" aria-modal="true" aria-label="{{ __('nav.menu') }}">
    <div class="drawer-backdrop" data-drawer-close></div>
    <div class="drawer-panel">
        <div class="flex items-center justify-between border-b border-hairline px-5 py-4">
            <span class="text-base font-extrabold text-ink-900">{{ __('brand.name') }}</span>
            <button type="button" data-drawer-close class="flex h-10 w-10 items-center justify-center rounded-xl border border-hairline text-ink-700 hover:text-forest-700">
                <span class="sr-only">{{ __('header.close_menu') }}</span>
                <x-bader.icon name="close" class="h-5 w-5" />
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4" aria-label="{{ __('header.main_nav') }}">
            <a href="{{ route('home') }}" class="drawer-link">{{ __('nav.home') }}</a>

            <details class="drawer-group" @if ($megaMenus['about']['active']) open @endif>
                <summary class="drawer-link">{{ __('nav.about') }} <x-bader.icon name="chevron-down" class="h-4 w-4" /></summary>
                <div class="drawer-sub">
                    @foreach ($aboutLinks as $link)
                        <a href="{{ route($link['route']) }}">{{ __($link['key']) }}</a>
                    @endforeach
                </div>
            </details>

            <details class="drawer-group" @if ($megaMenus['regions']['active']) open @endif>
                <summary class="drawer-link">{{ __('nav.regions') }} <x-bader.icon name="chevron-down" class="h-4 w-4" /></summary>
                <div class="drawer-sub">
                    <a href="{{ route('impact-map') }}" class="font-bold">{{ __('header.view_map') }}</a>
                    @foreach ($menus['regions'] as $region)
                        <a href="{{ route('regions.show', $region->key) }}">{{ $region->name }}</a>
                    @endforeach
                </div>
            </details>

            <details class="drawer-group" @if ($megaMenus['programs']['active']) open @endif>
                <summary class="drawer-link">{{ __('nav.programs') }} <x-bader.icon name="chevron-down" class="h-4 w-4" /></summary>
                <div class="drawer-sub">
                    <a href="{{ route('programs') }}" class="font-bold">{{ __('header.all_programs') }}</a>
                    @foreach ($menus['programs'] as $program)
                        <a href="{{ route('programs.show', $program->key) }}">{{ $program->title }}</a>
                    @endforeach
                </div>
            </details>

            <a href="{{ route('projects') }}" class="drawer-link">{{ __('nav.projects') }}</a>
            <a href="{{ route('campaigns') }}" class="drawer-link">{{ __('nav.campaigns') }}</a>
            <a href="{{ route('sponsorship') }}" class="drawer-link">{{ __('nav.sponsorship') }}</a>
            <a href="{{ route('news') }}" class="drawer-link">{{ __('nav.news') }}</a>
            <a href="{{ route('gift') }}" class="drawer-link">{{ __('nav.gift') }}</a>
            <a href="{{ route('zakat') }}" class="drawer-link">{{ __('nav.zakat') }}</a>
        </nav>

        <div class="grid gap-3 border-t border-hairline p-5">
            <a href="{{ route('donate') }}" class="btn-primary w-full">
                <x-bader.icon name="heart" class="h-4 w-4" />
                <span>{{ __('nav.donate') }}</span>
            </a>
            <a href="{{ route('locale.switch', $otherLocale) }}" lang="{{ $otherLocale }}" class="btn-outline w-full">
                <x-bader.icon name="globe" class="h-4 w-4" />
                <span>{{ __('locale.'.$otherLocale) }}</span>
            </a>
        </div>
    </div>
</div>
