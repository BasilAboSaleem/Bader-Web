@extends('layouts.public')

@use('App\Support\Money')

@php
    $projectImage = $completedProject->image ?: ($completedProject->galleryPhotos()[0] ?? null);
    $paragraphs = array_values(array_filter(array_map('trim', preg_split('/\R{2,}/u', trim((string) $completedProject->content)) ?: [])));
    $shareUrl = url()->current();
@endphp

@section('title', $completedProject->title.' — '.__('brand.name'))
@section('meta_description', \Illuminate\Support\Str::limit((string) $completedProject->description, 160))

@section('content')
    <x-bader.page-hero
        :kicker="$completedProject->program?->title ?? __('completed_project_page.kicker')"
        :title="$completedProject->title"
        :breadcrumbs="[['label' => __('completed_project_page.title'), 'url' => route('completed-projects')]]"
    >
        <div class="mt-5 flex flex-wrap gap-2 text-xs font-bold">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-forest-700 px-3 py-1.5 text-white">
                <x-bader.icon name="badge-check" class="h-3.5 w-3.5" />
                {{ __('completed_project_page.badge') }}
            </span>
            @if ($completedProject->region)
                <a href="{{ route('regions.show', $completedProject->region->key) }}" class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1.5 text-ink-800 ring-1 ring-hairline hover:text-forest-700">
                    <x-bader.icon name="map-pin" class="h-3.5 w-3.5" />
                    {{ $completedProject->region->name }}
                </a>
            @endif
            @if ($completedProject->completed_at)
                <time datetime="{{ $completedProject->completed_at->toDateString() }}" class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1.5 text-ink-800 ring-1 ring-hairline">
                    <x-bader.icon name="calendar" class="h-3.5 w-3.5" />
                    {{ $completedProject->completed_at->translatedFormat('j F Y') }}
                </time>
            @endif
        </div>
    </x-bader.page-hero>

    <section class="band-base section-y">
        <div class="container-bader grid grid-cols-1 items-start gap-8 lg:grid-cols-[minmax(0,1fr)_22rem]">
            <div class="min-w-0">
                @if ($projectImage)
                    <figure class="overflow-hidden rounded-3xl bg-paper-3 shadow-card-md" data-reveal>
                        <a href="{{ asset($projectImage) }}" data-lightbox data-close-label="{{ __('common.close') }}" class="group relative block cursor-zoom-in">
                            <img src="{{ asset($projectImage) }}" alt="{{ $completedProject->title }}" class="aspect-[16/9] w-full object-cover transition duration-700 ease-bader group-hover:scale-[1.02]">
                        </a>
                    </figure>
                @endif

                @if ($completedProject->description)
                    <p class="mt-8 text-lg font-semibold leading-9 text-ink-800" data-reveal>{{ $completedProject->description }}</p>
                @endif

                @if ($paragraphs !== [])
                    <div class="mt-6 space-y-4 leading-8 text-muted" data-reveal>
                        @foreach ($paragraphs as $paragraph)
                            <p>{!! nl2br(e($paragraph)) !!}</p>
                        @endforeach
                    </div>
                @endif

                <x-bader.media-gallery :model="$completedProject" class="mt-10" />

                <div class="mt-10 flex flex-wrap items-center justify-end gap-2 border-t border-hairline pt-5" data-reveal>
                    <button type="button" class="chip" data-share data-share-url="{{ $shareUrl }}" data-share-title="{{ $completedProject->title }}" data-copied-text="{{ __('common.link_copied') }}">
                        <x-bader.icon name="share" class="h-4 w-4" />
                        <span data-share-label>{{ __('common.share') }}</span>
                    </button>
                    <a href="https://api.whatsapp.com/send?text={{ urlencode($completedProject->title."\n".$shareUrl) }}" target="_blank" rel="noopener" class="chip px-3" aria-label="{{ __('story_page.share_whatsapp') }}">
                        <x-bader.icon name="whatsapp" class="h-4 w-4" />
                    </a>
                </div>
            </div>

            <aside class="space-y-4 lg:sticky lg:top-28">
                <dl class="surface-card divide-y divide-hairline" data-reveal>
                    @if ($completedProject->completed_at)
                        <div class="flex items-center gap-4 p-5">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-forest-600/10 text-forest-700"><x-bader.icon name="calendar" class="h-5 w-5" /></span>
                            <div>
                                <dt class="text-xs font-bold text-subtle">{{ __('completed_project_page.completed_at') }}</dt>
                                <dd class="mt-0.5 font-extrabold text-ink-900">{{ $completedProject->completed_at->translatedFormat('j F Y') }}</dd>
                            </div>
                        </div>
                    @endif
                    @if ($completedProject->beneficiaries)
                        <div class="flex items-center gap-4 p-5">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-forest-600/10 text-forest-700"><x-bader.icon name="users" class="h-5 w-5" /></span>
                            <div>
                                <dt class="text-xs font-bold text-subtle">{{ __('completed_project_page.beneficiaries') }}</dt>
                                <dd class="mt-0.5 text-xl font-extrabold text-ink-900">{{ number_format($completedProject->beneficiaries) }}</dd>
                            </div>
                        </div>
                    @endif
                    @if ($completedProject->cost)
                        <div class="flex items-center gap-4 p-5">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-forest-600/10 text-forest-700"><x-bader.icon name="hand-heart" class="h-5 w-5" /></span>
                            <div>
                                <dt class="text-xs font-bold text-subtle">{{ __('completed_project_page.cost') }}</dt>
                                <dd class="mt-0.5 text-xl font-extrabold text-ink-900" dir="ltr">{{ Money::format($completedProject->cost) }}</dd>
                            </div>
                        </div>
                    @endif
                    @if ($completedProject->program)
                        <div class="flex items-center gap-4 p-5">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-forest-600/10 text-forest-700"><x-bader.icon name="sprout" class="h-5 w-5" /></span>
                            <div>
                                <dt class="text-xs font-bold text-subtle">{{ __('campaign_page.program') }}</dt>
                                <dd class="mt-0.5 font-extrabold text-ink-900">
                                    <a href="{{ route('programs.show', $completedProject->program->key) }}" class="hover:text-forest-700">{{ $completedProject->program->title }}</a>
                                </dd>
                            </div>
                        </div>
                    @endif
                </dl>

                @if ($completedProject->region)
                    <a href="{{ route('impact-map', ['region' => $completedProject->region->key]) }}" class="flex items-center gap-3 rounded-2xl border border-hairline bg-white p-4 text-sm font-bold text-ink-800 transition hover:border-forest-600 hover:text-forest-700" data-reveal>
                        <x-bader.icon name="map-pin" class="h-5 w-5 text-forest-600" />
                        {{ __('home.map.view_on_map') }}
                        <x-bader.icon name="arrow" class="ms-auto h-4 w-4" />
                    </a>
                @endif

                <div class="relative isolate overflow-hidden rounded-3xl bg-teal-950 p-6 text-white shadow-card-md" data-reveal>
                    <x-bader.icon name="hand-heart" class="h-8 w-8 text-gold-400" />
                    <p class="mt-4 text-xl font-extrabold leading-snug">{{ __('completed_project_page.support_title') }}</p>
                    <p class="mt-2 text-sm leading-7 text-white/75">{{ __('completed_project_page.support_text') }}</p>
                    <a href="{{ route('donate') }}" class="btn-primary mt-6 w-full">
                        <x-bader.icon name="heart" class="h-4 w-4" />
                        {{ __('nav.donate') }}
                    </a>
                    <a href="{{ route('campaigns') }}" class="btn-ghost mt-3 w-full">{{ __('header.all_projects') }}</a>
                </div>
            </aside>
        </div>
    </section>

    @if ($relatedProjects->isNotEmpty())
        <section class="band-tint section-y">
            <div class="container-bader">
                <div class="flex items-end justify-between gap-4" data-reveal>
                    <h2 class="section-title">{{ __('completed_project_page.related') }}</h2>
                    <a href="{{ route('completed-projects') }}" class="btn-outline shrink-0">{{ __('completed_project_page.view_all') }}</a>
                </div>
                <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($relatedProjects as $relatedProject)
                        <x-bader.completed-project-card :project="$relatedProject" data-reveal style="animation-delay: {{ $loop->index * 80 }}ms" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
