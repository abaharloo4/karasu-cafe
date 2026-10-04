@props([
    'title' => '',
    'href' => null,
    'class' => '',
])

<h3 {{ $attributes->merge([
    'class' => "text-sm sm:text-base font-black text-[#19242B] dark:text-white group-hover:text-[#387780] dark:group-hover:text-[#52a6b2] transition-colors leading-snug line-clamp-1 {$class}"
]) }}>
    @if($href)
        <a href="{{ $href }}" class="focus:outline-none focus:underline" title="{{ $title }}">
            {{ $title }}
        </a>
    @else
        <span title="{{ $title }}">{{ $title }}</span>
    @endif
</h3>
