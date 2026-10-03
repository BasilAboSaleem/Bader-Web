@extends('layouts.public')

@section('title', $region->name.' — '.__('brand.name'))
@section('meta_description', \Illuminate\Support\Str::limit((string) ($region->description ?: __('home.regions.text')), 160))

@section('content')
    <x-bader.page-hero
        :kicker="__('home.map.region_label')"
        :title="$region->name"
        :intro="$region->description"
        :breadcrumbs="[['label' => __('nav.impact_map'), 'url' => route('impact-map')]]"
    >
        <dl class="mt-6 flex flex-wrap gap-3">
            <div class="rounded-2xl bg-white px-5 py-3 ring-1 ring-hairline">
                <dt class="text-xs text-subtle">{{ __('home.map.projects') }}</dt>
                <dd class="text-2xl font-extrabold text-forest-700">{{ $campaigns->count() }}</dd>
            </div>
            <div class="rounded-2xl bg-white px-5 py-3 ring-1 ring-hairline">
                <dt class="text-xs text-subtle">{{ __('home.map.facilities') }}</dt>
                <dd class="text-2xl font-extrabold text-forest-700">{{ $region->facilities->count() }}</dd>
            </div>
            @foreach ($region->localizedImpactMetrics() as $metric)
                <div class="rounded-2xl bg-white px-5 py-3 ring-1 ring-hairline">
                    <dt class="text-xs text-subtle">{{ $metric['label'] }}</dt>
                    <dd class="text-2xl font-extrabold text-forest-700" dir="ltr">{{ $metric['value'] }}</dd>
                </div>
            @endforeach
        </dl>
        <a href="{{ route('impact-map', ['region' => $region->key]) }}" class="btn-outline mt-5">
            <x-bader.icon name="map-pin" class="h-4 w-4" />
            {{ __('home.map.view_on_map') }}
        </a>
    </x-bader.page-hero>

    <section class="band-base section-y">
        <div class="container-bader">
            <h2 class="section-title" data-reveal>{{ __('region_page.projects_title', ['region' => $region->name]) }}</h2>

            @if ($campaigns->isNotEmpty())
                <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($campaigns as $campaign)
                        <x-bader.project-card :campaign="$campaign" />
                    @endforeach
                </div>
            @else
                <div class="mt-6 rounded-3xl border border-dashed border-hairline-strong bg-white p-8 text-center">
                    <p class="text-muted">{{ __('region_page.no_projects') }}</p>
                    <a href="{{ route('donate') }}" class="btn-primary mt-5">{{ __('nav.donate') }}</a>
                </div>
            @endif
        </div>
    </section>

    @if ($region->facilities->isNotEmpty())
        <section class="band-tint section-y">
            <div class="container-bader">
                <h2 class="section-title" data-reveal>{{ __('region_page.facilities_title') }}</h2>
                <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($region->facilities as $facility)
                        <x-bader.facility-card :facility="$facility" data-reveal style="animation-delay: {{ $loop->index * 80 }}ms" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($cases->isNotEmpty())
        <section class="band-base section-y">
            <div class="container-bader">
                <div class="flex items-end justify-between gap-4" data-reveal>
                    <h2 class="section-title">{{ __('region_page.cases_title') }}</h2>
                    <a href="{{ route('sponsorship', ['region' => $region->key]) }}" class="btn-outline">{{ __('header.all_cases') }}</a>
                </div>
                <div class="mt-6 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($cases as $case)
                        <x-bader.sponsorship-case-card :case="$case" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($otherRegions->isNotEmpty())
        <section class="band-tint py-7">
            <div class="container-bader flex flex-wrap items-center gap-2">
                <span class="me-2 text-sm font-bold text-subtle">{{ __('region_page.other_regions') }}</span>
                @foreach ($otherRegions as $otherRegion)
                    <a href="{{ route('regions.show', $otherRegion->key) }}" class="chip">
                        <x-bader.icon name="map-pin" class="h-3.5 w-3.5" />
                        {{ $otherRegion->name }}
                    </a>
                @endforeach
            </div>
        </section>
    @endif
@endsection
