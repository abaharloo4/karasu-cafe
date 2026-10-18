@php
    $siteContent = $siteContent ?? \App\Models\SiteContentSetting::getForCafe($cafe->id ?? 1);
    $activeTab = $activeTab ?? 'menu';
    $customerUser = auth('customer')->user();
    $adminUser = (auth()->check() && (auth()->user()->role === 'admin' || (method_exists(auth()->user(), 'isAdmin') && auth()->user()->isAdmin()))) ? auth()->user() : null;
@endphp

<!-- Unified Global Karasu B Cafe Navigation Header -->
<header class="sticky top-0 z-40 bg-white/95 dark:bg-[#141D22]/95 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800 transition-colors shadow-xs"
        x-data="{ mobileNavOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 sm:h-20 flex items-center justify-between gap-4">
        
        <!-- Brand & Local Cafe Info -->
        <div class="flex items-center gap-3 sm:gap-4 flex-shrink-0">
            <a href="{{ route('storefront.menu') }}" class="flex items-center gap-3 group">
                <div class="relative flex-shrink-0">
                    <img src="{{ asset('images/logo.jpeg') }}" 
                         alt="بی کافه" 
                         class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl object-cover border border-slate-200 dark:border-slate-700 shadow-2xs group-hover:scale-105 transition-transform">
                    <span class="absolute -bottom-1 -start-1 w-3.5 h-3.5 rounded-full {{ (($cafe ?? false) instanceof \App\Models\Cafe) && $cafe->isOrderingOpen() ? 'bg-emerald-500' : 'bg-amber-500' }} border-2 border-white dark:border-[#141D22] shadow-2xs" 
                          title="{{ (($cafe ?? false) instanceof \App\Models\Cafe) && $cafe->isOrderingOpen() ? 'سفارش‌گیری باز است' : 'خارج از شیفت کاری' }}"></span>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-base sm:text-lg font-black text-[#19242B] dark:text-slate-100 block leading-tight group-hover:text-[#387780] dark:group-hover:text-teal-400 transition-colors">بی کافه (B Cafe)</span>
                    </div>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="text-[11px] text-slate-500 dark:text-slate-400 font-bold flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-[#387780] dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                            <span>شیراز • بار تخصصی قهوه</span>
                        </span>
                        @if(($cafe ?? false) instanceof \App\Models\Cafe)
                            <span class="hidden sm:inline-flex text-[10px] px-2 py-0.5 rounded-md font-bold {{ $cafe->isOrderingOpen() ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800' : 'bg-amber-50 text-amber-800 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800' }}">
                                {{ $cafe->isOrderingOpen() ? 'سفارش‌گیری باز' : 'خارج از شیفت' }}
                            </span>
                        @endif
                    </div>
                </div>
            </a>
        </div>

        <!-- Desktop Navigation Links -->
        <nav class="hidden md:flex items-center gap-6 lg:gap-8 text-xs font-bold text-slate-600 dark:text-slate-300">
            <a href="{{ route('storefront.menu') }}" 
               class="py-1 transition-colors {{ $activeTab === 'menu' ? 'text-[#387780] dark:text-teal-400 font-black border-b-2 border-[#387780] dark:border-teal-400' : 'hover:text-[#387780] dark:hover:text-teal-400' }}">
                منوی آنلاین
            </a>
            <a href="{{ route('storefront.subscriptions.index') }}" 
               class="py-1 transition-colors flex items-center gap-1.5 {{ $activeTab === 'subscriptions' ? 'text-[#387780] dark:text-teal-400 font-black border-b-2 border-[#387780] dark:border-teal-400' : 'hover:text-[#387780] dark:hover:text-teal-400' }}">
                <span>اشتراک‌های روزانه</span>
                <span class="px-1.5 py-0.5 rounded-md bg-[#387780]/10 text-[#387780] dark:bg-teal-500/20 dark:text-teal-300 text-[10px] font-black">ویژه</span>
            </a>
            <a href="{{ route('storefront.about') }}" 
               class="py-1 transition-colors {{ $activeTab === 'about' ? 'text-[#387780] dark:text-teal-400 font-black border-b-2 border-[#387780] dark:border-teal-400' : 'hover:text-[#387780] dark:hover:text-teal-400' }}">
                درباره ما و شعبه
            </a>
            <a href="{{ route('storefront.menu') }}#footer-contact" 
               class="py-1 transition-colors hover:text-[#387780] dark:hover:text-teal-400">
                تماس و لوکیشن
            </a>
        </nav>

        <!-- Right Side: Dark Mode Toggle + Cart Capsule + Profile / Admin / Login -->
        <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">

            <!-- Dark / Light Mode Switcher Toggle Button -->
            <x-theme-toggle />

            <!-- Cart Capsule Button (Always accessible) -->
            <template x-if="typeof openCartDrawer === 'function'">
                <button type="button" 
                        @click="openCartDrawer()"
                        class="px-3 sm:px-3.5 py-2 sm:py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-[#19242B] dark:text-slate-100 rounded-xl text-xs font-bold btn-tactile border border-slate-200 dark:border-slate-700 flex items-center gap-2 shadow-2xs min-h-[42px]">
                    <svg class="w-4 h-4 text-[#387780] dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <span class="hidden sm:inline">سبد خرید</span>
                    <span class="px-1.5 py-0.5 rounded-full bg-[#19242B] dark:bg-teal-500 text-white dark:text-[#19242B] text-[11px] font-mono tabular-nums" 
                          x-text="typeof cartTotalCount !== 'undefined' ? cartTotalCount : (JSON.parse(localStorage.getItem('bcafe_cart') || '[]')).reduce((s, i) => s + (i.quantity || 1), 0)"></span>
                </button>
            </template>
            <template x-if="typeof openCartDrawer !== 'function'">
                <a href="{{ route('cart.index') }}" 
                   class="px-3 sm:px-3.5 py-2 sm:py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-[#19242B] dark:text-slate-100 rounded-xl text-xs font-bold btn-tactile border border-slate-200 dark:border-slate-700 flex items-center gap-2 shadow-2xs min-h-[42px]">
                    <svg class="w-4 h-4 text-[#387780] dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <span class="hidden sm:inline">سبد خرید</span>
                    <span class="px-1.5 py-0.5 rounded-full bg-[#19242B] dark:bg-teal-500 text-white dark:text-[#19242B] text-[11px] font-mono tabular-nums"
                          x-data="{ count: 0 }"
                          x-init="const getC = () => { try { count = JSON.parse(localStorage.getItem('bcafe_cart') || '[]').reduce((s, i) => s + (parseInt(i.quantity) || 1), 0); } catch(e){} }; getC(); window.addEventListener('storage', getC);"
                          x-text="count"></span>
                </a>
            </template>

            <!-- Admin Dropdown if logged in as Admin -->
            @if($adminUser)
                <div class="relative hidden sm:block" x-data="{ userMenuOpen: false }">
                    <button type="button" @click="userMenuOpen = !userMenuOpen"
                            class="px-3.5 py-2.5 bg-[#387780] hover:bg-[#2E636B] text-white rounded-xl text-xs font-bold btn-tactile flex items-center gap-2 shadow-xs transition-colors min-h-[42px]">
                        <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>پنل مدیریت</span>
                        <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="userMenuOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="userMenuOpen" @click.outside="userMenuOpen = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                         class="absolute left-0 mt-2 w-64 bg-white dark:bg-[#19242B] rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 py-2 z-50 overflow-hidden"
                         style="display: none;">
                        <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50">
                            <div class="text-xs font-black text-[#19242B] dark:text-white">{{ $adminUser->name }}</div>
                            <div class="text-[11px] text-[#387780] dark:text-teal-400 font-bold">مدیر سیستم کافه</div>
                        </div>
                        <div class="py-1">
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-[#387780] dark:hover:text-teal-400 transition-colors">
                                <svg class="w-4 h-4 text-[#387780]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                <span>پیشخوان مدیریت</span>
                            </a>
                            <a href="{{ route('orders.live') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-[#387780] dark:hover:text-teal-400 transition-colors">
                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>سفارشات زنده آشپزخانه / بار</span>
                            </a>
                            <a href="{{ route('products.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-[#387780] dark:hover:text-teal-400 transition-colors">
                                <svg class="w-4 h-4 text-[#284B63]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                <span>مدیریت منو و محصولات</span>
                            </a>
                        </div>
                        <div class="border-t border-slate-100 dark:border-slate-800 pt-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-right flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    <span>خروج از پنل مدیریت</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @elseif($customerUser)
                <!-- Customer Profile Dropdown / Link -->
                <div class="relative hidden sm:block" x-data="{ userMenuOpen: false }">
                    <button type="button" @click="userMenuOpen = !userMenuOpen"
                            class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-[#19242B] dark:text-slate-100 rounded-xl text-xs font-bold btn-tactile border border-slate-200 dark:border-slate-700 flex items-center gap-2 shadow-2xs transition-colors min-h-[42px]">
                        <div class="w-5 h-5 rounded-full bg-[#387780] text-white flex items-center justify-center text-[10px] font-black">
                            {{ mb_substr($customerUser->name ?? 'ک', 0, 1) }}
                        </div>
                        <span>{{ $customerUser->name }}</span>
                        <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="userMenuOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="userMenuOpen" @click.outside="userMenuOpen = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                         class="absolute left-0 mt-2 w-64 bg-white dark:bg-[#19242B] rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 py-2 z-50 overflow-hidden"
                         style="display: none;">
                        <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50">
                            <div class="text-xs font-black text-[#19242B] dark:text-white">{{ $customerUser->name }}</div>
                            <div class="text-[11px] text-slate-500 font-mono mt-0.5">{{ $customerUser->phone }}</div>
                            <div class="flex items-center gap-2 mt-2 pt-2 border-t border-slate-200/60 dark:border-slate-800 text-[11px]">
                                <span class="font-bold text-[#387780] dark:text-teal-400">
                                    {{ number_format($customerUser->total_loyalty_points ?? 0) }} امتیاز
                                </span>
                                <span class="text-slate-400">•</span>
                                <span class="font-bold text-slate-700 dark:text-slate-300">
                                    {{ number_format($customerUser->wallet_balance ?? 0) }} ت کیف‌ پول
                                </span>
                            </div>
                        </div>
                        <div class="py-1">
                            <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-[#387780] dark:hover:text-teal-400 transition-colors">
                                <svg class="w-4 h-4 text-[#387780]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                <span>پنل کاربری من</span>
                            </a>
                            <a href="{{ route('customer.orders') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-[#387780] dark:hover:text-teal-400 transition-colors">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                <span>تاریخچه سفارش‌های من</span>
                            </a>
                            <a href="{{ route('customer.subscriptions') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-[#387780] dark:hover:text-teal-400 transition-colors">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                <span>اشتراک‌های من</span>
                            </a>
                        </div>
                        <div class="border-t border-slate-100 dark:border-slate-800 pt-1">
                            <form method="POST" action="{{ route('customer.logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-right flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    <span>خروج از حساب</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <!-- Login Button -->
                <a href="{{ route('customer.login') }}" 
                   class="px-3.5 sm:px-4 py-2 sm:py-2.5 bg-[#387780] hover:bg-[#2E636B] text-white rounded-xl text-xs font-bold btn-tactile shadow-xs flex items-center gap-1.5 transition-colors min-h-[42px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    <span>ورود / عضویت</span>
                </a>
            @endif

            <!-- Mobile Menu Toggle Button (Hamburger) -->
            <button type="button" 
                    @click="mobileNavOpen = !mobileNavOpen"
                    class="md:hidden w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center btn-tactile border border-slate-200/80 dark:border-slate-700"
                    aria-label="منوی سایت">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>

    </div>

    <!-- Mobile Slide-Down Menu Links -->
    <div x-show="mobileNavOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         @click.outside="mobileNavOpen = false"
         class="md:hidden border-t border-slate-200/80 dark:border-slate-800 bg-white/95 dark:bg-[#141D22]/95 px-4 py-4 space-y-2"
         style="display: none;">
        <a href="{{ route('storefront.menu') }}" 
           class="flex items-center justify-between p-3 rounded-xl text-xs font-bold {{ $activeTab === 'menu' ? 'bg-[#387780]/10 text-[#387780] dark:text-teal-400 font-black' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>منوی آنلاین کافه</span>
            <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <a href="{{ route('storefront.subscriptions.index') }}" 
           class="flex items-center justify-between p-3 rounded-xl text-xs font-bold {{ $activeTab === 'subscriptions' ? 'bg-[#387780]/10 text-[#387780] dark:text-teal-400 font-black' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>باشگاه اشتراک‌های روزانه</span>
            <span class="px-2 py-0.5 rounded-md bg-[#387780]/20 text-[#387780] dark:text-teal-300 text-[10px] font-black">ویژه</span>
        </a>
        <a href="{{ route('storefront.about') }}" 
           class="flex items-center justify-between p-3 rounded-xl text-xs font-bold {{ $activeTab === 'about' ? 'bg-[#387780]/10 text-[#387780] dark:text-teal-400 font-black' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
            <span>درباره ما و شعبه شیراز</span>
            <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <a href="{{ route('cart.index') }}" 
           class="flex items-center justify-between p-3 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
            <span>سبد خرید و تکمیل سفارش</span>
            <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        @if($customerUser)
            <a href="{{ route('customer.dashboard') }}" 
               class="flex items-center justify-between p-3 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border-t border-slate-200/60 dark:border-slate-800">
                <span>پنل مشتریان ({{ $customerUser->name }})</span>
                <span class="text-[11px] font-mono text-[#387780] dark:text-teal-400">{{ number_format($customerUser->total_loyalty_points ?? 0) }} امتیاز</span>
            </a>
        @endif
    </div>
</header>
