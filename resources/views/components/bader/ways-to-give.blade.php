@props(['goldPricePerGram'])

@php
    $ways = [
        ['key' => 'monthly', 'icon' => 'repeat', 'url' => route('donate', ['frequency' => 'monthly'])],
        ['key' => 'sadaqah_jariyah', 'icon' => 'sprout', 'url' => route('donate', ['category' => 'sadaqah_jariyah'])],
        ['key' => 'gift', 'icon' => 'gift', 'url' => route('gift')],
        ['key' => 'partner', 'icon' => 'building', 'url' => route('partners')],
    ];
@endphp

<section class="band-tint section-y" aria-labelledby="ways-to-give-title">
    <div class="container-bader">
        <div class="max-w-2xl" data-reveal>
            <p class="kicker">{{ __('home.support_kicker') }}</p>
            <h2 id="ways-to-give-title" class="section-title mt-3">{{ __('home.support_title') }}</h2>
            <p class="section-lead mt-3">{{ __('home.support_intro') }}</p>
        </div>

        <div class="mt-10 grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,24rem)]">
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($ways as $way)
                    <a href="{{ $way['url'] }}" class="surface-card surface-card-hover group flex flex-col p-6" data-reveal style="animation-delay: {{ $loop->index * 70 }}ms">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-forest-600/10 text-forest-700 transition group-hover:bg-forest-700 group-hover:text-white">
                            <x-bader.icon :name="$way['icon']" class="h-6 w-6" />
                        </span>
                        <h3 class="mt-5 text-lg font-extrabold text-ink-900">{{ __('home.ways.'.$way['key'].'_title') }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-muted">{{ __('home.ways.'.$way['key'].'_text') }}</p>
                        <span class="mt-auto inline-flex items-center gap-1.5 pt-5 text-sm font-extrabold text-forest-700">
                            {{ __('home.support_action') }}
                            <x-bader.icon name="arrow" class="h-4 w-4 transition group-hover:translate-x-1 rtl:group-hover:-translate-x-1" />
                        </span>
                    </a>
                @endforeach
            </div>

            <div class="rounded-3xl bg-teal-950 p-6 text-white shadow-card-lg sm:p-7"
                data-zakat
                data-mode="money"
                data-gold-price="{{ $goldPricePerGram }}"
                data-donate-base="{{ route('donate') }}"
                data-reveal>
                <p class="inline-flex items-center gap-2 text-xs font-extrabold text-gold-400">
                    <x-bader.icon name="calculator" class="h-4 w-4" />
                    {{ __('home.zakat.kicker') }}
                </p>
                <h3 class="mt-2 text-xl font-extrabold">{{ __('home.zakat.title') }}</h3>
                <p class="mt-1 text-sm text-white/70">{{ __('home.zakat.text') }}</p>

                <div class="mt-5 space-y-3" data-zakat-section="money">
                    @foreach (['cash', 'savings'] as $field)
                        <label class="block">
                            <span class="mb-1 block text-xs font-bold text-white/80">{{ __('zakat.field.'.$field) }}</span>
                            <span class="relative block">
                                <span class="pointer-events-none absolute inset-y-0 start-3 flex items-center text-sm font-bold text-ink-700">$</span>
                                <input type="number" min="0" step="any" inputmode="decimal" class="field-input ps-7" placeholder="0" data-zakat-input="{{ $field }}">
                            </span>
                        </label>
                    @endforeach
                </div>

                <div class="mt-5 rounded-2xl bg-white/10 p-4">
                    <div class="flex items-baseline justify-between gap-3">
                        <span class="text-sm text-white/75">{{ __('zakat.due') }}</span>
                        <span class="text-2xl font-extrabold text-gold-400" dir="ltr">$<span data-zakat-output="due">0</span></span>
                    </div>
                    <p class="mt-2 text-xs text-white/60">{{ __('zakat.nisab') }}: <span dir="ltr">$<span data-zakat-output="nisab">0</span></span></p>
                    <p class="mt-2 text-xs font-semibold text-gold-300" data-zakat-status="below" hidden>{{ __('zakat.below_nisab') }}</p>
                </div>

                <div class="mt-5 flex flex-wrap gap-2">
                    <a href="{{ route('donate', ['category' => 'zakat']) }}" class="btn-primary flex-1" data-zakat-pay>
                        {{ __('zakat.pay') }}
                    </a>
                    <a href="{{ route('zakat') }}" class="btn-ghost">{{ __('home.zakat.full_calculator') }}</a>
                </div>
            </div>
        </div>
    </div>
</section>
