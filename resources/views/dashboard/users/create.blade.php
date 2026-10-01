@extends('layouts.dashboard')

@section('title', __('dashboard.users_add_admin'))

@section('content')
  <div class="mx-auto max-w-2xl">
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('dashboard.users_add_admin') }}</h1>
      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('dashboard.users_admins_description') }}</p>
    </div>

    <form method="POST" action="{{ route('dashboard.users.store') }}" class="space-y-5 rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
      @csrf
      <div>
        <label for="name" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('dashboard.users_name') }}</label>
        <input id="name" name="name" type="text" value="{{ old('name') }}" required autocomplete="name" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-900 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
        @error('name') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
      </div>

      <div>
        <label for="email" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('dashboard.users_email') }}</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-900 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
        @error('email') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
      </div>

      <div>
        <label for="password" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('dashboard.users_password') }}</label>
        <input id="password" name="password" type="password" required minlength="12" autocomplete="new-password" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-900 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
        <p class="mt-1 text-xs text-gray-500">{{ __('dashboard.users_password_hint') }}</p>
        @error('password') <p class="mt-1 text-sm text-error-500">{{ $message }}</p> @enderror
      </div>

      <div>
        <label for="password_confirmation" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('dashboard.users_password_confirmation') }}</label>
        <input id="password_confirmation" name="password_confirmation" type="password" required minlength="12" autocomplete="new-password" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-900 outline-none focus:border-brand-500 dark:border-gray-700 dark:text-white">
      </div>

      <div class="flex flex-wrap gap-3 pt-2">
        <button type="submit" class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-600">{{ __('dashboard.users_create') }}</button>
        <a href="{{ route('dashboard.users.index') }}" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 dark:border-gray-700 dark:text-gray-300">{{ __('dashboard.cancel') }}</a>
      </div>
    </form>
  </div>
@endsection
