@props(['regions', 'selected' => null, 'variant' => 'home'])

@use('App\Support\Money')

@php
    $isPage = $variant === 'page';
    $areas = config('bader.map_areas');
    [$viewWidth, $viewHeight] = config('bader.map_view_box');
    $regionsByArea = $regions->filter(fn ($region) => isset($areas[$region->map_area]))->keyBy('map_area');
    $maxProjects = max(1, (int) $regions->max('campaigns_count'));
    $totalProjects = $regions->sum('campaigns_count');
    $totalFacilities = $regions->sum(fn ($region) => $region->facilities->count());
    $totalRaised = Money::format($regions->sum('raised_total'));
    $selected = $regions->contains('key', $selected) ? $selected : 'all';
    $regionStats = fn ($region) => [
        trans_choice('region.projects_count', $region->campaigns_count, ['count' => $region->campaigns_count]),
        trans_choice('region.facilities_count', $region->facilities->count(), ['count' => $region->facilities->count()]),
        __('home.map.raised_amount', ['amount' => "\u{2066}".Money::format($region->raised_total ?? 0)."\u{2069}"]),
    ];
    $tooltipStats = fn ($region) => [
        ...array_map(fn (array $metric): string => "\u{2066}".$metric['value']."\u{2069} ".$metric['label'], array_slice($region->localizedImpactMetrics(), 0, 2)),
        ...$regionStats($region),
    ];
@endphp

