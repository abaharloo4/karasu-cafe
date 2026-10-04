@props([
    'name' => 'modalOpen',
    'title' => '',
    'maxWidth' => 'md',
])

@php
    $widths = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
    ];
    $widthClass = $widths[$maxWidth] ?? 'max-w-md';
@endphp

<div x-show="{{ $name }}"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @keydown.escape.window="{{ $name }} = false"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs text-right"
     dir="rtl"
     style="display: none;">

    <div @click.outside="{{ $name }} = false"
         x-show="{{ $name }}"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
         class="w-full {{ $widthClass }} bg-white rounded-3xl shadow-2xl border border-[#353535]/15 overflow-hidden flex flex-col">

        <!-- Modal Header -->
        <div class="px-6 py-4.5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm sm:text-base font-black text-[#353535] flex items-center gap-2">
                {{ $title }}
            </h3>
            <button type="button"
                    @click="{{ $name }} = false"
                    class="w-8 h-8 rounded-xl flex items-center justify-center hover:bg-slate-100 text-slate-400 hover:text-slate-700 transition-colors btn-tactile">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto custom-scrollbar">
            {{ $slot }}
        </div>

        @isset($footer)
            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-slate-50/60 border-t border-slate-100 flex items-center justify-end gap-2.5">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>
