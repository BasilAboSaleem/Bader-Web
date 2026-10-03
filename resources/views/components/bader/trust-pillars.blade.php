@php
    $pillars = [
        ['key' => 'verified', 'icon' => 'shield'],
        ['key' => 'dignity', 'icon' => 'lock'],
        ['key' => 'updates', 'icon' => 'eye'],
    ];
@endphp

<section class="band-anchor relative isolate overflow-hidden section-y" aria-labelledby="trust-pillars-title">
    <img src="{{ \App\Support\SiteSettings::brandAsset('mark_star') }}" alt="" class="pointer-events-none absolute -end-20 top-1/2 -z-10 h-96 w-96 -translate-y-1/2 opacity-[0.04]" aria-hidden="true">
    <div class="container-bader">
        <div class="grid gap-10 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.4fr)] lg:items-center">
            <div data-reveal>
                <p class="inline-flex items-center gap-2 text-xs font-extrabold text-gold-400">
                    <x-bader.icon name="shield" class="h-4 w-4" />
                    {{ __('home.trust_kicker') }}
                </p>
                <h2 id="trust-pillars-title" class="mt-3 text-3xl font-extrabold leading-tight sm:text-4xl">{{ __('home.trust_title') }}</h2>
                <p class="mt-4 leading-relaxed text-white/75">{{ __('home.trust_text') }}</p>
                <dl class="mt-6 grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-2xl border border-white/10 p-4">
                        <dt class="text-white/60">{{ __('home.trust_hq_label') }}</dt>
                        <dd class="mt-1 font-bold">{{ \App\Support\SiteSettings::hqLocation() }}</dd>
                    </div>
                    <div class="rounded-2xl border border-white/10 p-4">
                        <dt class="text-white/60">{{ __('home.trust_field_label') }}</dt>
                        <dd class="mt-1 font-bold">{{ \App\Support\SiteSettings::fieldLocation() }}</dd>
                    </div>
                </dl>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                @foreach ($pillars as $pillar)
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur transition duration-300 hover:-translate-y-1 hover:bg-white/10" data-reveal style="animation-delay: {{ $loop->index * 90 }}ms">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gold-500 text-teal-950">
                            <x-bader.icon :name="$pillar['icon']" class="h-6 w-6" />
                        </span>
                        <h3 class="mt-5 text-lg font-extrabold">{{ __('home.trust_'.$pillar['key'].'_title') }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-white/70">{{ __('home.trust_'.$pillar['key'].'_text') }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-12 flex flex-col items-center justify-between gap-4 rounded-3xl bg-white/5 p-6 text-center ring-1 ring-white/10 sm:flex-row sm:text-start" data-reveal>
            <p class="text-lg font-extrabold">{{ __('home.cta_title') }}</p>
            <div class="flex flex-wrap justify-center gap-3">
                <a href="{{ route('donate') }}" class="btn-primary">
                    <x-bader.icon name="heart" class="h-4 w-4" />
                    {{ __('nav.donate') }}
                </a>
                <a href="{{ route('partners') }}" class="btn-ghost">{{ __('home.cta_partner') }}</a>
            </div>
        </div>
    </div>
</section>
