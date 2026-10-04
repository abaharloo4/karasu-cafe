@props([
    'product' => null,
    'variant' => 'default',
    'showGallery' => true,
    'showFavorite' => true,
    'showDiscount' => true,
    'showCategory' => true,
    'showQuantity' => true,
    'showAddToCart' => true,
    'compact' => false,
    'href' => null,
    'addToCartText' => 'افزودن به سبد خرید',
    'currency' => 'تومان',
    'categoryName' => null,
    'isFavorite' => null,
])

<x-product-card.index :product="$product"
                      :variant="$variant"
                      :show-gallery="$showGallery"
                      :show-favorite="$showFavorite"
                      :show-discount="$showDiscount"
                      :show-category="$showCategory"
                      :show-quantity="$showQuantity"
                      :show-add-to-cart="$showAddToCart"
                      :compact="$compact"
                      :href="$href"
                      :add-to-cart-text="$addToCartText"
                      :currency="$currency"
                      :category-name="$categoryName"
                      :is-favorite="$isFavorite"
                      {{ $attributes }}>
    {{ $slot }}
</x-product-card.index>
