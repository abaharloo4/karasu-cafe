@props([
    'productId' => null,
    'stock' => 99,
    'class' => '',
])

<div {{ $attributes->merge([
    'class' => "flex items-center justify-between w-full p-0.5 sm:p-1 rounded-xl bg-[#F7F9FA] shadow-2xs select-none {$class}"
]) }} @click.stop>
    <!-- Decrement Button -->
    <button type="button"
            @click.stop="handleDecrement()"
            aria-label="کاهش تعداد"
            class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-white hover:bg-rose-50 text-rose-600 font-black text-xs sm:text-sm flex items-center justify-center btn-tactile shadow-2xs min-h-[28px] min-w-[28px] focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500">
        <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/>
        </svg>
    </button>

    <!-- Quantity Indicator -->
    <span class="flex-1 text-center font-black text-xs sm:text-sm text-[#353535] tabular-nums px-1"
          x-text="currentQty">
        1
    </span>

    <!-- Increment Button -->
    <button type="button"
            @click.stop="handleIncrement()"
            :disabled="currentQty >= stock"
            aria-label="افزایش تعداد"
            class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-[#353535] hover:bg-[#1E2912] text-white font-black text-xs sm:text-sm flex items-center justify-center btn-tactile shadow-2xs min-h-[28px] min-w-[28px] disabled:opacity-40 disabled:cursor-not-allowed focus:outline-none focus-visible:ring-2 focus-visible:ring-[#3C6E71]">
        <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
    </button>
</div>
