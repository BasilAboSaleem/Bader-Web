@props(['route', 'class' => 'h-4 w-4'])

<svg {{ $attributes->class([$class, 'shrink-0']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
    @switch($route)
        @case('home')
            <path d="m3 10 9-7 9 7" />
            <path d="M5 9v12h14V9M9 21v-7h6v7" />
            @break
        @case('about')
            <circle cx="9" cy="8" r="3" />
            <path d="M3 20v-1a6 6 0 0 1 12 0v1M16 5.2a3 3 0 0 1 0 5.6M18 14a5 5 0 0 1 3 4.6V20" />
            @break
        @case('programs')
            <path d="M12 7v14M3 18V5a2 2 0 0 1 2-2h5a2 2 0 0 1 2 2v16M12 7a2 2 0 0 1 2-2h5a2 2 0 0 1 2 2v13h-7a2 2 0 0 0-2 1" />
            @break
        @case('campaigns')
            <path d="m3 11 18-5-5 15-3-7-7-3Z" />
            <path d="M13 14 9 21l-2-1 2-7M3 11v4" />
            @break
        @case('impact')
            <path d="M4 19V5M4 19h17" />
            <path d="M8 15v-4M13 15V7M18 15V4" />
            @break
        @case('news')
            <path d="M4 4h16v16H4zM8 8h8M8 12h8M8 16h4" />
            <path d="M2 7v12a1 1 0 0 0 1 1" />
            @break
        @case('sponsorship')
        @case('donate')
            <path d="M20.8 8.6c0 5.1-8.8 11-8.8 11s-8.8-5.9-8.8-11A4.6 4.6 0 0 1 12 6a4.6 4.6 0 0 1 8.8 2.6Z" />
            @break
        @case('partners')
            <path d="m8 12 2.5 2.5a2 2 0 0 0 2.8 0l3.2-3.2M7 8l2-2h5l3 3M3 10l4-4 3 3-4 4-3-3ZM21 10l-4-4-3 3 4 4 3-3Z" />
            @break
        @case('volunteer')
            <path d="M12 21s-8-4.7-8-11a4.5 4.5 0 0 1 8-2.8A4.5 4.5 0 0 1 20 10c0 6.3-8 11-8 11Z" />
            <path d="M8 12h2l1-2 2 4 1-2h2" />
            @break
        @case('faq')
            <circle cx="12" cy="12" r="9" />
            <path d="M9.6 9a2.5 2.5 0 1 1 4.2 1.8c-1 .9-1.8 1.2-1.8 2.7M12 17h.01" />
            @break
        @case('contact')
            <path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8A8.5 8.5 0 0 1 8.7 3.9a8.4 8.4 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8v.5Z" />
            @break
        @case('more')
            <circle cx="5" cy="12" r="1" />
            <circle cx="12" cy="12" r="1" />
            <circle cx="19" cy="12" r="1" />
            @break
    @endswitch
</svg>
