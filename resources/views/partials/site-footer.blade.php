@php
    use App\Support\PublicNavigation;
    use App\Support\SiteSettings;

    $menus = $navMenus ?? PublicNavigation::menus();
    $givingLinks = [
        ['route' => 'donate', 'key' => 'nav.donate'],
        ['route' => 'campaigns', 'key' => 'nav.campaigns'],
        ['route' => 'sponsorship', 'key' => 'nav.sponsorship'],
        ['route' => 'gift', 'key' => 'nav.gift'],
        ['route' => 'zakat', 'key' => 'nav.zakat'],
    ];
    $socialLinks = SiteSettings::socialLinks();
@endphp

<footer class="site-footer relative overflow-hidden bg-teal-950 text-sand-100/80">
    <div class="pointer-events-none absolute -end-32 -top-32 h-96 w-96 rounded-full bg-forest-500/10 blur-3xl" aria-hidden="true"></div>

    {{-- Call to action strip --}}
    <div class="relative border-b border-white/10">
        <div class="container-bader flex flex-col gap-6 py-10 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-2xl font-extrabold text-white">{{ __('footer.action_title') }}</p>
                <p class="mt-2 max-w-xl text-sm leading-relaxed text-sand-100/70">{{ __('footer.action_desc') }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('donate') }}" class="btn-primary">
                    <x-bader.icon name="heart" class="h-4 w-4" />
                    <span>{{ __('nav.donate') }}</span>
                </a>
                <a href="{{ route('sponsorship') }}" class="btn-ghost">{{ __('footer.sponsor_cta') }}</a>
            </div>
        </div>
    </div>

    <div class="relative container-bader grid gap-10 py-14 sm:grid-cols-2 lg:grid-cols-[1.4fr_1fr_1fr_1fr] lg:gap-12">
        <div class="space-y-5">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl border border-gold-500/30 bg-teal-900 p-2.5">
                    <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="" width="28" height="28" class="h-full w-full object-contain">
                </span>
                <span class="leading-tight">
                    <span class="block text-xl font-extrabold text-white">{{ __('brand.name') }}</span>
                    <span class="mt-1 block text-xs font-semibold text-gold-400">{{ __('brand.tagline') }}</span>
                </span>
            </a>
            <p class="max-w-sm text-sm leading-relaxed text-sand-100/70">{{ __('footer.tagline') }}</p>
            <ul class="grid gap-2.5 text-sm">
                <li class="flex items-start gap-2.5">
                    <x-bader.icon name="building" class="mt-0.5 h-4 w-4 text-gold-400" />
                    <span><span class="font-bold text-sand-50">{{ __('home.trust_hq_label') }}:</span> {{ SiteSettings::hqLocation() }}</span>
                </li>
                <li class="flex items-start gap-2.5">
                    <x-bader.icon name="map-pin" class="mt-0.5 h-4 w-4 text-gold-400" />
                    <span><span class="font-bold text-sand-50">{{ __('home.trust_field_label') }}:</span> {{ SiteSettings::fieldLocation() }}</span>
                </li>
                <li class="flex items-center gap-2.5">
                    <x-bader.icon name="mail" class="h-4 w-4 text-gold-400" />
                    <a href="mailto:{{ SiteSettings::contactEmail() }}" dir="ltr" class="transition-colors hover:text-gold-400">{{ SiteSettings::contactEmail() }}</a>
                </li>
                <li class="flex items-center gap-2.5">
                    <x-bader.icon name="phone" class="h-4 w-4 text-gold-400" />
                    <a href="tel:{{ SiteSettings::contactPhone() }}" dir="ltr" class="transition-colors hover:text-gold-400">{{ SiteSettings::contactPhone() }}</a>
                </li>
            </ul>
            @if ($socialLinks !== [])
                <ul class="flex flex-wrap gap-2" aria-label="{{ __('footer.follow_us') }}">
                    @foreach ($socialLinks as $platform => $url)
                        <li>
                            <a href="{{ $url }}" target="_blank" rel="noopener" title="{{ __('footer.social.'.$platform) }}"
                                class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-sand-100/80 transition hover:-translate-y-0.5 hover:border-gold-500/40 hover:bg-gold-500 hover:text-teal-950">
                                <x-bader.icon :name="$platform" class="h-4 w-4" />
                                <span class="sr-only">{{ __('footer.social.'.$platform) }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div>
            <p class="text-sm font-extrabold text-white">{{ __('nav.about') }}</p>
            <nav class="mt-4 flex flex-col gap-2.5 text-sm" aria-label="{{ __('nav.about') }}">
                @foreach (PublicNavigation::aboutLinks() as $link)
                    <a href="{{ route($link['route']) }}" class="transition-colors hover:text-gold-400">{{ __($link['key']) }}</a>
                @endforeach
                <a href="{{ route('news') }}" class="transition-colors hover:text-gold-400">{{ __('nav.news') }}</a>
            </nav>
        </div>

        <div>
            <p class="text-sm font-extrabold text-white">{{ __('nav.programs') }}</p>
            <nav class="mt-4 flex flex-col gap-2.5 text-sm" aria-label="{{ __('nav.programs') }}">
                @foreach ($menus['programs']->take(6) as $program)
                    <a href="{{ route('programs.show', $program->key) }}" class="transition-colors hover:text-gold-400">{{ $program->title }}</a>
                @endforeach
                <a href="{{ route('programs') }}" class="font-bold text-gold-400 hover:text-gold-300">{{ __('header.all_programs') }}</a>
            </nav>
        </div>

        <div>
            <p class="text-sm font-extrabold text-white">{{ __('footer.give_title') }}</p>
            <nav class="mt-4 flex flex-col gap-2.5 text-sm" aria-label="{{ __('footer.give_title') }}">
                @foreach ($givingLinks as $link)
                    <a href="{{ route($link['route']) }}" class="transition-colors hover:text-gold-400">{{ __($link['key']) }}</a>
                @endforeach
            </nav>
        </div>
    </div>

    <div class="relative border-t border-white/10">
        <div class="container-bader flex flex-col gap-5 py-6 text-xs text-sand-100/55 md:flex-row md:items-center md:justify-between">
            <div class="flex flex-wrap items-center gap-2" aria-label="{{ __('footer.payment_methods') }}">
                <span class="me-1 font-semibold text-sand-100/70">{{ __('footer.payment_methods') }}</span>
                <span class="payment-badge" title="Visa"><span class="font-black italic tracking-tight text-[#1a1f71]">VISA</span></span>
                <span class="payment-badge" title="Mastercard">
                    <span class="relative flex">
                        <span class="h-3.5 w-3.5 rounded-full bg-[#eb001b]"></span>
                        <span class="-ms-1.5 h-3.5 w-3.5 rounded-full bg-[#f79e1b] mix-blend-multiply"></span>
                    </span>
                </span>
                <span class="payment-badge" title="Apple Pay"><span class="text-[10px] font-bold text-ink-900">Apple Pay</span></span>
                <span class="payment-badge gap-1" title="{{ __('donation.method.bank_transfer') }}"><x-bader.icon name="bank" class="h-3.5 w-3.5 text-ink-700" /></span>
                <span class="ms-2 inline-flex items-center gap-1 text-sand-100/60"><x-bader.icon name="lock" class="h-3.5 w-3.5" /> {{ __('footer.secure_payments') }}</span>
            </div>
            <div class="flex flex-wrap items-center gap-x-5 gap-y-2">
                <p>{{ __('footer.copyright', ['year' => date('Y')]) }}</p>
                <a href="{{ route('login') }}" class="transition-colors hover:text-gold-400">{{ __('footer.staff_access') }}</a>
            </div>
        </div>
    </div>
</footer>
