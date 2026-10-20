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
    <div class="mt-4 flex items-center gap-4 rounded-xl border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-900">
        @if (! empty($row['photo_url']))
            <a href="{{ $row['photo_url'] }}" data-lightbox="orphan-portraits" data-close-label="{{ __('common.close') }}" class="shrink-0 cursor-zoom-in">
                <img src="{{ $row['photo_url'] }}" alt="{{ $field('full_name') }}" width="72" height="72" class="h-16 w-16 rounded-full object-cover">
            </a>
        @else
            <span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-gray-100 text-center text-xs leading-4 text-gray-500 dark:bg-gray-800 dark:text-gray-400">{{ __('orphans.field.orphan_photo_empty') }}</span>
        @endif
        <label class="block min-w-0 flex-1">
            <span class="{{ $ui['label'] }}">{{ __('orphans.field.orphan_photo') }}</span>
            <input type="file" name="orphans[{{ $index }}][photo]" accept="image/jpeg,image/png,image/webp" data-max-bytes="{{ $photoMaxBytes }}" data-too-large-message="{{ __('orphans.photo_too_large') }}" class="{{ $ui['file'] }}">
            <span class="{{ $ui['hint'] }}">{{ __('orphans.field.orphan_photo_hint') }}</span>
        </label>
    </div>
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
