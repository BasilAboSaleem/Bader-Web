@props([
    'title',
    'donateParams' => [],
    'amounts' => \App\Models\Campaign::DEFAULT_PRESET_AMOUNTS,
    'allowsMonthly' => true,
    'frequency' => 'once',
    'shareTitle' => null,
])

@use('App\Support\Money')

@php
    $amounts = array_values($amounts);
    $defaultAmount = $amounts[1] ?? $amounts[0] ?? 25;
@endphp

<div {{ $attributes->class(['surface-card overflow-hidden']) }}
    data-amount-picker
    data-donate-base="{{ route('donate', $donateParams) }}"
    data-amount="{{ $defaultAmount }}"
    data-frequency="{{ $frequency }}">
    <div class="border-b border-hairline bg-paper-2 px-5 py-4">
        <p class="inline-flex items-center gap-2 text-xs font-extrabold text-forest-700">
            <x-bader.icon name="hand-heart" class="h-4 w-4" />
            {{ __('donate_box.kicker') }}
        </p>
        <p class="mt-1 font-extrabold text-ink-900">{{ $title }}</p>
    </div>

    <div class="space-y-4 p-5">
        {{ $slot }}

        @if ($allowsMonthly)
            <div class="segmented w-full" role="group" aria-label="{{ __('donation.frequency_label') }}">
                <button type="button" class="flex-1" data-frequency-option data-value="once" aria-pressed="{{ $frequency === 'once' ? 'true' : 'false' }}">{{ __('donation.frequency.once') }}</button>
                <button type="button" class="flex-1" data-frequency-option data-value="monthly" aria-pressed="{{ $frequency === 'monthly' ? 'true' : 'false' }}">{{ __('donation.frequency.monthly') }}</button>
            </div>
        @endif

        <div>
            <p class="field-label">{{ __('donation.amount_label') }}</p>
            <div class="grid grid-cols-3 gap-2" role="group" aria-label="{{ __('donation.amount_label') }}">
                @foreach ($amounts as $amount)
                    <button type="button" class="chip px-2" data-amount-option data-amount="{{ $amount }}" aria-pressed="{{ $amount == $defaultAmount ? 'true' : 'false' }}" dir="ltr">{{ Money::format($amount) }}</button>
                @endforeach
            </div>
        </div>

        <label class="relative block">
            <span class="sr-only">{{ __('donation.custom_amount') }}</span>
            <span class="pointer-events-none absolute inset-y-0 start-3 flex items-center text-sm font-bold text-subtle">$</span>
            <input type="number" min="1" step="1" inputmode="decimal" class="field-input ps-7" placeholder="{{ __('donation.other_amount') }}" data-amount-input>
        </label>

        <a href="{{ route('donate', array_merge($donateParams, ['amount' => $defaultAmount, 'frequency' => $frequency])) }}" class="btn-primary min-h-12 w-full text-base" data-donate-link>
            <x-bader.icon name="heart" class="h-5 w-5" />
            <span data-frequency-display="once" @if ($frequency !== 'once') hidden @endif>{{ __('donate_box.donate_once') }}</span>
            <span data-frequency-display="monthly" @if ($frequency !== 'monthly') hidden @endif>{{ __('donate_box.donate_monthly') }}</span>
            <span dir="ltr">$<span data-amount-display>{{ $defaultAmount }}</span></span>
        </a>

        <div class="flex items-center justify-between gap-3 border-t border-hairline pt-4 text-xs text-subtle">
            <span class="inline-flex items-center gap-1.5">
                <x-bader.icon name="lock" class="h-4 w-4 text-forest-600" />
                {{ __('footer.secure_payments') }}
            </span>
            <button type="button" class="inline-flex items-center gap-1.5 font-bold text-forest-700 hover:text-forest-800"
                data-share
                data-share-url="{{ url()->current() }}"
                data-share-title="{{ $shareTitle ?? $title }}"
                data-copied-text="{{ __('common.link_copied') }}">
                <x-bader.icon name="share" class="h-4 w-4" />
                <span data-share-label>{{ __('common.share') }}</span>
            </button>
        </div>
    </div>
</div>
