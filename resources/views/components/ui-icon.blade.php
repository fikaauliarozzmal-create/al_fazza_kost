@props(['name', 'size' => 20])

<svg {{ $attributes->merge(['class' => 'af-ui-icon']) }} width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
    @switch($name)
        @case('dashboard')
            <path d="m3 10 9-7 9 7v10H3Z"/><path d="M9 20v-6h6v6"/><path d="m18.5 3 .4 1.1L20 4.5l-1.1.4-.4 1.1-.4-1.1-1.1-.4 1.1-.4Z"/>
            @break
        @case('bed')
            <path d="M3 19V9a2 2 0 0 1 2-2h5a3 3 0 0 1 3 3v4"/><path d="M3 15h18v4M7 19v2m10-2v2M5 15v-3h5a2 2 0 0 1 2 2"/><path d="M19 8v4"/>
            @break
        @case('booking')
            <rect x="3" y="4" width="18" height="17" rx="3"/><path d="M8 2v4m8-4v4M3 9h18m-11 6 1.5 1.5L15 13"/>
            @break
        @case('home-shield')
    <path d="m3 10 9-7 9 7v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z"/>
    <path d="M9 21v-6h6v6"/>
    <path d="m12 9 .9 1.8 2 .3-1.45 1.4.35 2-1.8-.95-1.8.95.35-2L9.1 11.1l2-.3Z"/>
    @break

@case('map-pin')
    <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/>
    <circle cx="12" cy="10" r="2.5"/>
    @break

@case('building')
    <path d="M4 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16"/>
    <path d="M16 9h3a1 1 0 0 1 1 1v11"/>
    <path d="M2 21h20"/>
    <path d="M8 7h4M8 11h4M8 15h4M8 19h4"/>
    @break
        @case('wallet')
            <path d="M4 7.5V6a2 2 0 0 1 2-2h11l3 3v11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2"/><path d="M4 8h16v5h-5a2.5 2.5 0 0 0 0 5h5"/><circle cx="15" cy="15.5" r=".7"/>
            @break
        @case('receipt')
            <path d="M6 3h12v18l-3-2-3 2-3-2-3 2Z"/><path d="M9 8h6M9 12h6M9 16h3"/>
            @break
        @case('resident')
            <circle cx="10" cy="8" r="3"/><path d="M4 20c.5-4 2.5-6 6-6s5.5 2 6 6"/><path d="m16 13 4-3 3 2v8h-5"/>
            @break
        @case('service')
            <path d="M5 5h14a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-8l-4 3v-3H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z"/><path d="M9 11h.01M12 11h.01M15 11h.01"/><path d="m17.5 3 .3.8.8.3-.8.3-.3.8-.3-.8-.8-.3.8-.3Z"/>
            @break
        @case('settings')
            <path d="M4 6h16M4 12h16M4 18h16"/><circle cx="9" cy="6" r="2"/><circle cx="15" cy="12" r="2"/><circle cx="11" cy="18" r="2"/>
            @break
        @case('report')
            <path d="M5 3h10l4 4v14H5Z"/><path d="M15 3v5h5M9 17v-3m3 3v-6m3 6v-2"/>
            @break
        @case('bell')
            <path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/><path d="m19 4 .35.95.95.35-.95.35-.35.95-.35-.95-.95-.35.95-.35Z"/>
            @break
        @case('profile')
            <circle cx="12" cy="8" r="4"/><path d="M4 21c.7-4.3 3.3-6.5 8-6.5s7.3 2.2 8 6.5"/>
            @break
        @case('globe')
            <circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.2 2.4 3.3 5.4 3.3 9S14.2 18.6 12 21c-2.2-2.4-3.3-5.4-3.3-9S9.8 5.4 12 3"/>
            @break
        @case('logout')
            <path d="M10 4H5v16h5M14 8l4 4-4 4m4-4H9"/>
            @break
        @case('mail')
            <rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>
            @break
        @default
            <circle cx="12" cy="12" r="8"/>
    @endswitch
</svg>