<section id="regions-map" @class(['band-base section-y scroll-mt-28', '!pt-10' => $isPage]) @if ($isPage) aria-label="{{ __('home.map.kicker') }}" @else aria-labelledby="regions-map-title" @endif>
    <div class="container-bader">
        @unless ($isPage)
            <div class="flex flex-wrap items-end justify-between gap-4" data-reveal>
                <div class="max-w-2xl">
                    <p class="kicker">{{ __('home.map.kicker') }}</p>
                    <h2 id="regions-map-title" class="section-title mt-3">{{ __('home.map.title') }}</h2>
                    <p class="section-lead mt-3">{{ __('home.map.text') }}</p>
                </div>
                <a href="{{ route('impact-map') }}" class="btn-outline shrink-0">
                    {{ __('home.map.open_full') }}
                    <x-bader.icon name="arrow" class="h-4 w-4" />
                </a>
            </div>
        @endunless

        @if ($regions->isNotEmpty())
            <div @class([
                    'grid grid-cols-1 items-start gap-8',
                    'mt-10 lg:grid-cols-[minmax(0,24rem)_minmax(0,1fr)]' => ! $isPage,
                    'lg:grid-cols-[minmax(0,32rem)_minmax(0,1fr)]' => $isPage,
                ])
                data-region-map
                data-initial-region="{{ $selected }}"
                @if ($isPage) data-sync-url @endif
                data-reveal>
                <div @class(['mx-auto w-full', 'max-w-[24rem]' => ! $isPage, 'max-w-[32rem] lg:sticky lg:top-28' => $isPage])>
                    <div class="region-map-canvas" data-region-map-canvas>
                        <span class="pointer-events-none absolute left-3 top-[30%] z-0 text-[0.65rem] font-bold tracking-wider text-sky-700/50 [writing-mode:vertical-rl]">{{ __('home.map.sea') }}</span>

                        <div class="region-map-stage" style="aspect-ratio: {{ $viewWidth }} / {{ $viewHeight }}" data-region-map-stage>
                            <svg viewBox="0 0 {{ $viewWidth }} {{ $viewHeight }}" class="absolute inset-0 h-full w-full" aria-hidden="true">
                                @foreach ($areas as $definition)
                                    <polygon class="region-map-outline" points="{{ $definition['points'] }}" />
                                @endforeach
                                @foreach ($areas as $definition)
                                    <polygon class="region-map-land" points="{{ $definition['points'] }}" />
                                @endforeach
                                @foreach ($areas as $area => $definition)
                                    @php $areaRegion = $regionsByArea->get($area); @endphp
                                    <polygon
                                        @class(['region-area', 'is-linked' => $areaRegion, 'is-active' => $areaRegion?->key === $selected])
                                        points="{{ $definition['points'] }}"
                                        style="--heat: {{ $areaRegion ? round(0.18 + 0.62 * $areaRegion->campaigns_count / $maxProjects, 2) : 0 }}"
                                        @if ($areaRegion) data-region-area data-region="{{ $areaRegion->key }}" @endif />
                                @endforeach
                            </svg>

                            @foreach ($regions as $region)
                                @foreach ($region->facilities as $facility)
                                    @php
                                        $angle = deg2rad(-90 + $loop->index * (360 / $loop->count));
                                        $facilityX = max(4, min(96, $region->map_x + cos($angle) * 8));
                                        $facilityY = max(3, min(97, $region->map_y + sin($angle) * 6.4));
                                    @endphp
                                    <a href="{{ route('facilities.show', $facility->key) }}"
                                        class="region-facility"
                                        style="left: {{ round($facilityX, 2) }}%; top: {{ round($facilityY, 2) }}%"
                                        data-region-facility
                                        data-region="{{ $region->key }}"
                                        tabindex="-1"
                                        aria-hidden="true">
                                        <x-bader.icon name="building" class="h-3.5 w-3.5" />
                                        <span class="region-facility-label">{{ $facility->name }}</span>
                                    </a>
                                @endforeach

                                <button type="button"
                                    @class(['region-pin', 'is-active' => $region->key === $selected])
                                    style="left: {{ $region->map_x }}%; top: {{ $region->map_y }}%"
                                    data-region-pin
                                    data-region-marker
                                    data-region="{{ $region->key }}"
                                    data-x="{{ $region->map_x }}"
                                    data-y="{{ $region->map_y }}"
                                    data-name="{{ $region->name }}"
                                    data-stats="{{ implode('|', $tooltipStats($region)) }}"
                                    aria-pressed="{{ $region->key === $selected ? 'true' : 'false' }}"
                                    aria-controls="region-panel-{{ $region->key }}">
                                    <span class="region-pin-dot"></span>
                                    <span class="region-pin-label">{{ $region->name }}</span>
                                </button>
                            @endforeach
                        </div>

                        <div class="region-tooltip" data-region-tooltip role="tooltip" hidden>
                            <p class="font-extrabold text-ink-900" data-region-tooltip-title></p>
                            <ul class="mt-1.5 space-y-0.5 text-xs text-muted" data-region-tooltip-stats></ul>
                        </div>

                        <button type="button" class="region-map-reset" data-region-reset @if ($selected === 'all') hidden @endif>
                            <x-bader.icon name="globe" class="h-4 w-4" />
                            {{ __('home.map.reset') }}
                        </button>

                        <div class="region-map-legend" aria-hidden="true">
                            <span>{{ __('home.map.legend_less') }}</span>
                            <span class="region-map-legend-bar"></span>
                            <span>{{ __('home.map.legend_more') }}</span>
                        </div>
                    </div>
                    <p class="mt-2 text-center text-[0.65rem] text-subtle">
                        {{ __('home.map.attribution') }}
                        <a href="https://www.geoboundaries.org" target="_blank" rel="noopener" class="underline hover:text-forest-700">geoBoundaries</a>
                        (<a href="https://creativecommons.org/licenses/by/4.0/" target="_blank" rel="noopener" class="underline hover:text-forest-700">CC BY 4.0</a>)
                    </p>
                </div>

                <div class="min-w-0">
                    <div class="mb-4 flex flex-wrap gap-2">
                        <button type="button" @class(['chip', 'is-active' => $selected === 'all']) data-region-pin data-region="all" aria-pressed="{{ $selected === 'all' ? 'true' : 'false' }}" aria-controls="region-panel-all">{{ __('home.map.all') }}</button>
                        @foreach ($regions as $region)
                            <button type="button" @class(['chip', 'is-active' => $region->key === $selected]) data-region-pin data-region="{{ $region->key }}" aria-pressed="{{ $region->key === $selected ? 'true' : 'false' }}" aria-controls="region-panel-{{ $region->key }}">{{ $region->name }}</button>
                        @endforeach
                    </div>

                    <div id="region-panel-all" class="surface-card p-6 sm:p-8" data-region-panel data-region="all" aria-live="polite" @if ($selected !== 'all') hidden @endif>
                        <h3 class="text-xl font-extrabold text-ink-900">{{ __('home.map.overview_title') }}</h3>
                        <dl class="mt-5 grid grid-cols-2 gap-3 text-center sm:grid-cols-4">
                            @foreach ([
                                'regions' => $regions->count(),
                                'projects' => $totalProjects,
                                'facilities' => $totalFacilities,
                                'raised' => $totalRaised,
                            ] as $statKey => $statValue)
                                <div class="rounded-2xl bg-paper-2 p-4">
                                    <dt class="text-xs text-subtle">{{ __('home.map.'.$statKey) }}</dt>
                                    <dd class="mt-1 text-2xl font-extrabold text-forest-700" dir="ltr" data-countup="{{ $statValue }}">{{ $statValue }}</dd>
                                </div>
                            @endforeach
                        </dl>
                        <ul class="mt-6 divide-y divide-hairline">
                            @foreach ($regions as $region)
                                <li>
                                    <button type="button" class="region-list-item" data-region-pin data-region="{{ $region->key }}" aria-pressed="false" aria-controls="region-panel-{{ $region->key }}">
                                        <span class="inline-flex items-center gap-2 font-bold text-ink-900">
                                            <x-bader.icon name="map-pin" class="h-4 w-4 text-forest-600" />
                                            {{ $region->name }}
                                        </span>
                                        <span class="text-xs text-subtle">{{ trans_choice('region.projects_count', $region->campaigns_count, ['count' => $region->campaigns_count]) }}</span>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    @foreach ($regions as $region)
                        @php
                            $regionMetrics = $region->localizedImpactMetrics();
                            $shareUrl = route('impact-map', ['region' => $region->key]);
                            $shareTitle = __('home.map.share_title', ['region' => $region->name]);
                        @endphp
                        <div id="region-panel-{{ $region->key }}" class="surface-card p-6 sm:p-8" data-region-panel data-region="{{ $region->key }}" @if ($region->key !== $selected) hidden @endif>
                            <p class="inline-flex items-center gap-2 text-xs font-extrabold text-forest-700">
                                <x-bader.icon name="map-pin" class="h-4 w-4" />
                                {{ __('home.map.region_label') }}
                            </p>
                            <h3 class="mt-2 text-2xl font-extrabold text-ink-900">{{ $region->name }}</h3>
                            @if ($region->description)
                                <p class="mt-3 leading-relaxed text-muted">{{ $region->description }}</p>
                            @endif

                            @if ($regionMetrics !== [])
                                <dl class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3">
                                    @foreach ($regionMetrics as $metric)
                                        <div class="region-metric">
                                            <x-bader.icon :name="$metric['icon']" class="h-5 w-5 text-forest-600" />
                                            <dt class="order-last mt-0.5 text-xs text-muted">{{ $metric['label'] }}</dt>
                                            <dd class="mt-2 text-xl font-extrabold text-ink-900" dir="ltr">{{ $metric['value'] }}</dd>
                                        </div>
                                    @endforeach
                                </dl>
                            @endif

                            <div class="mt-5 flex flex-wrap gap-3 text-sm font-bold">
                                @foreach ($regionStats($region) as $stat)
                                    <span class="rounded-full bg-paper-2 px-3 py-1.5 text-ink-800">{{ $stat }}</span>
                                @endforeach
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
                            <div class="mt-6 flex flex-wrap gap-3">
                                <a href="{{ route('regions.show', $region->key) }}" class="btn-brand">
                                    {{ __('home.map.view_region') }}
                                    <x-bader.icon name="arrow" class="h-4 w-4" />
                                </a>
                                <button type="button" class="btn-outline" data-region-pin data-region="all" aria-pressed="false" aria-controls="region-panel-all">
                                    {{ __('home.map.reset') }}
                                </button>
                            </div>
                            <div class="mt-5 flex flex-wrap items-center gap-2 border-t border-hairline pt-4">
                                <span class="me-1 text-xs font-bold text-subtle">{{ __('home.map.share') }}</span>
                                <button type="button" class="chip" data-share data-share-url="{{ $shareUrl }}" data-share-title="{{ $shareTitle }}" data-copied-text="{{ __('common.link_copied') }}">
                                    <x-bader.icon name="share" class="h-4 w-4" />
                                    <span data-share-label>{{ __('home.map.copy_link') }}</span>
                                </button>
                                <a href="https://api.whatsapp.com/send?text={{ urlencode($shareTitle."\n".$shareUrl) }}" target="_blank" rel="noopener" class="chip" data-region-whatsapp>
                                    <x-bader.icon name="whatsapp" class="h-4 w-4" />
                                    {{ __('home.map.share_whatsapp') }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
