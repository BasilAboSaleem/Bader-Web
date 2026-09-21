@php
    use App\Support\SiteSettings;
    $urgentEnabled = SiteSettings::urgentEnabled();
    $urgentText = SiteSettings::urgentText();
    $urgentUrl = SiteSettings::urgentUrl();
@endphp

@if ($urgentEnabled && !empty($urgentText))
    <div class="bg-bader-green text-center text-sm text-white">
        @if (!empty($urgentUrl))
            <a href="{{ $urgentUrl }}" class="block px-4 py-2 font-semibold hover:bg-bader-green-deep">
                {{ $urgentText }}
            </a>
        @else
            <p class="px-4 py-2">{{ $urgentText }}</p>
        @endif
    </div>
@endif
