@props(['src', 'sizes' => '100vw', 'width' => \App\Support\ResponsiveImage::DEFAULT_WIDTH])

@php
    $srcset = \App\Support\ResponsiveImage::srcset($src);
@endphp

<img src="{{ \App\Support\ResponsiveImage::url($src, $width) }}" @if ($srcset) srcset="{{ $srcset }}" sizes="{{ $sizes }}" @endif decoding="async" {{ $attributes }}>
