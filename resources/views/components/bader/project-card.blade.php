@props(['campaign'])

@use('App\Support\Money')

@php
    $amounts = array_slice($campaign->amount_options, 0, 3);
    $defaultAmount = $amounts[1] ?? $amounts[0];
    $image = $campaign->image ? asset($campaign->image) : asset('images/programs/water.jpg');
    $detailsUrl = route('campaigns.show', $campaign->key);
@endphp

<article {{ $attributes->class(['surface-card surface-card-hover group flex flex-col overflow-hidden']) }}
    data-reveal
    data-amount-picker
    data-donate-base="{{ route('donate', ['target_type' => 'campaign', 'target_id' => $campaign->id]) }}"
    data-amount="{{ $defaultAmount }}"
    data-frequency="once">
    <a href="{{ $detailsUrl }}" class="relative block aspect-[16/10] overflow-hidden bg-paper-3" tabindex="-1" aria-hidden="true">
        <img src="{{ $image }}" alt="" loading="lazy" class="h-full w-full object-cover transition duration-700 ease-bader group-hover:scale-105">
        <span class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-black/45 to-transparent"></span>
        @if ($campaign->program)
            <span class="absolute start-3 top-3 rounded-full bg-white/95 px-3 py-1 text-xs font-bold text-forest-800 shadow-card-xs">{{ $campaign->program->title }}</span>
        @endif
        @if ($campaign->region)
            <span class="absolute bottom-3 start-3 inline-flex items-center gap-1 text-xs font-semibold text-white">
                <x-bader.icon name="map-pin" class="h-3.5 w-3.5" />
                {{ $campaign->region->name }}
            </span>
        @endif
    </a>

    <div class="flex flex-1 flex-col p-5">
        <h3 class="text-lg font-extrabold leading-snug text-ink-900">
            <a href="{{ $detailsUrl }}" class="transition hover:text-forest-700">{{ $campaign->title }}</a>
        </h3>
        @if ($campaign->description)
            <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-muted">{{ $campaign->description }}</p>
        @endif

        <div class="mt-4">
            @if ($campaign->isOngoing())
                <p class="inline-flex items-center gap-1.5 rounded-full bg-forest-600/10 px-3 py-1 text-xs font-bold text-forest-700">
                    <x-bader.icon name="repeat" class="h-3.5 w-3.5" />
                    {{ __('project.ongoing') }}
                </p>
            @else
                <div class="mb-2 flex items-baseline justify-between gap-2 text-xs">
                    <span class="font-bold text-ink-900">{{ __('project.raised', ['amount' => Money::format($campaign->raised_amount)]) }}</span>
                    <span class="font-extrabold text-forest-700">{{ $campaign->progress_percent }}%</span>
                </div>
                <div class="progress-track" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $campaign->progress_percent }}" aria-label="{{ __('project.progress') }}">
                    <div class="progress-bar" style="width: {{ $campaign->progress_percent }}%"></div>
                </div>
                <p class="mt-2 text-xs text-subtle">{{ __('project.goal', ['amount' => Money::format($campaign->goal_amount)]) }}</p>
            @endif
            @isset($campaign->donors_count)
                <p class="mt-2 inline-flex items-center gap-1.5 text-xs text-subtle">
                    <x-bader.icon name="users" class="h-3.5 w-3.5" />
                    {{ trans_choice('project.donors_count', $campaign->donors_count, ['count' => number_format($campaign->donors_count)]) }}
                </p>
            @endisset
        </div>

        <div class="mt-auto pt-5">
            @if ($campaign->allows_monthly)
                <div class="segmented mb-3 w-full" role="group" aria-label="{{ __('donation.frequency_label') }}">
                    <button type="button" class="flex-1" data-frequency-option data-value="once" aria-pressed="true">{{ __('donation.frequency.once') }}</button>
                    <button type="button" class="flex-1" data-frequency-option data-value="monthly" aria-pressed="false">{{ __('donation.frequency.monthly') }}</button>
                </div>
            @endif
            <div class="grid grid-cols-3 gap-2" role="group" aria-label="{{ __('donation.amount_label') }}">
                @foreach ($amounts as $amount)
                    <button type="button" class="chip px-2" data-amount-option data-amount="{{ $amount }}" aria-pressed="{{ $amount == $defaultAmount ? 'true' : 'false' }}" dir="ltr">{{ Money::format($amount) }}</button>
                @endforeach
            </div>
            <div class="mt-3 flex gap-2">
                <a href="{{ route('donate', ['target_type' => 'campaign', 'target_id' => $campaign->id, 'amount' => $defaultAmount]) }}" class="btn-primary flex-1" data-donate-link>
                    <x-bader.icon name="heart" class="h-4 w-4" />
                    {{ __('nav.donate') }}
                </a>
                <a href="{{ $detailsUrl }}" class="btn-outline px-3" aria-label="{{ __('project.details_for', ['title' => $campaign->title]) }}">
                    <x-bader.icon name="arrow" class="h-4 w-4" />
                </a>
            </div>
        </div>
    </div>
</article>
