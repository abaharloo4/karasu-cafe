@props([
    'price' => 0,
    'originalPrice' => null,
    'currency' => 'تومان',
    'class' => '',
])

@php
    $curPrice = is_numeric($price) ? (float)$price : 0;
    $origPrice = is_numeric($originalPrice) ? (float)$originalPrice : null;
    $hasDiscount = $origPrice !== null && $origPrice > $curPrice;
@endphp

<div {{ $attributes->merge(['class' => "flex items-baseline flex-wrap gap-x-2 gap-y-0.5 select-none {$class}"]) }}>
    <span class="text-base sm:text-lg font-black text-[#19242B] dark:text-white tabular-nums tracking-tight">
        {{ number_format($curPrice) }}
    </span>

    @if($hasDiscount)
        <span class="text-xs sm:text-sm line-through text-slate-400 dark:text-slate-500 tabular-nums font-medium">
            {{ number_format($origPrice) }}
        </span>
    @endif

    <span class="text-[11px] sm:text-xs font-bold text-slate-500 dark:text-slate-400">
        {{ $currency }}
    </span>
</div>
