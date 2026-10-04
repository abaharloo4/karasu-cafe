@props([
    'discount' => null,
    'text' => null,
    'variant' => 'discount',
    'class' => '',
])

@php
    $discountVal = is_numeric($discount) ? (int)$discount : null;
    $hasBadge = ($discountVal !== null && $discountVal > 0) || !empty($text);

    $labelText = $text;
    if (empty($labelText) && $discountVal !== null && $discountVal > 0) {
        $labelText = $discountVal . '% تخفیف';
    }

    $variants = [
        'discount' => 'bg-gradient-to-r from-[#FF7A18] to-[#FF903F] text-white shadow-xs',
        'accent' => 'bg-[#387780] text-white shadow-xs',
        'neutral' => 'bg-slate-800 text-white shadow-xs',
    ];

    $badgeStyle = $variants[$variant] ?? $variants['discount'];
@endphp

@if($hasBadge)
    <span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-black tracking-tight tabular-nums select-none {$badgeStyle} {$class}"]) }}>
        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
        </svg>
        <span>{{ $labelText }}</span>
    </span>
@endif
