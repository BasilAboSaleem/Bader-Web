@props(['regions'])

@php
    $totalProjects = $regions->sum('campaigns_count');
    $totalFacilities = $regions->sum(fn ($region) => $region->facilities->count());
@endphp

<section id="regions-map" class="band-base section-y scroll-mt-28" aria-labelledby="regions-map-title">
    <div class="container-bader">
        <div class="max-w-2xl" data-reveal>
            <p class="kicker">{{ __('home.map.kicker') }}</p>
            <h2 id="regions-map-title" class="section-title mt-3">{{ __('home.map.title') }}</h2>
            <p class="section-lead mt-3">{{ __('home.map.text') }}</p>
        </div>

        @if ($regions->isNotEmpty())
            <div class="mt-10 grid items-start gap-8 lg:grid-cols-[minmax(0,22rem)_minmax(0,1fr)]" data-region-map data-reveal>
                <div class="relative mx-auto aspect-[5/8] w-full max-w-[22rem] rounded-3xl bg-gradient-to-br from-sky-50 to-paper-2 p-4 shadow-card-sm ring-1 ring-hairline">
                    <span class="absolute start-4 top-1/2 -translate-y-1/2 text-[0.65rem] font-bold tracking-wider text-sky-700/60 [writing-mode:vertical-rl]">{{ __('home.map.sea') }}</span>
                    <div class="relative h-full w-full">
                        <svg viewBox="0 0 100 100" preserveAspectRatio="none" class="absolute inset-0 h-full w-full" aria-hidden="true">
                            <path class="region-map-land" d="M80 1 L93 6 L89 16 L81 30 L71 46 L61 62 L53 76 L46 88 L42 99 L12 97 L7 92 L15 81 L25 66 L35 50 L45 34 L55 18 L66 6 Z" />
                            <path class="region-map-border" d="M80 1 L93 6 L89 16 L81 30 L71 46 L61 62 L53 76 L46 88 L42 99" />
                        </svg>

                        @foreach ($regions as $region)
                            <button type="button"
                                class="region-pin"
                                style="left: {{ $region->map_x }}%; top: {{ $region->map_y }}%"
                                data-region-pin
                                data-region="{{ $region->key }}"
                                aria-pressed="false"
                                aria-controls="region-panel-{{ $region->key }}">
                                <span class="region-pin-dot"></span>
                                <span class="region-pin-label">{{ $region->name }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="min-w-0">
                    <div class="mb-4 flex flex-wrap gap-2">
                        <button type="button" class="chip" data-region-pin data-region="all" aria-pressed="true" aria-controls="region-panel-all">{{ __('home.map.all') }}</button>
                        @foreach ($regions as $region)
                            <button type="button" class="chip" data-region-pin data-region="{{ $region->key }}" aria-pressed="false" aria-controls="region-panel-{{ $region->key }}">{{ $region->name }}</button>
                        @endforeach
                    </div>

                    <div id="region-panel-all" class="surface-card p-6 sm:p-8" data-region-panel data-region="all" aria-live="polite">
                        <h3 class="text-xl font-extrabold text-ink-900">{{ __('home.map.overview_title') }}</h3>
                        <dl class="mt-5 grid grid-cols-3 gap-3 text-center">
                            <div class="rounded-2xl bg-paper-2 p-4">
                                <dt class="text-xs text-subtle">{{ __('home.map.regions') }}</dt>
                                <dd class="mt-1 text-2xl font-extrabold text-forest-700">{{ $regions->count() }}</dd>
                            </div>
                            <div class="rounded-2xl bg-paper-2 p-4">
                                <dt class="text-xs text-subtle">{{ __('home.map.projects') }}</dt>
                                <dd class="mt-1 text-2xl font-extrabold text-forest-700">{{ $totalProjects }}</dd>
                            </div>
                            <div class="rounded-2xl bg-paper-2 p-4">
                                <dt class="text-xs text-subtle">{{ __('home.map.facilities') }}</dt>
                                <dd class="mt-1 text-2xl font-extrabold text-forest-700">{{ $totalFacilities }}</dd>
                            </div>
                        </dl>
                        <ul class="mt-6 divide-y divide-hairline">
                            @foreach ($regions as $region)
                                <li class="flex items-center justify-between gap-3 py-3">
                                    <span class="inline-flex items-center gap-2 font-bold text-ink-900">
                                        <x-bader.icon name="map-pin" class="h-4 w-4 text-forest-600" />
                                        {{ $region->name }}
                                    </span>
                                    <span class="text-xs text-subtle">{{ trans_choice('region.projects_count', $region->campaigns_count, ['count' => $region->campaigns_count]) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    @foreach ($regions as $region)
                        <div id="region-panel-{{ $region->key }}" class="surface-card p-6 sm:p-8" data-region-panel data-region="{{ $region->key }}" hidden>
                            <p class="inline-flex items-center gap-2 text-xs font-extrabold text-forest-700">
                                <x-bader.icon name="map-pin" class="h-4 w-4" />
                                {{ __('home.map.region_label') }}
                            </p>
                            <h3 class="mt-2 text-2xl font-extrabold text-ink-900">{{ $region->name }}</h3>
                            @if ($region->description)
                                <p class="mt-3 leading-relaxed text-muted">{{ $region->description }}</p>
                            @endif
                            <div class="mt-5 flex flex-wrap gap-3 text-sm font-bold">
                                <span class="rounded-full bg-paper-2 px-3 py-1.5 text-ink-800">{{ trans_choice('region.projects_count', $region->campaigns_count, ['count' => $region->campaigns_count]) }}</span>
                                <span class="rounded-full bg-paper-2 px-3 py-1.5 text-ink-800">{{ trans_choice('region.facilities_count', $region->facilities->count(), ['count' => $region->facilities->count()]) }}</span>
                            </div>
                            @if ($region->facilities->isNotEmpty())
                                <ul class="mt-5 grid gap-2 sm:grid-cols-2">
                                    @foreach ($region->facilities as $facility)
                                        <li>
                                            <a href="{{ route('facilities.show', $facility->key) }}" class="flex items-center gap-2 rounded-xl border border-hairline p-3 text-sm font-bold text-ink-800 transition hover:border-forest-600 hover:text-forest-700">
                                                <x-bader.icon name="building" class="h-4 w-4 shrink-0 text-forest-600" />
                                                {{ $facility->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            <a href="{{ route('regions.show', $region->key) }}" class="btn-brand mt-6">
                                {{ __('home.map.view_region') }}
                                <x-bader.icon name="arrow" class="h-4 w-4" />
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
