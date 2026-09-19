@props([
    'title',
    'excerpt',
    'category',
    'date',
    'href' => '#',
])

<article {{ $attributes->class(['group overflow-hidden rounded-2xl border border-white/10 bg-bader-green/40']) }} data-reveal>
    <a href="{{ $href }}" class="block p-6">
        <div class="mb-4 flex h-28 items-center justify-center rounded-xl bg-bader-green-deep/80">
            <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="" class="h-14 w-14 opacity-90">
        </div>
        <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-bader-lime">
            {{ $category }}
            <span class="mx-2 text-white/30">•</span>
            <time class="text-white/55 normal-case tracking-normal" datetime="{{ $date }}">{{ $date }}</time>
        </p>
        <h3 class="mt-3 text-lg font-semibold leading-snug text-white group-hover:text-bader-lime">{{ $title }}</h3>
        <p class="mt-2 text-sm leading-relaxed text-white/70">{{ $excerpt }}</p>
    </a>
</article>
