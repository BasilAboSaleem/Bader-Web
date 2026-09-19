@props([
    'href' => '#',
    'variant' => 'primary',
])

@php
    $classes = match ($variant) {
        'line' => 'border border-bader-green bg-transparent text-bader-green hover:bg-bader-green hover:text-white',
        'light' => 'bg-white text-bader-green hover:bg-bader-lime',
        default => 'border border-bader-green bg-bader-green text-white hover:bg-bader-green-deep',
    };
@endphp

<a href="{{ $href }}" {{ $attributes->class(['inline-flex min-h-11 items-center justify-center rounded-full px-5 text-sm font-semibold transition-colors duration-200', $classes]) }}>
    {{ $slot }}
</a>
