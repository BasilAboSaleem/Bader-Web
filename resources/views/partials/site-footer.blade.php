@php
    use App\Support\PublicNavigation;
    use App\Support\SiteSettings;
@endphp

<footer class="bg-bader-black text-bader-on-dark">
    <div class="mx-auto grid max-w-6xl gap-10 px-4 py-12 sm:grid-cols-2 lg:grid-cols-3">
        <div>
            <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="" width="48" height="48" class="h-12 w-12">
            <p class="mt-4 text-lg font-semibold">{{ __('brand.name') }}</p>
            <p class="mt-1 text-sm text-white/70">{{ __('footer.tagline') }}</p>
            <p class="mt-4 text-sm text-bader-lime">{{ SiteSettings::hqLocation() }}</p>
            <p class="text-sm text-bader-lime">{{ SiteSettings::fieldLocation() }}</p>
        </div>

        <nav class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm" aria-label="{{ __('footer.rights') }}">
            @foreach (PublicNavigation::all() as $item)
                <a href="{{ route($item['route']) }}" class="py-1 text-white/80 hover:text-white">{{ __($item['key']) }}</a>
            @endforeach
        </nav>

        <div class="sm:col-span-2 lg:col-span-1">
            <x-bader.button :href="route('donate')" variant="light">{{ __('nav.donate') }}</x-bader.button>
        </div>
    </div>
    <div class="border-t border-white/10 px-4 py-4 text-center text-xs text-white/50">
        {{ __('footer.rights') }}
    </div>
</footer>
