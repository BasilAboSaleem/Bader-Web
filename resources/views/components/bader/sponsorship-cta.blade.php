@props(['case' => null, 'from' => null])

@use('App\Support\Money')

<section class="band-anchor relative isolate overflow-hidden section-y" aria-labelledby="sponsorship-cta-title">
    <div class="pointer-events-none absolute -end-24 -top-24 -z-10 h-80 w-80 rounded-full bg-forest-600/30 blur-3xl" aria-hidden="true"></div>
    <div class="pointer-events-none absolute -bottom-32 -start-24 -z-10 h-80 w-80 rounded-full bg-gold-500/10 blur-3xl" aria-hidden="true"></div>

    <div class="container-bader grid items-center gap-10 lg:grid-cols-2">
        <div data-reveal>
            <p class="inline-flex items-center gap-2 text-xs font-extrabold text-gold-400">
                <x-bader.icon name="hand-heart" class="h-4 w-4" />
                {{ __('home.sponsorship.kicker') }}
            </p>
            <h2 id="sponsorship-cta-title" class="mt-3 text-3xl font-extrabold leading-tight sm:text-4xl">{{ __('home.sponsorship.title') }}</h2>
            <p class="mt-4 max-w-xl leading-relaxed text-white/75">{{ __('home.sponsorship.text') }}</p>
            @if ($from)
                <p class="mt-5 inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-sm font-bold">
                    {{ __('home.sponsorship.from', ['amount' => Money::format($from)]) }}
                </p>
            @endif
            <ul class="mt-6 grid gap-3 text-sm text-white/85 sm:grid-cols-2">
                @foreach (['monthly_reports', 'privacy', 'cancel_anytime', 'direct_support'] as $point)
                    <li class="flex items-center gap-2">
                        <x-bader.icon name="check" class="h-4 w-4 shrink-0 text-gold-400" />
                        {{ __('home.sponsorship.point_'.$point) }}
                    </li>
                @endforeach
            </ul>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('sponsorship') }}" class="btn-primary">
                    {{ __('home.sponsorship.browse') }}
                    <x-bader.icon name="arrow" class="h-4 w-4" />
                </a>
                <a href="{{ route('sponsorship') }}#sponsorship-how" class="btn-ghost">{{ __('home.sponsorship.how') }}</a>
            </div>
        </div>

        <div class="relative" data-reveal>
            @if ($case)
                <p class="mb-3 inline-flex items-center gap-2 text-xs font-bold text-white/70">
                    <span class="h-2 w-2 animate-pulse-dot rounded-full bg-gold-400"></span>
                    {{ __('home.sponsorship.longest_waiting') }}
                </p>
                <x-bader.sponsorship-case-card :case="$case" class="text-start text-ink-900 shadow-card-float" />
            @else
                <div class="rounded-3xl border border-white/15 bg-white/5 p-8 text-center">
                    <x-bader.icon name="users" class="mx-auto h-10 w-10 text-gold-400" />
                    <p class="mt-4 text-lg font-extrabold">{{ __('header.no_waiting_cases') }}</p>
                    <a href="{{ route('sponsorship') }}" class="btn-ghost mt-6">{{ __('header.all_cases') }}</a>
                </div>
            @endif
        </div>
    </div>
</section>
