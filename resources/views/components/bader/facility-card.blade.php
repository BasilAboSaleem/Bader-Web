@props(['facility'])

@php
    $facilityImage = $facility->image ? asset($facility->image) : asset('images/programs/water.jpg');
@endphp

<article {{ $attributes->class(['surface-card surface-card-hover group relative flex flex-col overflow-hidden']) }}>
    <div class="relative aspect-[16/10] overflow-hidden bg-paper-3">
        <img src="{{ $facilityImage }}" alt="" loading="lazy" class="h-full w-full object-cover transition duration-700 ease-bader group-hover:scale-105">
        <span class="absolute start-3 top-3 inline-flex items-center gap-1.5 rounded-full bg-white/95 px-3 py-1 text-xs font-extrabold text-forest-700 shadow-card-xs">
            <span class="h-1.5 w-1.5 animate-pulse-dot rounded-full bg-forest-600"></span>
            {{ __('facility_page.active') }}
        </span>
    </div>
    <div class="flex flex-1 flex-col p-5">
        @if ($facility->location)
            <p class="inline-flex items-center gap-1.5 text-xs font-semibold text-subtle">
                <x-bader.icon name="map-pin" class="h-3.5 w-3.5" />
                {{ $facility->location }}
            </p>
        @endif
        <h3 class="mt-2 text-lg font-extrabold leading-snug text-ink-900 transition group-hover:text-forest-700">
            <a href="{{ route('facilities.show', $facility->key) }}" class="after:absolute after:inset-0">{{ $facility->name }}</a>
        </h3>
        @if ($facility->description)
            <p class="mt-2 line-clamp-2 text-sm leading-7 text-muted">{{ $facility->description }}</p>
        @endif
        <span class="mt-auto inline-flex items-center gap-1.5 pt-4 text-sm font-extrabold text-forest-700">
            {{ __('facility_page.view') }}
            <x-bader.icon name="arrow" class="h-4 w-4 transition-transform group-hover:translate-x-1 rtl:group-hover:-translate-x-1" />
        </span>
    </div>
</article>
