@props(['designs'])

@php
    $locale = app()->getLocale();
    $previewDesigns = array_slice(array_values($designs), 0, 3);
    $cardPlacements = [
        'start-0 top-10 -rotate-6 [animation-delay:0s]',
        'end-0 top-0 rotate-3 [animation-delay:1.2s]',
        'start-1/2 top-40 -translate-x-1/2 rtl:translate-x-1/2 rotate-0 [animation-delay:2.4s]',
    ];
@endphp

<section class="band-base section-y overflow-hidden" aria-labelledby="gift-section-title">
    <div class="container-bader grid items-center gap-12 lg:grid-cols-2">
        <div data-reveal>
            <p class="kicker">{{ __('home.gift.kicker') }}</p>
            <h2 id="gift-section-title" class="section-title mt-3">{{ __('home.gift.title') }}</h2>
            <p class="section-lead mt-4 max-w-xl">{{ __('home.gift.text') }}</p>
            <ul class="mt-6 space-y-3 text-sm font-semibold text-ink-800">
                @foreach (['step_choose', 'step_write', 'step_send'] as $step)
                    <li class="flex items-center gap-3">
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-gold-500 text-teal-950">
                            <x-bader.icon name="check" class="h-4 w-4" />
                        </span>
                        {{ __('home.gift.'.$step) }}
                    </li>
                @endforeach
            </ul>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('gift') }}" class="btn-brand">
                    <x-bader.icon name="gift" class="h-4 w-4" />
                    {{ __('home.gift.cta') }}
                </a>
            </div>
        </div>

        <div class="relative mx-auto h-[22rem] w-full max-w-md sm:h-[26rem]" aria-hidden="true" data-reveal>
            <div class="absolute inset-8 rounded-full bg-gold-300/40 blur-3xl"></div>
            @foreach ($previewDesigns as $index => $design)
                <div class="gift-card absolute w-64 animate-float motion-reduce:animate-none sm:w-72 {{ $cardPlacements[$index] }}"
                    style="--gift-from: {{ $design['from'] }}; --gift-to: {{ $design['to'] }}; --gift-accent: {{ $design['accent'] }}">
                    <div class="flex items-center justify-between">
                        <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="" class="h-7 w-7 opacity-90">
                        <x-bader.icon name="gift" class="h-5 w-5 text-[var(--gift-accent)]" />
                    </div>
                    <p class="mt-6 text-xs font-bold opacity-80">{{ __('home.gift.card_label') }}</p>
                    <p class="mt-1 text-lg font-extrabold">{{ $design['label_'.$locale] ?? $design['label_ar'] }}</p>
                    <div class="mt-5 h-px bg-white/20"></div>
                    <p class="mt-3 text-xs opacity-80">{{ __('home.gift.card_from') }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
