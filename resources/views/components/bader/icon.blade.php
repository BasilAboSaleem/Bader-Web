@props(['name', 'class' => 'h-5 w-5'])

@if ($name === 'whatsapp')
    <svg {{ $attributes->class([$class, 'shrink-0']) }} viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">
        <path d="M17.5 14.4c-.3-.1-1.8-.9-2-1s-.5-.1-.7.1-.8 1-1 1.2-.4.2-.6.1a8.2 8.2 0 0 1-4-3.5c-.3-.5.3-.5.9-1.6.1-.2 0-.4 0-.5l-.9-2.2c-.2-.6-.5-.5-.7-.5h-.6a1.2 1.2 0 0 0-.8.4 3.6 3.6 0 0 0-1.1 2.6 6.2 6.2 0 0 0 1.3 3.3 14.2 14.2 0 0 0 5.5 4.8c2 .9 2.8 1 3.8.8a3.3 3.3 0 0 0 2.1-1.5 2.7 2.7 0 0 0 .2-1.5c-.1-.1-.3-.2-.6-.3ZM12 21.8a9.8 9.8 0 0 1-5-1.4l-.4-.2-3.7 1 1-3.6-.2-.4A9.8 9.8 0 1 1 12 21.8Zm0-21.6A11.8 11.8 0 0 0 1.9 18L.2 23.8l6-1.6A11.8 11.8 0 1 0 12 .2Z" />
    </svg>
