@props(['category', 'size' => 'default'])

@php
    $icon = $category->line_icon;
    $classes = $size === 'small' ? 'category-line-icon category-line-icon-sm' : 'category-line-icon';
@endphp

@if ($icon)
    <svg class="{{ $classes }}" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        @switch($icon)
            @case('thermometer')
                <path d="M25 9a5 5 0 0 0-10 0v19a9 9 0 1 0 10 0V9Z"/><path d="M20 16v17"/><circle cx="20" cy="35" r="3" fill="currentColor" stroke="none"/>
                @break
            @case('capsule')
                <g transform="rotate(-45 24 24)"><rect x="10" y="17" width="28" height="14" rx="7"/><path d="M24 17v14"/></g>
                @break
            @case('vitamins')
                <circle cx="18" cy="20" r="7"/><path d="M27 29c0-5 4-9 9-9s8 4 8 9-4 9-9 9-8-4-8-9Z"/><path d="m30 24 10 10"/>
                @break
            @case('stomach')
                <path d="M22 9v10c0 4 3 7 7 7h2c6 0 9 4 9 9 0 4-3 7-7 7-7 0-12-5-12-12v-5c-7 0-12 4-12 10 0 3 2 6 5 7"/>
                @break
            @case('cough')
                <path d="M20 12a10 10 0 0 0-9 10v8c0 6 5 10 11 10h8"/><path d="M20 12c0 6 4 9 9 9h4"/><path d="m33 15 5-3M36 21h6M34 27l5 3"/>
                @break
            @case('skin')
                <path d="M17 14h14l-2 25H19l-2-25Z"/><path d="M19 14v-4h10v4M24 22v9"/><path d="m35 13 1 2 2 1-2 1-1 2-1-2-2-1 2-1 1-2Z"/>
                @break
            @case('heart')
                <path d="M24 39S8 30 8 19c0-5 4-8 9-8 4 0 6 2 7 5 1-3 4-5 7-5 5 0 9 3 9 8 0 11-16 20-16 20Z"/><path d="M10 24h8l3-6 5 12 3-6h9"/>
                @break
            @case('diabetes')
                <path d="M24 7s11 12 11 21a11 11 0 1 1-22 0C13 19 24 7 24 7Z"/><path d="M20 30c1 2 3 3 5 3"/>
                @break
            @case('baby')
                <circle cx="24" cy="25" r="13"/><path d="M20 12c1-4 6-5 8-1"/><circle cx="19" cy="24" r="1" fill="currentColor" stroke="none"/><circle cx="29" cy="24" r="1" fill="currentColor" stroke="none"/><path d="M19 30c3 3 7 3 10 0"/>
                @break
            @case('tooth')
                <path d="M14 10c5-3 10 0 10 0s5-3 10 0c7 4 2 15 0 19-2 5-3 10-6 10-4 0-2-9-5-9s-2 9-6 9c-3 0-4-5-6-10-2-4-7-15 0-19Z"/>
                @break
            @default
                <g transform="rotate(-45 24 24)"><rect x="10" y="17" width="28" height="14" rx="7"/><path d="M24 17v14"/></g>
        @endswitch
    </svg>
@elseif ($category->icon_class)
    <i class="{{ $category->icon_class }}" aria-hidden="true"></i>
@endif
