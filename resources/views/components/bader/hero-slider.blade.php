@props(['campaigns'])

@use('App\Support\Money')

@php
    $slides = $campaigns->map(fn ($campaign) => [
        'title' => $campaign->title,
        'text' => $campaign->description,
        'badge' => $campaign->program?->title ?? __('home.hero.urgent'),
        'image' => $campaign->image ? asset($campaign->image) : asset('images/programs/water.jpg'),
        'progress' => $campaign->isOngoing() ? null : $campaign->progress_percent,
        'raised' => Money::format($campaign->raised_amount),
        'goal' => Money::format($campaign->goal_amount),
        'donateUrl' => route('donate', ['target_type' => 'campaign', 'target_id' => $campaign->id]),
        'detailsUrl' => route('campaigns.show', $campaign->key),
    ])->values();

    if ($slides->isEmpty()) {
        $slides = collect([[
            'title' => __('home.hero_title'),
            'text' => __('home.hero_text'),
            'badge' => __('home.hero_kicker'),
            'image' => asset('images/programs/water.jpg'),
            'progress' => null,
            'raised' => null,
            'goal' => null,
            'donateUrl' => route('donate'),
            'detailsUrl' => route('about'),
        ]]);
    }

    $particles = [[8, 0], [17, 3.2], [29, 6.1], [41, 1.4], [55, 4.6], [66, 2.3], [78, 7.2], [90, 5.1]];
@endphp

<section class="relative isolate overflow-hidden bg-teal-950 text-white"
    data-slider
    data-autoplay="7000"
    aria-roledescription="carousel"
    aria-label="{{ __('home.hero.label') }}">
    <h1 class="sr-only">{{ __('brand.name') }} — {{ __('home.hero_title') }}</h1>

    <div class="relative h-[calc(100svh-7.5rem)] min-h-[34rem] max-h-[54rem]">
        <div class="flex h-full transition-transform duration-[900ms] ease-bader motion-reduce:transition-none" data-slider-track>
            @foreach ($slides as $index => $slide)
                <article class="relative h-full w-full shrink-0 overflow-hidden"
                    data-slider-slide
                    aria-roledescription="slide"
                    aria-label="{{ __('home.hero.slide_of', ['current' => $index + 1, 'total' => $slides->count()]) }}"
                    @if ($index > 0) aria-hidden="true" inert @endif>
                    <img src="{{ $slide['image'] }}" alt="" class="absolute inset-0 h-full w-full animate-kenburns object-cover motion-reduce:animate-none" data-replay @if ($index > 0) loading="lazy" @else fetchpriority="high" @endif>
                    <div class="absolute inset-0 bg-gradient-to-t from-teal-950 via-teal-950/55 to-teal-950/20"></div>
                    <div class="absolute inset-0 bg-gradient-to-l from-transparent via-transparent to-teal-950/60 rtl:bg-gradient-to-r"></div>

                    <div class="container-bader relative z-10 flex h-full flex-col justify-end pb-36 sm:justify-center sm:pb-28">
                        <div class="max-w-2xl">
                            <span class="inline-flex animate-fade-up items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-xs font-bold backdrop-blur" data-replay>
                                <span class="h-2 w-2 animate-pulse-dot rounded-full bg-gold-400"></span>
                                {{ $slide['badge'] }}
                            </span>
                            <h2 class="mt-5 text-4xl font-extrabold leading-[1.15] sm:text-5xl lg:text-6xl">
                                @foreach (preg_split('/\s+/u', trim($slide['title'])) as $wordIndex => $word)
                                    <span class="inline-block animate-wordin motion-reduce:animate-none" style="animation-delay: {{ 120 + $wordIndex * 70 }}ms" data-replay>{{ $word }}</span>
                                @endforeach
                            </h2>
                            @if ($slide['text'])
                                <p class="mt-5 max-w-xl animate-fade-up text-base leading-relaxed text-white/85 [animation-delay:450ms] sm:text-lg" data-replay>{{ $slide['text'] }}</p>
                            @endif

                            @if ($slide['progress'] !== null)
                                <div class="mt-6 max-w-sm animate-fade-up [animation-delay:550ms]" data-replay>
                                    <div class="mb-2 flex justify-between text-xs font-semibold text-white/80">
                                        <span>{{ __('project.raised', ['amount' => $slide['raised']]) }}</span>
                                        <span>{{ __('project.goal', ['amount' => $slide['goal']]) }}</span>
                                    </div>
                                    <div class="h-1.5 overflow-hidden rounded-full bg-white/20">
                                        <div class="h-full rounded-full bg-gold-400" style="width: {{ $slide['progress'] }}%"></div>
                                    </div>
                                </div>
                            @endif

                            <div class="mt-8 flex animate-fade-up flex-wrap gap-3 [animation-delay:650ms]" data-replay>
                                <a href="{{ $slide['donateUrl'] }}" class="btn-primary min-h-12 px-7 text-base">
                                    <x-bader.icon name="heart" class="h-5 w-5" />
                                    {{ __('home.hero.donate') }}
                                </a>
                                <a href="{{ $slide['detailsUrl'] }}" class="btn-ghost min-h-12 px-6 text-base">
                                    {{ __('home.hero.details') }}
                                    <x-bader.icon name="arrow" class="h-4 w-4" />
                                </a>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="pointer-events-none absolute inset-0 overflow-hidden motion-reduce:hidden" aria-hidden="true">
            @foreach ($particles as [$left, $delay])
                <span class="hero-particle" style="left: {{ $left }}%; animation-delay: {{ $delay }}s"></span>
            @endforeach
        </div>

        @if ($slides->count() > 1)
            <div class="container-bader absolute inset-x-0 bottom-24 z-20 flex items-center justify-between gap-4 sm:bottom-20">
                <div class="flex items-center gap-2">
                    @foreach ($slides as $index => $slide)
                        <button type="button" class="hero-dot" data-slider-dot data-index="{{ $index }}" aria-label="{{ __('home.hero.go_to', ['number' => $index + 1]) }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}"></button>
                    @endforeach
                </div>
                <div class="flex gap-2">
                    <button type="button" class="hero-arrow" data-slider-prev aria-label="{{ __('home.hero.previous') }}">
                        <x-bader.icon name="chevron-start" class="h-5 w-5" />
                    </button>
                    <button type="button" class="hero-arrow" data-slider-next aria-label="{{ __('home.hero.next') }}">
                        <x-bader.icon name="chevron-end" class="h-5 w-5" />
                    </button>
                </div>
            </div>
        @endif

        <div class="pointer-events-none absolute inset-x-0 -bottom-px z-10 h-16 overflow-hidden text-paper" aria-hidden="true">
            <div class="wave-track h-full">
                @foreach ([1, 2] as $copy)
                    <svg class="h-full w-1/2 shrink-0" viewBox="0 0 1440 64" preserveAspectRatio="none" fill="currentColor">
                        <path d="M0 40c120-18 240-26 360-12s240 30 360 22 240-36 360-34 240 22 360 24v24H0z" opacity="0.45" />
                        <path d="M0 48c120-10 240-16 360-6s240 20 360 14 240-24 360-22 240 14 360 14v16H0z" />
                    </svg>
                @endforeach
            </div>
        </div>
    </div>
</section>
