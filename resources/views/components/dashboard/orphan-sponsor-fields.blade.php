{{--
    Sponsorship status with the sponsor, amount, currency and start date, which only open (and become required)
    while "sponsored" is chosen. $prefix is the input name prefix ('' for a widow's family, 'orphans[ID]' for one orphan).
    A widow's family takes its amounts per person in the slot instead of a single amount.
--}}
@props(['label', 'status', 'sponsorship' => null, 'sponsors', 'prefix' => '', 'inputClass', 'withAmount' => true])

@php
  use App\Models\OrphanFamily;

  $errorKey = $prefix === '' ? '' : str_replace(['[', ']'], ['.', ''], $prefix).'.';
  $name = fn (string $field): string => $prefix === '' ? $field : $prefix.'['.$field.']';
  $current = [
      'sponsorship_status' => $status,
      'sponsor_id' => $sponsorship?->sponsor_id,
      'monthly_amount' => $sponsorship?->monthly_amount,
      'currency' => $sponsorship?->currency,
      'started_at' => $sponsorship?->started_at?->format('Y-m-d') ?? now()->startOfMonth()->format('Y-m-d'),
  ];
  $value = fn (string $field) => old($errorKey.$field, $current[$field]);
  $isSponsored = $value('sponsorship_status') === OrphanFamily::SPONSORSHIP_SPONSORED;
  $labelClass = 'mb-1 block text-theme-sm font-medium text-gray-700 dark:text-gray-400';
  $star = '<span class="text-error-500">*</span>';
  $fields = $withAmount ? ['sponsorship_status', 'sponsor_id', 'monthly_amount', 'currency', 'started_at'] : ['sponsorship_status', 'sponsor_id', 'currency', 'started_at'];
@endphp

<div data-sponsor-scope {{ $attributes->class('space-y-3') }}>
  <label class="block">
    <span class="{{ $labelClass }}">{{ $label }}</span>
    <select name="{{ $name('sponsorship_status') }}" data-sponsor-status class="{{ $inputClass }}">
      @foreach (OrphanFamily::SPONSORSHIP_STATUSES as $option)
        <option value="{{ $option }}" @selected($value('sponsorship_status') === $option)>{{ __('sponsorship.status.'.$option) }}</option>
      @endforeach
    </select>
  </label>

  <fieldset data-sponsor-fields class="space-y-3 rounded-lg bg-gray-50 p-3 dark:bg-white/[0.03]" @disabled(! $isSponsored) @if (! $isSponsored) hidden @endif>
    <label class="block">
      <span class="{{ $labelClass }}">{{ __('sponsors.field.sponsor') }} {!! $star !!}</span>
      <select name="{{ $name('sponsor_id') }}" required data-sponsor-select class="{{ $inputClass }}">
        <option value="">{{ __('sponsors.choose_sponsor') }}</option>
        @foreach ($sponsors as $sponsor)
          <option value="{{ $sponsor->id }}" data-currency="{{ $sponsor->preferred_currency }}" @selected((string) $value('sponsor_id') === (string) $sponsor->id)>{{ $sponsor->name }} ({{ $sponsor->code }})</option>
        @endforeach
      </select>
    </label>
    <div class="grid grid-cols-2 gap-3">
      @if ($withAmount)
        <label class="block">
          <span class="{{ $labelClass }}">{{ __('sponsors.field.monthly_amount') }} {!! $star !!}</span>
          <input type="number" name="{{ $name('monthly_amount') }}" value="{{ $value('monthly_amount') }}" required min="1" max="100000" step="0.01" dir="ltr" class="{{ $inputClass }}">
        </label>
      @endif
      <label class="block">
        <span class="{{ $labelClass }}">{{ __('sponsors.field.currency') }} {!! $star !!}</span>
        <select name="{{ $name('currency') }}" required data-currency-select data-currency-chosen="{{ $value('currency') ? 'true' : 'false' }}" class="{{ $inputClass }}">
          @foreach (config('bader.sponsorship_currencies') as $currency)
            <option value="{{ $currency }}" @selected($value('currency') === $currency)>{{ __('sponsors.currency.'.$currency) }}</option>
          @endforeach
        </select>
      </label>
      <label class="block {{ $withAmount ? 'col-span-2' : '' }}">
        <span class="{{ $labelClass }}">{{ __('sponsors.field.started_at') }} {!! $star !!}</span>
        <input type="date" name="{{ $name('started_at') }}" value="{{ $value('started_at') }}" required class="{{ $inputClass }}">
      </label>
    </div>
    {{ $slot }}
  </fieldset>

  @foreach ($fields as $field)
    @error($errorKey.$field)
      <p class="text-theme-xs text-error-600">{{ $message }}</p>
    @enderror
  @endforeach
</div>
