@props([
    'title',
    'text' => null,
    'status' => null,
    'goal' => null,
    'currency' => null,
    'href' => '#',
])

<article {{ $attributes->class(['overflow-hidden rounded-3xl bg-bader-green text-white']) }} data-reveal>
    <a href="{{ $href }}" class="block p-6 sm:p-8">
        @if ($status)
            <span class="mb-4 inline-flex rounded-full bg-bader-lime px-3 py-1 text-xs font-semibold text-bader-green-deep">{{ $status }}</span>
        @endif
        <h3 class="text-2xl font-semibold">{{ $title }}</h3>
        @if ($text)
            <p class="mt-3 max-w-xl text-sm leading-relaxed text-white/80">{{ $text }}</p>
        @endif
        @if ($goal && $currency)
            <p class="mt-5 text-sm text-white/70">
                {{ __('campaign.goal') }}
                <strong class="ms-1 text-lg text-bader-lime">{{ number_format($goal) }} {{ $currency }}</strong>
            </p>
        @endif
        <span class="mt-6 inline-block text-sm font-semibold text-bader-lime">{{ __('nav.donate') }}</span>
    </a>
</article>
