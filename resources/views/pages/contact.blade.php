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

    {{-- Interactive Contact & Inquiry Form --}}
    <section class="border-t border-bader-green/10 bg-white py-16">
        <div class="mx-auto max-w-3xl px-4">
            <x-bader.section-heading :kicker="__('form.contact.kicker')" :title="__('form.contact.heading')" />
            <p class="mt-3 text-sm text-bader-ink/70">{{ __('form.contact.subheading') }}</p>

            @if (session('success_message'))
                <div class="mt-6 rounded-2xl border border-bader-green/20 bg-bader-paper p-5 text-sm font-semibold text-bader-green-deep">
                    {{ session('success_message') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-5 text-sm text-red-700">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('contact.submit') }}" class="mt-8 space-y-5" data-reveal>
                @csrf

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-semibold text-bader-ink">{{ __('form.field.name') }} *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="mt-1.5 block w-full rounded-xl border border-bader-green/15 bg-bader-paper px-4 py-3 text-sm text-bader-ink placeholder-bader-ink/40 focus:border-bader-green focus:bg-white focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-bader-ink">{{ __('form.field.email') }} *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required dir="ltr"
                               class="mt-1.5 block w-full rounded-xl border border-bader-green/15 bg-bader-paper px-4 py-3 text-sm text-bader-ink placeholder-bader-ink/40 focus:border-bader-green focus:bg-white focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-bader-ink">{{ __('form.field.phone') }}</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" dir="ltr"
                               class="mt-1.5 block w-full rounded-xl border border-bader-green/15 bg-bader-paper px-4 py-3 text-sm text-bader-ink placeholder-bader-ink/40 focus:border-bader-green focus:bg-white focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-bader-ink">{{ __('form.field.subject') }}</label>
                        <input type="text" name="subject" value="{{ old('subject') }}"
                               class="mt-1.5 block w-full rounded-xl border border-bader-green/15 bg-bader-paper px-4 py-3 text-sm text-bader-ink placeholder-bader-ink/40 focus:border-bader-green focus:bg-white focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-bader-ink">{{ __('form.field.message') }} *</label>
                    <textarea name="message" rows="5" required
                              class="mt-1.5 block w-full rounded-xl border border-bader-green/15 bg-bader-paper p-4 text-sm text-bader-ink placeholder-bader-ink/40 focus:border-bader-green focus:bg-white focus:outline-none">{{ old('message') }}</textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-bader-green px-8 py-3.5 text-sm font-semibold text-white shadow-sm hover:bg-bader-green-deep transition">
                        {{ __('form.submit_contact') }}
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection
