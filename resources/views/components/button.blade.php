@props([
    'as' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
    'disabled' => false,
])

@php
    $baseClasses = 'inline-flex items-center justify-center gap-2 font-bold rounded-xl transition-all select-none btn-tactile focus:outline-none focus:ring-2 focus:ring-offset-1 disabled:opacity-50 disabled:cursor-not-allowed disabled:pointer-events-none';

    $variants = [
        'primary' => 'bg-[#284B63] hover:bg-[#1E374A] text-white shadow-xs focus:ring-[#284B63]/30',
        'secondary' => 'bg-[#3C6E71] hover:bg-[#2E5B5E] text-white shadow-xs focus:ring-[#3C6E71]/30',
        'gold' => 'bg-[#C8A35A] hover:bg-[#B38F48] text-white shadow-xs focus:ring-[#C8A35A]/30',
        'danger' => 'bg-rose-600 hover:bg-rose-700 text-white shadow-xs focus:ring-rose-500/30',
        'outline' => 'bg-white hover:bg-slate-50 text-[#353535] border border-[#353535]/20 hover:border-[#3C6E71] focus:ring-[#3C6E71]/20',
        'ghost' => 'bg-transparent hover:bg-slate-100 text-[#353535] focus:ring-slate-300',
    ];

    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs min-h-[38px]',
        'md' => 'px-4 py-2.5 text-xs min-h-[44px]',
        'lg' => 'px-6 py-3.5 text-sm min-h-[48px]',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

@if($as === 'a' || $href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
