@php
  use App\Support\SponsorshipAmounts;

  $print = $print ?? false;
  $caseName = $family->isWidowFamily()
      ? ($family->mother_full_name ?: $family->display_name)
      : $family->display_name;
  $th = $print
      ? ''
      : 'border border-gray-200 bg-gray-50 px-3 py-2 text-center align-middle text-theme-xs font-semibold text-gray-600 dark:border-gray-700 dark:bg-white/[0.04] dark:text-gray-300';
  $td = $print
      ? ''
      : 'border border-gray-200 px-3 py-2 text-center align-middle text-theme-sm text-gray-800 dark:border-gray-700 dark:text-white/90';
@endphp

<div @class(['space-y-5' => ! $print])>
  <div @class([
      'flex flex-wrap items-start justify-between gap-3 rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]' => ! $print,
      'case-head' => $print,
  ])>
    <div>
      <p @class(['text-theme-xs text-gray-500 dark:text-gray-400' => ! $print, 'meta' => $print])>{{ __('sponsors.report.case_name') }}</p>
      <p @class(['text-lg font-bold text-gray-900 dark:text-white' => ! $print, 'case-name' => $print])>{{ $caseName }}</p>
      <p @class(['mt-1 font-mono text-theme-xs text-gray-500' => ! $print]) dir="ltr">{{ $family->code }}</p>
    </div>
    <div @class(['text-end' => ! $print, 'sponsor-side' => $print])>
      <p @class(['text-theme-xs text-gray-500 dark:text-gray-400' => ! $print, 'meta' => $print])>{{ __('sponsors.field.sponsor') }}</p>
      <p @class(['text-base font-semibold text-gray-900 dark:text-white' => ! $print, 'sponsor-name' => $print])>{{ $sponsorName }}</p>
    </div>
  </div>

  <div>
    <h2 @class(['mb-2 text-base font-semibold text-gray-900 dark:text-white' => ! $print])>{{ __('orphans.section.orphans') }}</h2>
    <div @class(['overflow-x-auto' => ! $print])>
      <table @class(['w-full border-collapse' => ! $print, 'data grid' => $print])>
        <thead>
          <tr>
            <th class="{{ $th }}">#</th>
            <th class="{{ $th }}">{{ __('orphans.field.orphan_full_name') }}</th>
            <th class="{{ $th }}">{{ __('orphans.field.orphan_birth_date') }}</th>
            <th class="{{ $th }}">{{ __('orphans.field.orphan_national_id') }}</th>
            <th class="{{ $th }}">{{ __('orphans.field.orphan_education_stage') }}</th>
            <th class="{{ $th }}">{{ __('orphans.field.orphan_health_status') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($family->orphans as $index => $orphan)
            <tr>
              <td class="{{ $td }}">{{ $index + 1 }}</td>
              <td class="{{ $td }}">{{ $orphan->full_name }}</td>
              <td class="{{ $td }}" dir="ltr">{{ $orphan->birth_date?->format('Y-m-d') ?? '—' }}</td>
              <td class="{{ $td }}" dir="ltr">{{ $orphan->national_id ?: '—' }}</td>
              <td class="{{ $td }}">{{ __('orphans.education.'.$orphan->education_stage) }}</td>
              <td class="{{ $td }}">
                {{ __('orphans.health.'.$orphan->health_status) }}
                @if ($orphan->health_notes)
                  <span @class(['block text-theme-xs text-gray-500' => ! $print])>{{ $orphan->health_notes }}</span>
                @endif
              </td>
            </tr>
          @empty
            <tr><td class="{{ $td }}" colspan="6">—</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div @class([
      'rounded-2xl border border-brand-200 bg-brand-50/60 p-4 text-center dark:border-brand-500/30 dark:bg-brand-500/10' => ! $print,
      'approved-box' => $print,
  ])>
    <p @class(['text-theme-xs font-medium text-brand-700 dark:text-brand-300' => ! $print])>{{ __('sponsors.report.approved_amount') }}</p>
    <p @class(['mt-1 text-xl font-bold text-brand-800 dark:text-brand-200' => ! $print, 'approved-value' => $print])>
      @forelse ($approvedAmounts as $currency => $amount)
        <span dir="ltr">{{ SponsorshipAmounts::format($amount, $currency) }}</span>@if (! $loop->last) · @endif
      @empty
        —
      @endforelse
    </p>
  </div>

  <div>
    <h2 @class(['mb-2 text-base font-semibold text-gray-900 dark:text-white' => ! $print])>{{ __('sponsors.report.finance_case') }}</h2>
    <div @class(['overflow-x-auto' => ! $print])>
      <table @class(['w-full border-collapse' => ! $print, 'data grid' => $print])>
        <thead>
          <tr>
            <th class="{{ $th }}">{{ __('sponsors.disbursements.period') }}</th>
            <th class="{{ $th }}">{{ __('sponsors.column.monthly_amount') }}</th>
            <th class="{{ $th }}">{{ __('sponsors.disbursements.notes') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($monthRows as $row)
            <tr>
              <td class="{{ $td }}" dir="ltr">{{ $row['period'] }}</td>
              <td class="{{ $td }}" dir="ltr">{{ SponsorshipAmounts::format($row['amount'], $row['currency']) }}</td>
              <td class="{{ $td }}">{{ $row['notes'] }}</td>
            </tr>
          @empty
            <tr><td class="{{ $td }}" colspan="3">{{ __('sponsors.disbursements.empty') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div @class([
      'rounded-2xl border border-gray-200 bg-white p-4 text-center dark:border-gray-800 dark:bg-white/[0.03]' => ! $print,
      'totals' => $print,
  ])>
    <p @class(['text-theme-xs text-gray-500 dark:text-gray-400' => ! $print])>{{ __('sponsors.report.received_total') }}</p>
    <p @class(['mt-1 text-lg font-bold text-gray-900 dark:text-white' => ! $print])>
      @forelse ($totals as $currency => $amount)
        <span dir="ltr">{{ SponsorshipAmounts::format($amount, $currency) }}</span>@if (! $loop->last) · @endif
      @empty
        —
      @endforelse
    </p>
  </div>
</div>
