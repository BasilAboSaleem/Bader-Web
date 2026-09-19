@props([
    'title',
    'text' => null,
    'href' => '#',
])

<article {{ $attributes->class(['bader-card rounded-2xl border border-bader-green/12 bg-white p-6']) }} data-reveal>
    <a href="{{ $href }}" class="block">
        <span class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-bader-lime/60" aria-hidden="true">
            <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="" class="h-6 w-6">
        </span>
        <h3 class="text-lg font-semibold text-bader-green">{{ $title }}</h3>
        @if ($text)
            <p class="mt-2 text-sm leading-relaxed text-bader-ink/70">{{ $text }}</p>
        @endif
    </a>
</article>
