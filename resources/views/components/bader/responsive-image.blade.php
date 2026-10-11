@props(['src', 'sizes' => '100vw', 'width' => \App\Support\ResponsiveImage::DEFAULT_WIDTH])

@php
    $srcset = \App\Support\ResponsiveImage::srcset($src);
@endphp

@if ($srcset)
    <picture class="contents">
        <source type="image/avif" srcset="{{ \App\Support\ResponsiveImage::srcset($src, 'avif') }}" sizes="{{ $sizes }}">
        <img src="{{ \App\Support\ResponsiveImage::url($src, $width) }}" srcset="{{ $srcset }}" sizes="{{ $sizes }}" decoding="async" {{ $attributes }}>
    </picture>
@else
    <img src="{{ \App\Support\ResponsiveImage::url($src, $width) }}" decoding="async" {{ $attributes }}>
@endif
