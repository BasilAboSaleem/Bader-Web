@php
    use App\Support\PublicNavigation;
    use App\Support\SiteSettings;

    $primaryLinks = PublicNavigation::primary();
    $secondaryLinks = PublicNavigation::secondary();
@endphp

<footer class="site-footer relative overflow-hidden border-t border-teal-800 bg-teal-950 text-sand-100/80 section-pad !py-14 sm:!py-16">
    <div class="pointer-events-none absolute -end-24 -top-24 h-72 w-72 rounded-full bg-forest-500/5 blur-3xl"></div>

    <div class="relative z-10 mx-auto max-w-7xl">
        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4 lg:gap-12">
            <div class="space-y-4 lg:col-span-2">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl border border-gold-500/30 bg-teal-900 p-2.5 shadow-sm">
                        <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="" class="h-full w-full object-contain">
                    </span>
                    <span class="leading-tight">
                        <span class="block font-display text-xl font-bold tracking-tight text-white">{{ __('brand.name') }}</span>
                        <span class="mt-1 block font-mono text-xs font-semibold tracking-wider text-gold-400">{{ __('brand.tagline') }}</span>
                    </span>
                </a>

                <p class="max-w-sm text-sm leading-relaxed text-sand-100/70">{{ __('footer.tagline') }}</p>

                <div class="grid max-w-sm gap-2 pt-2 text-xs sm:grid-cols-2">
                    <div class="rounded-xl border border-teal-800/80 bg-teal-900/40 px-3 py-2">
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-gold-400">{{ __('home.trust_hq_label') }}</span>
                        <span class="mt-1 block text-sand-100/75">{{ SiteSettings::hqLocation() }}</span>
                    </div>
                    <div class="rounded-xl border border-teal-800/80 bg-teal-900/40 px-3 py-2">
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-forest-400">{{ __('home.trust_field_label') }}</span>
                        <span class="mt-1 block text-sand-100/75">{{ SiteSettings::fieldLocation() }}</span>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <p class="font-display text-sm font-bold uppercase tracking-wider text-gold-400">{{ __('footer.nav_title') }}</p>
                <nav class="flex flex-col gap-2 text-sm" aria-label="{{ __('footer.nav_title') }}">
                    @foreach ($primaryLinks as $link)
                        <a href="{{ route($link['route']) }}" class="text-sand-100/70 transition-colors hover:text-gold-400">
                            {{ __($link['key']) }}
                        </a>
                    @endforeach
                </nav>
            </div>

            <div class="space-y-4">
                <p class="font-display text-sm font-bold uppercase tracking-wider text-gold-400">{{ __('nav.more') }}</p>
                <nav class="flex flex-col gap-2 text-sm" aria-label="{{ __('nav.more') }}">
                    @foreach ($secondaryLinks as $link)
                        <a href="{{ route($link['route']) }}" class="text-sand-100/70 transition-colors hover:text-gold-400">
                            {{ __($link['key']) }}
                        </a>
                    @endforeach
                </nav>

                <div class="border-t border-teal-800/80 pt-4 text-xs text-sand-100/65">
                    <p class="mb-2 font-bold text-sand-100/85">{{ __('footer.action_title') }}</p>
                    <p class="leading-relaxed">{{ __('footer.action_desc') }}</p>
                </div>
                <a href="{{ route('donate') }}" class="btn-primary block w-full text-center !py-3 text-xs font-bold sm:text-sm">
                    {{ __('nav.donate') }}
                </a>
            </div>
        </div>

        <div class="mt-10 flex flex-col gap-4 border-t border-teal-800/80 pt-6 text-xs text-sand-100/50 sm:flex-row sm:items-center sm:justify-between">
            <p>{{ __('footer.copyright', ['year' => date('Y')]) }}</p>
            <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
                <a href="mailto:{{ SiteSettings::contactEmail() }}" dir="ltr" class="transition-colors hover:text-sand-100">{{ SiteSettings::contactEmail() }}</a>
                <a href="tel:{{ SiteSettings::contactPhone() }}" dir="ltr" class="transition-colors hover:text-sand-100">{{ SiteSettings::contactPhone() }}</a>
                <a href="{{ route('login') }}" class="font-mono transition-colors hover:text-gold-400">{{ __('footer.staff_access') }}</a>
            </div>
        </div>
    </div>
</footer>
