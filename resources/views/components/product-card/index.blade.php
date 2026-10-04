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
    'addToCartText' => 'سفارش',
    'currency' => 'تومان',
    'categoryName' => null,
    'isFavorite' => null,
])

@php
    if (!$product) {
        return;
    }

    $isArr = is_array($product);

    // Normalize ID
    $id = $isArr ? ($product['id'] ?? null) : ($product->id ?? null);

    // Normalize Name / Title
    $name = $isArr ? ($product['name'] ?? $product['title'] ?? '') : ($product->name ?? $product->title ?? '');

    // Normalize Category Name
    if (empty($categoryName)) {
        if ($isArr) {
            $categoryName = $product['category_name'] ?? $product['category'] ?? null;
        } else {
            if (isset($product->category) && is_object($product->category)) {
                $categoryName = $product->category->name ?? null;
            } elseif (isset($product->category) && is_string($product->category)) {
                $categoryName = $product->category;
            } elseif (isset($product->category_name)) {
                $categoryName = $product->category_name;
            }
        }
    }

    // Normalize Price & Discounts
    $price = 0;
    $originalPrice = null;
    $discountPercent = null;

    if ($isArr) {
        $price = isset($product['price']) ? (float)$product['price'] : 0;
        $originalPrice = isset($product['original_price']) ? (float)$product['original_price'] : null;
        $discountPercent = $product['discount_percentage'] ?? $product['discount_percent'] ?? null;
    } else {
        // Eloquent Model support
        if (method_exists($product, 'isHourlyDiscountActive') && $product->isHourlyDiscountActive()) {
            $price = (float)$product->effective_price;
            $originalPrice = (float)$product->price;
            $discountPercent = $product->discount_percent;
        } else {
            $price = (float)($product->price ?? 0);
            $originalPrice = isset($product->original_price) ? (float)$product->original_price : null;
            $discountPercent = $product->discount_percent ?? null;
        }
    }

    $isDiscounted = ($originalPrice !== null && $originalPrice > $price) || (!empty($discountPercent) && (int)$discountPercent > 0);

    // Normalize Main Image & Gallery Images
    $mainImage = $isArr ? ($product['image'] ?? null) : ($product->image ?? null);

    $galleryImages = [];
    if ($isArr) {
        $rawImages = $product['images'] ?? $product['thumbnail_images'] ?? $product['gallery'] ?? [];
        if (is_array($rawImages)) {
            $galleryImages = $rawImages;
        }
    } else {
        if (isset($product->images) && is_array($product->images)) {
            $galleryImages = $product->images;
        } elseif (isset($product->thumbnail_images) && is_array($product->thumbnail_images)) {
            $galleryImages = $product->thumbnail_images;
        }
    }

    if (!empty($mainImage) && !in_array($mainImage, $galleryImages)) {
        array_unshift($galleryImages, $mainImage);
    }
    $galleryImages = array_values(array_unique(array_filter($galleryImages)));

    // Stock & Status
    $stock = null;
    if ($isArr) {
        $stock = isset($product['stock']) && $product['stock'] !== null ? (int)$product['stock'] : null;
    } else {
        $stock = $product->stock !== null ? (int)$product->stock : null;
    }
    $isOutOfStock = ($stock !== null && $stock <= 0) || (!$isArr && ($product->status ?? 'active') === 'inactive');

    // Discount Remaining Window
    $discountRemainingSeconds = null;
    $discountRemainingHuman = null;
    if (!$isArr && method_exists($product, 'discountRemainingSeconds') && $isDiscounted) {
        $discountRemainingSeconds = $product->discountRemainingSeconds();
        $discountRemainingHuman = $product->discountRemainingHuman();
    } elseif ($isArr && !empty($product['discount_remaining_seconds'])) {
        $discountRemainingSeconds = (int)$product['discount_remaining_seconds'];
        $discountRemainingHuman = $product['discount_remaining_human'] ?? null;
    }

    // Initial Favorite & In-Cart States
    if ($isFavorite === null) {
        $isFavorite = $isArr ? (bool)($product['is_favorite'] ?? false) : (bool)($product->is_favorite ?? false);
    }
    $initialQuantity = $isArr ? (int)($product['quantity'] ?? 0) : (int)($product->quantity ?? 0);

    // URL
    $targetUrl = $href;
    if (!$targetUrl) {
        $targetUrl = $isArr ? ($product['url'] ?? null) : ($product->url ?? null);
    }

    // Modal data dictionary for detail popup
    $modalData = [
        'id' => $id,
        'name' => $name,
        'category_id' => $isArr ? ($product['category_id'] ?? null) : ($product->category_id ?? null),
        'category_name' => $categoryName,
        'price' => $price,
        'original_price' => $originalPrice,
        'has_discount' => $isDiscounted,
        'discount_percent' => $discountPercent,
        'discount_remaining_seconds' => $discountRemainingSeconds,
        'discount_remaining_human' => $discountRemainingHuman,
        'stock' => $stock,
        'is_out_of_stock' => $isOutOfStock,
        'description' => $isArr ? ($product['description'] ?? 'تهیه شده از مرغوب‌ترین دانه‌های قهوه تخصصی روز بی کافه...') : ($product->description ?? 'تهیه شده از مرغوب‌ترین دانه‌های قهوه تخصصی روز بی کافه...'),
        'image' => $mainImage,
        'type' => $isArr ? ($product['type'] ?? 'hot') : ($product->type ?? 'hot'),
        'prep_time' => $isArr ? ($product['prep_time'] ?? 15) : ($product->prep_time ?? 15),
        'tasting_notes' => $isArr ? ($product['tasting_notes'] ?? 'شکلاتی، فندقی با بافت مخملی') : ($product->tasting_notes ?? 'شکلاتی، فندقی با بافت مخملی'),
        'bean_type' => $isArr ? ($product['bean_type'] ?? '۱۰۰٪ عربیکا تخصصی بی کافه') : ($product->bean_type ?? '۱۰۰٪ عربیکا تخصصی بی کافه'),
        'serving_type' => $isArr ? ($product['serving_type'] ?? 'سرو حضوری فنجان سرامیکی / بیرون‌بر') : ($product->serving_type ?? 'سرو حضوری فنجان سرامیکی / بیرون‌بر'),
        'addons' => $isArr ? ($product['addons'] ?? []) : ($product->addons ?? []),
    ];

    // Variant calculation
    $isCompact = $compact || ($variant === 'compact');
    $isDark = ($variant === 'dark');

    // JSON config for Alpine data
    $alpineConfig = [
        'id' => $id,
        'name' => $name,
        'price' => $price,
        'originalPrice' => $originalPrice,
        'hasDiscount' => $isDiscounted,
        'discountPercent' => $discountPercent,
        'discountRemainingSeconds' => $discountRemainingSeconds,
        'discountRemainingHuman' => $discountRemainingHuman,
        'image' => $mainImage,
        'images' => $galleryImages,
        'stock' => $stock !== null ? $stock : 99,
        'isOutOfStock' => $isOutOfStock,
        'isFavorite' => (bool)$isFavorite,
        'initialQuantity' => $initialQuantity,
        'currency' => $currency,
    ];
