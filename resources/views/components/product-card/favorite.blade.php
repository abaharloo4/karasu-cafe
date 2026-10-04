@props([
    'productId' => null,
    'name' => '',
    'isFavorite' => false,
    'class' => '',
])

<button type="button"
        @click.stop="toggleFavorite()"
        :aria-pressed="isFavorite ? 'true' : 'false'"
        aria-label="افزودن {{ $name ?: 'محصول' }} به علاقه‌مندی‌ها"
        {{ $attributes->merge([
            'class' => "w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white/95 backdrop-blur-xs border border-[#353535]/12 shadow-2xs flex items-center justify-center btn-tactile hover:scale-105 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#3C6E71] transition-all {$class}"
        ]) }}>
    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 transition-transform duration-200"
         :class="isFavorite ? 'fill-[#FF7A18] stroke-[#FF7A18] text-[#FF7A18] scale-110' : 'fill-transparent stroke-slate-400 text-slate-400 hover:stroke-[#FF7A18]'"
         viewBox="0 0 24 24"
         stroke-width="2"
         stroke-linecap="round"
         stroke-linejoin="round"
         aria-hidden="true">
        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
    </svg>
</button>
