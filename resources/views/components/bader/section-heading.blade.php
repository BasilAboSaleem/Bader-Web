@props([
    'kicker' => null,
    'title',
    'align' => 'start',
])

@php
    $alignClass = $align === 'center' ? 'text-center mx-auto' : 'text-start';
@endphp

<header {{ $attributes->class(['max-w-2xl', $alignClass]) }} data-reveal>
    @if ($kicker)
        <p class="mb-2 text-xs font-semibold uppercase tracking-[0.18em] text-bader-green">{{ $kicker }}</p>
    @endif
    <h2 class="text-2xl font-semibold text-bader-ink sm:text-3xl">{{ $title }}</h2>
    @if ($slot->isNotEmpty())
        <div class="mt-3 text-sm leading-relaxed text-bader-ink/75">{{ $slot }}</div>
    @endif
</header>
