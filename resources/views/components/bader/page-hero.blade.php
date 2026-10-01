@props(['kicker', 'title', 'intro'])

@php
    $iconRoute = request()->route()?->getName() ?? 'about';
@endphp

<section class="relative isolate overflow-hidden border-b border-sand-200 bg-white section-pad !py-12 sm:!py-16">
    <div class="pointer-events-none absolute -end-16 -top-20 -z-10 h-64 w-64 rounded-full bg-sand-50 blur-2xl sm:h-80 sm:w-80" aria-hidden="true"></div>
    <div class="mx-auto max-w-7xl">
        <div class="flex items-center gap-3">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl border border-forest-700/10 bg-forest-700/10 text-forest-700 shadow-sm">
                <x-bader.nav-icon :route="$iconRoute" class="h-5 w-5" />
            </span>
            <p class="{{ app()->isLocale('ar') ? 'text-sm font-bold text-forest-700' : 'eyebrow' }}">{{ $kicker }}</p>
        </div>
        <h1 class="mt-3 max-w-4xl font-display text-4xl font-semibold leading-tight text-ink-900 sm:text-5xl lg:text-6xl">
            {{ $title }}
        </h1>
        @if (filled($intro))
            <p class="mt-5 max-w-3xl text-base leading-8 text-ink-700/80 sm:mt-6 sm:text-lg">
                {{ $intro }}
            </p>
        @endif
        <div class="mt-8 h-1 w-16 rounded-full bg-gold-500" aria-hidden="true"></div>
    </div>
</section>