@else
    <svg {{ $attributes->class([$class, 'shrink-0']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
        @switch($name)
            @case('heart')
                <path d="M20.8 8.6c0 5.1-8.8 11-8.8 11s-8.8-5.9-8.8-11A4.6 4.6 0 0 1 12 6a4.6 4.6 0 0 1 8.8 2.6Z" />
                @break
            @case('gift')
                <path d="M20 12v9H4v-9M2 7h20v5H2zM12 21V7" />
                <path d="M12 7H7.5a2.5 2.5 0 1 1 0-5C11 2 12 7 12 7ZM12 7h4.5a2.5 2.5 0 1 0 0-5C13 2 12 7 12 7Z" />
                @break
            @case('calculator')
                <rect x="4" y="2" width="16" height="20" rx="2" />
                <path d="M8 6h8M8 11h.01M12 11h.01M16 11h.01M8 15h.01M12 15h.01M16 15v3M8 18h.01M12 18h.01" />
                @break
            @case('map-pin')
                <path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z" />
                <circle cx="12" cy="10" r="3" />
                @break
            @case('users')
                <circle cx="9" cy="8" r="3" />
                <path d="M3 20v-1a6 6 0 0 1 12 0v1M16 5.2a3 3 0 0 1 0 5.6M18 14a5 5 0 0 1 3 4.6V20" />
                @break
            @case('arrow')
                <path d="M5 12h14M13 6l6 6-6 6" class="rtl:origin-center rtl:-scale-x-100" />
                @break
            @case('chevron-down')
                <path d="m6 9 6 6 6-6" />
                @break
            @case('chevron-start')
                <path d="m15 18-6-6 6-6" class="rtl:origin-center rtl:-scale-x-100" />
                @break
            @case('chevron-end')
                <path d="m9 18 6-6-6-6" class="rtl:origin-center rtl:-scale-x-100" />
                @break
            @case('menu')
                <path d="M4 6h16M4 12h16M4 18h16" />
                @break
            @case('close')
                <path d="M18 6 6 18M6 6l12 12" />
                @break
            @case('globe')
                <circle cx="12" cy="12" r="9" />
                <path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18" />
                @break
            @case('check')
                <path d="m5 12 5 5L20 7" />
                @break
            @case('shield')
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z" />
                <path d="m9 12 2 2 4-4" />
                @break
            @case('eye')
                <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z" />
                <circle cx="12" cy="12" r="3" />
                @break
            @case('share')
                <circle cx="18" cy="5" r="3" />
                <circle cx="6" cy="12" r="3" />
                <circle cx="18" cy="19" r="3" />
                <path d="m8.6 13.5 6.8 4M15.4 6.5l-6.8 4" />
                @break
            @case('calendar')
                <rect x="3" y="4" width="18" height="18" rx="2" />
                <path d="M16 2v4M8 2v4M3 10h18" />
                @break
            @case('clock')
                <circle cx="12" cy="12" r="9" />
                <path d="M12 7v5l3 2" />
                @break
            @case('repeat')
                <path d="m17 2 4 4-4 4" />
                <path d="M3 11v-1a4 4 0 0 1 4-4h14M7 22l-4-4 4-4" />
                <path d="M21 13v1a4 4 0 0 1-4 4H3" />
                @break
            @case('hand-heart')
                <path d="M11 14h2a2 2 0 1 0 0-4h-3c-.6 0-1.1.2-1.4.6L3 16" />
                <path d="m7 20 1.6-1.4c.3-.4.8-.6 1.4-.6h4c1.1 0 2.1-.4 2.8-1.2l4.6-4.4a2 2 0 0 0-2.8-2.8l-4.2 3.9M2 15l6 6" />
                <path d="M19.5 8.5c.7-.7 1.5-1.6 1.5-2.7A2.7 2.7 0 0 0 16 4a2.7 2.7 0 0 0-5 1.8c0 1.2.8 2 1.5 2.8L16 12Z" />
                @break
            @case('droplet')
                <path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5S12.5 5.5 12 2.5c-.5 3-2 5.4-4 7S5 13 5 15a7 7 0 0 0 7 7Z" />
                @break
            @case('sprout')
                <path d="M7 20h10M10 20c5.5-2.5.8-6.4 3-10" />
                <path d="M9.5 9.4c1.1.8 1.8 2.2 2.3 3.7-2 .4-3.5.4-4.8-.3-1.2-.6-2.3-1.9-3-4.2 2.8-.5 4.4 0 5.5.8ZM14.1 6a7 7 0 0 0-1.1 4c1.9-.1 3.3-.6 4.3-1.4 1-1 1.6-2.3 1.7-4.6-2.7.1-4 1-4.9 2Z" />
                @break
            @case('mail')
                <rect x="2" y="4" width="20" height="16" rx="2" />
                <path d="m22 7-10 6L2 7" />
                @break
            @case('phone')
                <path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2Z" />
                @break
            @case('bank')
                <path d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11M8 14v3M12 14v3M16 14v3" />
                @break
            @case('card')
                <rect x="2" y="5" width="20" height="14" rx="2" />
                <path d="M2 10h20M6 15h4" />
                @break
            @case('lock')
                <rect x="4" y="11" width="16" height="10" rx="2" />
                <path d="M8 11V7a4 4 0 0 1 8 0v4" />
                @break
            @case('printer')
                <path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                <path d="M6 14h12v8H6z" />
                @break
            @case('building')
                <rect x="4" y="2" width="16" height="20" rx="2" />
                <path d="M9 22v-4h6v4M8 6h.01M16 6h.01M12 6h.01M12 10h.01M12 14h.01M16 10h.01M16 14h.01M8 10h.01M8 14h.01" />
                @break
            @case('sparkle')
                <path d="M12 3v4M12 17v4M3 12h4M17 12h4M5.6 5.6l2.8 2.8M15.6 15.6l2.8 2.8M5.6 18.4l2.8-2.8M15.6 8.4l2.8-2.8" />
                @break
            @case('info')
                <circle cx="12" cy="12" r="9" />
                <path d="M12 16v-4M12 8h.01" />
                @break
            @case('grid')
                <rect x="3" y="3" width="7" height="7" rx="1" />
                <rect x="14" y="3" width="7" height="7" rx="1" />
                <rect x="14" y="14" width="7" height="7" rx="1" />
                <rect x="3" y="14" width="7" height="7" rx="1" />
                @break
        @endswitch
    </svg>
@endif
