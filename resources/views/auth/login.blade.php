<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0f3d22">
    <title>{{ __('auth.login_title') }}</title>
    <link rel="icon" href="{{ asset(config('bader.assets.favicon')) }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-bader-admin-paper text-bader-ink antialiased flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center px-4">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-3 transition hover:opacity-85">
            <img src="{{ asset(config('bader.assets.mark_star')) }}" alt="" width="48" height="48" class="h-12 w-12">
            <span class="text-2xl font-bold tracking-tight text-bader-green-deep">{{ __('brand.name') }}</span>
        </a>
        <h1 class="mt-6 text-2xl font-bold tracking-tight text-bader-ink">{{ __('auth.login_heading') }}</h1>
        <p class="mt-2 text-sm text-bader-ink/70">{{ __('auth.login_description') }}</p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
        <div class="rounded-2xl border border-bader-green/10 bg-white p-6 shadow-sm sm:p-8">
            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-semibold text-bader-ink">{{ __('auth.email') }}</label>
                    <div class="mt-1.5">
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="email"
                            class="block w-full rounded-xl border border-bader-green/20 bg-bader-paper/30 px-3.5 py-2.5 text-sm text-bader-ink placeholder:text-bader-ink/40 focus:border-bader-green focus:bg-white focus:outline-none focus:ring-2 focus:ring-bader-green/20"
                            placeholder="admin@baderhumanitarian.com"
                        >
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-bader-ink">{{ __('auth.password') }}</label>
                    <div class="mt-1.5">
                        <input
                            type="password"
                            name="password"
                            id="password"
                            required
                            autocomplete="current-password"
                            class="block w-full rounded-xl border border-bader-green/20 bg-bader-paper/30 px-3.5 py-2.5 text-sm text-bader-ink placeholder:text-bader-ink/40 focus:border-bader-green focus:bg-white focus:outline-none focus:ring-2 focus:ring-bader-green/20"
                        >
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input
                            type="checkbox"
                            name="remember"
                            id="remember"
                            value="1"
                            {{ old('remember') ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-bader-green/30 text-bader-green focus:ring-bader-green"
                        >
                        <span class="text-sm text-bader-ink/75">{{ __('auth.remember_me') }}</span>
                    </label>
                </div>

                <div>
                    <button
                        type="submit"
                        class="flex w-full justify-center rounded-xl bg-bader-green px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-bader-green-deep focus:outline-none focus:ring-2 focus:ring-bader-green/40 active:translate-y-0.5"
                    >
                        {{ __('auth.login_button') }}
                    </button>
                </div>
            </form>
        </div>

        <p class="mt-6 text-center text-sm">
            <a href="{{ route('home') }}" class="font-medium text-bader-green hover:underline">
                &larr; {{ __('auth.back_to_site') }}
            </a>
        </p>
    </div>
</body>
</html>
