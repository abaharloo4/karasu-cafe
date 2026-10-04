@props([
    'show' => 'false',
    'text' => 'در حال پردازش اطلاعات...',
    'global' => false,
])

@if($global)
<div x-data="{ loading: false, loadingText: 'در حال پردازش...' }"
     x-on:show-preloader.window="loading = true; if($event.detail?.text) loadingText = $event.detail.text"
     x-on:hide-preloader.window="loading = false"
     x-show="loading"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-[#19242B]/60 backdrop-blur-sm select-none"
     style="display: none;"
     role="status"
     aria-live="polite">
    
    <div class="bg-white/95 border border-slate-200/80 rounded-3xl p-6 sm:p-8 flex flex-col items-center gap-4 shadow-2xl max-w-xs mx-4 text-center">
        <!-- Animated B-Leaf Logo -->
        <div class="relative w-20 h-20 flex items-center justify-center">
            <span class="absolute inset-0 rounded-full bg-[#387780]/20 animate-ping opacity-75"></span>
            <span class="absolute -inset-2 rounded-full border-2 border-dashed border-[#387780]/40 animate-spin" style="animation-duration: 6s;"></span>
            <div class="w-16 h-16 rounded-2xl bg-[#F8FAFC] border border-[#387780]/30 shadow-inner flex items-center justify-center relative z-10 p-2 transform transition-transform animate-pulse">
                <img src="{{ asset('images/logo.svg') }}" alt="بی کافه" class="w-full h-full object-contain">
            </div>
        </div>

        <div>
            <h4 class="font-bold text-slate-800 text-sm sm:text-base" x-text="loadingText">در حال پردازش...</h4>
            <p class="text-xs text-slate-500 mt-1">لطفاً چند لحظه شکیبا باشید</p>
        </div>
    </div>
</div>
@else
<div x-show="{{ $show }}"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     {{ $attributes->merge(['class' => 'fixed inset-0 z-50 flex flex-col items-center justify-center bg-[#19242B]/60 backdrop-blur-sm select-none']) }}
     style="display: none;"
     role="status"
     aria-live="polite">
    
    <div class="bg-white/95 border border-slate-200/80 rounded-3xl p-6 sm:p-8 flex flex-col items-center gap-4 shadow-2xl max-w-xs mx-4 text-center">
        <!-- Animated B-Leaf Logo -->
        <div class="relative w-20 h-20 flex items-center justify-center">
            <span class="absolute inset-0 rounded-full bg-[#387780]/20 animate-ping opacity-75"></span>
            <span class="absolute -inset-2 rounded-full border-2 border-dashed border-[#387780]/40 animate-spin" style="animation-duration: 6s;"></span>
            <div class="w-16 h-16 rounded-2xl bg-[#F8FAFC] border border-[#387780]/30 shadow-inner flex items-center justify-center relative z-10 p-2 transform transition-transform animate-pulse">
                <img src="{{ asset('images/logo.svg') }}" alt="بی کافه" class="w-full h-full object-contain">
            </div>
        </div>

        <div>
            <h4 class="font-bold text-slate-800 text-sm sm:text-base">{{ $text }}</h4>
            <p class="text-xs text-slate-500 mt-1">لطفاً چند لحظه شکیبا باشید</p>
        </div>
    </div>
</div>
@endif
