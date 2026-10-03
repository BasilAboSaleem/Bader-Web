@php
    use App\Support\SiteSettings;
    $urgentEnabled = SiteSettings::urgentEnabled();
    $urgentText = SiteSettings::urgentText();
    $urgentUrl = SiteSettings::urgentUrl();
@endphp

@if ($urgentEnabled && !empty($urgentText))
    <div class="bg-gradient-to-r from-red-950 via-teal-950 to-red-950 border-b border-red-500/30 text-center text-xs sm:text-sm text-sand-50 py-2.5 px-4 shadow-inner relative z-50">
        <div class="max-w-7xl mx-auto flex items-center justify-center gap-3 flex-wrap">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-red-500/20 border border-red-500/40 px-2.5 py-0.5 text-[11px] font-bold text-red-300 uppercase tracking-wider">
                <span class="h-1.5 w-1.5 rounded-full bg-red-400 animate-ping"></span>
                <span class="h-1.5 w-1.5 rounded-full bg-red-400 absolute"></span>
                <span>{{ __('header.urgent_label') }}</span>
            </span>
            @if (!empty($urgentUrl))
                <a href="{{ $urgentUrl }}" class="inline-flex items-center gap-2 font-medium text-sand-100 hover:text-gold-400 transition-colors">
                    <span>{{ $urgentText }}</span>
                    <span aria-hidden="true" class="text-gold-400 font-bold">&larr;</span>
                </a>
            @else
                <p class="inline-flex items-center gap-2 font-medium text-sand-100">
                    <span>{{ $urgentText }}</span>
                </p>
            @endif
        </div>
    </div>
@endif
