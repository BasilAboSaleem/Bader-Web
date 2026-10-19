@extends('reports.layouts.print')

@section('title', __('sponsors.report.finance_case') . ' — ' . $family->code)

@section('subtitle')
  {{ __('sponsors.report.finance_case') }} — <span dir="ltr">{{ $family->code }}</span>
@endsection

@section('content')
  @include('reports.partials.case-finance-body', [
      'family' => $family,
      'sponsorName' => $sponsorName,
      'approvedAmounts' => $approvedAmounts,
      'monthRows' => $monthRows,
      'totals' => $totals,
      'print' => true,
  ])
@endsection
