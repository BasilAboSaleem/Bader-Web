@props(['stories'])

@php
    $leadStory = $stories->first();
    $otherStories = $stories->slice(1, 3);
    $storyImage = fn ($story) => $story->image ? asset($story->image) : asset('images/programs/education.jpg');
@endphp

<section class="band-tint section-y" aria-labelledby="home-news-title">
    <div class="container-bader">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between" data-reveal>
            <div class="max-w-2xl">
                <p class="kicker">{{ __('home.stories_kicker') }}</p>
                <h2 id="home-news-title" class="section-title mt-3">{{ __('home.stories_title') }}</h2>
                <p class="section-lead mt-3">{{ __('home.stories_intro') }}</p>
            </div>
            <a href="{{ route('news') }}" class="btn-outline shrink-0">
                {{ __('home.stories_link') }}
                <x-bader.icon name="arrow" class="h-4 w-4" />
            </a>
        </div>

        @if ($leadStory)
            <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1.35fr)_minmax(0,1fr)]">
                <article class="group relative isolate flex min-h-[24rem] overflow-hidden rounded-3xl bg-teal-950 text-white shadow-card-md" data-reveal>
                    <img src="{{ $storyImage($leadStory) }}" alt="" loading="lazy" class="absolute inset-0 -z-10 h-full w-full object-cover transition duration-700 ease-bader group-hover:scale-105">
                    <div class="absolute inset-0 -z-10 bg-gradient-to-t from-teal-950 via-teal-950/50 to-transparent"></div>
                    <div class="mt-auto p-6 sm:p-8">
                        <div class="flex flex-wrap items-center gap-3 text-xs font-semibold text-white/80">
                            @if ($leadStory->category)
                                <span class="rounded-full bg-gold-500 px-3 py-1 font-extrabold text-teal-950">{{ $leadStory->category }}</span>
                            @endif
                            @if ($leadStory->published_at)
                                <time datetime="{{ $leadStory->published_at->toDateString() }}" class="inline-flex items-center gap-1.5">
                                    <x-bader.icon name="calendar" class="h-3.5 w-3.5" />
                                    {{ $leadStory->published_at->translatedFormat('j F Y') }}
                                </time>
                            @endif
                        </div>
                        <h3 class="mt-4 text-2xl font-extrabold leading-snug sm:text-3xl">
                            <a href="{{ route('news.show', $leadStory->key) }}" class="after:absolute after:inset-0">{{ $leadStory->title }}</a>
                        </h3>
                        @if ($leadStory->excerpt)
                            <p class="mt-3 line-clamp-2 max-w-xl text-sm leading-relaxed text-white/80">{{ $leadStory->excerpt }}</p>
                        @endif
                    </div>
                </article>

                <div class="grid gap-4">
                    @foreach ($otherStories as $story)
                        <article class="surface-card surface-card-hover group relative flex gap-4 p-3" data-reveal style="animation-delay: {{ $loop->index * 80 }}ms">
                            <div class="h-24 w-28 shrink-0 overflow-hidden rounded-xl bg-paper-3 sm:w-32">
                                <img src="{{ $storyImage($story) }}" alt="" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            </div>
                            <div class="min-w-0 py-1">
                                @if ($story->published_at)
                                    <time datetime="{{ $story->published_at->toDateString() }}" class="text-xs font-semibold text-subtle">{{ $story->published_at->translatedFormat('j F Y') }}</time>
                                @endif
                                <h3 class="mt-1 line-clamp-2 font-extrabold leading-snug text-ink-900 group-hover:text-forest-700">
                                    <a href="{{ route('news.show', $story->key) }}" class="after:absolute after:inset-0">{{ $story->title }}</a>
                                </h3>
                                @if ($story->category)
                                    <p class="mt-1 text-xs font-bold text-forest-700">{{ $story->category }}</p>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        @else
            <p class="mt-8 rounded-2xl border border-dashed border-hairline-strong bg-white p-8 text-center text-muted">{{ __('page.news.empty') }}</p>
        @endif
    </div>
</section>
