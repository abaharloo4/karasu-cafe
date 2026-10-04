@extends('layouts.customer')

@section('title', 'علاقه‌مندی‌های من')

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[#D9D9D9]/70 pb-4">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-[#353535] flex items-center gap-2">
                <svg class="w-6 h-6 text-[#FF7A18]" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>
                <span>محصولات مورد علاقه من</span>
            </h1>
            <p class="text-xs text-[#353535]/65 mt-1">آیتم‌های محبوب شما در بی کافه برای دسترسی و سفارش سریع</p>
        </div>

        <a href="{{ route('storefront.menu') }}" class="px-4 py-2.5 bg-[#284B63] hover:bg-[#1E374A] text-white text-xs font-black rounded-xl btn-tactile shadow-xs flex items-center gap-1.5 transition-colors min-h-[44px] self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            <span>مشاهده کل منو</span>
        </a>
    </div>

    @if($favorites->count() > 0)
        <!-- Grid of Favorite Products: 2 on mobile, 3 on tablet, 4 on desktop -->
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
            @foreach($favorites as $product)
                <x-product-card :product="$product"
                                :is-favorite="true"
                                class="h-full" />
            @endforeach
        </div>

        <div class="pt-4">
            {{ $favorites->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="p-10 rounded-3xl bg-white border border-[#D9D9D9] text-center space-y-4 shadow-xs">
            <div class="w-16 h-16 rounded-full bg-orange-50 text-[#FF7A18] flex items-center justify-center mx-auto shadow-inner">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </div>
            <div class="space-y-1">
                <h3 class="text-sm sm:text-base font-black text-[#353535]">هنوز محصولی را به علاقه‌مندی‌ها اضافه نکرده‌اید</h3>
                <p class="text-xs text-[#353535]/65 max-w-md mx-auto leading-relaxed">
                    با کلیک روی آیکون قلب روی هر یک از آیتم‌های منو، قهوه یا دسر دلخواهتان را نشان کنید تا همیشه دم دستتان باشد.
                </p>
            </div>
            <a href="{{ route('storefront.menu') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#3C6E71] hover:bg-[#2E585B] text-white text-xs font-black rounded-xl btn-tactile shadow-xs">
                <span>رفتن به منوی بی کافه</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
        </div>
    @endif

</div>
@endsection
