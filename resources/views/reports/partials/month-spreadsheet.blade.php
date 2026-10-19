@php
  use App\Support\SponsorshipAmounts;
  use Carbon\CarbonImmutable;

  $print = $print ?? false;
  $monthLabel = CarbonImmutable::create($year, $month, 1)->translatedFormat('F Y');
  $th = $print ? '' : 'px-2 py-2 text-center align-middle text-theme-xs font-semibold text-gray-500 dark:text-gray-400';
  $td = $print ? '' : 'px-2 py-2 text-center align-middle text-theme-xs text-gray-700 dark:text-gray-300';
  $money = fn (?float $amount, string $currency): string => $amount === null
      ? '—'
      : number_format($amount, $amount == floor($amount) ? 0 : 2);
@endphp

@if ($print)
  <div class="sheet-banner">{{ __('sponsors.report.project_title') }}</div>
  <div class="sheet-sub">{{ __('sponsors.report.month_statement', ['month' => $monthLabel]) }}</div>
@else
  <p class="text-theme-sm font-medium text-gray-700 dark:text-gray-300">
    {{ __('sponsors.report.month_statement', ['month' => $monthLabel]) }}
    — {{ __('sponsors.report.cases_count', ['count' => $rows->count()]) }}
  </p>
@endif

<div @class(['overflow-x-auto rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]' => ! $print])>
  <table @class(['w-full' => ! $print, 'sheet-table' => $print])>
    <thead>
      @if ($print)
        <tr class="group-head">
          <th rowspan="2">#</th>
          <th colspan="8">{{ __('sponsors.report.basic_data') }}</th>
          <th rowspan="2">{{ __('sponsors.report.col.started_at') }}</th>
          <th rowspan="2" class="amount-col">{{ __('sponsors.report.col.month_amount') }}</th>
          <th rowspan="2">{{ __('sponsors.report.col.paid_on') }}</th>
          <th rowspan="2">{{ __('sponsors.report.col.sponsor') }}</th>
        </tr>
        <tr class="sub-head">
          <th>{{ __('sponsors.report.col.widow_name') }}</th>
          <th>{{ __('sponsors.report.col.national_id') }}</th>
          <th>{{ __('sponsors.report.col.phone') }}</th>
          <th>{{ __('sponsors.report.col.orphans_count') }}</th>
          <th>{{ __('sponsors.report.col.wife') }}</th>
          <th>{{ __('sponsors.report.col.children') }}</th>
          <th>{{ __('sponsors.report.col.displacement') }}</th>
          <th>{{ __('sponsors.report.col.estimated') }}</th>
        </tr>
      @else
        <tr class="border-b border-gray-100 dark:border-gray-800">
          <th class="{{ $th }}">#</th>
          <th class="{{ $th }}">{{ __('sponsors.report.col.widow_name') }}</th>
          <th class="{{ $th }}">{{ __('sponsors.report.col.national_id') }}</th>
          <th class="{{ $th }}">{{ __('sponsors.report.col.phone') }}</th>
          <th class="{{ $th }}">{{ __('sponsors.report.col.orphans_count') }}</th>
          <th class="{{ $th }}">{{ __('sponsors.report.col.wife') }}</th>
          <th class="{{ $th }}">{{ __('sponsors.report.col.children') }}</th>
          <th class="{{ $th }}">{{ __('sponsors.report.col.displacement') }}</th>
          <th class="{{ $th }}">{{ __('sponsors.report.col.estimated') }}</th>
          <th class="{{ $th }}">{{ __('sponsors.report.col.started_at') }}</th>
          <th class="{{ $th }} bg-orange-50 text-orange-800 dark:bg-orange-500/10 dark:text-orange-300">{{ __('sponsors.report.col.month_amount') }}</th>
          <th class="{{ $th }}">{{ __('sponsors.report.col.paid_on') }}</th>
          <th class="{{ $th }}">{{ __('sponsors.report.col.sponsor') }}</th>
        </tr>
      @endif
    </thead>
    <tbody @class(['divide-y divide-gray-100 dark:divide-gray-800' => ! $print])>
      @forelse ($rows as $index => $row)
        <tr>
          <td class="{{ $td }}">{{ $index + 1 }}</td>
          <td class="{{ $td }}">{{ $row['case_name'] }}</td>
          <td class="{{ $td }}" dir="ltr">{{ $row['national_id'] }}</td>
          <td class="{{ $td }}" dir="ltr">{{ $row['phone'] }}</td>
          <td class="{{ $td }}">{{ $row['orphans_count'] }}</td>
          <td class="{{ $td }}" dir="ltr">{{ $money($row['wife_amount'], $row['currency']) }}</td>
          <td class="{{ $td }}" dir="ltr">{{ $money($row['children_amount'], $row['currency']) }}</td>
          <td class="{{ $td }}" dir="ltr">{{ $row['displacement_amount'] === null ? '—' : $money($row['displacement_amount'], $row['currency']) }}</td>
          <td class="{{ $td }}" dir="ltr">{{ $money($row['estimated_amount'], $row['currency']) }}</td>
          <td class="{{ $td }}" dir="ltr">{{ $row['started_at'] ?: '—' }}</td>
          <td @class([$td, 'bg-orange-50 font-semibold text-orange-900 dark:bg-orange-500/10 dark:text-orange-200' => ! $print, 'amount-col' => $print]) dir="ltr">
            {{ $money($row['month_amount'], $row['currency']) }}
          </td>
          <td class="{{ $td }}" dir="ltr">{{ $row['paid_on'] ?: '—' }}</td>
          <td class="{{ $td }}">{{ $row['sponsor_name'] }}</td>
        </tr>
      @empty
        <tr>
          <td class="{{ $td }}" colspan="13">{{ __('sponsors.disbursements.empty') }}</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

@if ($totals->isNotEmpty())
  <p @class(['mt-4 text-theme-sm font-semibold text-gray-900 dark:text-white' => ! $print, 'totals' => $print])>
    {{ __('sponsors.disbursements.totals') }}:
    @foreach ($totals as $currency => $total)
      <span dir="ltr">{{ SponsorshipAmounts::format($total, $currency) }}</span>@if (! $loop->last) · @endif
    @endforeach
  </p>
@endif
