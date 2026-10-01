@extends('layouts.public')

@use('App\Support\Money')

@section('title', __('page.donate.title').' — '.__('brand.name'))
@section('meta_description', __('page.donate.intro'))

@php
    $locale = app()->getLocale();
    $amounts = array_values($target['amounts']);
    $selectedAmount = old('amount', $amount);
    $selectedFrequency = old('frequency', $frequency);
    $selectedCategory = old('donation_category', $category);
    $selectedMethod = old('payment_method', $paymentMethods[0]);
    $isGift = (bool) old('is_gift', $gift['enabled']);
    $selectedDesign = old('gift_card_design', $gift['design']);
    $isPresetAmount = in_array((float) $selectedAmount, array_map('floatval', $amounts), true);
    $targetImage = $target['image'] ? asset($target['image']) : asset('images/programs/water.jpg');
@endphp

@section('content')
    <x-bader.page-hero
        :kicker="__('page.donate.kicker')"
        :title="__('page.donate.title')"
        :intro="__('page.donate.intro')"
    />

    <section class="band-base section-y">
        <div class="container-bader"
            data-amount-picker
            data-amount="{{ $selectedAmount }}"
            data-frequency="{{ $selectedFrequency }}">

            @if (session('warning_message'))
                <div class="mb-6 flex items-start gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm font-semibold text-amber-800" role="status">
                    <x-bader.icon name="info" class="mt-0.5 h-5 w-5 shrink-0" />
                    {{ session('warning_message') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" role="alert">
                    <p class="font-bold">{{ __('donate_page.errors_title') }}</p>
                    <ul class="mt-2 list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('donate.checkout') }}" class="grid items-start gap-8 lg:grid-cols-[minmax(0,1fr)_23rem]" data-payment-form data-gift>
                @csrf
                <input type="hidden" name="target_type" value="{{ $target['type'] }}">
                @if ($target['id'])
                    <input type="hidden" name="target_id" value="{{ $target['id'] }}">
                @endif
                <input type="hidden" name="currency" value="USD">
                <input type="hidden" name="amount" value="{{ $selectedAmount }}" data-picker-field="amount">
                <input type="hidden" name="frequency" value="{{ $selectedFrequency }}" data-picker-field="frequency">

                <div class="min-w-0 space-y-6">
                    {{-- Amount --}}
                    <fieldset class="surface-card p-6 sm:p-8">
                        <legend class="sr-only">{{ __('donate_page.step_amount') }}</legend>
                        <p class="flex items-center gap-3 text-lg font-extrabold text-ink-900">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gold-500 text-sm text-teal-950">1</span>
                            {{ __('donate_page.step_amount') }}
                        </p>

                        @if ($target['allows_monthly'])
                            <div class="segmented mt-5" role="group" aria-label="{{ __('donation.frequency_label') }}">
                                <button type="button" data-frequency-option data-value="once" aria-pressed="{{ $selectedFrequency === 'once' ? 'true' : 'false' }}">{{ __('donation.frequency.once') }}</button>
                                <button type="button" data-frequency-option data-value="monthly" aria-pressed="{{ $selectedFrequency === 'monthly' ? 'true' : 'false' }}">{{ __('donation.frequency.monthly') }}</button>
                            </div>
                        @endif

                        <div class="mt-5 grid grid-cols-3 gap-2 sm:grid-cols-4" role="group" aria-label="{{ __('donation.amount_label') }}">
                            @foreach ($amounts as $presetAmount)
                                <button type="button" class="chip min-h-12 text-base" data-amount-option data-amount="{{ $presetAmount }}" aria-pressed="{{ (float) $presetAmount === (float) $selectedAmount ? 'true' : 'false' }}" dir="ltr">{{ Money::format($presetAmount) }}</button>
                            @endforeach
                            <label class="relative col-span-3 sm:col-span-1">
                                <span class="sr-only">{{ __('donation.custom_amount') }}</span>
                                <span class="pointer-events-none absolute inset-y-0 start-3 flex items-center text-sm font-bold text-subtle">$</span>
                                <input type="number" min="1" max="1000000" step="any" inputmode="decimal" class="field-input min-h-12 ps-7" placeholder="{{ __('donation.other_amount') }}" value="{{ $isPresetAmount ? '' : $selectedAmount }}" data-amount-input>
                            </label>
                        </div>

                        <div class="mt-6">
                            <p class="field-label">{{ __('donate_page.category') }}</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($categories as $donationCategory)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="donation_category" value="{{ $donationCategory }}" class="peer sr-only" @checked($selectedCategory === $donationCategory)>
                                        <span class="chip peer-checked:border-forest-700 peer-checked:bg-forest-700 peer-checked:text-white peer-focus-visible:outline-2 peer-focus-visible:outline-forest-600">{{ __('donation.category.'.$donationCategory) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </fieldset>

                    {{-- Donor --}}
                    <fieldset class="surface-card p-6 sm:p-8">
                        <legend class="sr-only">{{ __('donate_page.step_donor') }}</legend>
                        <p class="flex items-center gap-3 text-lg font-extrabold text-ink-900">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gold-500 text-sm text-teal-950">2</span>
                            {{ __('donate_page.step_donor') }}
                        </p>
                        <div class="mt-5 grid gap-4 sm:grid-cols-2">
                            <label class="block">
                                <span class="field-label">{{ __('form.field.donor_name') }}</span>
                                <input type="text" name="donor_name" value="{{ old('donor_name') }}" maxlength="255" class="field-input" autocomplete="name">
                            </label>
                            <label class="block">
                                <span class="field-label">{{ __('form.field.donor_email') }} <span class="font-normal text-subtle">{{ __('donate_page.email_hint') }}</span></span>
                                <input type="email" name="donor_email" value="{{ old('donor_email') }}" maxlength="255" class="field-input" dir="ltr" autocomplete="email">
                            </label>
                            <label class="block">
                                <span class="field-label">{{ __('form.field.phone') }}</span>
                                <input type="tel" name="donor_phone" value="{{ old('donor_phone') }}" maxlength="50" class="field-input" dir="ltr" autocomplete="tel">
                            </label>
                            <label class="flex items-center gap-3 self-end rounded-xl border border-hairline bg-paper px-4 py-3 text-sm font-semibold text-ink-800">
                                <input type="checkbox" name="is_anonymous" value="1" class="h-4 w-4 rounded accent-forest-700" @checked(old('is_anonymous'))>
                                {{ __('donate_page.anonymous') }}
                            </label>
                        </div>
                    </fieldset>

                    {{-- Gift --}}
                    <fieldset class="surface-card p-6 sm:p-8">
                        <legend class="sr-only">{{ __('donate_page.step_gift') }}</legend>
                        <label class="flex cursor-pointer items-center justify-between gap-4">
                            <span class="flex items-center gap-3 text-lg font-extrabold text-ink-900">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gold-500 text-teal-950">
                                    <x-bader.icon name="gift" class="h-4 w-4" />
                                </span>
                                {{ __('donate_page.step_gift') }}
                            </span>
                            <input type="checkbox" name="is_gift" value="1" class="toggle-switch" @checked($isGift) data-gift-toggle>
                        </label>
                        <p class="mt-2 text-sm text-muted">{{ __('donate_page.gift_hint') }}</p>

                        <div class="mt-5 space-y-4" data-gift-fields>
                            <div class="grid grid-cols-3 gap-2 sm:grid-cols-6">
                                @foreach ($giftDesigns as $design)
                                    @php $designLabel = $design['label_'.$locale] ?? $design['label_ar']; @endphp
                                    <label class="gift-design-option" title="{{ $designLabel }}">
                                        <input type="radio" name="gift_card_design" value="{{ $design['key'] }}" class="sr-only"
                                            data-gift-design data-label="{{ $designLabel }}" data-from="{{ $design['from'] }}" data-to="{{ $design['to'] }}" data-accent="{{ $design['accent'] }}"
                                            @checked($selectedDesign === $design['key'])>
                                        <span class="block h-10 rounded-lg" style="background-image: linear-gradient(135deg, {{ $design['from'] }}, {{ $design['to'] }})"></span>
                                        <span class="mt-1 block truncate text-[0.65rem] font-bold text-ink-800">{{ $designLabel }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <label class="block">
                                    <span class="field-label">{{ __('gift_page.recipient_name') }} *</span>
                                    <input type="text" name="gift_recipient_name" value="{{ old('gift_recipient_name', $gift['recipient_name']) }}" maxlength="255" class="field-input" data-gift-bind="recipient">
                                </label>
                                <label class="block">
                                    <span class="field-label">{{ __('gift_page.recipient_contact') }}</span>
                                    <input type="text" name="gift_recipient_contact" value="{{ old('gift_recipient_contact', $gift['recipient_contact']) }}" maxlength="255" class="field-input" dir="ltr">
                                </label>
                                <label class="block sm:col-span-2">
                                    <span class="field-label">{{ __('gift_page.sender_name') }}</span>
                                    <input type="text" name="gift_sender_name" value="{{ old('gift_sender_name', $gift['sender_name']) }}" maxlength="255" class="field-input" data-gift-bind="sender">
                                </label>
                                <label class="block sm:col-span-2">
                                    <span class="field-label">{{ __('gift_page.message') }}</span>
                                    <textarea name="gift_message" rows="2" maxlength="500" class="field-input" data-gift-bind="message">{{ old('gift_message', $gift['message']) }}</textarea>
                                </label>
                            </div>
                        </div>
                    </fieldset>

                    {{-- Payment --}}
                    <fieldset class="surface-card p-6 sm:p-8">
                        <legend class="sr-only">{{ __('donate_page.step_payment') }}</legend>
                        <p class="flex items-center gap-3 text-lg font-extrabold text-ink-900">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gold-500 text-sm text-teal-950">3</span>
                            {{ __('donate_page.step_payment') }}
                        </p>
                        <div class="mt-5 grid gap-3 sm:grid-cols-2">
                            @foreach ($paymentMethods as $method)
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="{{ $method }}" class="sr-only" data-payment-method="{{ $method }}" @checked($selectedMethod === $method)>
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-forest-600/10 text-forest-700">
                                        <x-bader.icon :name="$method === 'stripe' ? 'card' : 'bank'" class="h-5 w-5" />
                                    </span>
                                    <span class="min-w-0">
                                        <span class="block font-extrabold text-ink-900">{{ __('donation.method.'.$method) }}</span>
                                        <span class="block text-xs text-subtle">{{ __('donate_page.method_'.$method.'_hint') }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>

                        @if (in_array('stripe', $paymentMethods, true))
                            <div class="mt-4 flex flex-wrap items-center gap-2" data-card-fields>
                                <span class="payment-badge">VISA</span>
                                <span class="payment-badge">Mastercard</span>
                                <span class="payment-badge">Apple Pay</span>
                                <span class="text-xs text-subtle">{{ __('donate_page.stripe_redirect') }}</span>
                            </div>
                        @endif

                        <div class="mt-5 space-y-4 rounded-2xl bg-paper-2 p-5" data-bank-fields>
                            <p class="text-sm leading-relaxed text-muted">{{ __('donate_page.bank_intro') }}</p>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <label class="block">
                                    <span class="field-label">{{ __('form.field.reference_number') }} *</span>
                                    <input type="text" name="reference_number" value="{{ old('reference_number') }}" maxlength="100" class="field-input" dir="ltr">
                                </label>
                                <label class="block">
                                    <span class="field-label">{{ __('form.field.transfer_date') }}</span>
                                    <input type="date" name="transfer_date" value="{{ old('transfer_date', now()->toDateString()) }}" class="field-input">
                                </label>
                                <label class="block sm:col-span-2">
                                    <span class="field-label">{{ __('form.field.notes') }}</span>
                                    <textarea name="notes" rows="2" maxlength="1000" class="field-input">{{ old('notes') }}</textarea>
                                </label>
                            </div>
                            <a href="{{ route('contact') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-forest-700 hover:text-forest-800">
                                <x-bader.icon name="info" class="h-4 w-4" />
                                {{ __('donate_page.bank_details_link') }}
                            </a>
                        </div>
                    </fieldset>
                </div>

                {{-- Summary --}}
                <aside class="space-y-4 lg:sticky lg:top-28" aria-label="{{ __('donate_page.summary') }}">
                    <div class="surface-card overflow-hidden">
                        <div class="relative h-32 overflow-hidden bg-paper-3">
                            <img src="{{ $targetImage }}" alt="" class="h-full w-full object-cover">
                            <span class="absolute inset-0 bg-gradient-to-t from-teal-950/80 to-transparent"></span>
                            <p class="absolute bottom-3 start-4 end-4 font-extrabold text-white">{{ $target['title'] }}</p>
                        </div>
                        <div class="space-y-3 p-5 text-sm">
                            @if ($target['description'])
                                <p class="line-clamp-3 text-muted">{{ $target['description'] }}</p>
                            @endif
                            @if ($target['url'])
                                <a href="{{ $target['url'] }}" class="inline-flex items-center gap-1 text-xs font-bold text-forest-700 hover:text-forest-800">
                                    {{ __('donate_page.view_target') }}
                                    <x-bader.icon name="arrow" class="h-3.5 w-3.5" />
                                </a>
                            @endif
                            <dl class="space-y-2 border-t border-hairline pt-3">
                                <div class="flex justify-between gap-3">
                                    <dt class="text-subtle">{{ __('donation.amount_label') }}</dt>
                                    <dd class="font-extrabold text-ink-900" dir="ltr">$<span data-amount-display>{{ $selectedAmount }}</span></dd>
                                </div>
                                <div class="flex justify-between gap-3">
                                    <dt class="text-subtle">{{ __('donation.frequency_label') }}</dt>
                                    <dd class="font-bold text-ink-900">
                                        <span data-frequency-display="once" @if ($selectedFrequency !== 'once') hidden @endif>{{ __('donation.frequency.once') }}</span>
                                        <span data-frequency-display="monthly" @if ($selectedFrequency !== 'monthly') hidden @endif>{{ __('donation.frequency.monthly') }}</span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <div class="gift-card" data-gift-preview data-gift-summary>
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold opacity-80" data-gift-design-label></span>
                            <x-bader.icon name="gift" class="h-4 w-4 text-[var(--gift-accent)]" />
                        </div>
                        <p class="mt-3 text-xs opacity-70">{{ __('gift_page.preview_to') }}</p>
                        <p class="text-lg font-extrabold text-[var(--gift-accent)]" data-gift-out="recipient" data-placeholder="{{ __('gift_page.preview_recipient') }}">{{ __('gift_page.preview_recipient') }}</p>
                        <p class="mt-2 line-clamp-2 text-xs opacity-90" data-gift-out="message" data-placeholder="{{ __('gift_page.preview_message') }}">{{ __('gift_page.preview_message') }}</p>
                    </div>

                    <button type="submit" class="btn-primary min-h-13 w-full text-base">
                        <x-bader.icon name="lock" class="h-5 w-5" />
                        {{ __('donate_page.submit') }}
                        <span dir="ltr">$<span data-amount-display>{{ $selectedAmount }}</span></span>
                    </button>

                    <ul class="space-y-2 text-xs text-subtle">
                        <li class="flex items-center gap-2"><x-bader.icon name="shield" class="h-4 w-4 text-forest-600" /> {{ __('footer.secure_payments') }}</li>
                        <li class="flex items-center gap-2"><x-bader.icon name="check" class="h-4 w-4 text-forest-600" /> {{ __('donate_page.receipt_note') }}</li>
                    </ul>
                </aside>
            </form>
        </div>
    </section>
@endsection
