@extends('layouts.public')

@use('App\Support\Money')

@section('title', __('gift_page.title').' — '.__('brand.name'))
@section('meta_description', __('gift_page.intro'))

@php
    $locale = app()->getLocale();
    $firstDesign = reset($giftDesigns);
    $amounts = [25, 50, 100];
    $defaultAmount = 50;
@endphp

@section('content')
    <x-bader.page-hero
        :kicker="__('nav.gift')"
        :title="__('gift_page.title')"
        :intro="__('gift_page.intro')"
    />

    <section class="band-base section-y">
        <form method="GET" action="{{ route('donate') }}" class="container-bader grid grid-cols-1 items-start gap-8 lg:grid-cols-[minmax(0,1fr)_24rem]"
            data-gift
            data-amount-picker
            data-amount="{{ $defaultAmount }}"
            data-frequency="once">
            <input type="hidden" name="gift" value="1">
            <input type="hidden" name="frequency" value="once" data-picker-field="frequency">
            <input type="hidden" name="amount" value="{{ $defaultAmount }}" data-picker-field="amount">
            @if ($target['id'])
                <input type="hidden" name="target_type" value="{{ $target['type'] }}">
                <input type="hidden" name="target_id" value="{{ $target['id'] }}">
            @endif

            <div class="min-w-0 space-y-6">
                @if ($target['id'])
                    <div class="flex items-center gap-3 rounded-2xl border border-hairline bg-white p-4 text-sm" data-reveal>
                        <x-bader.icon name="heart" class="h-5 w-5 shrink-0 text-forest-600" />
                        <span class="text-muted">{{ __('gift_page.gifting_to_project') }}</span>
                        <strong class="text-ink-900">{{ $target['title'] }}</strong>
                    </div>
                @endif

                <fieldset class="surface-card p-6 sm:p-8" data-reveal>
                    <legend class="sr-only">{{ __('gift_page.step_design') }}</legend>
                    <p class="flex items-center gap-3 text-lg font-extrabold text-ink-900">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gold-500 text-sm text-teal-950">1</span>
                        {{ __('gift_page.step_design') }}
                    </p>
                    <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3">
                        @foreach ($giftDesigns as $design)
                            @php $designLabel = $design['label_'.$locale] ?? $design['label_ar']; @endphp
                            <label class="gift-design-option">
                                <input type="radio" name="gift_design" value="{{ $design['key'] }}" class="sr-only"
                                    data-gift-design
                                    data-label="{{ $designLabel }}"
                                    data-from="{{ $design['from'] }}"
                                    data-to="{{ $design['to'] }}"
                                    data-accent="{{ $design['accent'] }}"
                                    @checked($loop->first)>
                                <span class="block h-16 rounded-xl" style="background-image: linear-gradient(135deg, {{ $design['from'] }}, {{ $design['to'] }})"></span>
                                <span class="mt-2 block text-xs font-bold text-ink-800">{{ $designLabel }}</span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                <fieldset class="surface-card p-6 sm:p-8" data-reveal>
                    <legend class="sr-only">{{ __('gift_page.step_details') }}</legend>
                    <p class="flex items-center gap-3 text-lg font-extrabold text-ink-900">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gold-500 text-sm text-teal-950">2</span>
                        {{ __('gift_page.step_details') }}
                    </p>
                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <label class="block">
                            <span class="field-label">{{ __('gift_page.recipient_name') }} *</span>
                            <input type="text" name="gift_recipient_name" required maxlength="255" class="field-input" data-gift-bind="recipient">
                        </label>
                        <label class="block">
                            <span class="field-label">{{ __('gift_page.recipient_contact') }}</span>
                            <input type="text" name="gift_recipient_contact" maxlength="255" class="field-input" dir="ltr" placeholder="name@example.com / +970…">
                        </label>
                        <label class="block sm:col-span-2">
                            <span class="field-label">{{ __('gift_page.sender_name') }}</span>
                            <input type="text" name="gift_sender_name" maxlength="255" class="field-input" data-gift-bind="sender">
                        </label>
                        <label class="block sm:col-span-2">
                            <span class="field-label">{{ __('gift_page.message') }}</span>
                            <textarea name="gift_message" rows="3" maxlength="500" class="field-input" placeholder="{{ __('gift_page.message_placeholder') }}" data-gift-bind="message"></textarea>
                        </label>
                    </div>
                </fieldset>

                <fieldset class="surface-card p-6 sm:p-8" data-reveal>
                    <legend class="sr-only">{{ __('gift_page.step_amount') }}</legend>
                    <p class="flex items-center gap-3 text-lg font-extrabold text-ink-900">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gold-500 text-sm text-teal-950">3</span>
                        {{ __('gift_page.step_amount') }}
                    </p>
                    <div class="mt-5 grid grid-cols-3 gap-2 sm:grid-cols-4" role="group" aria-label="{{ __('donation.amount_label') }}">
                        @foreach ($amounts as $amount)
                            <button type="button" class="chip" data-amount-option data-amount="{{ $amount }}" aria-pressed="{{ $amount === $defaultAmount ? 'true' : 'false' }}" dir="ltr">{{ Money::format($amount) }}</button>
                        @endforeach
                        <label class="relative col-span-3 sm:col-span-1">
                            <span class="sr-only">{{ __('donation.custom_amount') }}</span>
                            <span class="pointer-events-none absolute inset-y-0 start-3 flex items-center text-sm font-bold text-subtle">$</span>
                            <input type="number" min="1" step="1" inputmode="decimal" class="field-input min-h-10 ps-7" placeholder="{{ __('donation.other_amount') }}" data-amount-input>
                        </label>
                    </div>
                    @unless ($target['id'])
                        <label class="mt-5 block">
                            <span class="field-label">{{ __('donate_page.category') }}</span>
                            <select name="category" class="field-input">
                                @foreach ($categories as $category => $categoryLabel)
                                    <option value="{{ $category }}" @selected($category === 'sadaqah')>{{ $categoryLabel }}</option>
                                @endforeach
                            </select>
                        </label>
                    @endunless
                </fieldset>
            </div>

            <aside class="space-y-4 lg:sticky lg:top-28">
                <p class="text-sm font-bold text-subtle">{{ __('gift_page.preview') }}</p>
                <div class="gift-card gift-card-preview" data-gift-preview data-design="{{ $firstDesign['key'] }}"
                    style="--gift-from: {{ $firstDesign['from'] }}; --gift-to: {{ $firstDesign['to'] }}; --gift-accent: {{ $firstDesign['accent'] }}">
                    <div class="flex items-center justify-between">
                        <img src="{{ \App\Support\SiteSettings::brandAsset('mark_star') }}" alt="" class="h-9 w-9">
                        <span class="rounded-full bg-white/15 px-3 py-1 text-xs font-bold" data-gift-design-label>{{ $firstDesign['label_'.$locale] ?? $firstDesign['label_ar'] }}</span>
                    </div>
                    <p class="mt-8 text-sm opacity-80">{{ __('gift_page.preview_to') }}</p>
                    <p class="mt-1 text-2xl font-extrabold text-[var(--gift-accent)]" data-gift-out="recipient" data-placeholder="{{ __('gift_page.preview_recipient') }}">{{ __('gift_page.preview_recipient') }}</p>
                    <p class="mt-5 min-h-12 text-sm leading-relaxed opacity-90" data-gift-out="message" data-placeholder="{{ __('gift_page.preview_message') }}">{{ __('gift_page.preview_message') }}</p>
                    <div class="mt-6 flex items-end justify-between gap-3 border-t border-white/20 pt-4 text-sm">
                        <div>
                            <p class="text-xs opacity-70">{{ __('gift_page.preview_from') }}</p>
                            <p class="font-bold" data-gift-out="sender" data-placeholder="{{ __('gift_page.preview_sender') }}">{{ __('gift_page.preview_sender') }}</p>
                        </div>
                        <p class="text-lg font-extrabold" dir="ltr">$<span data-amount-display>{{ $defaultAmount }}</span></p>
                    </div>
                </div>

                <button type="submit" class="btn-primary min-h-12 w-full text-base">
                    <x-bader.icon name="gift" class="h-5 w-5" />
                    {{ __('gift_page.continue') }}
                </button>
                <p class="flex items-start gap-2 text-xs leading-relaxed text-subtle">
                    <x-bader.icon name="info" class="mt-0.5 h-4 w-4 shrink-0" />
                    {{ __('gift_page.delivery_note') }}
                </p>
            </aside>
        </form>
    </section>
@endsection
