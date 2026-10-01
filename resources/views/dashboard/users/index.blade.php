@extends('layouts.dashboard')

@section('title', __('dashboard.users_title'))

@section('content')
  <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('dashboard.users_title') }}</h1>
      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('dashboard.users_admins_description') }}</p>
    </div>
    <a href="{{ route('dashboard.users.create') }}" class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-600">
      {{ __('dashboard.users_add_admin') }}
    </a>
  </div>

  @if (session('status'))
    <div role="status" class="mb-5 rounded-lg bg-success-50 px-4 py-3 text-sm font-medium text-success-700 dark:bg-success-500/10 dark:text-success-400">
      {{ session('status') }}
    </div>
  @endif

  <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <div class="overflow-x-auto">
      <table class="w-full text-start text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-500 dark:bg-gray-800 dark:text-gray-400">
          <tr>
            <th scope="col" class="px-5 py-3">{{ __('dashboard.users_name') }}</th>
            <th scope="col" class="px-5 py-3">{{ __('dashboard.users_email') }}</th>
            <th scope="col" class="px-5 py-3">{{ __('dashboard.users_created_at') }}</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          @forelse ($users as $user)
            <tr>
              <td class="px-5 py-4 font-medium text-gray-900 dark:text-white">{{ $user->name }}</td>
              <td class="px-5 py-4 text-gray-600 dark:text-gray-300">{{ $user->email }}</td>
              <td class="px-5 py-4 text-gray-600 dark:text-gray-300">{{ $user->created_at->format('Y-m-d') }}</td>
            </tr>
          @empty
            <tr><td colspan="3" class="px-5 py-8 text-center text-gray-500">{{ __('dashboard.users_empty') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection
