@extends('layouts.public')

@use('App\Support\Money')

@php
    $campaignImage = $campaign->image ? asset($campaign->image) : asset('images/programs/water.jpg');
    $breadcrumbs = [['label' => __('nav.campaigns'), 'url' => route('campaigns')]];
@endphp

@section('title', $campaign->title.' — '.__('brand.name'))
@section('meta_description', \Illuminate\Support\Str::limit((string) $campaign->description, 160))

@section('content')
    <x-bader.page-hero
        :kicker="$campaign->program?->title ?? __('page.campaigns.kicker')"
        :title="$campaign->title"
        :breadcrumbs="$breadcrumbs"
    >
        <div class="mt-5 flex flex-wrap gap-2 text-xs font-bold">
            @if ($campaign->region)
                <a href="{{ route('regions.show', $campaign->region->key) }}" class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1.5 text-ink-800 ring-1 ring-hairline hover:text-forest-700">
                    <x-bader.icon name="map-pin" class="h-3.5 w-3.5" />
                    {{ $campaign->region->name }}
                </a>
            @endif
            @if ($campaign->isOngoing())
                <span class="inline-flex items-center gap-1.5 rounded-full bg-forest-700 px-3 py-1.5 text-white">
                    <x-bader.icon name="repeat" class="h-3.5 w-3.5" />
                    {{ __('project.ongoing') }}
                </span>
            @endif
            <span class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1.5 text-ink-800 ring-1 ring-hairline">
                <x-bader.icon name="users" class="h-3.5 w-3.5" />
                {{ trans_choice('project.donors_count', $campaign->donors_count, ['count' => number_format($campaign->donors_count)]) }}
            </span>
        </div>
    </x-bader.page-hero>

    <section class="band-base section-y">
        <div class="container-bader grid grid-cols-1 items-start gap-8 lg:grid-cols-[minmax(0,1fr)_22rem]">
            <div class="min-w-0">
                <div class="overflow-hidden rounded-3xl bg-paper-3 shadow-card-md" data-reveal>
                    <img src="{{ $campaignImage }}" alt="{{ $campaign->title }}" class="aspect-[16/9] w-full object-cover">
                </div>

                @if ($campaign->description)
                    <p class="mt-8 text-lg font-semibold leading-9 text-ink-800" data-reveal>{{ $campaign->description }}</p>
                @endif

                @if ($campaign->content)
                    <div class="mt-6 space-y-4 leading-8 text-muted" data-reveal>
                        @foreach (preg_split('/\R{2,}/u', trim($campaign->content)) as $paragraph)
                            <p>{!! nl2br(e($paragraph)) !!}</p>
                        @endforeach
                    </div>
                @endif

                <x-bader.media-gallery :model="$campaign" class="mt-10" />

                <dl class="mt-10 grid gap-4 sm:grid-cols-3" data-reveal>
                    @if ($campaign->program)
                        <div class="rounded-2xl border border-hairline bg-white p-5">
                            <dt class="text-xs font-bold text-subtle">{{ __('campaign_page.program') }}</dt>
                            <dd class="mt-1 font-extrabold text-ink-900">
                                <a href="{{ route('programs.show', $campaign->program->key) }}" class="hover:text-forest-700">{{ $campaign->program->title }}</a>
                            </dd>
                        </div>
                    @endif
                    @if ($campaign->region)
                        <div class="rounded-2xl border border-hairline bg-white p-5">
                            <dt class="text-xs font-bold text-subtle">{{ __('campaign_page.region') }}</dt>
                            <dd class="mt-1 font-extrabold text-ink-900">{{ $campaign->region->name }}</dd>
                        </div>
                    @endif
                    <div class="rounded-2xl border border-hairline bg-white p-5">
                        <dt class="text-xs font-bold text-subtle">{{ __('campaign_page.giving_type') }}</dt>
                        <dd class="mt-1 font-extrabold text-ink-900">{{ $campaign->allows_monthly ? __('campaign_page.once_or_monthly') : __('donation.frequency.once') }}</dd>
                    </div>
                </dl>

                <div class="mt-10 flex items-start gap-4 rounded-3xl bg-teal-950 p-6 text-white" data-reveal>
                    <x-bader.icon name="shield" class="h-8 w-8 shrink-0 text-gold-400" />
                    <div>
                        <p class="font-extrabold">{{ __('campaign_page.trust_title') }}</p>
                        <p class="mt-1 text-sm leading-relaxed text-white/75">{{ __('campaign_page.trust_text') }}</p>
                    </div>
                </div>
            </div>

            <aside class="lg:sticky lg:top-28" aria-label="{{ __('donate_box.kicker') }}">
                <x-bader.donate-box
                    :title="$campaign->title"
                    :donate-params="['target_type' => 'campaign', 'target_id' => $campaign->id]"
                    :amounts="$campaign->amount_options"
                    :allows-monthly="$campaign->allows_monthly"
                >
                    @if ($campaign->isOngoing())
                        <p class="flex items-start gap-2 rounded-2xl bg-paper-2 p-4 text-sm text-muted">
                            <x-bader.icon name="repeat" class="mt-0.5 h-4 w-4 shrink-0 text-forest-600" />
                            {{ __('campaign_page.ongoing_note') }}
                        </p>
                    @else
                        <div data-reveal>
                            <div class="flex items-baseline justify-between gap-2">
                                <span class="text-2xl font-extrabold text-ink-900" dir="ltr">{{ Money::format($campaign->raised_amount) }}</span>
                                <span class="text-sm font-extrabold text-forest-700">{{ $campaign->progress_percent }}%</span>
                            </div>
                            <div class="progress-track mt-2" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $campaign->progress_percent }}" aria-label="{{ __('project.progress') }}">
                                <div class="progress-bar" style="width: {{ $campaign->progress_percent }}%"></div>
                            </div>
                            <div class="mt-2 flex justify-between text-xs text-subtle">
                                <span>{{ __('project.goal', ['amount' => Money::format($campaign->goal_amount)]) }}</span>
                                <span>{{ __('campaign_page.remaining', ['amount' => Money::format(max(0, $campaign->goal_amount - $campaign->raised_amount))]) }}</span>
                            </div>
                        </div>
                    @endif
                </x-bader.donate-box>

                <a href="{{ route('gift', ['target_type' => 'campaign', 'target_id' => $campaign->id]) }}" class="mt-4 flex items-center gap-3 rounded-2xl border border-hairline bg-white p-4 text-sm font-bold text-ink-800 transition hover:border-forest-600 hover:text-forest-700">
                    <x-bader.icon name="gift" class="h-5 w-5 text-forest-600" />
                    {{ __('campaign_page.gift_this') }}
                    <x-bader.icon name="arrow" class="ms-auto h-4 w-4" />
                </a>
            </aside>
        </div>
    </section>

    @if ($relatedCampaigns->isNotEmpty())
        <section class="band-tint section-y">
            <div class="container-bader">
                <div class="flex items-end justify-between gap-4" data-reveal>
                    <h2 class="section-title">{{ __('campaign_page.related') }}</h2>
                    <a href="{{ route('campaigns') }}" class="btn-outline">{{ __('header.all_projects') }}</a>
                </div>
                <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($relatedCampaigns as $relatedCampaign)
                        <x-bader.project-card :campaign="$relatedCampaign" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
