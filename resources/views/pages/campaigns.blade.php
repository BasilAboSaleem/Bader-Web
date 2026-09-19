@extends('layouts.public')

@section('title', __('page.campaigns.title').' — '.__('brand.name'))

@section('content')
    <section class="bg-bader-green-deep text-white">
        <div class="mx-auto max-w-6xl px-4 py-16 sm:py-20">
            <x-bader.section-heading :kicker="__('page.campaigns.kicker')" :title="__('page.campaigns.title')" theme="dark" />
            <p class="mt-5 max-w-2xl text-sm leading-relaxed text-white/75 sm:text-base">{{ __('page.campaigns.intro') }}</p>
        </div>
    </section>

    <section class="bg-bader-paper">
        <div class="mx-auto max-w-6xl px-4 py-14 sm:py-18">
            <div class="grid gap-6 md:grid-cols-2">
                @foreach ($campaigns as $campaign)
                    <article class="overflow-hidden rounded-3xl bg-bader-green text-white" data-reveal>
                        <div class="p-6 sm:p-8">
                            <span class="mb-4 inline-flex rounded-full bg-bader-lime px-3 py-1 text-xs font-semibold text-bader-green-deep">{{ __('campaign.status_active') }}</span>
                            <h2 class="text-2xl font-semibold">{{ __('campaign.'.$campaign['key']) }}</h2>
                            <p class="mt-3 text-sm leading-relaxed text-white/80">{{ __('campaign.'.$campaign['key'].'_text') }}</p>
                            @if ($campaign['goal'])
                                <p class="mt-5 text-sm text-white/70">{{ __('campaign.goal') }} <strong class="text-lg text-bader-lime">{{ $campaign['goal'] }} {{ __($campaign['currency']) }}</strong></p>
                            @else
                                <p class="mt-5 text-sm text-white/70">{{ __('page.campaigns.details_pending') }}</p>
                            @endif
                            <a href="{{ route('donate') }}" class="mt-6 inline-block text-sm font-semibold text-bader-lime">{{ __('nav.donate') }}</a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endsection
