@extends('layouts.public')

@section('title', __('page.contact.title').' — '.__('brand.name'))

@section('content')
    <section class="bg-bader-green-deep text-white">
        <div class="mx-auto max-w-6xl px-4 py-16 sm:py-20">
            <x-bader.section-heading :kicker="__('page.contact.kicker')" :title="__('page.contact.title')" theme="dark" />
            <p class="mt-5 max-w-2xl text-sm leading-relaxed text-white/75 sm:text-base">{{ __('page.contact.intro') }}</p>
        </div>
    </section>

    @php
        use App\Support\SiteSettings;
        $channelValues = [
            'email' => SiteSettings::contactEmail(),
            'phone' => SiteSettings::contactPhone(),
            'location' => SiteSettings::contactAddress(),
        ];
    @endphp

    <section class="bg-bader-paper">
        <div class="mx-auto grid max-w-6xl gap-6 px-4 py-14 sm:grid-cols-3">
            @foreach (['email', 'phone', 'location'] as $channel)
                <article class="rounded-2xl border border-bader-green/10 bg-white p-6" data-reveal>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-bader-green">{{ __('contact.'.$channel.'.label') }}</p>
                    <p class="mt-3 text-lg font-semibold text-bader-ink">{{ $channelValues[$channel] ?? __('contact.'.$channel.'.value') }}</p>
                    <p class="mt-2 text-sm leading-relaxed text-bader-ink/65">{{ __('contact.'.$channel.'.note') }}</p>
                </article>
            @endforeach
        </div>
    </section>
@endsection
