@extends('layouts.public')

@section('title', ($selectedRegion ? $selectedRegion->name.' — ' : '').__('home.map.kicker').' — '.__('brand.name'))
@section('meta_description', \Illuminate\Support\Str::limit((string) ($selectedRegion?->description ?: __('home.map.text')), 160))

@section('content')
    <x-bader.page-hero
        :kicker="__('home.map.kicker')"
        :title="__('home.map.title')"
        :intro="__('home.map.page_intro')"
    />

    <x-bader.region-map :regions="$regions" :selected="$selectedRegion?->key" variant="page" />
@endsection
