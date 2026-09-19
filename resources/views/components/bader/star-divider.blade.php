@props([
    'from' => 'bader-green-deep',
    'to' => 'paper',
])

@php
    $fill = $to === 'green' ? 'var(--bader-green)' : ($to === 'deep' ? 'var(--bader-green-deep)' : 'var(--bader-paper)');
@endphp

<div {{ $attributes->class(['bader-wave']) }} aria-hidden="true">
    <svg viewBox="0 0 1200 60" preserveAspectRatio="none" class="block h-10 w-full">
        <path d="M0 30 Q 100 8 200 30 T 400 30 T 600 30 T 800 30 T 1000 30 T 1200 30 V60 H0 Z" fill="{{ $fill }}" />
    </svg>
</div>
