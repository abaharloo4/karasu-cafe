@props([
    'size' => 'md', // sm, md, lg
    'class' => '',
])

@php
    $sizeClasses = match($size) {
        'sm' => 'w-9 h-9',
        'lg' => 'w-11 h-11',
        default => 'w-10 h-10',
    };

    $iconClasses = match($size) {
        'sm' => 'w-4.5 h-4.5',
        'lg' => 'w-5.5 h-5.5',
        default => 'w-5 h-5',
    };
@endphp

<!-- Luxury Animated Dark / Light Mode Switcher -->
<button type="button"
        @click="window.toggleDarkMode()"
        class="relative {{ $sizeClasses }} rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-amber-500 dark:text-cyan-300 flex items-center justify-center transition-all duration-300 btn-tactile border border-slate-200/80 dark:border-slate-700 shadow-2xs overflow-hidden group focus:outline-none focus-visible:ring-2 focus-visible:ring-[#387780] {{ $class }}"
        aria-label="تغییر تم شب و روز"
        title="تغییر تم شب و روز">

    <!-- Light Mode Indicator: Sun Icon (Shown when in Light Mode) -->
    <!-- In Light Mode: visible, scale-100, rotate-0. In Dark Mode: opacity-0, scale-0, rotate-90 -->
    <span class="absolute inset-0 flex items-center justify-center pointer-events-none transition-all duration-500 ease-spring transform opacity-100 scale-100 rotate-0 dark:opacity-0 dark:scale-0 dark:rotate-90">
        <svg class="{{ $iconClasses }} text-amber-500 group-hover:rotate-45 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="5"></circle>
            <line x1="12" y1="1" x2="12" y2="3"></line>
            <line x1="12" y1="21" x2="12" y2="23"></line>
            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
            <line x1="1" y1="12" x2="3" y2="12"></line>
            <line x1="21" y1="12" x2="23" y2="12"></line>
            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
        </svg>
    </span>

    <!-- Dark Mode Indicator: Moon Icon (Shown when in Dark Mode) -->
    <!-- In Light Mode: opacity-0, scale-0, -rotate-90. In Dark Mode: visible, scale-100, rotate-0 -->
    <span class="absolute inset-0 flex items-center justify-center pointer-events-none transition-all duration-500 ease-spring transform opacity-0 scale-0 -rotate-90 dark:opacity-100 dark:scale-100 dark:rotate-0">
        <svg class="{{ $iconClasses }} text-cyan-300 drop-shadow-[0_0_8px_rgba(103,232,249,0.5)] group-hover:-rotate-12 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
        </svg>
    </span>
</button>
