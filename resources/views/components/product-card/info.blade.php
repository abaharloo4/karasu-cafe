@props([
    'category' => null,
    'title' => '',
    'href' => null,
    'price' => 0,
    'originalPrice' => null,
    'currency' => 'تومان',
    'showCategory' => true,
    'class' => '',
])

<div {{ $attributes->merge(['class' => "space-y-2 {$class}"]) }}>
    <div class="space-y-0.5">
        @if($showCategory && !empty($category))
            <x-product-card.category :category="$category" />
        @endif

        <x-product-card.title :title="$title" :href="$href" />
    </div>

    <x-product-card.pricing :price="$price" :original-price="$originalPrice" :currency="$currency" />
</div>
