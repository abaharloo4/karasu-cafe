@props(['active' => ''])

<!-- Mobile Bottom Navigation Bar (Glassmorphic Luxury Pill) -->
<nav x-data="{
        mobileCartCount: 0,
        init() {
            this.updateCount();
            window.addEventListener('storage', () => this.updateCount());
            // Periodic check for cart changes
            setInterval(() => this.updateCount(), 1500);
        },
        updateCount() {
            try {
                const c = JSON.parse(localStorage.getItem('bcafe_cart') || '[]');
                this.mobileCartCount = c.reduce((sum, item) => sum + (parseInt(item.quantity) || 1), 0);
            } catch(e) {
                this.mobileCartCount = 0;
            }
        }
     }"
     class="md:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-xl border-t border-[#D9D9D9] px-2 pb-[env(safe-area-inset-bottom)] shadow-[0_-4px_20px_rgba(40,75,99,0.08)]">
    
    <div class="h-16 flex items-center justify-around max-w-md mx-auto">
        <!-- 1. Cafe Menu -->
        <a href="{{ route('storefront.menu') }}"
           class="flex flex-col items-center justify-center flex-1 py-1 text-center transition-all min-h-[44px] {{ ($active === 'menu' || request()->routeIs('storefront.menu')) ? 'text-[#3C6E71] font-black' : 'text-[#353535]/65 hover:text-[#353535]' }}">
            <div class="relative">
                <svg class="w-5 h-5 {{ ($active === 'menu' || request()->routeIs('storefront.menu')) ? 'stroke-[2.5]' : 'stroke-2' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                @if($active === 'menu' || request()->routeIs('storefront.menu'))
                    <span class="absolute -bottom-1 inset-x-0 mx-auto w-1 h-1 rounded-full bg-[#3C6E71]"></span>
                @endif
            </div>
            <span class="text-[10px] mt-1 font-bold">منوی کافه</span>
        </a>

        <!-- 2. Subscriptions -->
        <a href="{{ route('storefront.menu') }}#coffee-subscriptions"
           class="flex flex-col items-center justify-center flex-1 py-1 text-center transition-all min-h-[44px] {{ $active === 'subscriptions' ? 'text-[#3C6E71] font-black' : 'text-[#353535]/65 hover:text-[#353535]' }}">
            <div class="relative">
                <svg class="w-5 h-5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
            </div>
            <span class="text-[10px] mt-1 font-bold">اشتراک‌ها</span>
        </a>

        <!-- 3. Cart with Badge -->
        <a href="{{ route('cart.index') }}"
           class="flex flex-col items-center justify-center flex-1 py-1 text-center transition-all min-h-[44px] relative {{ ($active === 'cart' || request()->routeIs('cart.*')) ? 'text-[#3C6E71] font-black' : 'text-[#353535]/65 hover:text-[#353535]' }}">
            <div class="relative">
                <svg class="w-5 h-5 {{ ($active === 'cart' || request()->routeIs('cart.*')) ? 'stroke-[2.5]' : 'stroke-2' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <template x-if="mobileCartCount > 0">
                    <span class="absolute -top-1 -end-2 w-4 h-4 rounded-full bg-[#3C6E71] text-white text-[9px] font-black flex items-center justify-center tabular-nums shadow-xs" x-text="mobileCartCount > 9 ? '9+' : mobileCartCount"></span>
                </template>
                @if($active === 'cart' || request()->routeIs('cart.*'))
                    <span class="absolute -bottom-1 inset-x-0 mx-auto w-1 h-1 rounded-full bg-[#3C6E71]"></span>
                @endif
            </div>
            <span class="text-[10px] mt-1 font-bold">سبد خرید</span>
        </a>

        <!-- 4. Orders History -->
        @auth('customer')
            <a href="{{ route('customer.orders') }}"
               class="flex flex-col items-center justify-center flex-1 py-1 text-center transition-all min-h-[44px] {{ ($active === 'orders' || request()->routeIs('customer.orders*')) ? 'text-[#3C6E71] font-black' : 'text-[#353535]/65 hover:text-[#353535]' }}">
                <div class="relative">
                    <svg class="w-5 h-5 {{ ($active === 'orders' || request()->routeIs('customer.orders*')) ? 'stroke-[2.5]' : 'stroke-2' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    @if($active === 'orders' || request()->routeIs('customer.orders*'))
                        <span class="absolute -bottom-1 inset-x-0 mx-auto w-1 h-1 rounded-full bg-[#3C6E71]"></span>
                    @endif
                </div>
                <span class="text-[10px] mt-1 font-bold">سفارش‌ها</span>
            </a>
        @else
            <a href="{{ route('customer.login') }}"
               class="flex flex-col items-center justify-center flex-1 py-1 text-center transition-all min-h-[44px] text-[#353535]/65 hover:text-[#353535]">
                <div class="relative">
                    <svg class="w-5 h-5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <span class="text-[10px] mt-1 font-bold">پیگیری</span>
            </a>
        @endauth

        <!-- 5. Profile / Panel -->
        @auth('customer')
            <a href="{{ route('customer.dashboard') }}"
               class="flex flex-col items-center justify-center flex-1 py-1 text-center transition-all min-h-[44px] {{ ($active === 'panel' || request()->routeIs('customer.dashboard') || request()->routeIs('customer.profile')) ? 'text-[#3C6E71] font-black' : 'text-[#353535]/65 hover:text-[#353535]' }}">
                <div class="relative">
                    <svg class="w-5 h-5 {{ ($active === 'panel' || request()->routeIs('customer.dashboard') || request()->routeIs('customer.profile')) ? 'stroke-[2.5]' : 'stroke-2' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    @if($active === 'panel' || request()->routeIs('customer.dashboard') || request()->routeIs('customer.profile'))
                        <span class="absolute -bottom-1 inset-x-0 mx-auto w-1 h-1 rounded-full bg-[#3C6E71]"></span>
                    @endif
                </div>
                <span class="text-[10px] mt-1 font-bold">پنل من</span>
            </a>
        @else
            <a href="{{ route('customer.login') }}"
               class="flex flex-col items-center justify-center flex-1 py-1 text-center transition-all min-h-[44px] {{ request()->routeIs('customer.login') ? 'text-[#3C6E71] font-black' : 'text-[#353535]/65 hover:text-[#353535]' }}">
                <div class="relative">
                    <svg class="w-5 h-5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                </div>
                <span class="text-[10px] mt-1 font-bold">ورود</span>
            </a>
        @endauth
    </div>
</nav>
