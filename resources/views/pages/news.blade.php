@extends('layouts.public')

@section('title', __('page.news.title').' — '.__('brand.name'))

@section('content')
    <section class="bg-bader-green-deep text-white">
        <div class="mx-auto max-w-6xl px-4 py-16 sm:py-20">
            <x-bader.section-heading :kicker="__('page.news.kicker')" :title="__('page.news.title')" theme="dark" />
            <p class="mt-5 max-w-2xl text-sm leading-relaxed text-white/75 sm:text-base">{{ __('page.news.intro') }}</p>
        </div>
    </section>

    <section class="bg-bader-green-deep text-white">
        <div class="mx-auto grid max-w-6xl gap-5 px-4 pb-16 md:grid-cols-3">
            @foreach ($stories as $story)
                <x-bader.story-card
                    :title="__('story.'.$story['key'].'.title')"
                    :excerpt="__('story.'.$story['key'].'.excerpt')"
                    :category="__('story.'.$story['key'].'.category')"
                    :date="$story['date']"
                    :href="route('news')"
                />
            @endforeach
        </div>
    </section>
@endsection
