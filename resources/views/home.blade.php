@extends('layouts.public')

@section('title', __('brand.name'))
@section('headerTheme', 'dark')

@section('content')
    <section class="relative overflow-hidden bg-bader-green-deep text-bader-on-dark">
        <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="" class="pointer-events-none absolute -end-24 top-8 h-80 w-80 opacity-[0.08]" aria-hidden="true">
        <div class="relative mx-auto max-w-4xl px-4 pb-24 pt-20 sm:pb-28 sm:pt-28">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-bader-lime">{{ __('home.hero_kicker') }}</p>
                <h1 class="mt-5 max-w-4xl text-4xl font-semibold leading-[1.08] text-white sm:text-6xl lg:text-7xl">{{ __('home.hero_title') }}</h1>
                <p class="mt-6 max-w-2xl text-base leading-relaxed text-white/75 sm:text-lg">{{ __('home.hero_text') }}</p>
                <p class="mt-4 text-sm font-semibold text-bader-lime">{{ __('brand.locations') }}</p>
                <div class="mt-9 flex flex-wrap gap-4">
                    <x-bader.button :href="route('donate')" variant="lime">{{ __('nav.donate') }}</x-bader.button>
                    <x-bader.button :href="route('about')" variant="ghost-light">{{ __('home.hero_secondary') }}</x-bader.button>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-bader-green text-white" aria-labelledby="impact-strip-title">
        <div class="mx-auto grid max-w-7xl grid-cols-2 gap-8 px-4 py-12 sm:grid-cols-4 sm:gap-10">
            @if (isset($approvedMetrics) && $approvedMetrics->isNotEmpty())
                @foreach ($approvedMetrics as $metric)
                    <div data-reveal>
                        <p class="text-4xl font-semibold text-bader-lime">{{ $metric->value }} <span class="text-sm font-normal text-white/80">{{ $metric->unit() }}</span></p>
                        <p class="mt-2 text-sm text-white/70">{{ $metric->title() }}</p>
                    </div>
                @endforeach
            @else
                @foreach ($impacts as $impact)
                    <div data-reveal>
                        <p class="text-4xl font-semibold text-bader-lime">{{ __('home.impact_empty') }}</p>
                        <p class="mt-2 text-sm text-white/70">{{ __('impact.'.$impact) }}</p>
                    </div>
                @endforeach
            @endif
        </div>
    </section>

    <section class="bg-bader-paper" data-home-section="facilities">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:py-20">
            <x-bader.section-heading :kicker="__('home.facilities_kicker')" :title="__('home.facilities_title')" />
            <div class="mt-12 grid gap-6 sm:grid-cols-3">
                @foreach ($facilities as $facility)
                    <x-bader.program-card
                        :title="__('facility.'.$facility)"
                        :text="__('facility.'.$facility.'_text')"
                        :href="route('about')"
                    />
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-white" data-home-section="programs">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:py-20">
        <x-bader.section-heading :kicker="__('home.programs_kicker')" :title="__('home.programs_title')" />
        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($programs as $program)
                <x-bader.program-card
                    :title="__('program.'.$program)"
                    :text="__('program.'.$program.'_text')"
                    :href="route('programs')"
                />
            @endforeach
        </div>
        </div>
    </section>

    <x-bader.star-divider />

    <section class="mx-auto max-w-6xl px-4 pb-14" data-home-section="campaign">
        <x-bader.section-heading :kicker="__('home.campaign_kicker')" :title="__('home.campaign_title')" class="mb-6" />
        <x-bader.campaign-card
            :title="__('campaign.'.$campaign['key'])"
            :text="__('campaign.'.$campaign['key'].'_text')"
            :status="__('campaign.status_active')"
            :goal="$campaign['goal']"
            :currency="__($campaign['currency'])"
            :href="route('campaigns')"
        />
    </section>

    <section class="bg-bader-green-deep text-white" data-home-section="stories">
        <div class="mx-auto max-w-6xl px-4 py-14 sm:py-16">
            <div class="flex flex-wrap items-end justify-between gap-5">
                <x-bader.section-heading :kicker="__('home.stories_kicker')" :title="__('home.stories_title')" theme="dark" />
                <a href="{{ route('news') }}" class="text-sm font-semibold text-bader-lime hover:text-white">{{ __('home.stories_link') }}</a>
            </div>
            <div class="mt-10 grid gap-5 md:grid-cols-3">
                @foreach ($stories as $story)
                    <x-bader.story-card
                        :title="__('story.'.$story['key'].'.title')"
                        :excerpt="__('story.'.$story['key'].'.excerpt')"
                        :category="__('story.'.$story['key'].'.category')"
                        :date="$story['date']"
                        :href="route('news')"
                    />
                @endforeach
            </div>
        </div>
    </section>

    <section class="border-y border-bader-green/10 bg-white">
        <div class="mx-auto max-w-6xl px-4 py-14">
            <x-bader.section-heading :kicker="__('home.impact_kicker')" :title="__('home.impact_title')" align="center" />
            <div class="mt-10 grid grid-cols-2 gap-6 sm:grid-cols-4">
                @foreach ($impacts as $impact)
                    <div class="text-center" data-reveal>
                        <p class="text-3xl font-semibold text-bader-green">{{ __('home.impact_empty') }}</p>
                        <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-bader-ink/55">{{ __('impact.'.$impact) }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-3xl px-4 py-16 text-center" data-reveal>
        <h2 class="text-2xl font-semibold sm:text-3xl">{{ __('home.cta_title') }}</h2>
        <p class="mx-auto mt-3 max-w-xl text-sm leading-relaxed text-bader-ink/70">{{ __('home.cta_text') }}</p>
        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <x-bader.button :href="route('donate')">{{ __('nav.donate') }}</x-bader.button>
            <x-bader.button :href="route('partners')" variant="line">{{ __('home.cta_partner') }}</x-bader.button>
        </div>
    </section>
@endsection
