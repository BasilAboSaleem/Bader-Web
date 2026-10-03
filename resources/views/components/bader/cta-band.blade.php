@props(['title' => null, 'text' => null, 'band' => 'band-base'])

<section class="{{ $band }} pb-11 pt-3 sm:pb-14">
    <div class="container-bader">
        <div class="relative isolate overflow-hidden rounded-[2rem] bg-teal-950 px-6 py-10 text-white shadow-card-md sm:px-10 sm:py-12" data-reveal>
            <div class="pointer-events-none absolute -end-20 -top-24 -z-10 h-72 w-72 rounded-full bg-forest-600/40 blur-3xl" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -bottom-28 -start-16 -z-10 h-64 w-64 rounded-full bg-gold-500/15 blur-3xl" aria-hidden="true"></div>
            <img src="{{ \App\Support\SiteSettings::brandAsset('mark_star') }}" alt="" class="pointer-events-none absolute -end-8 top-1/2 -z-10 hidden h-56 w-56 -translate-y-1/2 opacity-[0.07] md:block" aria-hidden="true">

            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-2xl">
                    <p class="inline-flex items-center gap-2 text-xs font-extrabold text-gold-400">
                        <x-bader.icon name="hand-heart" class="h-4 w-4" />
                        {{ __('cta.kicker') }}
                    </p>
                    <h2 class="mt-3 text-2xl font-extrabold leading-snug sm:text-3xl">{{ $title ?? __('cta.title') }}</h2>
                    <p class="mt-3 text-sm leading-7 text-white/75 sm:text-base">{{ $text ?? __('cta.text') }}</p>
                </div>
                <div class="flex shrink-0 flex-wrap gap-3">
                    @if ($slot->isEmpty())
                        <a href="{{ route('donate') }}" class="btn-primary min-h-12 px-6">
                            <x-bader.icon name="heart" class="h-5 w-5" />
                            {{ __('nav.donate') }}
                        </a>
                        <a href="{{ route('campaigns') }}" class="btn-ghost min-h-12 px-6">{{ __('header.all_projects') }}</a>
                    @else
                        {{ $slot }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
