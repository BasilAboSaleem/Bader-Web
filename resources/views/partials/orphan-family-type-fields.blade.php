@php
    use App\Models\OrphanFamily;
@endphp

<section class="{{ $ui['section'] }}">
    <h2 class="{{ $ui['sectionTitle'] }}">{{ __('orphans.section.type') }} {!! $star !!}</h2>
    @if ($lockIdentity)
        <input type="hidden" name="type" value="{{ $selectedType }}" data-orphan-type>
        <p class="mt-4 rounded-2xl border border-hairline bg-paper-2 px-4 py-3 font-extrabold text-ink-900">{{ __('orphans.type.'.$selectedType) }}</p>
        <p class="{{ $ui['sectionHint'] }}">{{ __('orphan_case_update.type_locked') }}</p>
    @else
        <div class="{{ $ui['grid'] }}">
            @foreach (OrphanFamily::TYPES as $type)
                <label class="{{ $ui['choice'] }}">
                    <input type="radio" name="type" value="{{ $type }}" @checked($selectedType === $type) required data-orphan-type>
                    <span>{{ __('orphans.type.'.$type) }}</span>
                </label>
            @endforeach
        </div>
    @endif
</section>
