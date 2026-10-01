@props(['story', 'compact' => false])

@php
    $storyUrl = route('news.show', $story->key);
    $storyImage = $story->image ? asset($story->image) : asset('images/programs/education.jpg');
@endphp

<article {{ $attributes->class(['surface-card surface-card-hover group relative flex flex-col overflow-hidden']) }}>
    <div @class(['relative overflow-hidden bg-paper-3', 'aspect-[16/10]' => ! $compact, 'aspect-[16/9]' => $compact])>
        <img src="{{ $storyImage }}" alt="" loading="lazy" class="h-full w-full object-cover transition duration-700 ease-bader group-hover:scale-105">
        <div class="absolute inset-x-0 top-0 flex flex-wrap gap-2 p-3">
            @if ($story->category)
                <span class="rounded-full bg-white/95 px-3 py-1 text-xs font-extrabold text-forest-700 shadow-card-xs">{{ $story->category }}</span>
            @endif
            @if ($story->is_featured)
                <span class="inline-flex items-center gap-1 rounded-full bg-gold-500 px-3 py-1 text-xs font-extrabold text-teal-950 shadow-card-xs">
                    <x-bader.icon name="sparkle" class="h-3.5 w-3.5" />
                    {{ __('news_page.featured') }}
                </span>
            @endif
        </div>
    </div>

    <div @class(['flex flex-1 flex-col', 'p-5 sm:p-6' => ! $compact, 'p-4' => $compact])>
        @if ($story->published_at)
            <time datetime="{{ $story->published_at->toDateString() }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-subtle">
                <x-bader.icon name="calendar" class="h-3.5 w-3.5" />
                {{ $story->published_at->translatedFormat('j F Y') }}
            </time>
        @endif
        <h3 @class(['mt-2 font-extrabold leading-snug text-ink-900 transition group-hover:text-forest-700', 'text-lg' => ! $compact, 'line-clamp-2 text-base' => $compact])>
            <a href="{{ $storyUrl }}" class="after:absolute after:inset-0">{{ $story->title }}</a>
        </h3>
        @if (! $compact && $story->excerpt)
            <p class="mt-2 line-clamp-3 text-sm leading-7 text-muted">{{ $story->excerpt }}</p>
        @endif
        <span class="mt-auto inline-flex items-center gap-1.5 pt-4 text-sm font-extrabold text-forest-700">
            {{ __('common.read_more') }}
            <x-bader.icon name="arrow" class="h-4 w-4 transition-transform group-hover:translate-x-1 rtl:group-hover:-translate-x-1" />
        </span>
    </div>
</article>
