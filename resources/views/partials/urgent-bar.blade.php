@if (config('bader.urgent.enabled') && config('bader.urgent.text'))
    <div class="bg-bader-green text-center text-sm text-white">
        @if (config('bader.urgent.url'))
            <a href="{{ config('bader.urgent.url') }}" class="block px-4 py-2 font-semibold hover:bg-bader-green-deep">
                {{ config('bader.urgent.text') }}
            </a>
        @else
            <p class="px-4 py-2">{{ config('bader.urgent.text') }}</p>
        @endif
    </div>
@endif
