@extends('reports.layouts.print')

@section('title', __('sponsors.report.finance_period'))

@section('subtitle')
  {{ __('sponsors.report.project_title') }}
@endsection

@section('content')
  @include('reports.partials.month-spreadsheet', [
      'year' => $year,
      'month' => $month,
      'rows' => $rows,
      'totals' => $totals,
      'print' => true,
  ])
@endsection
