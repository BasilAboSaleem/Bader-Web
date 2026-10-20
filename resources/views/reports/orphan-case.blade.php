@extends('reports.layouts.print')

@php
  use App\Models\OrphanFamily;
  use App\Support\SponsorshipAmounts;

  $date = fn ($value) => $value?->format('Y-m-d') ?? '—';
  $isWidow = $family->isWidowFamily();
  $active = $family->relationLoaded('activeSponsorships')
      ? $family->activeSponsorships
      : $family->activeSponsorships()->with('sponsor')->get();
@endphp

@section('title', ($printTitle ?? __('sponsors.report.case_title')) . ' — ' . $family->code)

@section('subtitle')
  {{ __('sponsors.report.form_heading') }}
  — <span dir="ltr">{{ $family->code }}</span>
@endsection

@section('content')
  <p class="meta">
    {{ __('sponsors.report.filled_on') }}: <span dir="ltr">{{ now()->format('Y-m-d') }}</span>
    @if ($mode === 'sponsor' && $sponsorship)
      — {{ __('sponsors.beneficiary.'.$sponsorship->beneficiary_type) }}: {{ $sponsorship->beneficiaryName() }}
    @endif
  </p>

  @if ($isWidow)
    <h2>1. {{ __('orphans.section.mother') }}</h2>
    <table class="data">
      <tr>
        <th>{{ __('orphans.field.mother_full_name') }}</th>
        <td>{{ $family->mother_full_name ?: '—' }}</td>
        <th>{{ __('orphans.field.mother_national_id') }}</th>
        <td dir="ltr">{{ $family->mother_national_id ?: '—' }}</td>
      </tr>
      <tr>
        <th>{{ __('orphans.field.mother_birth_date') }}</th>
        <td dir="ltr">{{ $date($family->mother_birth_date) }}</td>
        <th>{{ __('orphans.field.mother_passport_number') }}</th>
        <td dir="ltr">{{ $family->mother_passport_number ?: '—' }}</td>
      </tr>
      <tr>
        <th>{{ __('orphans.field.mother_residence_area') }}</th>
        <td>{{ $family->mother_residence_area ? __('orphans.area.'.$family->mother_residence_area) : '—' }}</td>
        <th>{{ __('orphans.field.mother_birth_country') }}</th>
        <td>{{ $family->mother_birth_country ?: '—' }}</td>
      </tr>
      <tr>
        <th>{{ __('orphans.field.mother_neighborhood') }}</th>
        <td>{{ $family->mother_neighborhood ?: '—' }}</td>
        <th>{{ __('orphans.field.mother_birth_place') }}</th>
        <td>{{ $family->mother_birth_place ?: '—' }}</td>
      </tr>
      <tr>
        <th>{{ __('orphans.field.mother_nearest_landmark') }}</th>
        <td>{{ $family->mother_nearest_landmark ?: '—' }}</td>
        <th>{{ __('orphans.field.mother_nationality') }}</th>
        <td>{{ $family->mother_nationality ?: '—' }}</td>
      </tr>
      <tr>
        <th>{{ __('orphans.field.mother_phone') }}</th>
        <td dir="ltr" colspan="3">{{ $family->mother_phone ?: '—' }}</td>
      </tr>
    </table>
  @else
    <h2>1. {{ __('orphans.section.contact') }}</h2>
    <table class="data">
      <tr>
        <th>{{ __('orphans.field.contact_name') }}</th>
        <td>{{ $family->contact_name ?: '—' }}</td>
        <th>{{ __('orphans.field.contact_relation') }}</th>
        <td>{{ $family->contact_relation ?: '—' }}</td>
      </tr>
      <tr>
        <th>{{ __('orphans.field.contact_phone') }}</th>
        <td dir="ltr" colspan="3">{{ $family->contact_phone ?: '—' }}</td>
      </tr>
    </table>
  @endif

  <h2>2. {{ __('orphans.section.father') }}</h2>
  <table class="data">
    <tr>
      <th>{{ __('orphans.field.father_full_name') }}</th>
      <td>{{ $family->father_full_name }}</td>
      <th>{{ __('orphans.field.father_death_date') }}</th>
      <td dir="ltr">{{ $date($family->father_death_date) }}</td>
    </tr>
    <tr>
      <th>{{ __('orphans.field.father_death_cause') }}</th>
      <td colspan="3">{{ $family->father_death_cause }}</td>
    </tr>
  </table>

  <h2>3. {{ __('orphans.section.orphans') }}</h2>
  <table class="data grid">
    <thead>
      <tr>
        <th>#</th>
        <th>{{ __('orphans.field.orphan_full_name') }}</th>
        <th>{{ __('orphans.field.orphan_birth_date') }}</th>
        <th>{{ __('orphans.field.orphan_national_id') }}</th>
        <th>{{ __('orphans.field.orphan_education_stage') }}</th>
        <th>{{ __('orphans.field.orphan_health_status') }}</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($family->orphans as $index => $orphan)
        <tr>
          <td>{{ $index + 1 }}</td>
          <td>
            <span style="display:inline-flex;align-items:center;gap:8px;vertical-align:middle">
              @if ($mode === 'admin' && $orphan->photo)
                <a href="{{ route('dashboard.orphans.photo', [$family, $orphan]) }}" target="_blank" rel="noopener">
                  <img src="{{ route('dashboard.orphans.photo', [$family, $orphan]) }}" alt="{{ $orphan->full_name }}" width="40" height="40" style="display:block;width:40px;height:40px;object-fit:cover;border-radius:9999px">
                </a>
              @endif
              <span>{{ $orphan->full_name }}</span>
            </span>
          </td>
          <td dir="ltr">{{ $date($orphan->birth_date) }}</td>
          <td dir="ltr">{{ $orphan->national_id }}</td>
          <td>{{ __('orphans.education.'.$orphan->education_stage) }}</td>
          <td>
            {{ __('orphans.health.'.$orphan->health_status) }}
            @if ($orphan->health_notes)
              <br><span style="font-size:.8rem;color:#6b7280">{{ $orphan->health_notes }}</span>
            @endif
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <h2>4. {{ __('orphans.sponsorship_title') }}</h2>
  @if ($mode === 'sponsor' && $sponsorship)
    <table class="data">
      <tr>
        <th>{{ __('sponsors.field.sponsor') }}</th>
        <td>{{ $sponsorship->sponsor->name }}</td>
        <th>{{ __('sponsors.beneficiary.'.$sponsorship->beneficiary_type) }}</th>
        <td>{{ $sponsorship->beneficiaryName() }}</td>
      </tr>
      <tr>
        <th>{{ __('sponsors.field.monthly_amount') }}</th>
        <td dir="ltr">{{ SponsorshipAmounts::format($sponsorship->monthly_amount, $sponsorship->currency) }}</td>
        <th>{{ __('sponsors.field.started_at') }}</th>
        <td dir="ltr">{{ $date($sponsorship->started_at) }}</td>
      </tr>
      <tr>
        <th>{{ __('sponsors.field.renewal_type') }}</th>
        <td>{{ __('sponsors.renewal.'.$sponsorship->renewal_type) }}</td>
        <th>{{ __('sponsors.field.planned_ended_at') }}</th>
        <td dir="ltr">{{ $date($sponsorship->planned_ended_at) }}</td>
      </tr>
    </table>
  @else
    @php
      $lead = $active->firstWhere('beneficiary_type', \App\Models\Sponsorship::BENEFICIARY_FAMILY) ?? $active->sortBy('started_at')->first();
      $reportTotals = $family->sponsorship_amount !== null && $lead
          ? [$lead->currency => (float) $family->sponsorship_amount]
          : SponsorshipAmounts::monthlyTotals($active);
      $sponsorNames = $active->map(fn ($row) => $row->sponsor->name)->unique()->values();
    @endphp
    @if ($reportTotals === [])
      <p class="meta">{{ __('sponsors.no_active') }}</p>
    @else
      <table class="data">
        <tr>
          <th>{{ __('sponsors.field.sponsor') }}</th>
          <td>{{ $sponsorNames->implode(' · ') ?: '—' }}</td>
          <th>{{ __('sponsors.field.sponsorship_amount') }}</th>
          <td dir="ltr">
            @foreach ($reportTotals as $currency => $amount)
              {{ SponsorshipAmounts::format($amount, $currency) }}@if (! $loop->last) · @endif
            @endforeach
          </td>
        </tr>
        @if ($lead)
          <tr>
            <th>{{ __('sponsors.field.started_at') }}</th>
            <td dir="ltr">{{ $date($lead->started_at) }}</td>
            <th>{{ __('sponsors.field.renewal_type') }}</th>
            <td>
              {{ __('sponsors.renewal.'.$lead->renewal_type) }}
              @if ($lead->isPrepaid() && $lead->planned_ended_at)
                <br>{{ __('sponsors.prepaid_period', ['from' => $date($lead->started_at), 'until' => $date($lead->planned_ended_at)]) }}
              @endif
            </td>
          </tr>
          @if ($lead->planned_ended_at)
            <tr>
              <th>{{ __('sponsors.field.planned_ended_at') }}</th>
              <td dir="ltr" colspan="3">{{ $date($lead->planned_ended_at) }}</td>
            </tr>
          @endif
        @endif
      </table>
    @endif
  @endif

  @if ($family->review_notes)
    <h2>5. {{ __('orphans.review_notes') }}</h2>
    <table class="data">
      <tr><td>{{ $family->review_notes }}</td></tr>
    </table>
  @endif

  @php
    $caseDisbursements = $disbursements ?? collect();
    $caseFinanceTotals = $financeTotals ?? collect();
  @endphp
  <h2>{{ $family->review_notes ? '6' : '5' }}. {{ __('sponsors.report.finance_case') }}</h2>
  @if ($caseDisbursements->isEmpty())
    <p class="meta">{{ __('sponsors.disbursements.empty') }}</p>
  @else
    <table class="data grid">
      <thead>
        <tr>
          <th>{{ __('sponsors.disbursements.period') }}</th>
          <th>{{ __('sponsors.column.beneficiary') }}</th>
          <th>{{ __('sponsors.column.sponsor') }}</th>
          <th>{{ __('sponsors.column.monthly_amount') }}</th>
          <th>{{ __('sponsors.disbursements.paid_on') }}</th>
          <th>{{ __('sponsors.disbursements.notes') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($caseDisbursements as $row)
          <tr>
            <td dir="ltr">{{ $row->periodLabel() }}</td>
            <td>{{ $row->sponsorship->beneficiaryName() }}</td>
            <td>{{ $row->sponsorship->sponsor->name }}</td>
            <td dir="ltr">{{ SponsorshipAmounts::format($row->amount, $row->currency) }}</td>
            <td dir="ltr">{{ $row->paid_on->format('Y-m-d') }}</td>
            <td>{{ $row->notes ?: '—' }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
    @if ($caseFinanceTotals->isNotEmpty())
      <p class="totals">
        {{ __('sponsors.disbursements.totals') }}:
        @foreach ($caseFinanceTotals as $currency => $total)
          <span dir="ltr">{{ SponsorshipAmounts::format($total, $currency) }}</span>@if (! $loop->last) · @endif
        @endforeach
      </p>
    @endif
  @endif
@endsection
