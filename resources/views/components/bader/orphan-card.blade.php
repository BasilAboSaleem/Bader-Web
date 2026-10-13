{{-- Public card for an approved orphan or widow's family: first name, age(s) and sponsorship status only. --}}
@props(['orphan' => null, 'family' => null])

@php
    use App\Models\OrphanFamily;

    $isFamily = $family !== null && $orphan === null;
    $family ??= $orphan?->family;
    $name = $isFamily ? $family->mother_first_name : $orphan->first_name;
    $isAvailable = $isFamily ? $family->isAvailable() : $orphan->isAvailable();
    $requestUrl = route('sponsorship', [
        'case' => $family->code.' — '.$name,
        'sponsorship_type' => $isFamily ? 'widow' : 'orphan',
    ]).'#sponsorship-request';
@endphp

<article {{ $attributes->class(['surface-card surface-card-hover flex flex-col overflow-hidden']) }} data-reveal>
    <div class="flex items-start gap-4 p-5">
        <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-forest-600/10 text-forest-700">
            <x-bader.icon name="{{ $isFamily ? 'users' : 'heart' }}" class="h-7 w-7" />
        </span>
        <div class="min-w-0 flex-1">
            <span class="rounded-full bg-forest-600/10 px-2.5 py-0.5 text-xs font-extrabold text-forest-700">{{ __('sponsorship.type.'.($isFamily ? OrphanFamily::TYPE_WIDOW_FAMILY : OrphanFamily::TYPE_ORPHAN)) }}</span>
            <h3 class="mt-1.5 truncate text-lg font-extrabold text-ink-900">{{ $name }}</h3>
            <p class="mt-1 text-sm text-muted">
                @if ($isFamily)
                    {{ trans_choice('sponsorship.children_count', $family->orphans->count(), ['count' => $family->orphans->count()]) }}
                    @if ($family->orphans->isNotEmpty())
                        <span class="block text-xs text-subtle">{{ __('sponsorship.children_ages', ['ages' => $family->orphans->map(fn ($child) => $child->age)->sortDesc()->implode('، ')]) }}</span>
                    @endif
                @else
                    {{ __('sponsorship.age_years', ['age' => $orphan->age]) }}
                @endif
            </p>
        </div>
    </div>

    <div class="mt-auto flex items-center justify-between gap-3 border-t border-hairline px-5 py-4">
        @if ($isAvailable)
            <span class="inline-flex items-center gap-1.5 rounded-full bg-gold-500 px-3 py-1.5 text-xs font-extrabold text-teal-950">{{ __('sponsorship.status.available') }}</span>
            <a href="{{ $requestUrl }}" class="btn-primary shrink-0">
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
