@extends('layouts.public')

@section('title', $facility->name.' — '.__('brand.name'))
@section('meta_description', \Illuminate\Support\Str::limit((string) $facility->description, 160))

@php
    $facilityImage = $facility->image ? asset($facility->image) : asset('images/programs/water.jpg');
    $paragraphs = array_values(array_filter(array_map('trim', preg_split('/\R+/u', trim((string) $facility->content)) ?: [])));
    $gallery = array_values(array_filter((array) $facility->gallery));
    $breadcrumbs = $facility->region
        ? [['label' => $facility->region->name, 'url' => route('regions.show', $facility->region->key)]]
        : [];

    $videoUrl = $facility->video_url;
    if ($videoUrl && preg_match('~(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/)|youtu\.be/)([\w-]{6,})~', $videoUrl, $videoMatch)) {
        $videoUrl = 'https://www.youtube-nocookie.com/embed/'.$videoMatch[1];
    }
@endphp

@section('content')
    <x-bader.page-hero
        :kicker="__('facility_page.kicker')"
        :title="$facility->name"
        :breadcrumbs="$breadcrumbs"
    >
        <div class="mt-5 flex flex-wrap gap-2 text-xs font-bold">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-forest-700 px-3 py-1.5 text-white">
                <span class="h-1.5 w-1.5 animate-pulse-dot rounded-full bg-gold-400"></span>
                {{ __('facility_page.active') }}
            </span>
            @if ($facility->location)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1.5 text-ink-800 ring-1 ring-hairline">
                    <x-bader.icon name="map-pin" class="h-3.5 w-3.5" />
                    {{ $facility->location }}
                </span>
            @endif
            @if ($facility->established_year)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1.5 text-ink-800 ring-1 ring-hairline">
                    <x-bader.icon name="calendar" class="h-3.5 w-3.5" />
                    {{ __('facility_page.established', ['year' => $facility->established_year]) }}
                </span>
            @endif
            @if ($facility->capacity)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1.5 text-ink-800 ring-1 ring-hairline">
                    <x-bader.icon name="users" class="h-3.5 w-3.5" />
                    {{ $facility->capacity }}
                </span>
            @endif
        </div>
    </x-bader.page-hero>

    <section class="band-base section-y">
        <div class="container-bader grid grid-cols-1 items-start gap-8 lg:grid-cols-[minmax(0,1fr)_22rem]">
            <div class="min-w-0">
                <figure class="overflow-hidden rounded-3xl bg-paper-3 shadow-card-md" data-reveal>
                    @if ($facility->image)
                        <a href="{{ $facilityImage }}" data-lightbox data-close-label="{{ __('common.close') }}" class="group relative block cursor-zoom-in">
                            <img src="{{ $facilityImage }}" alt="{{ $facility->name }}" class="aspect-[16/9] w-full object-cover transition duration-700 ease-bader group-hover:scale-[1.02]">
                            <span class="absolute bottom-3 end-3 inline-flex items-center gap-1.5 rounded-full bg-black/55 px-3 py-1.5 text-xs font-bold text-white backdrop-blur">
                                <x-bader.icon name="eye" class="h-3.5 w-3.5" />
                                {{ __('common.zoom_image') }}
                            </span>
                        </a>
                    @else
                        <img src="{{ $facilityImage }}" alt="{{ $facility->name }}" class="aspect-[16/9] w-full object-cover">
                    @endif
                </figure>

                @if ($facility->description)
                    <p class="mt-8 text-lg font-semibold leading-9 text-ink-800" data-reveal>{{ $facility->description }}</p>
                @endif

                @if ($paragraphs)
                    <h2 class="mt-10 text-2xl font-extrabold text-ink-900" data-reveal>{{ __('facility_page.details') }}</h2>
                    <div class="prose-bader mt-4 text-muted" data-reveal>
                        @foreach ($paragraphs as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    </div>
                @endif

                @if ($facility->location || $facility->established_year || $facility->capacity)
                    <dl class="mt-10 grid gap-4 sm:grid-cols-3" data-reveal>
                        @if ($facility->location)
                            <div class="rounded-2xl border border-hairline bg-white p-5">
                                <dt class="text-xs font-bold text-subtle">{{ __('facility_page.location') }}</dt>
                                <dd class="mt-1 font-extrabold text-ink-900">{{ $facility->location }}</dd>
                            </div>
                        @endif
                        @if ($facility->established_year)
                            <div class="rounded-2xl border border-hairline bg-white p-5">
                                <dt class="text-xs font-bold text-subtle">{{ __('facility_page.year') }}</dt>
                                <dd class="mt-1 font-extrabold text-ink-900">{{ $facility->established_year }}</dd>
                            </div>
                        @endif
                        @if ($facility->capacity)
                            <div class="rounded-2xl border border-hairline bg-white p-5">
                                <dt class="text-xs font-bold text-subtle">{{ __('facility_page.capacity') }}</dt>
                                <dd class="mt-1 font-extrabold text-ink-900">{{ $facility->capacity }}</dd>
                            </div>
                        @endif
                    </dl>
                @endif

                @if ($videoUrl)
                    <div class="mt-10" data-reveal>
                        <h2 class="text-2xl font-extrabold text-ink-900">{{ __('facility_page.video') }}</h2>
                        <div class="relative mt-4 aspect-video overflow-hidden rounded-3xl bg-teal-950 shadow-card-md">
                            <iframe src="{{ $videoUrl }}" class="absolute inset-0 h-full w-full" allowfullscreen loading="lazy" title="{{ $facility->name }}" referrerpolicy="strict-origin-when-cross-origin" allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                        </div>
                    </div>
                @endif

                @if ($gallery)
                    <div class="mt-10" data-reveal>
                        <div class="flex items-baseline justify-between gap-4">
                            <h2 class="text-2xl font-extrabold text-ink-900">{{ __('facility_page.gallery') }}</h2>
                            <span class="text-sm font-bold text-subtle">{{ trans_choice('facility_page.photos_count', count($gallery), ['count' => count($gallery)]) }}</span>
                        </div>
                        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                            @foreach ($gallery as $photo)
                                <a href="{{ asset($photo) }}" data-lightbox data-close-label="{{ __('common.close') }}" class="group relative block aspect-[4/3] cursor-zoom-in overflow-hidden rounded-2xl bg-paper-3">
                                    <img src="{{ asset($photo) }}" alt="{{ __('facility_page.photo_alt', ['name' => $facility->name, 'number' => $loop->iteration]) }}" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-110">
                                    <span class="absolute inset-0 flex items-center justify-center bg-teal-950/0 text-white opacity-0 transition group-hover:bg-teal-950/30 group-hover:opacity-100">
                                        <x-bader.icon name="eye" class="h-7 w-7" />
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="mt-10 flex items-start gap-4 rounded-3xl bg-teal-950 p-6 text-white" data-reveal>
                    <x-bader.icon name="shield" class="h-8 w-8 shrink-0 text-gold-400" />
                    <div>
                        <p class="font-extrabold">{{ __('facility_page.verified_title') }}</p>
                        <p class="mt-1 text-sm leading-relaxed text-white/75">{{ __('facility_page.verified_text') }}</p>
                    </div>
                </div>
            </div>

            <aside class="lg:sticky lg:top-28" aria-label="{{ __('donate_box.kicker') }}">
                <x-bader.donate-box
                    :title="$facility->name"
                    :donate-params="['target_type' => 'facility', 'target_id' => $facility->id]"
                >
                    <p class="flex items-start gap-2 rounded-2xl bg-paper-2 p-4 text-sm leading-7 text-muted">
                        <x-bader.icon name="building" class="mt-1 h-4 w-4 shrink-0 text-forest-600" />
                        {{ __('facility_page.support_note') }}
                    </p>
                </x-bader.donate-box>

                @if ($facility->region)
                    <a href="{{ route('regions.show', $facility->region->key) }}" class="mt-4 flex items-center gap-3 rounded-2xl border border-hairline bg-white p-4 text-sm font-bold text-ink-800 transition hover:border-forest-600 hover:text-forest-700">
                        <x-bader.icon name="map-pin" class="h-5 w-5 text-forest-600" />
                        {{ __('facility_page.region_link', ['region' => $facility->region->name]) }}
                        <x-bader.icon name="arrow" class="ms-auto h-4 w-4" />
                    </a>
                @endif
            </aside>
        </div>
    </section>

    @if ($relatedFacilities->isNotEmpty())
        <section class="band-tint section-y">
            <div class="container-bader">
                <h2 class="section-title" data-reveal>{{ __('facility_page.related') }}</h2>
                <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($relatedFacilities as $related)
                        <x-bader.facility-card :facility="$related" data-reveal style="animation-delay: {{ $loop->index * 80 }}ms" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
