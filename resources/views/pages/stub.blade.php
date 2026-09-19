@extends('layouts.public')

@section('title', __($titleKey).' — '.__('brand.name'))

@section('content')
    <section class="mx-auto max-w-3xl px-4 py-16">
        <x-bader.section-heading :kicker="__('stub.kicker')" :title="__($titleKey)">
            {{ __('stub.body') }}
        </x-bader.section-heading>
        <div class="mt-10 flex flex-wrap gap-3">
            <x-bader.button :href="route('home')" variant="line">{{ __('nav.home') }}</x-bader.button>
            <x-bader.button :href="route('donate')">{{ __('nav.donate') }}</x-bader.button>
        </div>
    </section>
@endsection
