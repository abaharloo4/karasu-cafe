@props([
    'product' => null,
    'category' => null,
    'class' => 'w-6 h-6',
])

@php
    $catName = '';
    $prodName = '';
    $prodType = '';

    if ($product) {
        $prodName = $product->name ?? '';
        $prodType = $product->type ?? '';
        $catName = $product->category?->name ?? '';
    }

    if ($category) {
        $catName = is_string($category) ? $category : ($category->name ?? '');
    }

    // Determine category type
    // 1. Hot drinks: قهوه و نوشیدنی‌های گرم, اسپرسو, آمریکانو, لاته, کاپوچینو, هات چاکلت, چای, دمنوش
    // 2. Cold drinks: نوشیدنی‌های سرد و شیک, آیس, موهیتو, شیک, فراپه, اسموتی, شربت
    // 3. Cakes / desserts: کیک و دسر, چیزکیک, کوکی, کروسان, وافل, براونی
    // 4. Specialty beans & gear: قهوه تخصصی و اقلام همراه, دانه, دریپر, v60, کمکس, آئروپرس, آسیاب

    $isCold = str_contains($catName, 'سرد') || str_contains($catName, 'شیک') || str_contains($prodName, 'آیس') || str_contains($prodName, 'شیک') || str_contains($prodName, 'اسموتی') || str_contains($prodName, 'موهیتو') || str_contains($prodName, 'فراپه');
    $isCake = str_contains($catName, 'کیک') || str_contains($catName, 'دسر') || str_contains($prodName, 'کیک') || str_contains($prodName, 'چیزکیک') || str_contains($prodName, 'کوکی') || str_contains($prodName, 'کروسان') || str_contains($prodName, 'وافل') || str_contains($prodName, 'براونی');
    $isSpecialty = str_contains($catName, 'تخصصی') || str_contains($catName, 'دانه') || str_contains($prodName, 'دانه') || str_contains($prodName, 'V60') || str_contains($prodName, 'کمکس') || str_contains($prodName, 'آئروپرس');
@endphp

@if($isSpecialty)
    {{-- Specialty Coffee Beans & Gear (Pour-over kettle / V60 / Bean) --}}
    <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 100-6 3 3 0 000 6z" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 4h18l-2 4H5L3 4z" />
    </svg>
@elseif($isCake)
    {{-- Cake & Bakery (Piece of Cake / Pastry) --}}
    <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.27 6.96L12 12.01l8.73-5.05M12 22.08V12" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v3m0 0l-1.5-1M12 6l1.5-1" />
    </svg>
@elseif($isCold)
    {{-- Cold Drinks & Shakes (Tall glass with straw / ice) --}}
    <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 3h12l-1.5 16.5a2 2 0 01-2 1.5h-5a2 2 0 01-2-1.5L6 3z" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 7h14M15 2l2-1" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 11a1 1 0 100-2 1 1 0 000 2zm4 4a1 1 0 100-2 1 1 0 000 2z" />
    </svg>
@else
    {{-- Hot Coffee & Espresso (Hot Cup with Steam) --}}
    <svg class="{{ $class }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 1v3M10 1v3M14 1v3" />
    </svg>
@endif
