@extends('layouts.public')

@section('title', __('page.campaigns.title').' — '.__('brand.name'))

@section('content')
    <x-bader.page-hero
        :kicker="__('page.campaigns.kicker')"
        :title="__('page.campaigns.title')"
        :intro="__('page.campaigns.intro')"
    />

    <section class="bg-sand-50 section-pad">
        <div class="mx-auto max-w-6xl">
            <div class="grid gap-6 md:grid-cols-2">
                @foreach ($campaigns as $campaign)
                    @php
                        $isModel = $campaign instanceof \App\Models\Campaign;
                        $key = $isModel ? $campaign->key : $campaign['key'];
                        $title = $isModel ? $campaign->title : __('campaign.'.$key);
                        $desc = $isModel ? $campaign->description : __('campaign.'.$key.'_text');
                        $goal = $isModel ? ($campaign->goal_amount ? number_format($campaign->goal_amount) : null) : $campaign['goal'];
                        $currency = $isModel ? $campaign->currency : __($campaign['currency'] ?? 'campaign.currency');
                    @endphp
                    <article class="overflow-hidden rounded-3xl bg-teal-900 border border-teal-800 p-8 text-sand-50 shadow-lg flex flex-col justify-between" data-reveal>
                        <div>
                            <span class="mb-4 inline-flex rounded-full bg-gold-500/20 border border-gold-500/40 px-3 py-1 text-xs font-bold text-gold-300">{{ __('campaign.status_active') }}</span>
                            <h2 class="font-display text-2xl font-bold text-white">{{ $title }}</h2>
                            <p class="mt-3 text-sm leading-relaxed text-sand-100/80">{{ $desc }}</p>
                        </div>
                        <div class="mt-6 pt-5 border-t border-teal-800/80 flex items-center justify-between flex-wrap gap-4">
                            @if ($goal)
                                <p class="text-sm text-sand-100/70">{{ __('campaign.goal') }}: <strong class="text-lg text-gold-400 font-mono">{{ $goal }} {{ $currency }}</strong></p>
                            @else
                                <p class="text-sm text-sand-100/70">{{ __('page.campaigns.details_pending') }}</p>
                            @endif
                            <a href="{{ route('donate') }}" class="btn-primary !px-6 !py-2.5 text-xs sm:text-sm font-bold shadow-md">{{ __('nav.donate') }}</a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="border-t border-sand-200 bg-sand-100 section-pad text-center">
        <div class="mx-auto max-w-2xl" data-reveal>
            <h2 class="font-display text-2xl font-bold text-ink-900 sm:text-3xl">{{ __('home.support_title') }}</h2>
            <p class="mt-3 text-sm leading-relaxed text-ink-700/75">{{ __('home.support_intro') }}</p>
            <div class="mt-7 flex flex-wrap justify-center gap-3">
                <a href="{{ route('donate') }}" class="btn-primary">{{ __('nav.donate') }}</a>
                <a href="{{ route('contact') }}" class="btn-dark">{{ __('nav.contact') }}</a>
            </div>
        </div>
    </section>
@endsection
