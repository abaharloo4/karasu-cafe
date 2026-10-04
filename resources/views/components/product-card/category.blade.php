@props([
    'category' => null,
    'class' => '',
])

@if(!empty($category))
    <span {{ $attributes->merge([
        'class' => "text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-400 truncate block {$class}"
    ]) }}>
        {{ $category }}
    </span>
@endif
