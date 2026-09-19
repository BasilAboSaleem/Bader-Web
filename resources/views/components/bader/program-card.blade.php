@props([
    'title',
    'text' => null,
    'href' => '#',
])

<article {{ $attributes->class(['rounded-2xl border border-bader-green/12 bg-white p-5 shadow-[0_1px_0_rgba(16,36,24,0.04)]']) }} data-reveal>
    <a href="{{ $href }}" class="block">
        <span class="mb-4 inline-flex h-9 w-9 items-center justify-center rounded-full bg-bader-lime/70" aria-hidden="true">
            <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="" class="h-5 w-5">
        </span>
        <h3 class="text-lg font-semibold text-bader-green">{{ $title }}</h3>
        @if ($text)
            <p class="mt-2 text-sm leading-relaxed text-bader-ink/70">{{ $text }}</p>
        @endif
    </a>
</article>
