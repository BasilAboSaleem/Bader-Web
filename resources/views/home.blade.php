@extends('layouts.public')

@section('title', __('brand.name'))
@section('meta_description', __('home.hero_text'))

@section('content')
    <x-bader.hero-slider :campaigns="$heroCampaigns" />

    @foreach ($homeSections as $homeSection)
        @switch($homeSection)
            @case('quick_give')
                <x-bader.quick-give :options="$quickGiveOptions" />
                @break
            @case('regions')
                <x-bader.regions-band :regions="$regions" :metrics="$metrics" />
                @break
            @case('programs')
                <x-bader.programs-tabs :programs="$programs" />
                @break
            @case('projects')
                <x-bader.projects-scroller :projects="$projects" />
                @break
            @case('gift')
                <x-bader.gift-section :designs="$giftDesigns" />
                @break
            @case('news')
                <x-bader.news :stories="$stories" />
                @break
            @case('map')
                <x-bader.region-map :regions="$regions" />
                @break
            @case('sponsorship')
                <x-bader.sponsorship-cta :case="$featuredCase" :from="$sponsorshipFrom" />
                @break
            @case('ways_to_give')
                <x-bader.ways-to-give :gold-price-per-gram="$goldPricePerGram" />
                @break
            @case('trust')
                <x-bader.trust-pillars />
                @break
        @endswitch
    @endforeach
@endsection
