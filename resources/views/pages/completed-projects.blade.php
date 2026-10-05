@extends('layouts.public')

@section('title', __('completed_project_page.title').' — '.__('brand.name'))
@section('meta_description', __('completed_project_page.intro'))

@php
    $filterUrl = fn (array $overrides) => route('completed-projects', array_filter(array_merge([
        'program' => $selectedProgram?->key,
        'region' => $selectedRegion?->key,
    ], $overrides)));
@endphp

@section('content')
    <x-bader.page-hero
        :kicker="__('completed_project_page.kicker')"
        :title="__('completed_project_page.title')"
        :intro="__('completed_project_page.intro')"
    >
        <dl class="mt-6 flex flex-wrap gap-3">
            <div class="rounded-2xl bg-white px-5 py-3 ring-1 ring-hairline">
                <dt class="text-xs text-subtle">{{ __('completed_project_page.total_projects') }}</dt>
                <dd class="text-2xl font-extrabold text-forest-700">{{ number_format((int) $totals->projects_count) }}</dd>
            </div>
            @if ((int) $totals->beneficiaries_total > 0)
                <div class="rounded-2xl bg-white px-5 py-3 ring-1 ring-hairline">
                    <dt class="text-xs text-subtle">{{ __('completed_project_page.total_beneficiaries') }}</dt>
                    <dd class="text-2xl font-extrabold text-forest-700">{{ number_format((int) $totals->beneficiaries_total) }}</dd>
                </div>
            @endif
        </dl>
        <a href="{{ route('impact-map') }}" class="btn-outline mt-5">
            <x-bader.icon name="map-pin" class="h-4 w-4" />
            {{ __('header.view_map') }}
        </a>
    </x-bader.page-hero>

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

            @if ($completedProjects->isNotEmpty())
                <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($completedProjects as $completedProject)
                        <x-bader.completed-project-card :project="$completedProject" data-reveal style="animation-delay: {{ ($loop->index % 3) * 80 }}ms" />
                    @endforeach
                </div>

                @if ($completedProjects->hasPages())
                    <div class="mt-10">{{ $completedProjects->links() }}</div>
                @endif
            @else
                <div class="mt-8 rounded-3xl border border-dashed border-hairline-strong bg-white p-10 text-center">
                    <x-bader.icon name="badge-check" class="mx-auto h-10 w-10 text-forest-600" />
                    <p class="mt-4 text-muted">{{ __('completed_project_page.empty') }}</p>
                    @if ($selectedProgram || $selectedRegion)
                        <a href="{{ route('completed-projects') }}" class="btn-outline mt-6">{{ __('campaigns_page.clear_filters') }}</a>
                    @endif
                </div>
            @endif
        </div>
    </section>

    <section class="band-tint section-y text-center">
        <div class="container-bader max-w-2xl" data-reveal>
            <h2 class="section-title">{{ __('home.support_title') }}</h2>
            <p class="section-lead mt-3">{{ __('home.support_intro') }}</p>
            <div class="mt-7 flex flex-wrap justify-center gap-3">
                <a href="{{ route('campaigns') }}" class="btn-primary">{{ __('header.all_projects') }}</a>
                <a href="{{ route('donate') }}" class="btn-brand">{{ __('nav.donate') }}</a>
            </div>
        </div>
    </section>
@endsection
