@extends('layouts.public')

@section('title', __('page.donate.title').' — '.__('brand.name'))

@section('content')
    <section class="bg-bader-green-deep text-white">
        <div class="mx-auto max-w-6xl px-4 py-16 sm:py-20">
            <x-bader.section-heading :kicker="__('page.donate.kicker')" :title="__('page.donate.title')" theme="dark" />
            <p class="mt-5 max-w-2xl text-sm leading-relaxed text-white/75 sm:text-base">{{ __('page.donate.intro') }}</p>
        </div>
    </section>

    <section class="bg-bader-paper">
        <div class="mx-auto grid max-w-6xl gap-8 px-4 py-14 lg:grid-cols-[1.2fr_0.8fr]">
            <article class="rounded-3xl bg-bader-green p-6 text-white sm:p-8" data-reveal>
                <span class="inline-flex rounded-full bg-bader-lime px-3 py-1 text-xs font-semibold text-bader-green-deep">{{ __('campaign.status_active') }}</span>
                <h2 class="mt-5 text-2xl font-semibold">{{ __('campaign.'.$campaignKey) }}</h2>
                <p class="mt-3 text-sm leading-relaxed text-white/80">{{ __('campaign.'.$campaignKey.'_text') }}</p>
                <p class="mt-6 text-sm text-white/70">{{ __('campaign.goal') }} <strong class="text-lg text-bader-lime">120,000 {{ __('campaign.currency') }}</strong></p>
            </article>
            <aside class="rounded-2xl border border-bader-green/10 bg-white p-6" data-reveal>
                <h2 class="text-xl font-semibold text-bader-green">{{ __('page.donate.manual_title') }}</h2>
                <p class="mt-3 text-sm leading-relaxed text-bader-ink/70">{{ __('page.donate.manual_text') }}</p>
                <a href="{{ route('contact') }}" class="mt-6 inline-block text-sm font-semibold text-bader-green">{{ __('nav.contact') }}</a>
            </aside>
        </div>
    </section>
@endsection
