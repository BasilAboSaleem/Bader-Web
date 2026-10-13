{{--
    Sponsorship status with the internal sponsor details, which only open (and become required) while "sponsored" is chosen.
    $prefix is the input name prefix ('' for the family itself, 'orphans[ID]' for one orphan).
--}}
@props(['record', 'label', 'prefix' => '', 'inputClass'])

@php
  use App\Models\OrphanFamily;

  $errorKey = $prefix === '' ? '' : str_replace(['[', ']'], ['.', ''], $prefix).'.';
  $name = fn (string $field): string => $prefix === '' ? $field : $prefix.'['.$field.']';
  $value = fn (string $field) => old($errorKey.$field, $record->{$field} instanceof \Carbon\CarbonInterface ? $record->{$field}->format('Y-m-d') : $record->{$field});
  $isSponsored = $value('sponsorship_status') === OrphanFamily::SPONSORSHIP_SPONSORED;
  $labelClass = 'mb-1 block text-theme-sm font-medium text-gray-700 dark:text-gray-400';
  $star = '<span class="text-error-500">*</span>';
@endphp

<div data-sponsor-scope {{ $attributes->class('space-y-3') }}>
  <label class="block">
    <span class="{{ $labelClass }}">{{ $label }}</span>
    <select name="{{ $name('sponsorship_status') }}" data-sponsor-status class="{{ $inputClass }}">
      @foreach (OrphanFamily::SPONSORSHIP_STATUSES as $status)
        <option value="{{ $status }}" @selected($value('sponsorship_status') === $status)>{{ __('sponsorship.status.'.$status) }}</option>
      @endforeach
    </select>
  </label>

  <fieldset data-sponsor-fields class="space-y-3 rounded-lg bg-gray-50 p-3 dark:bg-white/[0.03]" @disabled(! $isSponsored) @if (! $isSponsored) hidden @endif>
    <label class="block">
      <span class="{{ $labelClass }}">{{ __('orphans.field.sponsor_name') }} {!! $star !!}</span>
      <input type="text" name="{{ $name('sponsor_name') }}" value="{{ $value('sponsor_name') }}" required maxlength="255" class="{{ $inputClass }}">
    </label>
    <label class="block">
      <span class="{{ $labelClass }}">{{ __('orphans.field.sponsorship_monthly_amount') }} {!! $star !!}</span>
      <input type="number" name="{{ $name('sponsorship_monthly_amount') }}" value="{{ $value('sponsorship_monthly_amount') }}" required min="1" max="100000" step="0.01" dir="ltr" class="{{ $inputClass }}">
    </label>
    <label class="block">
      <span class="{{ $labelClass }}">{{ __('orphans.field.sponsorship_started_at') }} {!! $star !!}</span>
      <input type="date" name="{{ $name('sponsorship_started_at') }}" value="{{ $value('sponsorship_started_at') }}" required class="{{ $inputClass }}">
    </label>
  </fieldset>

  @foreach (['sponsorship_status', ...OrphanFamily::SPONSOR_FIELDS] as $field)
    @error($errorKey.$field)
      <p class="text-theme-xs text-error-600">{{ $message }}</p>
    @enderror
  @endforeach
</div>
