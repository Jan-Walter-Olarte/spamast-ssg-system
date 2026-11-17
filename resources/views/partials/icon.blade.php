@php
    // Small line icons. They are included with a name and an optional size.
    $icons = [
        'home'       => '<path d="M3 11.5 12 4l9 7.5"/><path d="M5.5 10v10h13V10"/><path d="M10 20v-5.5h4V20"/>',
        'user'       => '<circle cx="12" cy="8" r="4"/><path d="M4.5 20.5c0-4 3.4-6.5 7.5-6.5s7.5 2.5 7.5 6.5"/>',
        'users'      => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.6 2.9-5.8 6.5-5.8s6.5 2.2 6.5 5.8"/><circle cx="17.5" cy="9" r="2.5"/><path d="M17.5 14.5c2.8 0 4.5 1.6 4.5 4.5"/>',
        'user-check' => '<circle cx="9.5" cy="8" r="3.8"/><path d="M2.5 20c0-3.8 3.1-6 7-6s7 2.2 7 6"/><path d="m16.5 9.5 2 2 3.5-3.5"/>',
        'user-plus'  => '<circle cx="9.5" cy="8" r="3.8"/><path d="M2.5 20c0-3.8 3.1-6 7-6s7 2.2 7 6"/><path d="M19 7v6M16 10h6"/>',
        'building'   => '<rect x="4.5" y="3.5" width="15" height="17" rx="1.5"/><path d="M9 8h1.5M13.5 8H15M9 12h1.5M13.5 12H15M10 20.5v-4h4v4"/>',
        'book'       => '<path d="M5 4.5h9.5A3.5 3.5 0 0 1 18 8v12H8.5A3.5 3.5 0 0 1 5 16.5v-12z"/><path d="M5 16.5A3.5 3.5 0 0 1 8.5 13H18"/>',
        'shield'     => '<path d="M12 3 4.5 6v5.5c0 4.6 3.2 7.6 7.5 9 4.3-1.4 7.5-4.4 7.5-9V6L12 3z"/><path d="m9 12 2 2 4-4"/>',
        'sliders'    => '<path d="M4 7h9M17 7h3M4 12h5M13 12h7M4 17h3M11 17h9"/><circle cx="15" cy="7" r="2"/><circle cx="11" cy="12" r="2"/><circle cx="9" cy="17" r="2"/>',
        'graduation' => '<path d="M2.5 9 12 4.5 21.5 9 12 13.5 2.5 9z"/><path d="M6.5 11.2v4.3c0 1.6 2.5 3 5.5 3s5.5-1.4 5.5-3v-4.3"/><path d="M21.5 9v5"/>',
        'flag'       => '<path d="M5.5 21V4"/><path d="M5.5 4.5h11l-2.2 3.8 2.2 3.7H5.5"/>',
        'calendar'   => '<rect x="3.5" y="5" width="17" height="15.5" rx="2"/><path d="M3.5 10h17M8 3v4M16 3v4"/>',
        'megaphone'  => '<path d="M3.5 10.5v3a1 1 0 0 0 1 1h3l8 4.5V5L7.5 9.5h-3a1 1 0 0 0-1 1z"/><path d="M19 9.5a4 4 0 0 1 0 5"/>',
        'chart'      => '<path d="M4.5 20V11M10 20V4.5M15.5 20v-7.5M21 20H3"/>',
        'clipboard'  => '<rect x="5.5" y="4" width="13" height="17" rx="2"/><path d="M9 4h6v3H9z"/><path d="m9 14.5 2 2 4-4"/>',
        'coin'       => '<circle cx="12" cy="12" r="9"/><path d="M12 7v10M9.5 9.8c0-1.1 1.1-1.8 2.5-1.8s2.5.7 2.5 1.8-1.1 1.7-2.5 1.7-2.5.7-2.5 1.8 1.1 1.8 2.5 1.8 2.5-.7 2.5-1.8"/>',
        'bell'       => '<path d="M6 16.5V11a6 6 0 0 1 12 0v5.5l1.5 2h-15L6 16.5z"/><path d="M10 21h4"/>',
        'help'       => '<circle cx="12" cy="12" r="9"/><path d="M9.5 9.5a2.6 2.6 0 0 1 5 .9c0 1.7-2.5 2.1-2.5 3.6"/><path d="M12 17.2v.1"/>',
        'menu'       => '<path d="M4 6.5h16M4 12h16M4 17.5h16"/>',
        'dot'        => '<circle cx="12" cy="12" r="3"/>',
    ];

    $size = $size ?? 20;
@endphp
<svg class="icon" viewBox="0 0 24 24" width="{{ $size }}" height="{{ $size }}" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">{!! $icons[$name] ?? $icons['dot'] !!}</svg>

