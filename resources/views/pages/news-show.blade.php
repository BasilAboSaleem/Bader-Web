@extends('layouts.public')

@section('title', $story->title.' — '.__('brand.name'))
@section('meta_description', \Illuminate\Support\Str::limit((string) $story->excerpt, 160))

@php
    $storyText = trim((string) ($story->content ?: $story->excerpt));
    $paragraphs = array_values(array_filter(array_map('trim', preg_split('/\R+/u', $storyText) ?: [])));
    $readingMinutes = max(1, (int) ceil(count(preg_split('/\s+/u', $storyText, -1, PREG_SPLIT_NO_EMPTY)) / 200));
    $shareUrl = url()->current();
@endphp

@section('content')
    <x-bader.page-hero
        :kicker="$story->category ?: __('page.news.kicker')"
        :title="$story->title"
        :breadcrumbs="[['label' => __('nav.news'), 'url' => route('news')]]"
    >
        <div class="mt-5 flex flex-wrap gap-2 text-xs font-bold">
            @if ($story->published_at)
                <time datetime="{{ $story->published_at->toDateString() }}" class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1.5 text-ink-800 ring-1 ring-hairline">
                    <x-bader.icon name="calendar" class="h-3.5 w-3.5" />
                    {{ $story->published_at->translatedFormat('j F Y') }}
                </time>
            @endif
            <span class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1.5 text-ink-800 ring-1 ring-hairline">
                <x-bader.icon name="clock" class="h-3.5 w-3.5" />
                {{ trans_choice('story_page.reading_time', $readingMinutes, ['count' => $readingMinutes]) }}
            </span>
            @if ($story->is_featured)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-gold-500 px-3 py-1.5 text-teal-950">
                    <x-bader.icon name="sparkle" class="h-3.5 w-3.5" />
                    {{ __('news_page.featured') }}
                </span>
            @endif
        </div>
    </x-bader.page-hero>

    <section class="band-base section-y">
        <div class="container-bader grid items-start gap-8 lg:grid-cols-[minmax(0,1fr)_20rem]">
            <article class="min-w-0">
                @if ($story->image)
                    <figure class="overflow-hidden rounded-3xl bg-paper-3 shadow-card-md" data-reveal>
                        <a href="{{ asset($story->image) }}" data-lightbox data-close-label="{{ __('common.close') }}" class="group relative block cursor-zoom-in">
                            <img src="{{ asset($story->image) }}" alt="{{ $story->title }}" class="aspect-[16/9] w-full object-cover transition duration-700 ease-bader group-hover:scale-[1.02]">
                            <span class="absolute bottom-3 end-3 inline-flex items-center gap-1.5 rounded-full bg-black/55 px-3 py-1.5 text-xs font-bold text-white backdrop-blur">
                                <x-bader.icon name="eye" class="h-3.5 w-3.5" />
                                {{ __('common.zoom_image') }}
                            </span>
                        </a>
                    </figure>
                @endif

                @if ($story->content && $story->excerpt)
                    <p class="mt-8 border-s-4 border-gold-500 ps-5 text-lg font-semibold leading-9 text-ink-800" data-reveal>{{ $story->excerpt }}</p>
                @endif

                <div class="prose-bader mt-8" data-reveal>
                    @foreach ($paragraphs as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>

                <div class="mt-10 flex flex-wrap items-center justify-between gap-4 border-y border-hairline py-5" data-reveal>
                    <div class="flex items-center gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-full bg-teal-950">
                            <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="" class="h-5 w-5">
                        </span>
                        <span>
                            <span class="block text-sm font-extrabold text-ink-900">{{ __('brand.name') }}</span>
                            <span class="block text-xs text-subtle">{{ __('story_page.source') }}</span>
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" class="chip" data-share data-share-url="{{ $shareUrl }}" data-share-title="{{ $story->title }}" data-copied-text="{{ __('common.link_copied') }}">
                            <x-bader.icon name="share" class="h-4 w-4" />
                            <span data-share-label>{{ __('common.share') }}</span>
                        </button>
                        <a href="https://api.whatsapp.com/send?text={{ urlencode($story->title."\n".$shareUrl) }}" target="_blank" rel="noopener" class="chip px-3" aria-label="{{ __('story_page.share_whatsapp') }}">
                            <x-bader.icon name="whatsapp" class="h-4 w-4" />
                        </a>
                    </div>
                </div>

                @if ($prevStory || $nextStory)
                    <nav class="mt-8 grid gap-4 sm:grid-cols-2" aria-label="{{ __('story_page.navigation') }}" data-reveal>
                        @if ($prevStory)
                            <a href="{{ route('news.show', $prevStory->key) }}" class="surface-card surface-card-hover group p-5">
                                <span class="inline-flex items-center gap-1.5 text-xs font-extrabold text-forest-700">
                                    <x-bader.icon name="chevron-start" class="h-4 w-4" />
                                    {{ __('story_page.previous') }}
                                </span>
                                <span class="mt-2 line-clamp-2 block font-extrabold leading-snug text-ink-900 group-hover:text-forest-700">{{ $prevStory->title }}</span>
                            </a>
                        @else
                            <span class="hidden sm:block"></span>
                        @endif
                        @if ($nextStory)
                            <a href="{{ route('news.show', $nextStory->key) }}" class="surface-card surface-card-hover group p-5 text-end">
                                <span class="inline-flex items-center gap-1.5 text-xs font-extrabold text-forest-700">
                                    {{ __('story_page.next') }}
                                    <x-bader.icon name="chevron-end" class="h-4 w-4" />
                                </span>
                                <span class="mt-2 line-clamp-2 block font-extrabold leading-snug text-ink-900 group-hover:text-forest-700">{{ $nextStory->title }}</span>
                            </a>
                        @endif
                    </nav>
                @endif
            </article>

            <aside class="space-y-4 lg:sticky lg:top-28">
                <div class="relative isolate overflow-hidden rounded-3xl bg-teal-950 p-6 text-white shadow-card-md" data-reveal>
                    <div class="pointer-events-none absolute -end-16 -top-16 -z-10 h-48 w-48 rounded-full bg-forest-600/40 blur-3xl" aria-hidden="true"></div>
                    <x-bader.icon name="hand-heart" class="h-8 w-8 text-gold-400" />
                    <p class="mt-4 text-xl font-extrabold leading-snug">{{ __('story_page.support_title') }}</p>
                    <p class="mt-2 text-sm leading-7 text-white/75">{{ __('story_page.support_text') }}</p>
                    <a href="{{ route('donate') }}" class="btn-primary mt-6 w-full">
                        <x-bader.icon name="heart" class="h-4 w-4" />
                        {{ __('nav.donate') }}
                    </a>
                    <a href="{{ route('campaigns') }}" class="btn-ghost mt-3 w-full">{{ __('header.all_projects') }}</a>
                </div>

                <div class="flex items-start gap-3 rounded-3xl border border-hairline bg-white p-5" data-reveal>
                    <x-bader.icon name="shield" class="h-6 w-6 shrink-0 text-forest-600" />
                    <div>
                        <p class="text-sm font-extrabold text-ink-900">{{ __('story_page.verified_title') }}</p>
                        <p class="mt-1 text-xs leading-6 text-muted">{{ __('story_page.verified_text') }}</p>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    @if ($relatedStories->isNotEmpty())
        <section class="band-tint section-y">
            <div class="container-bader">
                <div class="flex items-end justify-between gap-4" data-reveal>
                    <h2 class="section-title">{{ __('story_page.more') }}</h2>
                    <a href="{{ route('news') }}" class="btn-outline shrink-0">{{ __('news_page.all') }}</a>
                </div>
                <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($relatedStories as $related)
                        <x-bader.story-card :story="$related" compact data-reveal style="animation-delay: {{ $loop->index * 80 }}ms" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
