@extends('layouts.public')

@use('App\Support\SiteSettings')

@section('title', __('page.contact.title').' — '.__('brand.name'))
@section('meta_description', SiteSettings::pageIntro('contact'))

@php
    $whatsappNumber = SiteSettings::whatsappNumber();
    $channels = array_values(array_filter([
        [
            'icon' => 'mail',
            'label' => __('contact.email.label'),
            'value' => SiteSettings::contactEmail(),
            'note' => __('contact.email.note'),
            'href' => 'mailto:'.SiteSettings::contactEmail(),
            'ltr' => true,
        ],
        [
            'icon' => 'phone',
            'label' => __('contact.phone.label'),
            'value' => SiteSettings::contactPhone(),
            'note' => __('contact.phone.note'),
            'href' => 'tel:'.preg_replace('/[^\d+]/', '', SiteSettings::contactPhone()),
            'ltr' => true,
        ],
        $whatsappNumber ? [
            'icon' => 'whatsapp',
            'label' => __('contact_page.whatsapp_label'),
            'value' => '+'.$whatsappNumber,
            'note' => __('contact_page.whatsapp_note'),
            'href' => 'https://wa.me/'.$whatsappNumber,
            'ltr' => true,
        ] : null,
        [
            'icon' => 'map-pin',
            'label' => __('contact.location.label'),
            'value' => SiteSettings::contactAddress(),
            'note' => __('contact.location.note'),
            'href' => null,
            'ltr' => false,
        ],
    ]));
@endphp

@section('content')
    <x-bader.page-hero
        :kicker="__('page.contact.kicker')"
        :title="__('page.contact.title')"
        :intro="SiteSettings::pageIntro('contact')"
    />

    <section class="band-base section-y">
        <div class="container-bader grid grid-cols-1 items-start gap-8 lg:grid-cols-[minmax(0,0.85fr)_minmax(0,1.15fr)]">
            <div data-reveal>
                <p class="kicker">{{ __('contact_page.channels_kicker') }}</p>
                <h2 class="section-title mt-2">{{ __('contact_page.channels_title') }}</h2>
                <ul class="mt-6 grid grid-cols-1 gap-3">
                    @foreach ($channels as $channel)
                        @php $channelTag = $channel['href'] ? 'a' : 'div'; @endphp
                        <li>
                            <{{ $channelTag }}
                                @if ($channel['href']) href="{{ $channel['href'] }}" @endif
                                @if (str_starts_with((string) $channel['href'], 'https://')) target="_blank" rel="noopener" @endif
                                @class(['surface-card flex items-center gap-4 p-4 sm:p-5', 'surface-card-hover group' => $channel['href']])>
                                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-forest-600/10 text-forest-700 transition group-hover:bg-forest-700 group-hover:text-white">
                                    <x-bader.icon :name="$channel['icon']" class="h-5 w-5" />
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-xs font-bold text-subtle">{{ $channel['label'] }}</span>
                                    <span class="mt-0.5 block truncate font-extrabold text-ink-900" @if ($channel['ltr']) dir="ltr" @endif>{{ $channel['value'] }}</span>
                                    <span class="mt-0.5 block text-xs text-muted">{{ $channel['note'] }}</span>
                                </span>
                                @if ($channel['href'])
                                    <x-bader.icon name="arrow" class="h-4 w-4 shrink-0 text-subtle transition group-hover:text-forest-700" />
                                @endif
                            </{{ $channelTag }}>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-6 flex items-start gap-3 rounded-2xl bg-paper-2 p-4 text-sm leading-7 text-muted">
                    <x-bader.icon name="clock" class="mt-1 h-4 w-4 shrink-0 text-forest-600" />
                    {{ __('contact_page.response_note') }}
                </div>
            </div>

            <div class="surface-card p-6 sm:p-8" data-reveal>
                <p class="kicker">{{ __('form.contact.kicker') }}</p>
                <h2 class="mt-2 text-2xl font-extrabold text-ink-900">{{ __('form.contact.heading') }}</h2>
                <p class="mt-2 text-sm text-muted">{{ __('form.contact.subheading') }}</p>

                @if (session('success_message'))
                    <div class="mt-5 flex items-start gap-3 rounded-2xl border border-forest-600/20 bg-forest-600/10 p-4 text-sm font-semibold text-forest-800" role="status" data-flash>
                        <x-bader.icon name="check" class="h-5 w-5 shrink-0" />
                        {{ session('success_message') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mt-5 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" role="alert" data-flash>
                        <ul class="list-inside list-disc space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('contact.submit') }}" class="mt-6 space-y-4">
                    @csrf
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block">
                            <span class="field-label">{{ __('form.field.name') }} *</span>
                            <input type="text" name="name" value="{{ old('name') }}" required autocomplete="name" class="field-input">
                        </label>
                        <label class="block">
                            <span class="field-label">{{ __('form.field.email') }} *</span>
                            <input type="email" name="email" value="{{ old('email') }}" required dir="ltr" autocomplete="email" class="field-input">
                        </label>
                        <label class="block">
                            <span class="field-label">{{ __('form.field.phone') }}</span>
                            <input type="tel" name="phone" value="{{ old('phone') }}" dir="ltr" autocomplete="tel" class="field-input">
                        </label>
                        <label class="block">
                            <span class="field-label">{{ __('form.field.subject') }}</span>
                            <input type="text" name="subject" value="{{ old('subject') }}" class="field-input">
                        </label>
                    </div>
                    <label class="block">
                        <span class="field-label">{{ __('form.field.message') }} *</span>
                        <textarea name="message" rows="5" required class="field-input">{{ old('message') }}</textarea>
                    </label>
                    <button type="submit" class="btn-brand min-h-12 w-full px-8 sm:w-auto">
                        {{ __('form.submit_contact') }}
                        <x-bader.icon name="arrow" class="h-4 w-4" />
                    </button>
                </form>
            </div>
        </div>
    </section>
@endsection
