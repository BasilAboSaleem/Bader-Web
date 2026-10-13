{{-- One orphan inside the orphan sponsorship form. Expects: $index, $number, $row, $ui. --}}
@php
    use App\Models\Orphan;

    $field = fn (string $name) => $row[$name] ?? '';
    $star = '<span class="text-red-600">*</span>';
@endphp

<div class="{{ $ui['item'] }}" data-orphan-item>
    <div class="flex items-center justify-between gap-3">
        <h3 class="{{ $ui['itemTitle'] }}">{{ __('orphans.orphan_number') }} <span data-orphan-number>{{ $number }}</span></h3>
        <button type="button" class="{{ $ui['removeButton'] }}" data-orphan-remove>{{ __('orphans.remove_orphan') }}</button>
    </div>
    @if (! empty($row['id']))
        <input type="hidden" name="orphans[{{ $index }}][id]" value="{{ $row['id'] }}">
    @endif
    <div class="{{ $ui['grid'] }}">
        <label class="block">
            <span class="{{ $ui['label'] }}">{{ __('orphans.field.orphan_full_name') }} {!! $star !!}</span>
            <input type="text" name="orphans[{{ $index }}][full_name]" value="{{ $field('full_name') }}" required maxlength="255" class="{{ $ui['input'] }}">
        </label>
        <label class="block">
            <span class="{{ $ui['label'] }}">{{ __('orphans.field.orphan_birth_date') }} {!! $star !!}</span>
            <input type="date" name="orphans[{{ $index }}][birth_date]" value="{{ $field('birth_date') }}" required max="{{ now()->toDateString() }}" class="{{ $ui['input'] }}">
        </label>
        <label class="block">
            <span class="{{ $ui['label'] }}">{{ __('orphans.field.orphan_national_id') }} {!! $star !!}</span>
            <input type="text" name="orphans[{{ $index }}][national_id]" value="{{ $field('national_id') }}" required inputmode="numeric" pattern="\d{9}" maxlength="9" dir="ltr" class="{{ $ui['input'] }}">
        </label>
        <label class="block">
            <span class="{{ $ui['label'] }}">{{ __('orphans.field.orphan_education_stage') }} {!! $star !!}</span>
            <select name="orphans[{{ $index }}][education_stage]" required class="{{ $ui['input'] }}">
                <option value="">{{ __('orphans.choose') }}</option>
                @foreach (Orphan::EDUCATION_STAGES as $stage)
                    <option value="{{ $stage }}" @selected($field('education_stage') === $stage)>{{ __('orphans.education.'.$stage) }}</option>
                @endforeach
            </select>
        </label>
        <label class="block">
            <span class="{{ $ui['label'] }}">{{ __('orphans.field.orphan_health_status') }} {!! $star !!}</span>
            <select name="orphans[{{ $index }}][health_status]" required class="{{ $ui['input'] }}">
                <option value="">{{ __('orphans.choose') }}</option>
                @foreach (Orphan::HEALTH_STATUSES as $status)
                    <option value="{{ $status }}" @selected($field('health_status') === $status)>{{ __('orphans.health.'.$status) }}</option>
                @endforeach
            </select>
        </label>
        <label class="block">
            <span class="{{ $ui['label'] }}">{{ __('orphans.field.orphan_health_notes') }}</span>
            <input type="text" name="orphans[{{ $index }}][health_notes]" value="{{ $field('health_notes') }}" maxlength="500" placeholder="{{ __('orphans.field.orphan_health_notes_placeholder') }}" class="{{ $ui['input'] }}">
        </label>
    </div>
</div>
