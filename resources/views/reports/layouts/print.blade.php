<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', __('brand.name'))</title>
  <style>
    :root { color-scheme: light; }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: "Segoe UI", Tahoma, Arial, sans-serif;
      color: #111827;
      background: #f3f4f6;
      font-size: 13px;
      line-height: 1.5;
    }
    .sheet {
      max-width: 1100px;
      margin: 1.5rem auto;
      background: #fff;
      padding: 1.5rem 1.75rem 2rem;
      box-shadow: 0 1px 3px rgba(0,0,0,.08);
    }
    .toolbar {
      display: flex;
      gap: .75rem;
      justify-content: flex-end;
      margin-bottom: 1rem;
    }
    .toolbar button, .toolbar a {
      appearance: none;
      border: 1px solid #d1d5db;
      background: #fff;
      color: #111827;
      border-radius: .5rem;
      padding: .5rem .9rem;
      font: inherit;
      text-decoration: none;
      cursor: pointer;
    }
    .toolbar .primary { background: #0f766e; border-color: #0f766e; color: #fff; }
    .brand {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      border-bottom: 2px solid #0f766e;
      padding-bottom: .75rem;
      margin-bottom: 1rem;
    }
    .brand img { height: 48px; width: auto; }
    .brand h1 { margin: 0; font-size: 1.15rem; color: #0f766e; }
    .brand p { margin: .15rem 0 0; color: #6b7280; font-size: .85rem; }
    h2 {
      margin: 1.25rem 0 .5rem;
      font-size: .95rem;
      color: #0f766e;
      border-bottom: 1px solid #e5e7eb;
      padding-bottom: .35rem;
    }
    table.data {
      width: 100%;
      border-collapse: collapse;
      margin-top: .35rem;
    }
    table.data th, table.data td {
      border: 1px solid #d1d5db;
      padding: .4rem .55rem;
      vertical-align: middle;
      text-align: center;
    }
    table.data th {
      background: #f9fafb;
      font-weight: 600;
      width: 28%;
      color: #374151;
    }
    table.grid th { width: auto; background: #f3f4f6; }
    .meta { color: #6b7280; font-size: .8rem; margin: 0 0 .35rem; }
    .totals { margin-top: .75rem; font-weight: 700; text-align: center; }
    .note { margin-top: 1rem; color: #6b7280; font-size: .8rem; }
    .case-head { display: flex; justify-content: space-between; gap: 1rem; margin-bottom: 1rem; }
    .case-name, .sponsor-name { margin: .15rem 0 0; font-size: 1.05rem; font-weight: 700; }
    .sponsor-side { text-align: start; }
    .approved-box {
      margin: 1rem 0;
      padding: .75rem;
      text-align: center;
      border: 2px solid #0f766e;
      background: #ecfdf5;
    }
    .approved-value { margin: .25rem 0 0; font-size: 1.2rem; font-weight: 700; color: #0f766e; }
    .sheet-banner {
      background: #0f766e;
      color: #fff;
      text-align: center;
      font-weight: 700;
      padding: .55rem;
      margin-bottom: .35rem;
    }
    .sheet-sub {
      background: #1e3a5f;
      color: #fff;
      text-align: center;
      font-weight: 600;
      padding: .45rem;
      margin-bottom: .75rem;
    }
    table.sheet-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 11px;
    }
    table.sheet-table th, table.sheet-table td {
      border: 1px solid #94a3b8;
      padding: .28rem .3rem;
      text-align: center;
      vertical-align: middle;
    }
    table.sheet-table thead .group-head th {
      background: #1e3a5f;
      color: #fff;
      font-weight: 700;
    }
    table.sheet-table thead .sub-head th {
      background: #334155;
      color: #fff;
      font-weight: 600;
    }
    table.sheet-table td.amount-col,
    table.sheet-table th.amount-col {
      background: #fdba74;
      font-weight: 700;
    }
    @media print {
      body { background: #fff; }
      .sheet { margin: 0; box-shadow: none; max-width: none; padding: 0; }
      .toolbar { display: none !important; }
      a { color: inherit; text-decoration: none; }
      @page { size: A4 landscape; margin: 8mm; }
    }
  </style>
</head>
<body>
  <div class="sheet">
    <div class="toolbar no-print">
      <button type="button" class="primary" onclick="window.print()">{{ __('sponsors.report.print') }}</button>
      <a href="{{ url()->previous() }}">{{ __('dashboard.back') }}</a>
    </div>

    <header class="brand">
      <div>
        <h1>{{ __('brand.name') }}</h1>
        <p>@yield('subtitle')</p>
      </div>
      <img src="{{ \App\Support\SiteSettings::brandAsset('logo_full') }}" alt="{{ __('brand.name') }}">
    </header>

    @yield('content')

    <p class="note">{{ __('sponsors.report.footer') }} — {{ now()->format('Y-m-d H:i') }}</p>
  </div>
</body>
</html>
