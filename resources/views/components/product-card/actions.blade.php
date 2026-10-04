@props([
    'productId' => null,
    'stock' => 99,
    'isOutOfStock' => false,
    'addToCartText' => 'سفارش',
    'modalData' => null,
    'class' => '',
])

<div {{ $attributes->merge(['class' => "w-full select-none {$class}"]) }}>
    @if($isOutOfStock)
        <button type="button"
                disabled
                aria-disabled="true"
                class="w-full py-2 px-3 rounded-xl bg-slate-100 text-slate-400 font-bold text-xs border border-slate-200 cursor-not-allowed flex items-center justify-center gap-1.5 min-h-[36px]">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
            </svg>
            <span>ناموجود</span>
        </button>
    @else
        <!-- State 2: Quantity Controls when in cart -->
        <template x-if="currentQty > 0">
            <x-product-card.quantity :product-id="$productId" :stock="$stock" />
        </template>

        <!-- State 1: Order / Add to Cart CTA -->
        <template x-if="currentQty === 0">
            <button type="button"
                    @click.stop="handleActionClick({{ json_encode($modalData) }})"
                    class="w-full py-2 px-3 rounded-xl font-black text-xs bg-[#3C6E71] hover:bg-[#2E585B] text-white flex items-center justify-center gap-1.5 btn-tactile shadow-xs min-h-[36px] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#3C6E71]">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>{{ $addToCartText }}</span>
            </button>
        </template>
    @endif
</div>
