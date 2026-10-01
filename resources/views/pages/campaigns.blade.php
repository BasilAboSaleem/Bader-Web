@extends('layouts.public')

@section('title', __('page.campaigns.title').' — '.__('brand.name'))
@section('meta_description', __('page.campaigns.intro'))

@php
    $filterUrl = fn (array $overrides) => route('campaigns', array_filter(array_merge([
        'program' => $selectedProgram?->key,
        'region' => $selectedRegion?->key,
    ], $overrides)));
@endphp

@section('content')
    <x-bader.page-hero
        :kicker="__('page.campaigns.kicker')"
        :title="__('page.campaigns.title')"
        :intro="__('page.campaigns.intro')"
    />

    <section class="band-base section-y">
        <div class="container-bader">
            <div class="space-y-4" data-reveal>
                @if ($programs->isNotEmpty())
                    <nav aria-label="{{ __('campaigns_page.filter_program') }}">
                        <p class="field-label">{{ __('campaigns_page.filter_program') }}</p>
                        <div class="-mx-4 overflow-x-auto px-4 pb-1 [scrollbar-width:none]">
                            <ul class="flex w-max gap-2">
                                <li><a href="{{ $filterUrl(['program' => null]) }}" class="chip {{ $selectedProgram ? '' : 'is-active' }}" @unless ($selectedProgram) aria-current="true" @endunless>{{ __('campaigns_page.all') }}</a></li>
                                @foreach ($programs as $program)
                                    @php $isSelected = $selectedProgram?->is($program); @endphp
                                    <li><a href="{{ $filterUrl(['program' => $program->key]) }}" class="chip whitespace-nowrap {{ $isSelected ? 'is-active' : '' }}" @if ($isSelected) aria-current="true" @endif>{{ $program->title }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </nav>
                @endif

                @if ($regions->isNotEmpty())
                    <nav aria-label="{{ __('campaigns_page.filter_region') }}">
                        <p class="field-label">{{ __('campaigns_page.filter_region') }}</p>
                        <div class="-mx-4 overflow-x-auto px-4 pb-1 [scrollbar-width:none]">
                            <ul class="flex w-max gap-2">
                                <li><a href="{{ $filterUrl(['region' => null]) }}" class="chip {{ $selectedRegion ? '' : 'is-active' }}" @unless ($selectedRegion) aria-current="true" @endunless>{{ __('campaigns_page.all_regions') }}</a></li>
                                @foreach ($regions as $region)
                                    @php $isSelected = $selectedRegion?->is($region); @endphp
                                    <li>
                                        <a href="{{ $filterUrl(['region' => $region->key]) }}" class="chip whitespace-nowrap {{ $isSelected ? 'is-active' : '' }}" @if ($isSelected) aria-current="true" @endif>
                                            <x-bader.icon name="map-pin" class="h-3.5 w-3.5" />
                                            {{ $region->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </nav>
                @endif
            </div>

            <p class="mt-8 text-sm font-semibold text-subtle" aria-live="polite">
                {{ trans_choice('region.projects_count', $campaigns->total(), ['count' => $campaigns->total()]) }}
            </p>

            @if ($campaigns->isNotEmpty())
                <div class="mt-4 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($campaigns as $campaign)
                        <x-bader.project-card :campaign="$campaign" />
                    @endforeach
                </div>

                @if ($campaigns->hasPages())
                    <div class="mt-10">{{ $campaigns->links() }}</div>
                @endif
            @else
                <div class="mt-4 rounded-3xl border border-dashed border-hairline-strong bg-white p-10 text-center">
                    <x-bader.icon name="grid" class="mx-auto h-10 w-10 text-forest-600" />
                    <p class="mt-4 text-muted">{{ __('campaigns_page.empty') }}</p>
                    <div class="mt-6 flex flex-wrap justify-center gap-3">
                        @if ($selectedProgram || $selectedRegion)
                            <a href="{{ route('campaigns') }}" class="btn-outline">{{ __('campaigns_page.clear_filters') }}</a>
                        @endif
                        <a href="{{ route('donate') }}" class="btn-primary">{{ __('nav.donate') }}</a>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <section class="band-tint section-y text-center">
        <div class="container-bader max-w-2xl" data-reveal>
            <h2 class="section-title">{{ __('home.support_title') }}</h2>
            <p class="section-lead mt-3">{{ __('home.support_intro') }}</p>
            <div class="mt-7 flex flex-wrap justify-center gap-3">
                <a href="{{ route('donate') }}" class="btn-primary">{{ __('nav.donate') }}</a>
                <a href="{{ route('contact') }}" class="btn-brand">{{ __('nav.contact') }}</a>
            </div>
        </div>
    </section>
@endsection
