@props(['kicker', 'title', 'intro' => null, 'breadcrumbs' => []])

@php
    $iconRoute = \Illuminate\Support\Str::before(request()->route()?->getName() ?? 'about', '.');
@endphp

<section class="relative isolate overflow-hidden border-b border-hairline bg-paper-2">
    <div class="pointer-events-none absolute -end-24 -top-24 -z-10 h-72 w-72 rounded-full bg-gold-300/40 blur-3xl" aria-hidden="true"></div>
    <div class="pointer-events-none absolute -bottom-28 -start-16 -z-10 h-64 w-64 rounded-full bg-forest-500/10 blur-3xl" aria-hidden="true"></div>
    <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="" class="pointer-events-none absolute -end-10 top-1/2 -z-10 hidden h-72 w-72 -translate-y-1/2 opacity-[0.05] md:block" aria-hidden="true">

    <div class="container-bader py-10 sm:py-14">
        <nav aria-label="{{ __('common.breadcrumb') }}" class="animate-fade-up">
            <ol class="flex flex-wrap items-center gap-1.5 text-xs font-semibold text-subtle">
                <li><a href="{{ route('home') }}" class="transition hover:text-forest-700">{{ __('nav.home') }}</a></li>
                @foreach ($breadcrumbs as $crumb)
                    <li aria-hidden="true"><x-bader.icon name="chevron-end" class="h-3 w-3" /></li>
                    <li><a href="{{ $crumb['url'] }}" class="transition hover:text-forest-700">{{ $crumb['label'] }}</a></li>
                @endforeach
                <li aria-hidden="true"><x-bader.icon name="chevron-end" class="h-3 w-3" /></li>
                <li class="text-ink-800" aria-current="page">{{ $title }}</li>
            </ol>
        </nav>

        <div class="mt-6 flex animate-fade-up items-center gap-3 [animation-delay:60ms]">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-white text-forest-700 shadow-card-sm ring-1 ring-hairline">
                <x-bader.nav-icon :route="$iconRoute" class="h-5 w-5" />
            </span>
            <p class="kicker">{{ $kicker }}</p>
        </div>
        <h1 class="mt-4 max-w-4xl animate-fade-up text-3xl font-extrabold leading-tight text-ink-900 [animation-delay:120ms] sm:text-4xl lg:text-5xl">
            {{ $title }}
        </h1>
        @if (filled($intro))
            <p class="mt-4 max-w-3xl animate-fade-up text-base leading-8 text-muted [animation-delay:180ms] sm:text-lg">
                {{ $intro }}
            </p>
        @endif
        {{ $slot }}
    </div>
</section>
