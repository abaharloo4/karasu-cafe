@props([
    'image' => null,
    'name' => '',
    'aspectRatio' => 'aspect-[4/3]',
    'product' => null,
    'lazy' => true,
    'class' => '',
])

<div {{ $attributes->merge([
    'class' => "relative w-full {$aspectRatio} rounded-xl bg-[#F7F9FA] dark:bg-[#1E293B] flex items-center justify-center overflow-hidden select-none transition-colors {$class}"
]) }}>
    @if(!empty($image))
        <img src="{{ $image }}"
             :src="activeImage || '{{ $image }}'"
             alt="{{ $name }}"
             x-show="!hasImageError"
             x-on:error="hasImageError = true"
             {{ $lazy ? 'loading="lazy"' : '' }}
             class="w-full h-full object-cover sm:object-contain object-center transition-transform duration-300 ease-out group-hover:scale-105 pointer-events-none drop-shadow-xs">

        <div x-show="hasImageError"
             x-cloak
             class="w-full h-full p-4 flex flex-col items-center justify-center text-[#3C6E71] dark:text-[#52a6b2] transition-transform duration-300 group-hover:scale-105">
            @if($product)
                <x-product-icon :product="$product" class="w-10 h-10 sm:w-12 sm:h-12 stroke-current" />
            @else
                <svg class="w-10 h-10 sm:w-12 sm:h-12 stroke-current opacity-70" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            @endif
        </div>
    @else
        <div class="w-full h-full p-4 flex flex-col items-center justify-center text-[#3C6E71] dark:text-[#52a6b2] transition-transform duration-300 group-hover:scale-105">
            @if($product)
                <x-product-icon :product="$product" class="w-10 h-10 sm:w-12 sm:h-12 stroke-current" />
            @else
                <svg class="w-10 h-10 sm:w-12 sm:h-12 stroke-current opacity-70" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            @endif
        </div>
    @endif
</div>
