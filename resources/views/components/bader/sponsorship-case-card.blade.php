@props(['case'])

@use('App\Support\Money')

@php
    $detailsUrl = route('sponsorship.show', $case->code);
    $sponsorUrl = route('donate', [
        'target_type' => 'sponsorship',
        'target_id' => $case->id,
        'amount' => $case->monthly_amount + 0,
        'frequency' => 'monthly',
    ]);
@endphp

<article {{ $attributes->class(['surface-card surface-card-hover group relative flex flex-col overflow-hidden']) }} data-reveal>
    <div class="flex items-start gap-4 p-5">
        <div class="relative h-20 w-20 shrink-0 overflow-hidden rounded-2xl bg-paper-3">
            @if ($case->photo)
                <img src="{{ asset($case->photo) }}" alt="" loading="lazy" class="h-full w-full object-cover">
            @else
                <span class="flex h-full w-full items-center justify-center text-forest-600">
                    <x-bader.icon name="users" class="h-8 w-8" />
                </span>
            @endif
        </div>
        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-full bg-forest-600/10 px-2.5 py-0.5 text-xs font-extrabold text-forest-700">{{ $case->type_label }}</span>
                <span class="text-xs font-semibold text-subtle" dir="ltr">#{{ $case->code }}</span>
            </div>
            <h3 class="mt-1.5 truncate text-lg font-extrabold text-ink-900">
                <a href="{{ $detailsUrl }}" class="after:absolute after:inset-0">{{ $case->name }}</a>
            </h3>
            <p class="mt-1 flex flex-wrap gap-x-3 gap-y-1 text-xs text-muted">
                @if ($case->age)
                    <span>{{ __('sponsorship.age_years', ['age' => $case->age]) }}</span>
                @endif
                @if ($case->region)
                    <span class="inline-flex items-center gap-1">
                        <x-bader.icon name="map-pin" class="h-3.5 w-3.5" />
                        {{ $case->region->name }}
                    </span>
                @endif
            </p>
        </div>
    </div>

    @if ($case->bio)
        <p class="line-clamp-2 px-5 text-sm leading-relaxed text-muted">{{ $case->bio }}</p>
    @endif

    <div class="mt-auto flex items-center justify-between gap-3 border-t border-hairline px-5 py-4">
        <div>
            <p class="text-lg font-extrabold text-ink-900">{{ __('sponsorship.per_month', ['amount' => Money::format($case->monthly_amount)]) }}</p>
            @if ($case->isAvailable() && $case->waiting_since)
                <p class="inline-flex items-center gap-1 text-xs text-subtle">
                    <x-bader.icon name="clock" class="h-3.5 w-3.5" />
                    {{ __('sponsorship.waiting_since', ['time' => $case->waiting_since->diffForHumans(syntax: \Carbon\CarbonInterface::DIFF_ABSOLUTE)]) }}
                </p>
            @endif
        </div>
        @if ($case->isAvailable())
            <a href="{{ $sponsorUrl }}" class="btn-primary relative z-10 shrink-0">
                <x-bader.icon name="hand-heart" class="h-4 w-4" />
                {{ __('sponsorship.sponsor_now') }}
            </a>
        @else
            <span class="inline-flex items-center gap-1.5 rounded-full bg-paper-2 px-3 py-1.5 text-xs font-extrabold text-forest-700">
                <x-bader.icon name="check" class="h-4 w-4" />
                {{ __('sponsorship.status.sponsored') }}
            </span>
        @endif
    </div>
</article>
