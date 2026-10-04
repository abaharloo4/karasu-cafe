@props([
    'images' => [],
    'name' => '',
    'maxVisible' => 4,
    'class' => '',
])

@php
    $imagesList = is_array($images) ? array_values(array_filter($images)) : [];
    $totalCount = count($imagesList);
    $visibleCount = min($totalCount, $maxVisible);
    $overflowCount = max(0, $totalCount - $maxVisible);
@endphp

@if($totalCount > 1)
    <div {{ $attributes->merge(['class' => "flex items-center gap-1.5 pt-2 select-none {$class}"]) }} role="tablist" aria-label="گالری تصاویر {{ $name }}">
        @foreach(array_slice($imagesList, 0, $visibleCount) as $idx => $img)
            @php
                $isLastAndOverflowing = ($idx === $visibleCount - 1) && ($overflowCount > 0);
            @endphp
            <button type="button"
                    @click.stop="setActiveImage('{{ $img }}')"
                    :class="activeImage === '{{ $img }}' ? 'border-[#FF7A18] dark:border-[#FF7A18] ring-2 ring-[#FF7A18]/30 scale-105' : 'border-slate-200 dark:border-slate-700/80 hover:border-slate-300 dark:hover:border-slate-600 opacity-75 hover:opacity-100'"
                    class="relative w-8 h-8 sm:w-9 sm:h-9 rounded-lg overflow-hidden border-2 bg-white dark:bg-slate-900 flex items-center justify-center btn-tactile transition-all flex-shrink-0 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#387780]"
                    aria-label="نمایش تصویر شماره {{ $idx + 1 }} برای {{ $name }}">
                <img src="{{ $img }}"
                     alt="{{ $name }} - تصویر {{ $idx + 1 }}"
                     loading="lazy"
                     class="w-full h-full object-contain pointer-events-none">

                @if($isLastAndOverflowing)
                    <span class="absolute inset-0 bg-slate-900/80 backdrop-blur-[1px] text-white text-[10px] sm:text-xs font-black flex items-center justify-center tabular-nums">
                        +{{ $overflowCount }}
                    </span>
                @endif
            </button>
        @endforeach
    </div>
@endif
