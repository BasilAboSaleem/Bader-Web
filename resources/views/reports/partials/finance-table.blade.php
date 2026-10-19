@php
  use App\Support\SponsorshipAmounts;
  $print = $print ?? false;
  $showBeneficiary = $showBeneficiary ?? false;
  $showSponsor = $showSponsor ?? false;
  $showFile = $showFile ?? false;
  $heading = $print ? '' : 'px-3 py-2 text-center align-middle text-theme-xs font-semibold text-gray-500 dark:text-gray-400';
  $cell = $print ? '' : 'px-3 py-3 text-center align-middle text-theme-sm text-gray-600 dark:text-gray-300';
@endphp

@if ($print)
  <table class="data grid">
    <thead>
      <tr>
        <th>{{ __('sponsors.disbursements.period') }}</th>
        <th>{{ __('sponsors.disbursements.paid_on') }}</th>
        @if ($showFile)<th>{{ __('sponsors.column.file') }}</th>@endif
        @if ($showBeneficiary)<th>{{ __('sponsors.column.beneficiary') }}</th>@endif
        @if ($showSponsor)<th>{{ __('sponsors.column.sponsor') }}</th>@endif
        <th>{{ __('sponsors.column.monthly_amount') }}</th>
        <th>{{ __('sponsors.disbursements.notes') }}</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($disbursements as $row)
        <tr>
          <td dir="ltr">{{ $row->periodLabel() }}</td>
          <td dir="ltr">{{ $row->paid_on->format('Y-m-d') }}</td>
          @if ($showFile)<td dir="ltr">{{ $row->sponsorship->family->code }}</td>@endif
          @if ($showBeneficiary)<td>{{ $row->sponsorship->beneficiaryName() }}</td>@endif
          @if ($showSponsor)<td>{{ $row->sponsorship->sponsor->name }}</td>@endif
          <td dir="ltr">{{ SponsorshipAmounts::format($row->amount, $row->currency) }}</td>
          <td>{{ $row->notes ?: '—' }}</td>
        </tr>
      @empty
        <tr><td colspan="7">{{ __('sponsors.disbursements.empty') }}</td></tr>
      @endforelse
    </tbody>
  </table>
  @if ($totals->isNotEmpty())
    <p class="totals">
      {{ __('sponsors.disbursements.totals') }}:
      @foreach ($totals as $currency => $total)
        <span dir="ltr">{{ SponsorshipAmounts::format($total, $currency) }}</span>@if (! $loop->last) · @endif
      @endforeach
    </p>
  @endif
@else
  <div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <table class="w-full">
      <thead>
        <tr class="border-b border-gray-100 dark:border-gray-800">
          <th class="{{ $heading }}">{{ __('sponsors.disbursements.period') }}</th>
          <th class="{{ $heading }}">{{ __('sponsors.disbursements.paid_on') }}</th>
          @if ($showFile)<th class="{{ $heading }}">{{ __('sponsors.column.file') }}</th>@endif
          @if ($showBeneficiary)<th class="{{ $heading }}">{{ __('sponsors.column.beneficiary') }}</th>@endif
          @if ($showSponsor)<th class="{{ $heading }}">{{ __('sponsors.column.sponsor') }}</th>@endif
          <th class="{{ $heading }}">{{ __('sponsors.column.monthly_amount') }}</th>
          <th class="{{ $heading }}">{{ __('sponsors.disbursements.notes') }}</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
        @forelse ($disbursements as $row)
          <tr>
            <td class="{{ $cell }}" dir="ltr">{{ $row->periodLabel() }}</td>
            <td class="{{ $cell }}" dir="ltr">{{ $row->paid_on->format('Y-m-d') }}</td>
            @if ($showFile)<td class="{{ $cell }}" dir="ltr">{{ $row->sponsorship->family->code }}</td>@endif
            @if ($showBeneficiary)<td class="{{ $cell }}">{{ $row->sponsorship->beneficiaryName() }}</td>@endif
            @if ($showSponsor)<td class="{{ $cell }}">{{ $row->sponsorship->sponsor->name }}</td>@endif
            <td class="{{ $cell }} font-semibold text-gray-800 dark:text-white/90" dir="ltr">{{ SponsorshipAmounts::format($row->amount, $row->currency) }}</td>
            <td class="{{ $cell }}">{{ $row->notes ?: '—' }}</td>
          </tr>
        @empty
          <tr><td colspan="7" class="px-3 py-6 text-center text-theme-sm text-gray-500">{{ __('sponsors.disbursements.empty') }}</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if ($totals->isNotEmpty())
    <p class="text-theme-sm font-semibold text-gray-800 dark:text-white/90">
      {{ __('sponsors.disbursements.totals') }}:
      @foreach ($totals as $currency => $total)
        <span dir="ltr">{{ SponsorshipAmounts::format($total, $currency) }}</span>@if (! $loop->last) · @endif
      @endforeach
    </p>
  @endif
@endif
