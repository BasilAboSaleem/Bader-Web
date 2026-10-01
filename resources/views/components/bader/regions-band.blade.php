@props(['regions', 'metrics'])

<section class="band-base section-y" aria-labelledby="regions-band-title">
    <div class="container-bader">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between" data-reveal>
            <div class="max-w-2xl">
                <p class="kicker">{{ __('home.regions.kicker') }}</p>
                <h2 id="regions-band-title" class="section-title mt-3">{{ __('home.regions.title') }}</h2>
                <p class="section-lead mt-3">{{ __('home.regions.text') }}</p>
            </div>
            <a href="#regions-map" class="btn-outline shrink-0">
                <x-bader.icon name="map-pin" class="h-4 w-4" />
                {{ __('header.view_map') }}
            </a>
        </div>

        @if ($regions->isNotEmpty())
            <ul class="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
                @foreach ($regions as $region)
                    <li data-reveal style="animation-delay: {{ $loop->index * 60 }}ms">
                        <a href="{{ route('regions.show', $region->key) }}" class="region-chip group">
                            <span class="region-chip-icon">
                                <x-bader.icon name="map-pin" class="h-5 w-5" />
                            </span>
                            <span class="min-w-0">
                                <span class="block font-extrabold leading-snug text-ink-900 group-hover:text-forest-700">{{ $region->name }}</span>
                                <span class="block text-xs text-subtle">{{ trans_choice('region.projects_count', $region->campaigns_count, ['count' => $region->campaigns_count]) }}</span>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif

        @if ($metrics->isNotEmpty())
            <dl class="mt-10 grid grid-cols-2 gap-px overflow-hidden rounded-3xl bg-teal-800 text-white shadow-card-lg lg:grid-cols-4" data-reveal>
                @foreach ($metrics as $metric)
                    <div class="bg-teal-950 p-6 sm:p-8">
                        <dt class="text-sm text-white/70">{{ $metric->title() }}</dt>
                        <dd class="mt-2 flex items-baseline gap-1.5">
                            <span class="text-3xl font-extrabold text-gold-400 sm:text-4xl" data-countup="{{ $metric->value }}" dir="ltr">{{ $metric->value }}</span>
                            @if ($metric->unit())
                                <span class="text-sm font-semibold text-white/80">{{ $metric->unit() }}</span>
                            @endif
                        </dd>
                    </div>
                @endforeach
            </dl>
        @endif
    </div>
</section>
