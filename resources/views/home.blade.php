@extends('layouts.public')

@section('title', __('brand.name'))
@section('headerTheme', 'dark')

@section('content')
    {{-- ══════════════════════════════════════════════════════════
         1. HERO SECTION (Dynamic High-Impact NGO Hero with Photo Backdrop)
         ══════════════════════════════════════════════════════════ --}}
    <section class="relative min-h-[90vh] flex flex-col justify-center overflow-hidden bg-teal-950 bg-bader-green-deep text-sand-50 pt-20 pb-16 lg:h-[calc(100svh-5rem)] lg:min-h-0 lg:py-4 border-b border-teal-800/80">
        {{-- Hero Photographic Backdrop with Cinematic Gradient --}}
        <div class="absolute inset-0 z-0">
            <img
                src="{{ asset('images/programs/water.jpg') }}"
                alt="{{ __('brand.name') }}"
                class="w-full h-full object-cover object-center opacity-25 filter blur-[1px] scale-105 transform motion-safe:animate-pulse [animation-duration:10s]"
            >
            <div class="absolute inset-0 bg-gradient-to-t from-teal-950 via-teal-950/90 to-teal-900/80"></div>
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-forest-700/20 via-transparent to-teal-950/90"></div>
        </div>

        <div class="relative z-10 mx-auto flex w-full max-w-5xl flex-1 flex-col items-center px-4 text-center sm:px-6 lg:px-8">
            <div class="my-auto flex w-full flex-col items-center">
                {{-- Kicker Badge --}}
                <div class="inline-flex items-center gap-2 rounded-full border border-teal-700/80 bg-teal-900/80 px-4 py-1 text-xs sm:text-sm font-semibold text-gold-400 backdrop-blur-md mb-3 lg:mb-2 animate-rise shadow-lg shadow-black/20">
                    <span class="h-2 w-2 rounded-full bg-gold-400 animate-ping"></span>
                    <span>{{ \App\Support\SiteSettings::content('home.hero_kicker') }}</span>
                </div>

                {{-- Main Title --}}
                <h1 class="text-3xl sm:text-5xl lg:text-5xl font-extrabold text-white leading-tight tracking-tight font-sans animate-rise [animation-delay:100ms] max-w-4xl">
                    {{ __('brand.name') }} {{ \App\Support\SiteSettings::content('home.hero_prefix') }}
                    <span class="block text-gold-400 mt-3 font-display text-xl sm:text-3xl lg:text-3xl">
                        {{ \App\Support\SiteSettings::content('home.hero_title') }}
                    </span>
                </h1>

                {{-- Narrative description --}}
                <p class="mt-4 text-base sm:text-lg text-sand-100/90 max-w-3xl mx-auto leading-relaxed font-sans animate-rise [animation-delay:200ms]">
                    {{ \App\Support\SiteSettings::content('home.hero_text') }}
                </p>

                {{-- Operating Scope Badges --}}
                <div class="mt-3 flex flex-wrap justify-center items-center gap-3 animate-rise [animation-delay:250ms]">
                    <div class="flex items-center gap-2 rounded-full border border-teal-700 bg-teal-900/60 px-4 py-1 text-xs font-semibold text-sand-100/90 backdrop-blur-sm shadow-sm">
                        <span class="h-2 w-2 rounded-full bg-forest-600"></span>
                        <span>{{ __('brand.field') }}</span>
                    </div>
                </div>

                {{-- Hero Call to Actions --}}
                <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-3 w-full sm:w-auto animate-rise [animation-delay:300ms]">
                    <a href="{{ route('donate') }}" class="btn-primary !px-8 !py-3.5 text-base font-bold shadow-2xl shadow-gold-500/30 w-full sm:w-auto flex items-center justify-center gap-2.5 transform hover:scale-105 transition-all">
                        <svg class="h-5 w-5 text-teal-950" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                        <span>{{ __('nav.donate') }}</span>
                        <span aria-hidden="true" class="rtl:rotate-180">&rarr;</span>
                    </a>

                    <a href="{{ route('about') }}" class="btn-secondary !px-8 !py-3.5 text-base font-semibold w-full sm:w-auto border-white/40 hover:bg-white/10 hover:border-white transition-all">
                        {{ \App\Support\SiteSettings::content('home.hero_secondary') }}
                    </a>
                </div>
            </div>

            @if ($campaigns->isNotEmpty())
                <div class="mt-auto w-full max-w-4xl pt-6 animate-rise [animation-delay:400ms]" data-home-section="campaigns">
                    <div class="mb-2 flex items-center justify-between gap-4 text-start">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-gold-400">{{ __('home.campaigns_kicker') }}</p>
                            <h2 class="mt-0.5 text-lg font-bold text-white sm:text-xl">{{ __('home.campaigns_heading') }}</h2>
                        </div>
                    </div>

                    <div data-campaign-carousel role="region" class="overflow-hidden rounded-2xl pb-2" aria-label="{{ __('home.campaigns_heading') }}" aria-roledescription="carousel" aria-live="off">
                        <div data-campaign-track dir="ltr" class="flex transition-transform duration-700 ease-in-out will-change-transform">
                            @foreach ($campaigns as $homeCampaign)
                                @php
                                    $goal = (float) ($homeCampaign->goal_amount ?? 0);
                                    $raised = (float) $homeCampaign->raised_amount;
                                    $progress = $goal > 0 ? min(100, (int) round(($raised / $goal) * 100)) : 0;
                                @endphp
                                <article data-campaign-slide role="group" aria-label="{{ $homeCampaign->title }}" aria-roledescription="slide" aria-hidden="{{ $loop->first ? 'false' : 'true' }}" @unless ($loop->first) inert @endunless dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="w-full shrink-0 px-0.5">
                                    <div class="relative h-40 overflow-hidden rounded-2xl border border-white/15 bg-gradient-to-br from-teal-800 via-forest-700 to-teal-950 text-start shadow-xl shadow-black/10 transition hover:border-gold-400/50 sm:h-44">
                                        <div class="absolute inset-0 overflow-hidden">
                                            @if ($homeCampaign->image)
                                                <img src="{{ asset($homeCampaign->image) }}" alt="" class="h-full w-full object-cover opacity-80 transition duration-700 hover:scale-105" loading="lazy">
                                            @endif
                                            <div class="absolute inset-0 bg-gradient-to-t from-teal-950 via-teal-950/65 to-teal-950/10"></div>
                                            <span class="absolute start-4 top-4 inline-flex items-center gap-2 rounded-full border border-gold-400/40 bg-teal-950/70 px-3 py-1 text-xs font-semibold text-gold-400 backdrop-blur">
                                                <span class="size-1.5 rounded-full bg-gold-400"></span>
                                                {{ __('campaign.status_active') }}
                                            </span>
                                        </div>
                                        <div class="relative z-10 flex h-40 flex-col justify-between p-4 sm:h-44 sm:p-5">
                                            <div class="pt-8 sm:pt-9">
                                                <h3 class="line-clamp-1 text-lg font-bold text-white sm:text-xl">{{ $homeCampaign->title }}</h3>
                                                @if ($homeCampaign->description)
                                                    <p class="mt-1 line-clamp-1 text-xs leading-relaxed text-sand-100/80 sm:text-sm">{{ $homeCampaign->description }}</p>
                                                @endif
                                            </div>
                                            <div class="flex items-end justify-between gap-4">
                                                @if ($goal > 0)
                                                    <div class="min-w-0 flex-1">
                                                        <div class="mb-1 flex items-center justify-between gap-3 text-[10px] sm:text-xs">
                                                            <span class="text-sand-100/70">{{ __('campaign.goal') }}</span>
                                                            <span class="font-semibold text-gold-400">{{ number_format($goal) }} {{ $homeCampaign->currency }}</span>
                                                        </div>
                                                        <div class="h-1.5 overflow-hidden rounded-full bg-white/15" role="progressbar" aria-label="{{ __('campaign.goal') }}" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                                                            <div class="h-full rounded-full bg-gradient-to-r from-forest-600 to-gold-400" style="width: {{ $progress }}%"></div>
                                                        </div>
                                                    </div>
                                                @endif
                                                <a href="{{ route('donate', ['target_type' => 'campaign', 'target_id' => $homeCampaign->id]) }}" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-full bg-gold-500 px-5 py-2 text-xs font-bold text-teal-950 transition hover:bg-gold-400 sm:text-sm">
                                                    <span>{{ __('nav.donate') }}</span>
                                                    <svg class="size-4 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-7-7l7 7-7 7" /></svg>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════
         2. FIELD STORIES & NEWS (Inspired by Senabil's News Carousel)
         ══════════════════════════════════════════════════════════ --}}
    <section class="bg-white section-pad border-b border-sand-200" data-home-section="stories">
        <div class="max-w-7xl mx-auto">
            {{-- Header with Centered Icon --}}
            <div class="text-center max-w-3xl mx-auto mb-12" data-reveal>
                <div class="flex justify-center mb-4">
                    <div class="h-14 w-14 rounded-2xl bg-forest-700 text-white flex items-center justify-center shadow-lg shadow-forest-700/20">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                    </div>
                </div>

                <div class="inline-flex items-center gap-2 rounded-full border border-forest-700/20 bg-forest-700/10 px-3.5 py-1 text-xs font-bold text-forest-700 mb-2">
                    <span class="h-2 w-2 rounded-full bg-forest-600 animate-pulse"></span>
                    <span>{{ \App\Support\SiteSettings::content('home.stories_kicker') }}</span>
                </div>

                <h2 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-ink-900 tracking-tight leading-tight mb-3 font-sans">
                    {{ \App\Support\SiteSettings::content('home.stories_title') }}
                </h2>

                <p class="text-base sm:text-lg text-ink-700/80 leading-relaxed font-sans">
                    {{ \App\Support\SiteSettings::content('home.stories_intro') }}
                </p>
            </div>

            {{-- News Cards Grid --}}
            <div class="grid gap-8 md:grid-cols-3">
                @forelse ($stories as $story)
                    <article class="group relative bg-white rounded-3xl overflow-hidden border border-sand-200/90 shadow-sm hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 flex flex-col justify-between" data-reveal>
                        <div class="relative h-56 overflow-hidden bg-teal-950">
                            <a href="{{ route('news.show', $story->key) }}" class="block h-full w-full">
                                @if ($story->image)
                                    <img
                                        src="{{ asset($story->image) }}"
                                        alt="{{ $story->title }}"
                                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-108"
                                        loading="lazy"
                                    >
                                @else
                                    <div class="h-full w-full bg-gradient-to-br from-teal-800 to-forest-900 flex items-center justify-center">
                                        <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="" class="h-12 w-12 opacity-30">
                                    </div>
                                @endif
                            </a>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/20 to-transparent pointer-events-none"></div>

                            @if ($story->category)
                                <div class="absolute top-4 start-4">
                                    <span class="rounded-full bg-forest-700/90 backdrop-blur-md px-3 py-1 text-xs font-semibold text-white shadow-md">
                                        {{ $story->category }}
                                    </span>
                                </div>
                            @endif

                            <div class="absolute bottom-3 start-4 end-4 text-white/90 text-xs font-mono font-medium">
                                {{ $story->published_at?->format('Y-m-d') }}
                            </div>
                        </div>

                        <div class="p-6 sm:p-7 flex-1 flex flex-col justify-between">
                            <div>
                                <h3 class="text-lg sm:text-xl font-bold text-ink-900 group-hover:text-forest-700 transition-colors duration-300 font-sans mb-3 line-clamp-2">
                                    <a href="{{ route('news.show', $story->key) }}">
                                        {{ $story->title }}
                                    </a>
                                </h3>

                                @if ($story->excerpt)
                                    <p class="text-sm text-ink-700/85 leading-relaxed line-clamp-3 font-sans mb-5">
                                        {{ $story->excerpt }}
                                    </p>
                                @endif
                            </div>

                            <div class="pt-4 border-t border-sand-200/80 flex items-center justify-between">
                                <a href="{{ route('news.show', $story->key) }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-forest-700 hover:text-forest-800 transition-colors group/link">
                                    <span>{{ app()->isLocale('ar') ? 'اقرأ الخبر' : 'Read story' }}</span>
                                    <svg class="h-4 w-4 transition-transform duration-300 group-hover/link:-translate-x-1 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full text-center text-ink-700/50 py-10">{{ __('page.news.empty') }}</div>
                @endforelse
            </div>

            <div class="mt-10 text-center">
                <a href="{{ route('news') }}" class="btn-dark !px-8 !py-3.5 text-sm font-bold shadow-md hover:shadow-lg inline-flex items-center gap-2">
                    <span>{{ \App\Support\SiteSettings::content('home.stories_link') }}</span>
                    <span aria-hidden="true" class="rtl:rotate-180">&rarr;</span>
                </a>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════
         3. OPERATING ASSETS & FACILITIES (Inspired by Senabil's Projects)
         ══════════════════════════════════════════════════════════ --}}
    <section class="bg-sand-50 section-pad border-b border-sand-200" data-home-section="facilities">
        <div class="max-w-7xl mx-auto">
            {{-- Header with Centered Icon --}}
            <div class="text-center max-w-3xl mx-auto mb-14" data-reveal>
                <div class="flex justify-center mb-4">
                    <div class="h-14 w-14 rounded-2xl bg-forest-700 text-white flex items-center justify-center shadow-lg shadow-forest-700/20">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                </div>

                <div class="inline-flex items-center gap-2 rounded-full border border-forest-700/20 bg-forest-700/10 px-3.5 py-1 text-xs font-bold text-forest-700 mb-2">
                    <span class="h-2 w-2 rounded-full bg-forest-600 animate-pulse"></span>
                    <span>{{ \App\Support\SiteSettings::content('home.facilities_kicker') }}</span>
                </div>

                <h2 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-ink-900 tracking-tight leading-tight mb-3 font-sans">
                    {{ \App\Support\SiteSettings::content('home.facilities_title') }}
                </h2>

                <p class="text-base sm:text-lg text-ink-700/80 leading-relaxed font-sans">
                    {{ \App\Support\SiteSettings::content('home.facilities_intro') }}
                </p>
            </div>

            {{-- DB-driven Facility Cards --}}
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($facilities as $facility)
                    <article class="group relative bg-white rounded-3xl overflow-hidden border border-sand-200/90 shadow-sm hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 flex flex-col justify-between" data-reveal>
                        <div class="relative h-60 overflow-hidden bg-teal-950">
                            @if ($facility->image)
                                <img
                                    src="{{ asset($facility->image) }}"
                                    alt="{{ $facility->name }}"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-108"
                                    loading="lazy"
                                >
                            @else
                                <div class="h-full w-full bg-gradient-to-br from-teal-800 to-forest-900 flex items-center justify-center">
                                    <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="" class="h-14 w-14 opacity-30">
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/25 to-transparent"></div>

                            <div class="absolute top-4 start-4">
                                <span class="rounded-full bg-forest-700/90 backdrop-blur-md px-3.5 py-1 text-xs font-semibold text-white shadow-md">
                                    {{ \App\Support\SiteSettings::content('home.facility_badge') }}
                                </span>
                            </div>

                            <span class="absolute top-4 end-4 rounded-full bg-teal-950/85 backdrop-blur-md border border-teal-800/80 px-3 py-1 text-xs font-mono font-bold text-emerald-300 shadow-md">
                                {{ \App\Support\SiteSettings::content('home.facility_status') }}
                            </span>

                            @if ($facility->location)
                                <div class="absolute bottom-3 start-4 end-4 flex items-center gap-2 text-white text-xs font-semibold drop-shadow-md">
                                    <svg class="h-4 w-4 text-gold-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /></svg>
                                    <span>{{ $facility->location }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="p-6 sm:p-7 flex-1 flex flex-col justify-between">
                            <div>
                                <h3 class="text-xl sm:text-2xl font-bold text-ink-900 group-hover:text-forest-700 transition-colors duration-300 font-sans mb-3">
                                    {{ $facility->name }}
                                </h3>
                                @if ($facility->description)
                                    <p class="text-sm sm:text-base text-ink-700/85 leading-relaxed line-clamp-3 font-sans mb-5">
                                        {{ $facility->description }}
                                    </p>
                                @endif
                            </div>

                            <div class="pt-4 border-t border-sand-200/80 flex items-center justify-between">
                                <a href="{{ route('facilities.show', $facility->key) }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-forest-700 hover:text-forest-800 transition-colors group/link">
                                    <span>{{ \App\Support\SiteSettings::content('home.facility_action') }}</span>
                                    <span aria-hidden="true" class="transition-transform duration-300 group-hover/link:-translate-x-1">&larr;</span>
                                </a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full text-center text-ink-700/50 py-10">
                        {{ \App\Support\SiteSettings::content('home.facilities_empty') }}
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════
         3b. LIVE IMPACT METRICS (Dynamic from DB)
         ══════════════════════════════════════════════════════════ --}}
    @if ($approvedMetrics->isNotEmpty())
        <section class="bg-teal-950 text-sand-50 section-pad !py-16 border-b border-teal-800" data-home-section="impact-metrics">
            <div class="max-w-7xl mx-auto">
                <div class="text-center max-w-2xl mx-auto mb-10" data-reveal>
                    <div class="inline-flex items-center gap-2 rounded-full border border-teal-700/60 bg-teal-900/80 px-4 py-1.5 text-xs font-bold text-gold-400 mb-3">
                        <span class="h-2 w-2 rounded-full bg-gold-400 animate-pulse"></span>
                        <span>{{ \App\Support\SiteSettings::content('home.impact_kicker') }}</span>
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight font-sans">
                        {{ \App\Support\SiteSettings::content('home.impact_title') }}
                    </h2>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4" data-reveal>
                    @foreach ($approvedMetrics as $metric)
                        <div class="rounded-3xl bg-teal-900/70 border border-teal-700/50 p-7 text-center shadow-lg hover:border-gold-500/60 hover:-translate-y-1 transition-all duration-300">
                            <p class="font-display font-extrabold text-4xl sm:text-5xl text-gold-400 font-mono" data-countup="{{ $metric->value }}">{{ $metric->value }}</p>
                            @if ($metric->unit())
                                <p class="mt-1.5 text-xs font-mono uppercase tracking-wider text-sand-100/60">{{ $metric->unit() }}</p>
                            @endif
                            <p class="mt-3 text-sm font-semibold text-sand-100/90 leading-relaxed">{{ $metric->title() }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 text-center" data-reveal>
                    <a href="{{ route('impact') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gold-400 hover:text-gold-300 transition-colors">
                        <span>{{ \App\Support\SiteSettings::content('home.impact_link') }}</span>
                        <span aria-hidden="true" class="rtl:rotate-180">&rarr;</span>
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- ══════════════════════════════════════════════════════════
         4. PROGRAMS & INTERVENTION DOMAINS (High-End NGO Showcase)
         ══════════════════════════════════════════════════════════ --}}
    <section class="bg-sand-50/70 section-pad border-b border-sand-200 relative overflow-hidden" data-home-section="programs" id="programs-section">
        {{-- Subtle ambient glows --}}
        <div class="pointer-events-none absolute -start-28 top-20 h-96 w-96 rounded-full bg-forest-700/5 blur-3xl" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -end-28 bottom-20 h-96 w-96 rounded-full bg-gold-500/10 blur-3xl" aria-hidden="true"></div>

        <div class="max-w-7xl mx-auto relative">
            {{-- Section Header (Inspired by modern Palestinian NGO aesthetics) --}}
            <div class="text-center max-w-3xl mx-auto mb-12" data-reveal>
                <div class="flex justify-center mb-5">
                    <div class="h-16 w-16 rounded-2xl bg-forest-700 text-gold-400 flex items-center justify-center shadow-xl shadow-forest-700/20 transform hover:scale-105 transition-transform duration-300">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                </div>

                <div class="inline-flex items-center gap-2 rounded-full border border-forest-700/20 bg-forest-700/10 px-4 py-1 text-xs font-bold text-forest-700 mb-3">
                    <span class="h-2 w-2 rounded-full bg-forest-600 animate-pulse"></span>
                    <span>{{ \App\Support\SiteSettings::content('home.programs_kicker') }}</span>
                </div>

                <h2 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-ink-900 tracking-tight leading-tight mb-4 font-sans">
                    {{ \App\Support\SiteSettings::content('home.programs_title') }}
                </h2>

                <p class="text-base sm:text-lg text-ink-700/85 leading-relaxed font-sans">
                    {{ \App\Support\SiteSettings::content('home.programs_intro') }}
                </p>
            </div>

            {{-- Category Switcher Tabs --}}
            <div class="flex items-center justify-center gap-2 sm:gap-3 flex-wrap mb-10" id="programs-tab-bar" role="tablist" aria-label="{{ \App\Support\SiteSettings::content('home.programs_kicker') }}" data-reveal>
                <button type="button" data-filter="all" class="program-tab-btn active px-5 py-2.5 rounded-full text-xs sm:text-sm font-bold transition-all duration-300 flex items-center gap-2 bg-forest-700 text-white shadow-md shadow-forest-700/20 border border-forest-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
                    <span>{{ \App\Support\SiteSettings::content('home.programs_cat_all') }}</span>
                    <span class="ms-1 px-2 py-0.5 rounded-full text-[11px] font-mono bg-white/20 text-white">11</span>
                </button>

                <button type="button" data-filter="relief" class="program-tab-btn px-5 py-2.5 rounded-full text-xs sm:text-sm font-bold transition-all duration-300 flex items-center gap-2 bg-white text-ink-700 hover:text-forest-700 hover:bg-sand-100 border border-sand-200">
                    <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                    <span>{{ \App\Support\SiteSettings::content('home.programs_cat_relief') }}</span>
                    <span class="ms-1 px-2 py-0.5 rounded-full text-[11px] font-mono bg-sand-100 text-ink-700">5</span>
                </button>

                <button type="button" data-filter="development" class="program-tab-btn px-5 py-2.5 rounded-full text-xs sm:text-sm font-bold transition-all duration-300 flex items-center gap-2 bg-white text-ink-700 hover:text-forest-700 hover:bg-sand-100 border border-sand-200">
                    <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                    <span>{{ \App\Support\SiteSettings::content('home.programs_cat_development') }}</span>
                    <span class="ms-1 px-2 py-0.5 rounded-full text-[11px] font-mono bg-sand-100 text-ink-700">3</span>
                </button>

                <button type="button" data-filter="protection" class="program-tab-btn px-5 py-2.5 rounded-full text-xs sm:text-sm font-bold transition-all duration-300 flex items-center gap-2 bg-white text-ink-700 hover:text-forest-700 hover:bg-sand-100 border border-sand-200">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    <span>{{ \App\Support\SiteSettings::content('home.programs_cat_protection') }}</span>
                    <span class="ms-1 px-2 py-0.5 rounded-full text-[11px] font-mono bg-sand-100 text-ink-700">3</span>
                </button>
            </div>

            {{-- Programs Cards Grid (Rich Visual Cards) --}}
            <div id="programs-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 transition-all duration-300">
                @foreach ($programs as $index => $prog)
                    @php
                        $category = $prog->category ?: 'relief';
                        $badge = $prog->badge;
                        $highlight = $prog->highlight;
                        $image = $prog->image ?: 'images/programs/water.jpg';
                    @endphp

                    <article
                        class="program-card group relative bg-white rounded-3xl overflow-hidden border border-sand-200/90 shadow-sm hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 flex flex-col justify-between"
                        data-category="{{ $category }}"
                        data-reveal
                    >
                        <div>
                            {{-- Card Image Backdrop --}}
                            <div class="relative h-60 overflow-hidden bg-teal-950">
                                <img
                                    src="{{ asset($image) }}"
                                    alt="{{ $prog->title }}"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-108"
                                    loading="lazy"
                                >
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>

                                {{-- Category Pill --}}
                                <div class="absolute top-4 start-4">
                                    <span class="rounded-full bg-forest-700/90 backdrop-blur-md px-3.5 py-1 text-xs font-semibold text-white shadow-md">
                                        @if($category === 'relief')
                                            {{ \App\Support\SiteSettings::content('home.programs_cat_relief') }}
                                        @elseif($category === 'development')
                                            {{ \App\Support\SiteSettings::content('home.programs_cat_development') }}
                                        @else
                                            {{ \App\Support\SiteSettings::content('home.programs_cat_protection') }}
                                        @endif
                                    </span>
                                </div>

                                {{-- Key Badge --}}
                                @if ($badge)
                                    <div class="absolute top-4 end-4">
                                        <span class="rounded-full bg-teal-950/80 backdrop-blur-md border border-teal-700/80 px-3 py-1 text-xs font-mono font-bold text-gold-400 shadow-md">
                                            {{ $badge }}
                                        </span>
                                    </div>
                                @endif

                                {{-- Highlight Banner --}}
                                @if ($highlight)
                                    <div class="absolute bottom-3 start-4 end-4 text-white text-xs font-mono font-medium drop-shadow-md">
                                        <span class="inline-block px-2.5 py-0.5 rounded-lg bg-black/40 backdrop-blur-sm border border-white/10">
                                            {{ $highlight }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            {{-- Card Content --}}
                            <div class="p-6 sm:p-7">
                                <h3 class="text-xl sm:text-2xl font-bold text-ink-900 group-hover:text-forest-700 transition-colors duration-300 font-sans mb-3">
                                    {{ $prog->title }}
                                </h3>
                                @if ($prog->description)
                                    <p class="text-sm sm:text-base text-ink-700/85 leading-relaxed line-clamp-3 font-sans mb-5">
                                        {{ $prog->description }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        {{-- Card Footer Actions --}}
                        <div class="p-6 sm:p-7 pt-0 border-t border-sand-200/80 flex items-center justify-between mt-auto">
                            <a href="{{ route('donate') }}" class="btn-primary !px-5 !py-2.5 !text-xs !font-bold flex items-center gap-1.5 shadow-md">
                                <span>{{ \App\Support\SiteSettings::content('home.programs_donate_action') }}</span>
                                <span aria-hidden="true" class="rtl:rotate-180 font-mono">&rarr;</span>
                            </a>
                            <a href="{{ route('programs') }}" class="inline-flex items-center gap-1 text-xs font-bold text-forest-700 hover:text-forest-800 transition-colors">
                                <span>{{ \App\Support\SiteSettings::content('home.programs_details_action') }}</span>
                                <span aria-hidden="true" class="rtl:rotate-180">&rarr;</span>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Full Spectrum Overview & Direct Institutional Banner --}}
            <div class="mt-14 rounded-3xl bg-teal-950 bg-bader-green-deep text-sand-50 p-7 sm:p-10 border border-teal-800/80 relative overflow-hidden shadow-2xl" data-reveal>
                <div class="pointer-events-none absolute -end-20 top-0 h-72 w-72 rounded-full bg-gold-500/10 blur-3xl" aria-hidden="true"></div>
                <div class="relative flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                    <div class="max-w-2xl">
                        <div class="inline-flex items-center gap-2 text-xs font-mono font-bold text-gold-400 mb-2">
                            <span class="h-2 w-2 rounded-full bg-gold-400"></span>
                            <span>{{ \App\Support\SiteSettings::content('home.programs_all_badge') }}</span>
                        </div>
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-sand-50 font-sans tracking-tight">
                            {{ \App\Support\SiteSettings::content('home.programs_explore_more_desc') }}
                        </h3>
                        <p class="mt-2 text-sm sm:text-base text-sand-100/80 leading-relaxed font-sans">
                            {{ \App\Support\SiteSettings::content('home.programs_overview_text') }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-4 shrink-0">
                        <a href="{{ route('programs') }}" class="btn-primary !px-7 !py-4 text-base font-bold shadow-xl shadow-gold-500/20">
                            <span>{{ \App\Support\SiteSettings::content('home.programs_view_all_btn') }}</span>
                            <span aria-hidden="true" class="rtl:rotate-180">&rarr;</span>
                        </a>
                        <a href="{{ route('donate') }}" class="btn-secondary !border-teal-700 !text-sand-50 hover:!bg-teal-900 !px-6 !py-4 text-base font-semibold">
                            <span>{{ __('nav.donate') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Interactive Tab Filtering Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const campaignCarousel = document.querySelector('[data-campaign-carousel]');

            if (campaignCarousel) {
                const campaignTrack = campaignCarousel.querySelector('[data-campaign-track]');
                const campaignSlides = [...campaignTrack.querySelectorAll('[data-campaign-slide]')];
                const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
                let currentSlide = 0;
                let userPaused = reducedMotion.matches;
                let isHovered = false;
                let isFocused = false;

                const renderSlide = (index, animate = true) => {
                    currentSlide = (index + campaignSlides.length) % campaignSlides.length;
                    campaignTrack.classList.toggle('!transition-none', !animate);
                    campaignTrack.style.transform = `translateX(${-currentSlide * campaignCarousel.clientWidth}px)`;

                    campaignSlides.forEach((slide, slideIndex) => {
                        const isActive = slideIndex === currentSlide;
                        slide.setAttribute('aria-hidden', String(!isActive));
                        slide.inert = !isActive;
                    });
                };

                campaignCarousel.addEventListener('pointerenter', () => { isHovered = true; });
                campaignCarousel.addEventListener('pointerleave', () => { isHovered = false; });
                campaignCarousel.addEventListener('focusin', () => { isFocused = true; });
                campaignCarousel.addEventListener('focusout', () => {
                    window.setTimeout(() => { isFocused = campaignCarousel.contains(document.activeElement); }, 0);
                });
                window.addEventListener('resize', () => renderSlide(currentSlide, false));
                reducedMotion.addEventListener('change', (event) => { userPaused = event.matches; });

                renderSlide(0, false);

                if (campaignSlides.length > 1) {
                    window.setInterval(() => {
                        if (!userPaused && !isHovered && !isFocused && !document.hidden) {
                            renderSlide(currentSlide + 1);
                        }
                    }, 3500);
                }
            }

            const tabButtons = document.querySelectorAll('#programs-tab-bar .program-tab-btn');
            const programCards = document.querySelectorAll('#programs-grid .program-card');

            if (!tabButtons.length || !programCards.length) return;

            tabButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    const filter = this.getAttribute('data-filter');

                    // Switch active button styling
                    tabButtons.forEach(b => {
                        b.classList.remove('active', 'bg-forest-700', 'text-white', 'border-forest-700', 'shadow-md', 'shadow-forest-700/20');
                        b.classList.add('bg-white', 'text-ink-700', 'border-sand-200');
                    });
                    this.classList.add('active', 'bg-forest-700', 'text-white', 'border-forest-700', 'shadow-md', 'shadow-forest-700/20');
                    this.classList.remove('bg-white', 'text-ink-700', 'border-sand-200');

                    // Filter cards with smooth fade
                    programCards.forEach(card => {
                        const cardCat = card.getAttribute('data-category');
                        if (filter === 'all' || cardCat === filter) {
                            card.style.display = 'flex';
                            setTimeout(() => {
                                card.style.opacity = '1';
                                card.style.transform = 'translateY(0)';
                            }, 30);
                        } else {
                            card.style.opacity = '0';
                            card.style.transform = 'translateY(8px)';
                            setTimeout(() => {
                                card.style.display = 'none';
                            }, 200);
                        }
                    });
                });
            });
        });
    </script>

    {{-- ══════════════════════════════════════════════════════════
         5. INSTITUTIONAL CALL TO ACTION (Warm Sand Banner)
         ══════════════════════════════════════════════════════════ --}}
    <section class="bg-sand-100 border-t border-sand-200 section-pad text-center">
        <div class="max-w-3xl mx-auto" data-reveal>
            <div class="flex justify-center mb-4">
                <div class="h-14 w-14 rounded-2xl bg-teal-950 text-gold-400 flex items-center justify-center shadow-lg">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>

            <p class="eyebrow mb-2 text-forest-700 font-bold">{{ __('brand.name') }}</p>
            <h2 class="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold text-ink-900 leading-tight">
                {{ \App\Support\SiteSettings::content('home.cta_title') }}
            </h2>
            <p class="mx-auto mt-4 max-w-2xl text-base sm:text-lg leading-relaxed text-ink-700 font-sans">
                {{ \App\Support\SiteSettings::content('home.cta_text') }}
            </p>
            <div class="mt-8 flex flex-wrap justify-center items-center gap-4">
                <a href="{{ route('donate') }}" class="btn-primary !px-8 !py-4 text-base font-bold shadow-xl shadow-gold-500/20">
                    {{ __('nav.donate') }} &rarr;
                </a>
                <a href="{{ route('partners') }}" class="btn-dark !px-8 !py-4 text-base font-bold">
                    {{ \App\Support\SiteSettings::content('home.cta_partner') }}
                </a>
            </div>
        </div>
    </section>
@endsection
