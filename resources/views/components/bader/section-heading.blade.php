@props([
    'kicker' => null,
    'title',
    'align' => 'start',
    'theme' => 'light',
])

@php
    $alignClass = $align === 'center' ? 'text-center mx-auto' : 'text-start';
    $themeClasses = $theme === 'dark'
        ? ['text-white', 'kicker' => 'text-bader-lime', 'title' => 'text-white']
        : ['text-bader-ink', 'kicker' => 'text-bader-green', 'title' => 'text-bader-ink'];
@endphp

<header {{ $attributes->class(['max-w-2xl', $alignClass, $themeClasses[0]]) }} data-reveal>
    @if ($kicker)
        <p class="mb-2 text-xs font-semibold uppercase tracking-[0.18em] {{ $themeClasses['kicker'] }}">{{ $kicker }}</p>
    @endif
    <h2 class="text-2xl font-semibold {{ $themeClasses['title'] }} sm:text-3xl">{{ $title }}</h2>
    @if ($slot->isNotEmpty())
        <div class="mt-3 text-sm leading-relaxed {{ $theme === 'dark' ? 'text-white/75' : 'text-bader-ink/75' }}">{{ $slot }}</div>
    @endif
</header>
