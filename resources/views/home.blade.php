@extends('layouts.public')

@section('title', __('brand.name'))

@section('content')
    <section class="relative overflow-hidden">
        <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="" class="pointer-events-none absolute -end-16 top-8 h-64 w-64 opacity-[0.07]" aria-hidden="true">
        <div class="mx-auto grid max-w-6xl gap-10 px-4 py-14 lg:grid-cols-[1.2fr_0.8fr] lg:items-center lg:py-20">
            <div data-reveal>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-bader-green">{{ __('home.hero_kicker') }}</p>
                <h1 class="mt-3 max-w-xl text-3xl font-semibold leading-tight text-bader-ink sm:text-5xl">{{ __('home.hero_title') }}</h1>
                <p class="mt-4 max-w-xl text-sm leading-relaxed text-bader-ink/75 sm:text-base">{{ __('home.hero_text') }}</p>
                <p class="mt-3 text-sm font-semibold text-bader-green">{{ __('brand.locations') }}</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <x-bader.button :href="route('donate')">{{ __('nav.donate') }}</x-bader.button>
                    <x-bader.button :href="route('about')" variant="line">{{ __('home.hero_secondary') }}</x-bader.button>
                </div>
            </div>
            <div class="mx-auto w-full max-w-sm rounded-[2rem] bg-white p-8" data-reveal>
                <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="{{ __('brand.name') }}" width="180" height="180" class="mx-auto h-40 w-40">
            </div>
        </div>
    </section>

    <section class="border-y border-bader-green/10 bg-white">
        <div class="mx-auto max-w-6xl px-4 py-14">
            <x-bader.section-heading :kicker="__('home.facilities_kicker')" :title="__('home.facilities_title')" />
            <div class="mt-8 grid gap-4 sm:grid-cols-3">
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

    <section class="mx-auto max-w-6xl px-4 py-14">
        <x-bader.section-heading :kicker="__('home.programs_kicker')" :title="__('home.programs_title')" />
        <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($programs as $program)
                <x-bader.program-card
                    :title="__('program.'.$program)"
                    :text="__('program.'.$program.'_text')"
                    :href="route('programs')"
                />
            @endforeach
        </div>
    </section>

    <x-bader.star-divider />

    <section class="mx-auto max-w-6xl px-4 pb-14">
        <x-bader.section-heading :kicker="__('home.campaign_kicker')" :title="__('home.campaign_title')" class="mb-6" />
        <x-bader.campaign-card
            :title="__('campaign.labbayk')"
            :text="__('campaign.labbayk_text')"
            :status="__('campaign.status_active')"
            :href="route('campaigns')"
        />
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
