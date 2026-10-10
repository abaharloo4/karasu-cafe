<!DOCTYPE html>
<html lang="fa" dir="rtl" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>منوی آنلاین و اشتراک‌های بی کافه (B Cafe) - شیراز</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}?v=2.0.2">
    <link rel="alternate icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2.0.2">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}?v=2.0.2">

    @include('partials.pwa-meta', ['pwaPanel' => 'customer'])

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data="storefrontMenu()"
      @keydown.escape="closeProductModal(); mobileDrawerOpen = false"
      class="h-full antialiased selection:bg-[#3C6E71] selection:text-[#F7F9FA] bg-[#F7F9FA]/35 text-[#353535] pb-36 relative font-sans">

    @include('partials.page-preloader')

    <!-- Top Navigation Header -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-[#353535]/10 shadow-xs">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-20 flex items-center justify-between gap-4">
            
            <!-- Brand Logo & Shiraz Info -->
            <div class="flex items-center gap-3.5">
                <a href="{{ route('storefront.menu') }}" class="relative flex-shrink-0 group">
                    <img src="{{ asset('images/logo.jpeg') }}" alt="بی کافه" class="w-12 h-12 rounded-2xl object-cover border border-[#353535]/15 shadow-xs group-hover:scale-105 transition-transform">
                    <span class="absolute -bottom-1 -start-1 w-3.5 h-3.5 rounded-full {{ (($cafe ?? false) instanceof \App\Models\Cafe) && $cafe->isOrderingOpen() ? 'bg-[#3C6E71]' : 'bg-amber-500' }} border-2 border-white shadow-2xs" title="{{ (($cafe ?? false) instanceof \App\Models\Cafe) && $cafe->isOrderingOpen() ? 'سفارش‌گیری باز است' : 'خارج از شیفت کاری' }}"></span>
                </a>
                <div>
                    <a href="{{ route('storefront.menu') }}" class="text-base sm:text-lg font-black text-[#353535] block leading-tight hover:text-[#3C6E71] transition-colors">بی کافه (B Cafe)</a>
                    <div class="flex flex-wrap items-center gap-2 mt-0.5">
                        <span class="text-[11px] sm:text-xs text-[#353535]/65 flex items-center gap-1.5 font-bold">
                            <svg class="w-3.5 h-3.5 text-[#284B63]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                            <span>شیراز • بار تخصصی قهوه</span>
                        </span>
                        <span class="text-[10px] px-2 py-0.5 rounded-md font-bold {{ (($cafe ?? false) instanceof \App\Models\Cafe) && $cafe->isOrderingOpen() ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                            {{ (($cafe ?? false) instanceof \App\Models\Cafe) && $cafe->isOrderingOpen() ? 'سفارش‌گیری باز است' : 'خارج از شیفت (رزرو بعد)' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Desktop Navigation Links -->
            <nav class="hidden md:flex items-center gap-6 text-xs font-black text-[#353535]/80">
                <a href="#menu-catalog" class="hover:text-[#284B63] transition-colors">منوی کافه</a>
                <a href="{{ route('storefront.subscriptions.index') }}" class="hover:text-[#284B63] transition-colors flex items-center gap-1">
                    <span>اشتراک‌های روزانه</span>
                    <span class="px-1.5 py-0.5 rounded bg-[#284B63]/15 text-[#284B63] text-[10px] font-bold">ویژه</span>
                </a>
                <a href="{{ route('storefront.about') }}" class="hover:text-[#284B63] transition-colors">درباره ما</a>
                <a href="#footer-contact" class="hover:text-[#284B63] transition-colors">تماس و لوکیشن</a>
            </nav>

            <!-- Actions: Cart Capsule & User Profile / Mobile Drawer Toggle -->
            <div class="flex items-center gap-2.5 sm:gap-3">
                <!-- Cart Preview Button -->
                <a href="{{ route('cart.index') }}" @click="syncToStorage()"
                   class="relative px-3.5 py-2.5 bg-[#F7F9FA] hover:bg-[#3C6E71]/30 text-[#353535] rounded-xl text-xs font-black btn-tactile border border-[#353535]/12 flex items-center gap-2 shadow-2xs min-h-[44px]">
                    <svg class="w-4 h-4 text-[#284B63]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <span class="hidden sm:inline">سبد خرید</span>
                    <span class="px-1.5 py-0.5 rounded-full bg-[#353535] text-[#F7F9FA] text-[11px] font-mono tabular-nums" x-text="cartTotalCount"></span>
                </a>

                @if(auth()->check() && (auth()->user()->role === 'admin' || (method_exists(auth()->user(), 'isAdmin') && auth()->user()->isAdmin())))
                    <div class="relative hidden sm:block" x-data="{ userMenuOpen: false }">
                        <button type="button" @click="userMenuOpen = !userMenuOpen"
                                class="px-4 py-2.5 bg-[#3C6E71] text-white hover:bg-[#284B63] rounded-xl text-xs font-bold btn-tactile border border-[#3C6E71]/25 flex items-center gap-2 shadow-xs transition-colors min-h-[44px]">
                            <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>پنل مدیریت کافه</span>
                            <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="userMenuOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <!-- Floating Popover Dropdown -->
                        <div x-show="userMenuOpen" @click.outside="userMenuOpen = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                             class="absolute left-0 mt-2 w-64 bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-[#353535]/15 py-2 z-50 overflow-hidden"
                             style="display: none;">
                            <div class="px-4 py-3 border-b border-[#353535]/10 bg-[#F7F9FA]">
                                <div class="text-xs font-black text-[#353535]">{{ auth()->user()->name }}</div>
                                <div class="text-[11px] text-[#3C6E71] font-bold">مدیر سیستم کافه</div>
                            </div>
                            <div class="py-1">
                                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-[#353535] hover:bg-[#3C6E71]/10 hover:text-[#3C6E71] transition-colors">
                                    <svg class="w-4 h-4 text-[#3C6E71]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                    <span>پیشخوان مدیریت</span>
                                </a>
                                <a href="{{ route('orders.live') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-[#353535] hover:bg-[#3C6E71]/10 hover:text-[#3C6E71] transition-colors">
                                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>سفارشات زنده آشپزخانه / بار</span>
                                </a>
                                <a href="{{ route('products.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-[#353535] hover:bg-[#3C6E71]/10 hover:text-[#3C6E71] transition-colors">
                                    <svg class="w-4 h-4 text-[#284B63]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    <span>مدیریت منو و محصولات</span>
                                </a>
                                <a href="{{ route('admin.coupons.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-[#353535] hover:bg-[#3C6E71]/10 hover:text-[#3C6E71] transition-colors">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    <span>کدهای تخفیف</span>
                                </a>
                                <a href="{{ route('admin.loyalty.settings') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-[#353535] hover:bg-[#3C6E71]/10 hover:text-[#3C6E71] transition-colors">
                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                                    <span>تنظیمات باشگاه و کادو تولد</span>
                                </a>
                            </div>
                            <div class="border-t border-[#353535]/10 pt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-right flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-rose-600 hover:bg-rose-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        <span>خروج از پنل مدیریت</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @elseif($customer ?? false)
                    <div class="relative hidden sm:block" x-data="{ userMenuOpen: false }">
                        <button type="button" @click="userMenuOpen = !userMenuOpen"
                                class="px-4 py-2.5 bg-[#3C6E71]/15 text-[#353535] hover:bg-[#3C6E71] hover:text-[#F7F9FA] rounded-xl text-xs font-bold btn-tactile border border-[#3C6E71]/25 flex items-center gap-2 shadow-xs transition-colors min-h-[44px]">
                            <svg class="w-4 h-4 text-[#3C6E71]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>{{ $customer->name }}</span>
                            <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="userMenuOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <!-- Floating Popover Dropdown -->
                        <div x-show="userMenuOpen" @click.outside="userMenuOpen = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                             class="absolute left-0 mt-2 w-64 bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-[#353535]/15 py-2 z-50 overflow-hidden"
                             style="display: none;">
                            <div class="px-4 py-3 border-b border-[#353535]/10 bg-[#F7F9FA]">
                                <div class="text-xs font-black text-[#353535]">{{ $customer->name }}</div>
                                <div class="text-[11px] text-[#353535]/70 font-mono mt-0.5">{{ $customer->phone }}</div>
                                <div class="flex items-center gap-2 mt-2 pt-2 border-t border-[#353535]/10 text-[11px]">
                                    <span class="inline-flex items-center gap-1 font-bold text-[#3C6E71]">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        {{ number_format($customer->loyalty_points ?? 0) }} امتیاز
                                    </span>
                                    <span class="text-[#353535]/40">•</span>
                                    <span class="font-bold text-[#284B63]">{{ number_format($customer->wallet_balance ?? 0) }} ت کیف</span>
                                </div>
                            </div>
                            <div class="py-1">
                                <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-[#353535] hover:bg-[#3C6E71]/10 hover:text-[#3C6E71] transition-colors">
                                    <svg class="w-4 h-4 text-[#3C6E71]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                    <span>پیشخوان و وضعیت سفارش‌ها</span>
                                </a>
                                <a href="{{ route('customer.orders') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-[#353535] hover:bg-[#3C6E71]/10 hover:text-[#3C6E71] transition-colors">
                                    <svg class="w-4 h-4 text-[#284B63]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    <span>تاریخچه سفارشات من</span>
                                </a>
                                <a href="{{ route('customer.club') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-[#353535] hover:bg-[#3C6E71]/10 hover:text-[#3C6E71] transition-colors">
                                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>باشگاه مشتریان و امتیازات</span>
                                </a>
                                <a href="{{ route('customer.addresses') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-[#353535] hover:bg-[#3C6E71]/10 hover:text-[#3C6E71] transition-colors">
                                    <svg class="w-4 h-4 text-[#3C6E71]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>آدرس‌های تحویل من</span>
                                </a>
                                <a href="{{ route('customer.subscriptions') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-[#353535] hover:bg-[#3C6E71]/10 hover:text-[#3C6E71] transition-colors">
                                    <svg class="w-4 h-4 text-[#284B63]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span>اشتراک‌های قهوه من</span>
                                </a>
                            </div>
                            <div class="border-t border-[#353535]/10 pt-1">
                                <form method="POST" action="{{ route('customer.logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-right flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-rose-600 hover:bg-rose-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        <span>خروج از حساب</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('customer.login') }}" class="hidden sm:flex px-4 py-2.5 bg-[#353535] text-[#F7F9FA] hover:bg-[#1E2912] rounded-xl text-xs font-bold btn-tactile border border-[#353535]/15 items-center gap-2 shadow-xs min-h-[44px]">
                        <svg class="w-4 h-4 text-[#3C6E71]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        <span>ورود / عضویت</span>
                    </a>
                @endif

                <!-- Mobile Hamburger Button -->
                <button type="button" @click="mobileDrawerOpen = true"
                        aria-label="منوی اصلی"
                        class="md:hidden w-11 h-11 rounded-xl bg-[#F7F9FA]/80 hover:bg-[#F7F9FA] text-[#353535] flex items-center justify-center btn-tactile border border-[#353535]/12">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Navigation Drawer (Off-canvas) -->
    <div x-show="mobileDrawerOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex justify-start"
         style="display: none;">

        <div @click.outside="mobileDrawerOpen = false"
             x-show="mobileDrawerOpen"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-x-full rtl:translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full rtl:translate-x-full"
             class="w-full max-w-xs bg-white h-full shadow-2xl flex flex-col justify-between p-6 overflow-y-auto">

            <div class="space-y-6">
                <!-- Drawer Header -->
                <div class="flex items-center justify-between border-b border-[#353535]/10 pb-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/logo.jpeg') }}" alt="بی کافه" class="w-10 h-10 rounded-xl object-cover border border-[#353535]/15">
                        <div>
                            <span class="text-sm font-black text-[#353535] block">بی کافه (B Cafe)</span>
                            <span class="text-[10px] text-[#3C6E71] font-bold">شیراز • بار تخصصی قهوه</span>
                        </div>
                    </div>
                    <button type="button" @click="mobileDrawerOpen = false" class="w-9 h-9 rounded-xl bg-[#F7F9FA] text-[#353535] flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Navigation Links -->
                <nav class="space-y-2">
                    <a href="#menu-catalog" @click="mobileDrawerOpen = false" class="flex items-center gap-3 p-3 rounded-xl bg-[#F7F9FA]/50 text-[#353535] font-black text-xs">
                        <svg class="w-4 h-4 text-[#284B63]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <span>منوی کافه</span>
                    </a>

                    <a href="{{ route('storefront.subscriptions.index') }}" class="flex items-center justify-between p-3 rounded-xl bg-teal-50 text-[#387780] font-black text-xs border border-teal-200">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-[#387780]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2H5z"/></svg>
                            <span>باشگاه اشتراک روزانه</span>
                        </div>
                        <span class="px-2 py-0.5 rounded bg-[#387780] text-white text-[10px] font-bold">ویژه</span>
                    </a>

                    <a href="{{ route('storefront.about') }}" class="flex items-center gap-3 p-3 rounded-xl bg-[#F7F9FA]/50 text-[#353535] font-black text-xs">
                        <svg class="w-4 h-4 text-[#284B63]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>درباره بی کافه شیراز</span>
                    </a>

                    <a href="{{ route('cart.index') }}" class="flex items-center justify-between p-3 rounded-xl bg-[#F7F9FA]/50 text-[#353535] font-black text-xs">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-[#284B63]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            <span>سبد خرید</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-[#353535] text-[#F7F9FA] text-[11px] font-mono tabular-nums" x-text="cartTotalCount"></span>
                    </a>

                    @if(auth()->check() && (auth()->user()->role === 'admin' || (method_exists(auth()->user(), 'isAdmin') && auth()->user()->isAdmin())))
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 p-3 rounded-xl bg-[#3C6E71] text-white font-black text-xs shadow-xs">
                            <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>ورود به پنل مدیریت کافه</span>
                        </a>
                    @elseif($customer ?? false)
                        <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-3 p-3 rounded-xl bg-[#3C6E71]/15 text-[#353535] font-black text-xs">
                            <svg class="w-4 h-4 text-[#3C6E71]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>داشبورد من ({{ $customer->name }})</span>
                        </a>
                        <a href="{{ route('customer.orders') }}" class="flex items-center gap-3 p-3 rounded-xl bg-[#F7F9FA]/30 text-[#353535] font-bold text-xs">
                            <svg class="w-4 h-4 text-[#3C6E71]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <span>سفارشات من</span>
                        </a>
                    @else
                        <a href="{{ route('customer.login') }}" class="flex items-center gap-3 p-3 rounded-xl bg-[#353535] text-[#F7F9FA] font-black text-xs shadow-xs">
                            <svg class="w-4 h-4 text-[#3C6E71]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            <span>ورود به حساب کاربری</span>
                        </a>
                    @endif
                </nav>
            </div>

            <!-- Drawer Shiraz Info Footer -->
            <div class="pt-6 border-t border-[#353535]/10 space-y-2 text-xs text-[#353535]/70">
                <div class="flex items-center gap-2 text-[#353535] font-bold">
                    <svg class="w-4 h-4 text-[#3C6E71]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>ساعات کاری: {{ ((($cafe ?? false) instanceof \App\Models\Cafe) ? $cafe->getFormattedShiftHours() : '۰۸:۰۰ الی ۲۲:۰۰') }}</span>
                </div>
                <div class="flex items-center gap-2 text-[#353535] font-bold">
                    <svg class="w-4 h-4 text-[#284B63]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <span>پشتیبانی شیراز: ۰۷۱-۳۶۲۸۰۰۰۰</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Menu Sections -->
    <main class="max-w-6xl mx-auto px-4 sm:px-6 pt-6 space-y-10">

        <!-- Luxury Hero Banner (Black Forest & Copperwood) -->
        <section id="cafe-features" class="relative overflow-hidden p-6 sm:p-10 rounded-3xl bg-gradient-to-br from-[#353535] via-[#1E2912] to-[#141C0C] text-[#F7F9FA] shadow-xl border border-[#3C6E71]/25">
            <!-- Decorative Glow Rings -->
            <div class="absolute -end-16 -top-16 w-64 h-64 rounded-full bg-[#284B63]/20 blur-3xl pointer-events-none"></div>
            <div class="absolute -start-16 -bottom-16 w-64 h-64 rounded-full bg-[#3C6E71]/20 blur-3xl pointer-events-none"></div>

            <div class="relative z-10 space-y-4 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-[#284B63] text-[#F7F9FA] text-xs font-black shadow-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    <span>بار تخصصی قهوه بی کافه شیراز</span>
                </div>

                <h2 class="text-2xl sm:text-4xl font-black text-[#F7F9FA] leading-tight tracking-tight">
                    طعم اصیل قهوه تازه‌برشت با عصاره‌گیری استاندارد
                </h2>

                <p class="text-xs sm:text-sm text-[#F7F9FA]/85 leading-relaxed font-medium">
                    تجربه سرو روی میز در فضای آرام کافه، یا تحویل داغ و سریع با پیک اختصاصی در تمام مناطق شیراز. با اشتراک‌های ماهانه قهوه، از تخفیف‌های ویژه و سرو روزانه بهره‌مند شوید.
                </p>

                <!-- Feature Badges (Strict Zero Emojis) -->
                <div class="pt-2 flex flex-wrap items-center gap-3">
                    <div class="flex items-center gap-2 text-xs font-bold bg-white/10 px-3.5 py-2 rounded-xl border border-white/15 backdrop-blur-xs">
                        <svg class="w-4 h-4 text-[#3C6E71]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>آماده‌سازی فوری (۱۰ تا ۱۵ دقیقه)</span>
                    </div>

                    <div class="flex items-center gap-2 text-xs font-bold bg-white/10 px-3.5 py-2 rounded-xl border border-white/15 backdrop-blur-xs">
                        <svg class="w-4 h-4 text-[#3C6E71]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                        <span>پیک اختصاصی و بسته‌بندی گرم در شیراز</span>
                    </div>

                    <div class="flex items-center gap-2 text-xs font-bold bg-white/10 px-3.5 py-2 rounded-xl border border-white/15 backdrop-blur-xs">
                        <svg class="w-4 h-4 text-[#3C6E71]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>۵٪ کش‌بک در کیف‌پول باشگاه مشتریان</span>
                    </div>
                </div>

                <!-- CTAs -->
                <div class="pt-3 flex flex-wrap items-center gap-3">
                    <a href="#menu-catalog" class="px-6 py-3 bg-[#284B63] hover:bg-[#9B561B] text-[#F7F9FA] text-xs sm:text-sm font-black rounded-xl btn-tactile shadow-md flex items-center gap-2">
                        <span>مشاهده منو و ثبت سفارش</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </a>
                    <a href="#coffee-subscriptions" class="px-5 py-3 bg-white/10 hover:bg-white/20 text-[#F7F9FA] text-xs sm:text-sm font-bold rounded-xl btn-tactile border border-white/20 flex items-center gap-2">
                        <span>پلن‌های اشتراک ماهانه</span>
                    </a>
                </div>
            </div>
        </section>

        <!-- Coffee Subscriptions Showcase Section -->
        <section id="coffee-subscriptions" class="space-y-4 pt-2 scroll-mt-36">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#353535]/10 pb-3">
                <div>
                    <h3 class="text-lg font-black text-[#353535] flex items-center gap-2.5">
                        <span class="w-2.5 h-5 rounded-full bg-[#284B63]"></span>
                        <span>پلن‌های اشتراک روزانه قهوه بی کافه</span>
                    </h3>
                    <p class="text-xs text-[#353535]/65 mt-0.5">یک‌بار اشتراک بخرید، هر روز یک فنجان قهوه تازه با تخفیف ویژه و بدون معطلی میل کنید.</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('storefront.subscriptions.index') }}" class="text-xs font-black text-[#387780] hover:underline flex items-center gap-1">
                        <span>مشاهده صفحه کامل و محاسبه‌گر سود</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    <span class="px-3 py-1 rounded-xl bg-[#3C6E71]/15 text-[#353535] text-xs font-bold">صرفه‌جویی تا ۳۰٪</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                @forelse($subscriptionPlans as $plan)
                    @php
                        if (!is_object($plan)) continue;
                        $pricePerCup = ($plan->total_items ?? 0) > 0 ? round(($plan->price ?? 0) / $plan->total_items) : 0;
                    @endphp
                    <div class="bg-white border-2 border-[#353535]/12 hover:border-[#3C6E71] rounded-3xl p-6 shadow-xs flex flex-col justify-between space-y-5 transition-all group card-elevated">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="px-3 py-1 rounded-xl bg-[#F7F9FA] text-[#284B63] font-black text-[11px] border border-[#284B63]/20">
                                    {{ $plan->duration_days }} روزه
                                </span>
                                <span class="text-xs font-black text-[#3C6E71] tabular-nums">
                                    {{ $plan->total_items }} فنجان قهوه
                                </span>
                            </div>

                            <h4 class="text-base font-black text-[#353535] group-hover:text-[#3C6E71] transition-colors">{{ $plan->name }}</h4>

                            <div class="p-3 rounded-2xl bg-[#F7F9FA]/40 border border-[#353535]/8 space-y-1">
                                <div class="flex items-baseline justify-between">
                                    <span class="text-xs text-[#353535]/60 font-medium">مبلغ کل پلن:</span>
                                    <span class="text-base font-black text-[#284B63] tabular-nums">{{ number_format($plan->price) }} تومان</span>
                                </div>
                                <div class="flex items-baseline justify-between text-[11px]">
                                    <span class="text-[#353535]/50">هر فنجان حدوداً:</span>
                                    <span class="font-bold text-[#3C6E71] tabular-nums">{{ number_format($pricePerCup) }} تومان</span>
                                </div>
                            </div>

                            <ul class="space-y-2 text-xs text-[#353535]/75 pt-1">
                                <li class="flex items-center gap-2 font-medium">
                                    <svg class="w-4 h-4 text-[#3C6E71] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>سهمیه روزانه ۱ فنجان قهوه تخصصی</span>
                                </li>
                                <li class="flex items-center gap-2 font-medium">
                                    <svg class="w-4 h-4 text-[#3C6E71] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>امکان سرو حضوری در کافه یا ارسال با پیک</span>
                                </li>
                                <li class="flex items-center gap-2 font-medium">
                                    <svg class="w-4 h-4 text-[#3C6E71] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>اولویت در آماده‌سازی بار قهوه</span>
                                </li>
                                <li class="flex items-center gap-2 font-medium">
                                    <svg class="w-4 h-4 text-[#3C6E71] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>۵٪ کش‌بک خودکار در کیف‌پول</span>
                                </li>
                            </ul>
                        </div>

                        <div class="pt-3 border-t border-[#353535]/8 space-y-2">
                            <button type="button"
                                    @click="addPlanToCart({{ json_encode([
                                        'id' => $plan->id,
                                        'name' => $plan->name,
                                        'price' => (float)$plan->price,
                                        'duration_days' => $plan->duration_days,
                                        'total_items' => $plan->total_items,
                                    ]) }})"
                                    class="w-full py-3 bg-[#3C6E71] hover:bg-[#2E585B] text-white font-black rounded-xl text-xs btn-tactile shadow-xs flex items-center justify-center gap-2 min-h-[44px] cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                <span x-text="isPlanInCart({{ $plan->id }}) ? 'افزوده شد (' + getPlanQuantity({{ $plan->id }}) + ' در سبد)' : 'افزودن به سبد خرید'"></span>
                            </button>
                            <a href="{{ route('storefront.subscriptions.show', $plan) }}" class="w-full py-2 bg-[#F7F9FA] hover:bg-[#E2E8F0] text-[#353535] font-bold rounded-xl text-xs btn-tactile border border-[#353535]/10 flex items-center justify-center">
                                مشاهده جزئیات کامل پلن
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full p-8 bg-white rounded-3xl border border-[#353535]/10 text-center text-xs text-[#353535]/60 font-bold">
                        به زودی پلن‌های جدید اشتراک قهوه فعال خواهد شد.
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Sticky Categories Horizontal Pill Nav (Pinned directly above Menu Items) -->
        <nav class="sticky top-16 md:top-20 z-30 bg-white/95 backdrop-blur-md border-y border-[#353535]/10 shadow-xs py-2.5 -mx-4 sm:-mx-6 px-4 sm:px-6">
            <div class="max-w-6xl mx-auto flex items-center gap-2 overflow-x-auto custom-scrollbar">
                <a href="#coffee-subscriptions"
                   class="px-3.5 py-2 rounded-xl text-xs font-black whitespace-nowrap btn-tactile transition-all min-h-[38px] flex items-center gap-1.5 bg-teal-50 text-[#387780] border border-teal-200 hover:bg-teal-100 flex-shrink-0">
                    <svg class="w-3.5 h-3.5 inline-block text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    <span>اشتراک‌های قهوه</span>
                    <span class="px-1.5 py-0.5 rounded bg-[#387780] text-white text-[10px]">ویژه</span>
                </a>
                @foreach($categories as $category)
                    <a href="#cat-{{ $category->id }}"
                       class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap btn-tactile transition-all min-h-[38px] flex items-center gap-1.5 bg-white text-[#353535] border border-[#353535]/15 hover:bg-[#F7F9FA] hover:border-[#3C6E71] flex-shrink-0 shadow-2xs">
                        @if($category->icon_svg)
                            <div class="w-3.5 h-3.5 flex items-center justify-center [&>svg]:w-3.5 [&>svg]:h-3.5 flex-shrink-0">{!! $category->icon_svg !!}</div>
                        @else
                            <svg class="w-3.5 h-3.5 text-[#284B63]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8zM6 1v3M10 1v3M14 1v3"/></svg>
                        @endif
                        <span>{{ $category->name }}</span>
                        <span class="text-[10px] opacity-70 tabular-nums">({{ $category->products->count() }})</span>
                    </a>
                @endforeach
            </div>
        </nav>

        <!-- Live Instant Search Bar -->
        <section id="menu-catalog" class="space-y-4 pt-2">
            <div class="relative">
                <input type="text"
                       id="menu-search-input"
                       aria-label="جستجوی منوی بی کافه"
                       x-model="searchQuery"
                       placeholder="جستجوی نام قهوه، نوشیدنی، کیک یا دسر..."
                       class="w-full px-5 py-3.5 pe-12 rounded-2xl bg-white border border-[#353535]/15 text-[#353535] placeholder-[#353535]/45 text-xs sm:text-sm font-bold focus:outline-none focus:border-[#3C6E71] focus:ring-1 focus:ring-[#3C6E71] shadow-xs transition-all min-h-[48px]">
                <div class="absolute end-4 top-3.5 text-[#3C6E71] pointer-events-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <!-- SVG Clear Button (Zero Emojis) -->
                <button type="button" x-show="searchQuery" @click="searchQuery = ''"
                        title="پاک کردن جستجو"
                        aria-label="پاک کردن متن جستجو"
                        class="absolute start-3 top-2.5 w-9 h-9 min-h-[36px] min-w-[36px] rounded-lg text-[#353535]/60 hover:text-[#B44A4A] hover:bg-[#B44A4A]/10 flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </section>

        <!-- Product Categories Loop -->
        @foreach($categories as $category)
            <section id="cat-{{ $category->id }}" class="space-y-4 pt-2 scroll-mt-36">
                <div class="flex items-center justify-between border-b border-[#353535]/10 pb-3">
                    <h3 class="text-base font-black text-[#353535] flex items-center gap-2.5">
                        <span class="w-2.5 h-5 rounded-full bg-[#284B63]"></span>
                        @if($category->icon_svg)
                            <span class="w-5 h-5 flex items-center justify-center text-[#284B63] [&>svg]:w-5 [&>svg]:h-5">{!! $category->icon_svg !!}</span>
                        @endif
                        <span>{{ $category->name }}</span>
                    </h3>
                    <span class="text-xs text-[#353535]/60 font-bold tabular-nums">{{ $category->products->count() }} آیتم</span>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 items-stretch">
                    @foreach($category->products as $product)
                        @php
                            $hasHourlyDiscount = $product->isHourlyDiscountActive();
                            $effectivePrice = (float) $product->effective_price;
                            $productData = [
                                'id' => $product->id,
                                'name' => $product->name,
                                'category_id' => $product->category_id,
                                'category_name' => $category->name,
                                'price' => $effectivePrice,
                                'original_price' => (float)$product->price,
                                'has_discount' => $hasHourlyDiscount,
                                'discount_percent' => $product->discount_percent,
                                'description' => $product->description ?? 'تهیه شده از مرغوب‌ترین دانه‌های قهوه تخصصی روز بی کافه با عصاره‌گیری استاندارد باریستا.',
                                'image' => $product->image,
                                'type' => $product->type,
                                'prep_time' => $product->prep_time ?? 15,
                                'tasting_notes' => $product->tasting_notes ?? 'شکلاتی، فندقی با بافت مخملی',
                                'bean_type' => $product->bean_type ?? '۱۰۰٪ عربیکا تخصصی بی کافه',
                                'serving_type' => $product->serving_type ?? 'سرو حضوری فنجان سرامیکی / بیرون‌بر',
                                'addons' => $product->addons ?? [],
                            ];
                        @endphp
                        <div x-show="matchesSearch('{{ addslashes($product->name) }}')"
                             class="h-full flex flex-col">
                            <x-product-card :product="$product"
                                            :category-name="$category->name"
                                            :is-favorite="in_array($product->id, $customerFavoriteIds ?? [])"
                                            class="h-full flex-1" />
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach

    </main>

    <!-- Floating Sticky Cart Bottom Bar (Glass Pill Capsule) -->
    <div x-show="cartTotalCount > 0"
         x-transition:enter="transition ease-out duration-250"
         x-transition:enter-start="transform translate-y-full opacity-0"
         x-transition:enter-end="transform translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-180"
         x-transition:leave-start="transform translate-y-0 opacity-100"
         x-transition:leave-end="transform translate-y-full opacity-0"
         class="fixed bottom-20 md:bottom-6 inset-x-3 sm:inset-x-auto sm:start-1/2 sm:-translate-x-1/2 z-30 max-w-xl w-[calc(100%-1.5rem)] sm:w-full">
        <div class="bg-[#353535]/95 backdrop-blur-md text-[#F7F9FA] border border-[#3C6E71]/40 rounded-3xl p-3 sm:p-4 shadow-2xl flex items-center justify-between gap-3">
            <a href="{{ route('cart.index') }}" @click="syncToStorage()" class="flex items-center gap-3 min-w-0 hover:opacity-90 transition-opacity">
                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-[#3C6E71] text-[#F7F9FA] flex items-center justify-center font-black text-xs sm:text-sm shadow-xs border border-[#F7F9FA]/20 flex-shrink-0">
                    <span class="tabular-nums" x-text="cartTotalCount"></span>
                </div>
                <div class="min-w-0">
                    <span class="text-[11px] sm:text-xs text-[#F7F9FA]/70 block font-bold truncate">مجموع اقلام (<span class="underline">مشاهده سبد</span>):</span>
                    <span class="text-xs sm:text-base font-black text-[#3C6E71] tabular-nums" x-text="cartTotalPrice.toLocaleString('fa-IR') + ' تومان'"></span>
                </div>
            </a>

            <!-- Proceed to Checkout Button -->
            <div class="flex items-center gap-2 flex-shrink-0">
                <a href="{{ route('cart.index') }}" @click="syncToStorage()"
                   class="hidden sm:inline-flex px-3.5 py-2.5 bg-white/10 hover:bg-white/20 text-[#F7F9FA] font-bold rounded-xl text-xs btn-tactile border border-white/15">
                    سبد خرید
                </a>
                <a href="{{ route('cart.checkout') }}" @click="syncToStorage()"
                   class="px-4 sm:px-6 py-2.5 sm:py-3 bg-[#284B63] hover:bg-[#9B561B] text-[#F7F9FA] font-black rounded-2xl text-xs sm:text-sm btn-tactile shadow-md flex items-center gap-1.5 min-h-[44px]">
                    <span>تکمیل و پرداخت</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Mobile Bottom Navigation Bar -->
    <x-mobile-nav :active="'menu'"/>

    <!-- Dynamic Product Detail Popup Page / Modal (Editable Specs from DB) -->
    <div x-show="productModalOpen"
         x-transition:enter="transition ease-out duration-250"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/65 backdrop-blur-xs"
         style="display: none;">

        <div @click.outside="closeProductModal()"
             x-show="productModalOpen"
             x-transition:enter="transition ease-out duration-250"
             x-transition:enter-start="transform translate-y-full sm:translate-y-4 sm:scale-95 opacity-0"
             x-transition:enter-end="transform translate-y-0 sm:scale-100 opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="transform translate-y-0 sm:scale-100 opacity-100"
             x-transition:leave-end="transform translate-y-full sm:translate-y-4 sm:scale-95 opacity-0"
             class="w-full max-w-lg bg-white rounded-t-3xl sm:rounded-3xl shadow-2xl border border-[#353535]/15 overflow-hidden flex flex-col max-h-[92vh] sm:max-h-[88vh]">

            <!-- Modal Visual Header -->
            <div class="relative h-44 sm:h-52 bg-gradient-to-br from-[#353535] via-[#1E2912] to-[#141C0C] text-[#F7F9FA] p-5 flex flex-col justify-between overflow-hidden flex-shrink-0">
                <!-- Warm Glow Ambient Circle -->
                <div class="absolute -end-10 -top-10 w-44 h-44 rounded-full bg-[#284B63]/20 blur-2xl pointer-events-none"></div>

                <!-- Top Bar in Modal Header: Badge & Close Button -->
                <div class="relative z-10 flex items-center justify-between">
                    <span class="px-3 py-1 rounded-xl bg-[#3C6E71]/20 text-[#3C6E71] text-xs font-black border border-[#3C6E71]/30"
                          x-text="modalProduct ? modalProduct.category_name : ''"></span>

                    <button type="button" @click="closeProductModal()"
                            aria-label="بستن پنجره"
                            class="w-11 h-11 rounded-2xl bg-white/10 hover:bg-white/20 text-[#F7F9FA] flex items-center justify-center btn-tactile border border-white/15">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Product Title and Image Header -->
                <div class="relative z-10 flex items-center gap-3.5">
                    <template x-if="modalProduct && modalProduct.image">
                        <img :src="modalProduct.image" :alt="modalProduct.name" class="w-16 h-16 rounded-2xl object-cover border-2 border-white/20 shadow-lg flex-shrink-0" x-on:error="modalProduct.image = null">
                    </template>
                    <template x-if="!modalProduct || !modalProduct.image">
                        <div class="w-14 h-14 rounded-2xl bg-[#F7F9FA]/15 border border-[#F7F9FA]/25 flex items-center justify-center text-[#C8A35A] shadow-lg flex-shrink-0">
                            <!-- Cake / Dessert -->
                            <template x-if="modalProduct && (modalProduct.category_id == 3 || (modalProduct.name && (modalProduct.name.includes('کیک') || modalProduct.name.includes('کوکی') || modalProduct.name.includes('چیزکیک'))))">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.27 6.96L12 12.01l8.73-5.05M12 22.08V12"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v3m0 0l-1.5-1M12 6l1.5-1"/></svg>
                            </template>
                            <!-- Cold Drink -->
                            <template x-if="modalProduct && (modalProduct.category_id == 2 || (modalProduct.name && (modalProduct.name.includes('سرد') || modalProduct.name.includes('شیک') || modalProduct.name.includes('آیس'))))">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 3h12l-1.5 16.5a2 2 0 01-2 1.5h-5a2 2 0 01-2-1.5L6 3z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 7h14M15 2l2-1"/></svg>
                            </template>
                            <!-- Specialty Coffee Beans / V60 -->
                            <template x-if="modalProduct && (modalProduct.category_id == 5 || (modalProduct.name && (modalProduct.name.includes('دانه') || modalProduct.name.includes('تخصصی') || modalProduct.name.includes('V60'))))">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 100-6 3 3 0 000 6z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 4h18l-2 4H5L3 4z"/></svg>
                            </template>
                            <!-- Default Hot Coffee -->
                            <template x-if="!modalProduct || (![2, 3, 5].includes(Number(modalProduct.category_id)) && !(modalProduct.name && (modalProduct.name.includes('کیک') || modalProduct.name.includes('سرد') || modalProduct.name.includes('شیک') || modalProduct.name.includes('دانه'))))">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8zM6 1v3M10 1v3M14 1v3"/></svg>
                            </template>
                        </div>
                    </template>
                    <div class="min-w-0 flex-1">
                        <span class="text-xs text-[#3C6E71] font-bold block">بی کافه شیراز • منوی اختصاصی</span>
                        <h3 class="text-lg sm:text-xl font-black text-[#F7F9FA] leading-tight truncate" x-text="modalProduct ? modalProduct.name : ''"></h3>
                    </div>
                </div>
            </div>

            <!-- Scrollable Content Body -->
            <div class="p-5 sm:p-6 overflow-y-auto space-y-5 custom-scrollbar flex-1">
                <!-- Title & Price Header -->
                <div class="flex items-center justify-between border-b border-[#353535]/10 pb-3.5">
                    <div>
                        <h2 class="text-xl font-black text-[#353535]" x-text="modalProduct ? modalProduct.name : ''"></h2>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs text-[#353535]/60 font-bold" x-text="modalProduct ? modalProduct.category_name : ''"></span>
                            <template x-if="modalProduct && modalProduct.stock !== null && modalProduct.stock !== undefined">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md"
                                      :class="modalProduct.stock > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'"
                                      x-text="modalProduct.stock > 0 ? ('موجودی انبار: ' + modalProduct.stock + ' عدد') : 'ناموجود'"></span>
                            </template>
                        </div>
                    </div>

                    <div class="text-end">
                        <span class="text-xs text-[#353535]/60 block font-medium">قیمت پایه:</span>
                        <template x-if="modalProduct && modalProduct.has_discount">
                            <div>
                                <span class="line-through text-xs text-slate-400 tabular-nums block" x-text="modalProduct.original_price ? (modalProduct.original_price.toLocaleString('fa-IR') + ' تومان') : ''"></span>
                                <span class="text-lg font-black text-rose-600 tabular-nums"
                                      x-text="modalUnitPrice.toLocaleString('fa-IR') + ' تومان'"></span>
                                <template x-if="modalProduct.has_discount">
                                    <div class="mt-1 flex items-center justify-end" dir="rtl">
                                        <template x-if="modalCountdownFormatted">
                                            <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-rose-50 text-rose-700 border border-rose-200/90 font-mono text-[11px] font-bold select-none" dir="rtl">
                                                <svg class="w-3 h-3 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span class="text-[9px] font-sans font-bold text-rose-600">مهلت:</span>
                                                <span dir="ltr" class="tabular-nums font-black font-mono tracking-tight" x-text="modalCountdownFormatted"></span>
                                            </div>
                                        </template>
                                        <template x-if="!modalCountdownFormatted && modalProduct.discount_remaining_human">
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-rose-700 bg-rose-50 border border-rose-200 px-1.5 py-0.5 rounded-md">
                                                <svg class="w-2.5 h-2.5 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span x-text="modalProduct.discount_remaining_human"></span>
                                            </span>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template x-if="!modalProduct || !modalProduct.has_discount">
                            <span class="text-lg font-black text-[#284B63] tabular-nums"
                                  x-text="modalProduct ? (modalUnitPrice.toLocaleString('fa-IR') + ' تومان') : ''"></span>
                        </template>
                    </div>
                </div>

                <!-- Product Full Description Box -->
                <div class="space-y-1.5">
                    <h4 class="text-xs font-black text-[#353535]">توضیحات و مشخصات طعم‌یادها:</h4>
                    <div class="p-3.5 rounded-2xl bg-[#F7F9FA]/50 border border-[#353535]/10 text-xs sm:text-sm text-[#353535]/80 leading-relaxed font-medium"
                         x-text="modalProduct ? modalProduct.description : ''"></div>
                </div>

                <!-- Dynamic Service Specifications Matrix (Pure SVGs) -->
                <div class="grid grid-cols-2 gap-2.5 text-xs">
                    <!-- Bean Type -->
                    <div class="p-3 rounded-xl bg-white border border-[#353535]/10 flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-[#F7F9FA] flex items-center justify-center text-[#284B63] flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[10px] text-[#353535]/60 block">نوع دانه:</span>
                            <span class="font-bold text-[#353535] truncate block" x-text="modalProduct ? modalProduct.bean_type : 'عربیکا تخصصی'"></span>
                        </div>
                    </div>

                    <!-- Prep Time -->
                    <div class="p-3 rounded-xl bg-white border border-[#353535]/10 flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-[#F7F9FA] flex items-center justify-center text-[#3C6E71] flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[10px] text-[#353535]/60 block">زمان آماده‌سازی:</span>
                            <span class="font-bold text-[#353535] truncate block" x-text="modalProduct ? (modalProduct.prep_time + ' دقیقه') : '۱۵ دقیقه'"></span>
                        </div>
                    </div>

                    <!-- Courier Delivery -->
                    <div class="p-3 rounded-xl bg-white border border-[#353535]/10 flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-[#F7F9FA] flex items-center justify-center text-[#284B63] flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[10px] text-[#353535]/60 block">ارسال با پیک:</span>
                            <span class="font-bold text-[#353535] truncate block">بسته‌بندی ایزوله گرم</span>
                        </div>
                    </div>

                    <!-- Serving Type -->
                    <div class="p-3 rounded-xl bg-white border border-[#353535]/10 flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-[#F7F9FA] flex items-center justify-center text-[#353535] flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8zM6 1v3M10 1v3M14 1v3"/></svg>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[10px] text-[#353535]/60 block">نحوه سرو:</span>
                            <span class="font-bold text-[#353535] truncate block" x-text="modalProduct ? modalProduct.serving_type : 'حضوری / بیرون‌بر'"></span>
                        </div>
                    </div>
                </div>

                <!-- Admin-Configured Add-ons & Customizations Picker -->
                <template x-if="modalProduct && modalProduct.addons && modalProduct.addons.length > 0">
                    <div class="space-y-2.5 p-3.5 rounded-2xl bg-[#F7F9FA]/40 border border-[#353535]/10">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-black text-[#353535] flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-[#284B63]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                <span>افزودنی‌های دلخواه این آیتم:</span>
                            </label>
                            <span class="text-[11px] text-[#353535]/50">امکان انتخاب چند مورد</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <template x-for="(addon, idx) in modalProduct.addons" :key="idx">
                                <div @click="toggleAddon(addon)"
                                     :class="isAddonSelected(addon) ? 'border-[#284B63] bg-[#284B63]/10 ring-1 ring-[#284B63]' : 'border-[#353535]/15 bg-white hover:border-[#3C6E71]/40'"
                                     class="flex items-center justify-between p-2.5 rounded-xl border cursor-pointer select-none transition-all">
                                    <div class="flex items-center gap-2">
                                        <div :class="isAddonSelected(addon) ? 'bg-[#284B63] text-white border-[#284B63]' : 'bg-white border-[#353535]/25'"
                                             class="w-4 h-4 rounded border flex items-center justify-center transition-colors">
                                            <svg x-show="isAddonSelected(addon)" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                        <span class="text-xs font-bold text-[#353535]" x-text="addon.name"></span>
                                    </div>
                                    <span class="text-[11px] font-black text-[#284B63] tabular-nums" x-text="'+ ' + Number(addon.price).toLocaleString('fa-IR') + ' ت'"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- Customer Customization Note for Barista -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-black text-[#353535] flex items-center justify-between">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-[#284B63]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>یادداشت اختصاصی به باریستا (اختیاری):</span>
                        </span>
                        <span class="text-[11px] text-[#353535]/50 font-normal">کم‌شکر، بدون یخ، فوم بیشتر...</span>
                    </label>
                    <input type="text"
                           x-model="modalNote"
                           placeholder="توضیحات خاص نحوه آماده‌سازی یا سرو این آیتم..."
                           class="w-full px-4 py-3 rounded-xl bg-[#F7F9FA]/40 border border-[#353535]/15 text-xs text-[#353535] placeholder-[#353535]/45 focus:outline-none focus:border-[#3C6E71] focus:bg-white transition-all min-h-[44px]">
                </div>

                <!-- Quantity Stepper Controls -->
                <div class="flex items-center justify-between p-3.5 rounded-2xl bg-[#F7F9FA]/35 border border-[#353535]/10">
                    <div>
                        <span class="text-xs font-black text-[#353535] block">تعداد سفارش:</span>
                        <template x-if="modalProduct && modalProduct.stock !== null && modalProduct.stock !== undefined">
                            <span class="text-[10px] text-slate-500 font-bold"
                                  x-text="'حداکثر قابل سفارش: ' + modalProduct.stock + ' عدد'"></span>
                        </template>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="decrementModalQuantity()"
                                :disabled="modalQuantity <= 1 || (modalProduct && modalProduct.is_out_of_stock)"
                                class="w-11 h-11 rounded-xl bg-white hover:bg-[#B44A4A]/10 text-[#B44A4A] font-bold text-base flex items-center justify-center btn-tactile shadow-2xs border border-[#353535]/10 disabled:opacity-40 disabled:cursor-not-allowed">−</button>
                        <span class="w-8 text-center font-black text-sm text-[#353535] tabular-nums" x-text="modalQuantity"></span>
                        <button type="button" @click="incrementModalQuantity()"
                                :disabled="modalProduct && ((modalProduct.stock !== null && modalQuantity >= modalProduct.stock) || modalProduct.is_out_of_stock)"
                                class="w-11 h-11 rounded-xl bg-[#353535] hover:bg-[#1E2912] text-[#F7F9FA] font-bold text-base flex items-center justify-center btn-tactile shadow-2xs disabled:opacity-40 disabled:cursor-not-allowed">+</button>
                    </div>
                </div>
            </div>

            <!-- Sticky Modal Bottom Action CTA -->
            <div class="p-4 sm:p-5 border-t border-[#353535]/10 bg-white flex items-center justify-between gap-3 flex-shrink-0">
                <button type="button" @click="closeProductModal()"
                        class="px-5 py-3.5 rounded-2xl bg-[#F7F9FA]/70 hover:bg-[#F7F9FA] text-xs font-bold text-[#353535] btn-tactile border border-[#353535]/10 min-h-[48px]">
                    انصراف
                </button>

                <button type="button" @click="confirmModalToCart()"
                        :disabled="modalProduct && modalProduct.is_out_of_stock"
                        :class="modalProduct && modalProduct.is_out_of_stock ? 'bg-slate-300 text-slate-500 cursor-not-allowed shadow-none' : 'bg-[#284B63] hover:bg-[#9B561B] text-[#F7F9FA] shadow-md'"
                        class="flex-1 py-3.5 px-5 font-black rounded-2xl text-xs sm:text-sm btn-tactile flex items-center justify-between min-h-[48px]">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        <span x-text="modalProduct && modalProduct.is_out_of_stock ? 'این آیتم ناموجود است' : 'افزودن به سبد سفارش'"></span>
                    </span>
                    <span class="tabular-nums" x-show="!modalProduct || !modalProduct.is_out_of_stock" x-text="((modalUnitPrice * modalQuantity).toLocaleString('fa-IR')) + ' تومان'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Dedicated Custom Footer -->
    <footer id="footer-contact" class="mt-20 border-t border-[#353535]/15 bg-white pt-12 pb-16">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Brand & Bio -->
                <div class="md:col-span-2 space-y-3.5">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/logo.jpeg') }}" alt="بی کافه" class="w-12 h-12 rounded-2xl object-cover border border-[#353535]/15 shadow-xs">
                        <div>
                            <span class="text-base font-black text-[#353535] block">بی کافه (B Cafe)</span>
                            <span class="text-xs text-[#3C6E71] font-bold">شیراز • بار تخصصی قهوه و منوی روز</span>
                        </div>
                    </div>
                    <p class="text-xs text-[#353535]/70 leading-relaxed font-medium max-w-md">
                        بی کافه، فضایی تخصصی برای علاقه‌مندان به عصاره‌گیری استاندارد، قهوه‌های تک‌خاستگاه و دم‌آوری حرفه‌ای در شهر شیراز. ارائه سرو حضوری با کیفیت بالا و تحویل با پیک اختصاصی در سراسر شهر.
                    </p>
                    <div class="flex items-center gap-3 pt-2">
                        <span class="text-xs font-bold text-[#353535]">ما را دنبال کنید:</span>
                        <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-xl bg-[#F7F9FA] text-[#353535] hover:bg-[#3C6E71] hover:text-[#F7F9FA] flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Shiraz Location & Hours -->
                <div class="space-y-3">
                    <h4 class="text-xs font-black text-[#353535] border-b border-[#353535]/10 pb-2">اطلاعات شعبه شیراز</h4>
                    <div class="space-y-2 text-xs text-[#353535]/75 font-medium">
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-[#284B63] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                            <span>شیراز، خیابان ارم، بین کوچه ۱۴ و ۱۶، پلاک ۲۴</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#3C6E71] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>ساعات سرویس‌دهی: ۸:۰۰ الی ۲۳:۳۰</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#284B63] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <span>تلفن تماس: ۰۷۱-۳۶۲۸۰۰۰۰</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Navigation Links -->
                <div class="space-y-3">
                    <h4 class="text-xs font-black text-[#353535] border-b border-[#353535]/10 pb-2">دسترسی سریع</h4>
                    <ul class="space-y-1.5 text-xs text-[#353535]/75 font-bold">
                        <li><a href="#menu-catalog" class="hover:text-[#284B63] transition-colors">منوی آنلاین و سفارش</a></li>
                        <li><a href="{{ route('storefront.subscriptions.index') }}" class="hover:text-[#284B63] transition-colors">باشگاه اشتراک روزانه</a></li>
                        <li><a href="{{ route('storefront.about') }}" class="hover:text-[#284B63] transition-colors">درباره ما و شعبه شیراز</a></li>
                        <li><a href="{{ route('customer.login') }}" class="hover:text-[#284B63] transition-colors">ورود به حساب کاربری</a></li>
                        <li><a href="{{ route('cart.checkout') }}" class="hover:text-[#284B63] transition-colors">سبد خرید و تسویه</a></li>
                    </ul>
                </div>
            </div>

            <div class="mt-10 pt-6 border-t border-[#353535]/10 flex flex-wrap items-center justify-between gap-4 text-[11px] text-[#353535]/60 font-medium">
                <div>تمامی حقوق این وب‌سایت متعلق به کافه تخصصی بی (B Cafe) شیراز است.</div>
                <div>طراحی و توسعه توسط امیرمحمد بهارلو</div>
            </div>
        </div>
    </footer>

    <!-- Floating Cart Capsule (Mobile & Desktop) -->
    <div x-show="cartTotalCount > 0"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-6 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-6 sm:scale-95"
         style="display: none;"
         class="fixed z-40 inset-x-3 bottom-4 sm:inset-x-auto sm:bottom-6 sm:left-6">
        
        <!-- Mobile View (Full Bar) -->
        <div class="sm:hidden bg-[#353535] text-[#F7F9FA] rounded-2xl shadow-2xl p-3 flex items-center justify-between border border-white/10 backdrop-blur-md">
            <div class="flex items-center gap-3">
                <div class="relative w-10 h-10 rounded-xl bg-[#3C6E71] flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <span class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-amber-400 text-[#353535] font-black text-[11px] flex items-center justify-center tabular-nums" x-text="cartTotalCount"></span>
                </div>
                <div>
                    <div class="text-[11px] text-white/70 font-medium">مجموع سبد خرید</div>
                    <div class="text-xs font-black tabular-nums text-white" x-text="(cartTotalPrice.toLocaleString('fa-IR')) + ' تومان'"></div>
                </div>
            </div>
            <a href="{{ route('cart.index') }}" @click="syncToStorage()"
               class="px-4 py-2.5 rounded-xl bg-[#3C6E71] hover:bg-[#284B63] text-white text-xs font-black flex items-center gap-1.5 btn-tactile shadow-xs">
                <span>تکمیل سفارش</span>
                <svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <!-- Desktop View (Floating Island Capsule) -->
        <div class="hidden sm:flex items-center gap-4 bg-[#353535]/95 text-[#F7F9FA] rounded-full shadow-2xl px-5 py-3 border border-white/15 backdrop-blur-md">
            <div class="flex items-center gap-2.5">
                <div class="relative w-9 h-9 rounded-full bg-[#3C6E71] flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-amber-400 text-[#353535] font-black text-[10px] flex items-center justify-center tabular-nums" x-text="cartTotalCount"></span>
                </div>
                <div class="text-right">
                    <div class="text-[11px] text-white/70 font-medium"><span x-text="cartTotalCount"></span> آیتم در سبد</div>
                    <div class="text-xs font-black tabular-nums text-white" x-text="(cartTotalPrice.toLocaleString('fa-IR')) + ' تومان'"></div>
                </div>
            </div>
            <div class="h-6 w-px bg-white/20"></div>
            <a href="{{ route('cart.index') }}" @click="syncToStorage()"
               class="px-4 py-2 rounded-full bg-[#3C6E71] hover:bg-[#284B63] text-white text-xs font-black flex items-center gap-1.5 btn-tactile shadow-xs transition-colors">
                <span>مشاهده سبد و تسویه</span>
                <svg class="w-3.5 h-3.5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>

    <!-- Alpine.js Storefront Cart & Product Modal Logic -->
    <script>
    function storefrontMenu() {
        return {
            cart: JSON.parse(localStorage.getItem('bcafe_cart') || '[]'),
            searchQuery: '',
            mobileDrawerOpen: false,

            // Product Popup Modal State
            productModalOpen: false,
            modalProduct: null,
            modalQuantity: 1,
            modalNote: '',
            selectedAddons: [],

            init() {
                let isSyncing = false;
                this.$watch('cart', (val) => {
                    if (isSyncing) return;
                    localStorage.setItem('bcafe_cart', JSON.stringify(val));
                    window.dispatchEvent(new CustomEvent('cart-updated', {
                        detail: { source: 'storefrontMenu', cart: val }
                    }));
                });

                window.addEventListener('cart-updated', (e) => {
                    if (e.detail && e.detail.source === 'storefrontMenu') return;
                    isSyncing = true;
                    try {
                        const raw = localStorage.getItem('bcafe_cart');
                        this.cart = raw ? JSON.parse(raw) : [];
                    } catch (err) {}
                    this.$nextTick(() => {
                        isSyncing = false;
                    });
                });
            },

            matchesSearch(name) {
                if (!this.searchQuery) return true;
                return name.toLowerCase().includes(this.searchQuery.toLowerCase());
            },

            modalCountdownFormatted: '',
            modalTimerInterval: null,

            openProductModal(product) {
                this.modalProduct = product;
                this.selectedAddons = [];
                this.modalQuantity = 1;
                this.modalNote = '';
                this.productModalOpen = true;
                document.body.style.overflow = 'hidden';

                if (this.modalTimerInterval) clearInterval(this.modalTimerInterval);
                if (product && product.discount_remaining_seconds && product.discount_remaining_seconds > 0) {
                    let rem = Number(product.discount_remaining_seconds);
                    const updateModalTime = () => {
                        if (rem <= 0) {
                            this.modalCountdownFormatted = '';
                            if (this.modalProduct) {
                                this.modalProduct.has_discount = false;
                                if (this.modalProduct.original_price) {
                                    this.modalProduct.price = this.modalProduct.original_price;
                                    this.modalProduct.original_price = null;
                                }
                            }
                            clearInterval(this.modalTimerInterval);
                            return;
                        }
                        const h = String(Math.floor(rem / 3600)).padStart(2, '0');
                        const m = String(Math.floor((rem % 3600) / 60)).padStart(2, '0');
                        const s = String(rem % 60).padStart(2, '0');
                        this.modalCountdownFormatted = `${h}:${m}:${s}`;
                    };
                    updateModalTime();
                    this.modalTimerInterval = setInterval(() => {
                        rem--;
                        updateModalTime();
                    }, 1000);
                } else {
                    this.modalCountdownFormatted = product?.discount_remaining_human || '';
                }
            },

            closeProductModal() {
                if (this.modalTimerInterval) clearInterval(this.modalTimerInterval);
                this.productModalOpen = false;
                document.body.style.overflow = '';
            },

            isAddonSelected(addon) {
                return this.selectedAddons.some(a => a.name === addon.name);
            },

            toggleAddon(addon) {
                const idx = this.selectedAddons.findIndex(a => a.name === addon.name);
                if (idx > -1) {
                    this.selectedAddons.splice(idx, 1);
                } else {
                    this.selectedAddons.push({
                        name: addon.name,
                        price: Number(addon.price) || 0
                    });
                }
            },

            get modalUnitPrice() {
                const base = this.modalProduct ? (Number(this.modalProduct.price) || 0) : 0;
                const addonsSum = this.selectedAddons.reduce((sum, a) => sum + (Number(a.price) || 0), 0);
                return base + addonsSum;
            },

            incrementModalQuantity() {
                const max = (this.modalProduct && this.modalProduct.stock !== null && this.modalProduct.stock !== undefined) ? this.modalProduct.stock : 99;
                if (this.modalQuantity < max) {
                    this.modalQuantity++;
                }
            },

            decrementModalQuantity() {
                if (this.modalQuantity > 1) {
                    this.modalQuantity--;
                }
            },

            confirmModalToCart() {
                if (!this.modalProduct || this.modalProduct.is_out_of_stock) return;
                const unitPrice = this.modalUnitPrice;
                const addonsArr = [...this.selectedAddons];
                const noteText = this.modalNote ? this.modalNote.trim() : '';

                // Options dictionary for cart view display
                const optionsObj = {};
                if (addonsArr.length > 0) {
                    optionsObj['افزودنی'] = addonsArr.map(a => a.name + ' (+' + Number(a.price).toLocaleString('fa-IR') + ' ت)').join(' | ');
                }

                // Find if identical product with identical addons exists
                const existing = this.cart.find(i => {
                    if ((i.product_id || i.id) !== this.modalProduct.id) return false;
                    const iAddons = (i.addons || []).map(a => a.name).sort().join('|');
                    const mAddons = addonsArr.map(a => a.name).sort().join('|');
                    return iAddons === mAddons && (i.note || '') === noteText;
                });

                const maxStock = (this.modalProduct.stock !== null && this.modalProduct.stock !== undefined) ? this.modalProduct.stock : 99;

                if (existing) {
                    existing.quantity = Math.min(existing.quantity + this.modalQuantity, maxStock);
                } else {
                    this.cart.push({
                        id: this.modalProduct.id,
                        product_id: this.modalProduct.id,
                        name: this.modalProduct.name,
                        base_price: Number(this.modalProduct.price) || 0,
                        price: unitPrice,
                        quantity: Math.min(this.modalQuantity, maxStock),
                        stock: this.modalProduct.stock,
                        addons: addonsArr,
                        options: optionsObj,
                        note: noteText,
                        notes: noteText
                    });
                }
                this.closeProductModal();
            },

            getItemQuantity(productId) {
                return this.cart.filter(i => (i.product_id === productId || i.id === productId)).reduce((sum, i) => sum + i.quantity, 0);
            },

            increment(productId) {
                const item = this.cart.find(i => (i.product_id === productId || i.id === productId));
                if (item) {
                    const maxStock = (item.stock !== null && item.stock !== undefined) ? item.stock : 99;
                    if (item.quantity < maxStock) {
                        item.quantity++;
                    }
                }
            },

            decrement(productId) {
                const idx = this.cart.findIndex(i => (i.product_id === productId || i.id === productId));
                if (idx > -1) {
                    if (this.cart[idx].quantity > 1) {
                        this.cart[idx].quantity--;
                    } else {
                        this.cart.splice(idx, 1);
                    }
                }
            },

            get cartTotalCount() {
                return this.cart.reduce((sum, i) => sum + i.quantity, 0);
            },

            get cartTotalPrice() {
                return this.cart.reduce((sum, i) => sum + (i.price * i.quantity), 0);
            },

            addPlanToCart(plan) {
                const planId = plan.id;
                const existing = this.cart.find(i => (i.type === 'subscription' || i.subscription_plan_id === planId) && (i.id === 'sub-' + planId || i.subscription_plan_id === planId));
                if (existing) {
                    existing.quantity++;
                } else {
                    this.cart.push({
                        id: 'sub-' + planId,
                        product_id: null,
                        type: 'subscription',
                        subscription_plan_id: planId,
                        name: plan.name + ' (اشتراک قهوه)',
                        price: Number(plan.price) || 0,
                        base_price: Number(plan.price) || 0,
                        quantity: 1,
                        options: {
                            'مدت اعتبار': plan.duration_days + ' روز',
                            'سهمیه کل': plan.total_items + ' فنجان قهوه'
                        },
                        note: 'پلن اشتراک روزانه'
                    });
                }
                if (window.CafeToast) {
                    window.CafeToast.success('پلن «' + plan.name + '» به سبد خرید افزوده شد.', 'سبد خرید');
                }
            },

            isPlanInCart(planId) {
                return this.cart.some(i => (i.type === 'subscription' || i.subscription_plan_id === planId) && (i.id === 'sub-' + planId || i.subscription_plan_id === planId));
            },

            getPlanQuantity(planId) {
                const item = this.cart.find(i => (i.type === 'subscription' || i.subscription_plan_id === planId) && (i.id === 'sub-' + planId || i.subscription_plan_id === planId));
                return item ? item.quantity : 0;
            },

            syncToStorage() {
                localStorage.setItem('bcafe_cart', JSON.stringify(this.cart));
            }
        }
    }
    </script>

    @include('partials.pwa-prompt', ['pwaPanel' => 'customer'])

    <x-preloader :global="true" />
    <x-toast-container />
</body>
</html>