@endphp

<div x-data="productCard({{ json_encode($alpineConfig) }})"
     @click="handleCardClick({{ json_encode($modalData) }})"
     {{ $attributes->merge([
         'class' => $isDark
             ? 'dark bg-[#19242B] text-white'
             : 'bg-white text-[#353535]'
     ]) }}
     class="rounded-2xl p-2.5 sm:p-3.5 shadow-sm hover:shadow-md flex flex-col justify-between group relative select-none transition-all cursor-pointer {{ $isCompact ? 'flex-row items-center gap-3' : 'h-full' }}">

    @if($isCompact)
        {{-- COMPACT HORIZONTAL VARIANT --}}
        <div class="relative w-20 h-20 sm:w-24 sm:h-24 flex-shrink-0">
            <x-product-card.image :image="$mainImage"
                                  :name="$name"
                                  :product="$product"
                                  aspect-ratio="aspect-square"
                                  class="rounded-xl p-1.5" />

            @if($showDiscount && $isDiscounted)
                <div class="absolute -top-1.5 -start-1.5 z-10 scale-90">
                    <x-product-card.badge :discount="$discountPercent" />
                </div>
            @endif
        </div>

        <div class="flex-1 min-w-0 flex flex-col justify-between space-y-1.5">
            <div>
                @if($showCategory && !empty($categoryName))
                    <span class="text-[10px] text-[#353535]/60 font-bold block truncate">{{ $categoryName }}</span>
                @endif
                <h4 class="text-xs sm:text-sm font-black text-[#353535] group-hover:text-[#3C6E71] transition-colors leading-snug truncate" title="{{ $name }}">{{ $name }}</h4>
                <div class="flex items-baseline gap-1.5 pt-0.5">
                    <span class="text-xs sm:text-sm font-black text-[#284B63] tabular-nums">{{ number_format($price) }}</span>
                    @if($isDiscounted && $originalPrice > $price)
                        <span class="text-[10px] line-through text-slate-400 tabular-nums">{{ number_format($originalPrice) }}</span>
                    @endif
                    <span class="text-[10px] text-[#353535]/60 font-bold">{{ $currency }}</span>
                </div>
                @if($isDiscounted && ($discountRemainingSeconds !== null || !empty($discountRemainingHuman)))
                    <div class="pt-0.5 flex items-center justify-start text-start" dir="rtl">
                        <span class="inline-flex items-center gap-1 text-[9px] font-bold text-rose-700 bg-rose-50 border border-rose-200/80 rounded px-1.5 py-0.5 tabular-nums">
                            <svg class="w-2.5 h-2.5 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span x-text="discountCountdownText || '{{ $discountRemainingHuman }}'">{{ $discountRemainingHuman }}</span>
                        </span>
                    </div>
                @endif
            </div>

            @if($showAddToCart)
                <div class="pt-1">
                    <x-product-card.actions :product-id="$id"
                                           :stock="$stock ?? 99"
                                           :is-out-of-stock="$isOutOfStock"
                                           :add-to-cart-text="$addToCartText"
                                           :modal-data="$modalData" />
                </div>
            @endif
        </div>

        @if($showFavorite)
            <div class="absolute top-2.5 end-2.5 z-10">
                <x-product-card.favorite :product-id="$id" :name="$name" :is-favorite="$isFavorite" />
            </div>
        @endif

    @else
        {{-- STANDARD COMPACT 2-COL MOBILE FRIENDLY CARD --}}
        <div class="flex-1 flex flex-col justify-between space-y-2">
            <!-- Top Image Area with Standard 4:3 Aspect Ratio -->
            <div class="relative w-full aspect-[4/3] rounded-xl overflow-hidden shrink-0 bg-slate-50">
                <x-product-card.image :image="$mainImage"
                                      :name="$name"
                                      :product="$product"
                                      aspect-ratio="aspect-[4/3]"
                                      class="w-full h-full object-cover" />

                <!-- Discount Badge Top-Start -->
                @if($showDiscount && $isDiscounted)
                    <div class="absolute top-1.5 start-1.5 z-10 pointer-events-none">
                        <x-product-card.badge :discount="$discountPercent" />
                    </div>
                @endif

                <!-- Favorite Button Top-End -->
                @if($showFavorite)
                    <div class="absolute top-1.5 end-1.5 z-10">
                        <x-product-card.favorite :product-id="$id" :name="$name" :is-favorite="$isFavorite" />
                    </div>
                @endif

                <!-- Out of Stock Overlay -->
                @if($isOutOfStock)
                    <div class="absolute inset-0 bg-white/75 backdrop-blur-2xs flex items-center justify-center rounded-xl z-10 pointer-events-none">
                        <span class="px-2.5 py-1 rounded-xl bg-slate-900/85 text-white font-black text-[11px] shadow-sm">
                            ناموجود
                        </span>
                    </div>
                @endif
            </div>

            <!-- Thumbnail Gallery (if multiple images) -->
            @if($showGallery && count($galleryImages) > 1)
                <x-product-card.gallery :images="$galleryImages" :name="$name" class="py-0.5 shrink-0" />
            @endif

            <!-- Product Details: Category, Title, Price, and Equalized Countdown Slot -->
            <div class="flex-1 flex flex-col justify-between space-y-1.5">
                <!-- Title & Category Block with Fixed Heights -->
                <div class="space-y-0.5">
                    @if($showCategory)
                        <span class="text-[10px] text-[#353535]/60 font-bold block truncate h-3.5 leading-none">
                            {{ $categoryName ?: 'بی کافه' }}
                        </span>
                    @endif

                    <h4 class="text-xs sm:text-sm font-black text-[#353535] group-hover:text-[#3C6E71] transition-colors leading-tight line-clamp-1 h-5 flex items-center" title="{{ $name }}">
                        {{ $name }}
                    </h4>
                </div>

                <!-- Price & Digital Countdown Slot (Fixed Heights across all cards) -->
                <div class="space-y-1 pt-0.5">
                    <!-- Price Row (Always exact line height) -->
                    <div class="h-5 flex items-baseline flex-wrap gap-x-1.5 gap-y-0.5 select-none">
                        <span class="text-xs sm:text-sm font-black text-[#284B63] dark:text-[#52a6b2] tabular-nums">
                            {{ number_format($price) }}
                        </span>
                        @if($isDiscounted && $originalPrice > $price)
                            <span class="text-[10px] sm:text-xs line-through text-slate-400 tabular-nums">
                                {{ number_format($originalPrice) }}
                            </span>
                        @endif
                        <span class="text-[10px] sm:text-xs text-[#353535]/60 font-bold">
                            {{ $currency }}
                        </span>
                    </div>

                    <!-- Countdown / Promo / Stock Slot: Fixed 24px height so no card misaligns (Strictly RTL Aligned) -->
                    <div class="h-6 flex items-center justify-start text-start w-full overflow-hidden" dir="rtl">
                        @if($isDiscounted && ($discountRemainingSeconds !== null || !empty($discountRemainingHuman)))
                            <template x-if="hasValidCountdown">
                                <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-rose-50 text-rose-700 border border-rose-200/90 font-mono text-[10px] font-bold select-none shadow-2xs" dir="rtl">
                                    <svg class="w-3 h-3 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-[9px] font-sans font-bold text-rose-600 shrink-0">مهلت:</span>
                                    <div dir="ltr" class="inline-flex items-center gap-0.5 text-[11px] font-black tabular-nums tracking-tight font-mono text-rose-700">
                                        <span x-text="countdownHours">00</span>
                                        <span class="text-rose-400 font-bold">:</span>
                                        <span x-text="countdownMinutes">00</span>
                                        <span class="text-rose-400 font-bold">:</span>
                                        <span x-text="countdownSeconds">00</span>
                                    </div>
                                </div>
                            </template>
                            <template x-if="!hasValidCountdown">
                                <span class="inline-flex items-center gap-1 text-[9px] font-bold text-rose-700 bg-rose-50 border border-rose-200/80 rounded px-1.5 py-0.5">
                                    <svg class="w-2.5 h-2.5 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>پایان زمان تخفیف</span>
                                </span>
                            </template>
                        @elseif($isDiscounted)
                            <span class="inline-flex items-center gap-1 text-[9px] font-bold text-amber-800 bg-amber-50 border border-amber-200/80 rounded px-1.5 py-0.5">
                                <svg class="w-2.5 h-2.5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                <span>تخفیف ویژه</span>
                            </span>
                        @elseif($stock !== null && $stock > 0 && $stock <= 5)
                            <span class="inline-flex items-center gap-1 text-[10px] text-amber-700 font-bold truncate">
                                <svg class="w-2.5 h-2.5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>تنها {{ $stock }} عدد مانده</span>
                            </span>
                        @endif
                    </div>
                </div>

                {{ $slot }}
            </div>
        </div>

        <!-- Card Bottom Action: Order / Add to Cart / Quantity (Always pinned to bottom) -->
        @if($showAddToCart)
            <div class="pt-2 mt-auto shrink-0">
                <x-product-card.actions :product-id="$id"
                                       :stock="$stock ?? 99"
                                       :is-out-of-stock="$isOutOfStock"
                                       :add-to-cart-text="$addToCartText"
                                       :modal-data="$modalData" />
            </div>
        @endif
    @endif

</div>
