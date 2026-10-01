@php
    use App\Support\PublicNavigation;
    use App\Support\SiteSettings;
    $allLinks = PublicNavigation::all();
    $primaryLinks = PublicNavigation::primary();
    $secondaryLinks = PublicNavigation::secondary();
@endphp

<footer class="bg-teal-950 text-sand-100/80 section-pad !py-16 border-t border-teal-800 relative overflow-hidden site-footer">
    {{-- Decorative Background Patterns --}}
    <div class="absolute -top-24 -end-24 w-96 h-96 rounded-full bg-gold-500/5 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -start-24 w-96 h-96 rounded-full bg-forest-500/5 blur-3xl pointer-events-none"></div>

    <div class="mx-auto max-w-7xl relative z-10">
        <div class="grid gap-12 sm:grid-cols-2 lg:grid-cols-5">
            {{-- Column 1: Brand & Identity --}}
            <div class="lg:col-span-2 space-y-4">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-gold-500/20 via-teal-900 to-teal-950 border border-gold-500/40 p-2.5 shadow-lg">
                        <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="" class="h-full w-full object-contain">
                    </span>
                    <div>
                        <p class="font-display text-xl font-bold text-white tracking-tight">{{ __('brand.name') }}</p>
                        <p class="text-xs font-semibold text-gold-400 font-mono tracking-wider">{{ __('brand.tagline') }}</p>
                    </div>
                </a>

                <p class="text-sm leading-relaxed text-sand-100/70 max-w-sm">
                    {{ __('footer.tagline') }}
                </p>

                <div class="space-y-2 pt-2 text-xs font-mono text-sand-100/80">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-gold-400"></span>
                        <span>{{ SiteSettings::hqLocation() }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-forest-400"></span>
                        <span>{{ SiteSettings::fieldLocation() }}</span>
                    </div>
                </div>
            </div>

            {{-- Column 2: Quick Links --}}
            <div class="space-y-3">
                <p class="font-display text-sm font-bold uppercase tracking-wider text-gold-400">{{ __('footer.nav_title') }}</p>
                <nav class="flex flex-col space-y-2 text-sm" aria-label="{{ __('footer.nav_title') }}">
                    @foreach ($primaryLinks as $link)
                        <a href="{{ route($link['route']) }}" class="text-sand-100/70 hover:text-gold-400 transition-colors">
                            {{ __($link['key']) }}
                        </a>
                    @endforeach
                </nav>
            </div>

            {{-- Column 3: Institutional & Governance --}}
            <div class="space-y-3">
                <p class="font-display text-sm font-bold uppercase tracking-wider text-gold-400">{{ __('nav.more') }}</p>
                <nav class="flex flex-col space-y-2 text-sm" aria-label="{{ __('nav.more') }}">
                    @foreach ($secondaryLinks as $link)
                        <a href="{{ route($link['route']) }}" class="text-sand-100/70 hover:text-gold-400 transition-colors">
                            {{ __($link['key']) }}
                        </a>
                    @endforeach
                </nav>
            </div>

            {{-- Column 4: Direct Action & Impact --}}
            <div class="space-y-4">
                <p class="font-display text-sm font-bold uppercase tracking-wider text-gold-400">{{ __('footer.action_title') }}</p>
                <p class="text-xs leading-relaxed text-sand-100/70">
                    {{ __('footer.action_desc') }}
                </p>
                <a
                    href="{{ route('donate') }}"
                    class="btn-primary w-full text-center !py-3 text-xs sm:text-sm font-bold shadow-lg shadow-gold-500/20 block"
                >
                    {{ __('nav.donate') }}
                </a>
            </div>
        </div>

        {{-- Bottom Copyright Bar --}}
        <div class="mt-14 pt-8 border-t border-teal-800/80 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-sand-100/50">
            <p>{{ __('footer.copyright', ['year' => date('Y')]) }}</p>
            <div class="flex items-center gap-4">
                <a href="{{ route('about') }}" class="hover:text-sand-100 transition-colors">{{ __('page.about.title') }}</a>
                <span>•</span>
                <a href="{{ route('contact') }}" class="hover:text-sand-100 transition-colors">{{ __('page.contact.title') }}</a>
                <span>•</span>
                <a href="{{ route('login') }}" class="hover:text-gold-400 transition-colors font-mono">{{ __('footer.staff_access') }}</a>
            </div>
        </div>
    </div>
</footer>
