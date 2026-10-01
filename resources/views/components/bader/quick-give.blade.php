@props(['options'])

@use('App\Support\Money')

@php
    $firstOption = $options[0] ?? ['category' => 'general', 'amount' => 25];
@endphp

<section class="relative z-20 -mt-16 sm:-mt-12" aria-labelledby="quick-give-title">
    <div class="container-bader">
        <div class="rounded-3xl border border-hairline bg-white p-5 shadow-card-float sm:p-7"
            data-amount-picker
            data-donate-base="{{ route('donate') }}"
            data-amount="{{ $firstOption['amount'] }}"
            data-category="{{ $firstOption['category'] }}"
            data-frequency="once">
            <div class="grid gap-6 lg:grid-cols-[minmax(0,15rem)_minmax(0,1fr)_minmax(0,17rem)] lg:items-center">
                <div>
                    <p class="inline-flex items-center gap-2 text-xs font-extrabold text-forest-700">
                        <x-bader.icon name="hand-heart" class="h-4 w-4" />
                        {{ __('home.quick_give.kicker') }}
                    </p>
                    <h2 id="quick-give-title" class="mt-1 text-xl font-extrabold text-ink-900">{{ __('home.quick_give.title') }}</h2>
                    <p class="mt-1 text-sm text-muted">{{ __('home.quick_give.text') }}</p>
                </div>

                <div class="grid grid-cols-2 gap-2 sm:grid-cols-4" role="group" aria-label="{{ __('home.quick_give.choose') }}">
                    @foreach ($options as $option)
                        <button type="button"
                            class="quick-give-option"
                            data-amount-option
                            data-amount="{{ $option['amount'] }}"
                            data-category="{{ $option['category'] }}"
                            aria-pressed="{{ $loop->first ? 'true' : 'false' }}">
                            <span class="text-xs font-bold">{{ $option['label'] }}</span>
                            <span class="text-lg font-extrabold" dir="ltr">{{ Money::format($option['amount']) }}</span>
                        </button>
                    @endforeach
                </div>

                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <div class="segmented shrink-0" role="group" aria-label="{{ __('donation.frequency_label') }}">
                            <button type="button" data-frequency-option data-value="once" aria-pressed="true">{{ __('donation.frequency.once') }}</button>
                            <button type="button" data-frequency-option data-value="monthly" aria-pressed="false">{{ __('donation.frequency.monthly') }}</button>
                        </div>
                        <label class="relative min-w-0 flex-1">
                            <span class="sr-only">{{ __('donation.custom_amount') }}</span>
                            <span class="pointer-events-none absolute inset-y-0 start-3 flex items-center text-sm font-bold text-subtle">$</span>
                            <input type="number" min="1" step="1" inputmode="decimal" class="field-input min-h-10 ps-7 text-sm" placeholder="{{ __('donation.other_amount') }}" data-amount-input>
                        </label>
                    </div>
                    <a href="{{ route('donate', ['amount' => $firstOption['amount'], 'category' => $firstOption['category']]) }}" class="btn-primary min-h-12 w-full text-base" data-donate-link>
                        <x-bader.icon name="heart" class="h-5 w-5" />
                        {{ __('home.quick_give.submit') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
