<!DOCTYPE html>
<html lang="fa" dir="rtl" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'بی کافه') }} - @yield('title', 'پنل مشتریان')</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}?v=2.0.2">
    <link rel="alternate icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2.0.2">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}?v=2.0.2">

    @include('partials.pwa-meta', ['pwaPanel' => 'customer'])

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="h-full antialiased selection:bg-[#3C6E71] selection:text-white bg-[#F7F9FA] text-[#353535]" x-data="{ sidebarOpen: false }">

    @include('partials.page-preloader')

    <!-- Mobile Top Header Bar -->
    <header class="md:hidden bg-white border-b border-[#D9D9D9] sticky top-0 z-30 px-4 h-16 flex items-center justify-between shadow-2xs">
        <div class="flex items-center gap-3">
            <button type="button"
                    @click="sidebarOpen = true"
                    class="p-2 text-[#353535] hover:bg-[#F7F9FA] rounded-xl transition-colors btn-tactile min-w-[44px] min-h-[44px] flex items-center justify-center border border-[#D9D9D9]/80"
                    aria-label="باز کردن منو">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-2">
                <img src="{{ asset('images/logo.jpeg') }}" alt="بی کافه" class="w-8 h-8 rounded-xl object-cover border border-[#D9D9D9]">
                <div>
                    <span class="font-black text-xs text-[#353535] block leading-tight">بی کافه (B Cafe)</span>
                    <span class="text-[10px] text-[#3C6E71] font-bold">حساب کاربری</span>
                </div>
            </a>
        </div>

        <div class="flex items-center gap-2">
            @if(auth()->check() && (auth()->user()->role === 'admin' || (method_exists(auth()->user(), 'isAdmin') && auth()->user()->isAdmin())))
                <a href="{{ route('dashboard') }}" class="px-2.5 py-1.5 bg-[#3C6E71] hover:bg-[#284B63] text-white rounded-xl text-[11px] font-black btn-tactile flex items-center gap-1 shadow-2xs transition-colors">
                    <svg class="w-3.5 h-3.5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>پنل مدیریت</span>
                </a>
            @endif
            <a href="{{ route('storefront.menu') }}" class="px-2.5 py-1.5 bg-[#284B63] text-white rounded-xl text-[11px] font-bold btn-tactile flex items-center gap-1 shadow-2xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                <span>سفارش</span>
            </a>
            @if(auth('customer')->user()->tier ?? false)
                <span class="px-2 py-1 rounded-lg text-[10px] font-black text-white shadow-2xs" style="background-color: {{ auth('customer')->user()->tier->badge_color }}">
                    {{ auth('customer')->user()->tier->name }}
                </span>
            @endif
        </div>
    </header>

    <!-- Mobile Drawer Backdrop -->
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-out duration-250"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 md:hidden"
         style="display: none;"></div>

    <!-- Mobile Slide-over Drawer -->
    <aside x-show="sidebarOpen"
           x-transition:enter="transition ease-out duration-250 transform"
           x-transition:enter-start="translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in duration-200 transform"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="translate-x-full"
           class="fixed inset-y-0 start-0 z-50 w-72 sm:w-80 bg-white flex flex-col justify-between p-5 shadow-2xl md:hidden overflow-y-auto"
           style="display: none;">
        <div class="space-y-5">
            <!-- Mobile Drawer Header -->
            <div class="flex items-center justify-between border-b border-[#D9D9D9]/70 pb-4">
                <div class="flex items-center gap-2.5">
                    <img src="{{ asset('images/logo.jpeg') }}" alt="بی کافه" class="w-10 h-10 rounded-2xl object-cover border border-[#D9D9D9] shadow-2xs">
                    <div>
                        <span class="font-black text-sm text-[#353535] block">بی کافه (B Cafe)</span>
                        <span class="text-[10px] text-[#3C6E71] font-bold">باشگاه مشتریان و منو</span>
                    </div>
                </div>
                <button type="button" @click="sidebarOpen = false" class="p-2 text-[#353535]/60 hover:text-[#353535] hover:bg-[#F7F9FA] rounded-xl min-w-[40px] min-h-[40px] flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Customer Profile Card -->
            <div class="p-4 rounded-2xl bg-[#F7F9FA] border border-[#D9D9D9] space-y-3 shadow-2xs">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-[#284B63] text-white flex items-center justify-center font-black text-sm shadow-xs flex-shrink-0">
                        {{ mb_substr(auth('customer')->user()->name ?? 'ک', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-1.5">
                            <span class="font-black text-xs text-[#353535] truncate block">{{ auth('customer')->user()->name }}</span>
                            @if(auth('customer')->user()->tier ?? false)
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-black text-white shrink-0" style="background-color: {{ auth('customer')->user()->tier->badge_color }}">
                                    {{ auth('customer')->user()->tier->name }}
                                </span>
                            @endif
                        </div>
                        <span class="text-[11px] text-[#284B63] font-mono block dir-ltr text-right" dir="ltr">{{ auth('customer')->user()->phone }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-[#D9D9D9]/80 text-center">
                    <div class="p-2 bg-white rounded-xl border border-[#D9D9D9]/60">
                        <span class="text-[10px] text-[#353535]/65 block font-bold">کیف‌پول</span>
                        <span class="text-xs font-black text-[#284B63] tabular-nums">{{ number_format(auth('customer')->user()->wallet_balance ?? 0) }}</span>
                        <span class="text-[9px] text-[#353535]/50">تومان</span>
                    </div>
                    <div class="p-2 bg-white rounded-xl border border-[#D9D9D9]/60">
                        <span class="text-[10px] text-[#353535]/65 block font-bold">امتیازها</span>
                        <span class="text-xs font-black text-[#3C6E71] tabular-nums">{{ number_format(auth('customer')->user()->total_loyalty_points ?? 0) }}</span>
                        <span class="text-[9px] text-[#353535]/50">امتیاز</span>
                    </div>
                </div>
            </div>

            <!-- Navigation Menu Items -->
            <nav class="space-y-1">
                <a href="{{ route('customer.dashboard') }}"
                   class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-xs font-black transition-colors min-h-[46px] {{ request()->routeIs('customer.dashboard') ? 'bg-[#3C6E71] text-white shadow-xs' : 'text-[#353535] hover:bg-[#F7F9FA] hover:text-[#284B63]' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>داشبورد</span>
                </a>

                <a href="{{ route('customer.orders') }}"
                   class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-xs font-black transition-colors min-h-[46px] {{ request()->routeIs('customer.orders*') ? 'bg-[#3C6E71] text-white shadow-xs' : 'text-[#353535] hover:bg-[#F7F9FA] hover:text-[#284B63]' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <span>سفارش‌های من</span>
                </a>

                <a href="{{ route('customer.subscriptions') }}"
                   class="flex items-center justify-between px-3.5 py-3 rounded-xl text-xs font-black transition-colors min-h-[46px] {{ request()->routeIs('customer.subscriptions*') ? 'bg-[#3C6E71] text-white shadow-xs' : 'text-[#353535] hover:bg-[#F7F9FA] hover:text-[#284B63]' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <span>اشتراک روزانه قهوه</span>
                    </div>
                    @if(auth('customer')->user()->activeSubscription)
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    @endif
                </a>

                <a href="{{ route('customer.favorites') }}"
                   class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-xs font-black transition-colors min-h-[46px] {{ request()->routeIs('customer.favorites*') ? 'bg-[#3C6E71] text-white shadow-xs' : 'text-[#353535] hover:bg-[#F7F9FA] hover:text-[#284B63]' }}">
                    <svg class="w-5 h-5 flex-shrink-0 text-[#FF7A18]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    <span>علاقه‌مندی‌های من</span>
                </a>

                <a href="{{ route('customer.club') }}"
                   class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-xs font-black transition-colors min-h-[46px] {{ request()->routeIs('customer.club*') ? 'bg-[#3C6E71] text-white shadow-xs' : 'text-[#353535] hover:bg-[#F7F9FA] hover:text-[#284B63]' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>باشگاه و کش‌بک</span>
                </a>

                <a href="{{ route('customer.addresses') }}"
                   class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-xs font-black transition-colors min-h-[46px] {{ request()->routeIs('customer.addresses*') ? 'bg-[#3C6E71] text-white shadow-xs' : 'text-[#353535] hover:bg-[#F7F9FA] hover:text-[#284B63]' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>آدرس‌های من (شیراز)</span>
                </a>

                <a href="{{ route('customer.profile') }}"
                   class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-xs font-black transition-colors min-h-[46px] {{ request()->routeIs('customer.profile*') ? 'bg-[#3C6E71] text-white shadow-xs' : 'text-[#353535] hover:bg-[#F7F9FA] hover:text-[#284B63]' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>مشخصات و کادوی تولد</span>
                </a>

                <a href="{{ route('customer.notifications') }}"
                   class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-xs font-black transition-colors min-h-[46px] {{ request()->routeIs('customer.notifications*') ? 'bg-[#3C6E71] text-white shadow-xs' : 'text-[#353535] hover:bg-[#F7F9FA] hover:text-[#284B63]' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <span>پیام‌ها و اعلان‌ها</span>
                </a>
            </nav>
        </div>

        <!-- Drawer Bottom Actions -->
        <div class="space-y-2 pt-4 border-t border-[#D9D9D9]">
            <a href="{{ route('storefront.menu') }}" class="w-full py-3 bg-[#284B63] hover:bg-[#1E374A] text-white text-xs font-black rounded-xl btn-tactile shadow-xs flex items-center justify-center gap-2 min-h-[46px] transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                <span>سفارش از منو</span>
            </a>

            <form method="POST" action="{{ route('customer.logout') }}">
                @csrf
                <button type="submit" class="w-full py-2.5 text-[#B44A4A] hover:bg-[#B44A4A]/10 text-xs font-black rounded-xl transition-colors btn-tactile min-h-[42px] flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    <span>خروج</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Wrapper (Desktop Sidebar + Main Content Layout) -->
    <div class="flex-1 flex w-full min-h-screen">

        <!-- Desktop Persistent Right Sidebar -->
        <aside class="hidden md:flex flex-col w-64 lg:w-72 bg-white border-s border-[#D9D9D9] sticky top-0 h-screen overflow-y-auto shrink-0 select-none justify-between p-5 shadow-xs z-20">
            <div class="space-y-6">
                <!-- Brand Header -->
                <div class="flex items-center gap-3 pb-5 border-b border-[#D9D9D9]/80">
                    <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-3">
                        <img src="{{ asset('images/logo.jpeg') }}" alt="بی کافه" class="w-12 h-12 rounded-2xl object-cover border border-[#D9D9D9] shadow-xs flex-shrink-0">
                        <div>
                            <span class="font-black text-base text-[#353535] block leading-tight">بی کافه (B Cafe)</span>
                            <span class="text-[11px] text-[#3C6E71] font-bold">باشگاه مشتریان و منو</span>
                        </div>
                    </a>
                </div>

                <!-- Customer Mini Profile Card -->
                <div class="p-4 rounded-2xl bg-[#F7F9FA] border border-[#D9D9D9] space-y-3.5 shadow-2xs">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-[#284B63] text-white flex items-center justify-center font-black text-sm shadow-xs flex-shrink-0">
                            {{ mb_substr(auth('customer')->user()->name ?? 'ک', 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5">
                                <span class="font-black text-xs text-[#353535] truncate block">{{ auth('customer')->user()->name }}</span>
                                @if(auth('customer')->user()->tier ?? false)
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-black text-white shrink-0" style="background-color: {{ auth('customer')->user()->tier->badge_color }}">
                                        {{ auth('customer')->user()->tier->name }}
                                    </span>
                                @endif
                            </div>
                            <span class="text-[11px] text-[#284B63] font-mono block dir-ltr text-right" dir="ltr">{{ auth('customer')->user()->phone }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-[#D9D9D9]/80 text-center">
                        <div class="p-2 bg-white rounded-xl border border-[#D9D9D9]/60">
                            <span class="text-[10px] text-[#353535]/65 block font-bold">کیف‌پول</span>
                            <span class="text-xs font-black text-[#284B63] tabular-nums">{{ number_format(auth('customer')->user()->wallet_balance ?? 0) }}</span>
                            <span class="text-[9px] text-[#353535]/50">تومان</span>
                        </div>
                        <div class="p-2 bg-white rounded-xl border border-[#D9D9D9]/60">
                            <span class="text-[10px] text-[#353535]/65 block font-bold">امتیازها</span>
                            <span class="text-xs font-black text-[#3C6E71] tabular-nums">{{ number_format(auth('customer')->user()->total_loyalty_points ?? 0) }}</span>
                            <span class="text-[9px] text-[#353535]/50">امتیاز</span>
                        </div>
                    </div>
                </div>

                <!-- Navigation Menu Links -->
                <nav class="space-y-1.5">
                    <a href="{{ route('customer.dashboard') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-black transition-all min-h-[44px] {{ request()->routeIs('customer.dashboard') ? 'bg-[#3C6E71] text-white shadow-xs' : 'text-[#353535] hover:bg-[#F7F9FA] hover:text-[#284B63]' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>داشبورد</span>
                    </a>

                    <a href="{{ route('customer.orders') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-black transition-all min-h-[44px] {{ request()->routeIs('customer.orders*') ? 'bg-[#3C6E71] text-white shadow-xs' : 'text-[#353535] hover:bg-[#F7F9FA] hover:text-[#284B63]' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        <span>سفارش‌های من</span>
                    </a>

                    <a href="{{ route('customer.subscriptions') }}"
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-black transition-all min-h-[44px] {{ request()->routeIs('customer.subscriptions*') ? 'bg-[#3C6E71] text-white shadow-xs' : 'text-[#353535] hover:bg-[#F7F9FA] hover:text-[#284B63]' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            <span>اشتراک روزانه قهوه</span>
                        </div>
                        @if(auth('customer')->user()->activeSubscription)
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        @endif
                    </a>

                    <a href="{{ route('customer.favorites') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-black transition-all min-h-[44px] {{ request()->routeIs('customer.favorites*') ? 'bg-[#3C6E71] text-white shadow-xs' : 'text-[#353535] hover:bg-[#F7F9FA] hover:text-[#284B63]' }}">
                        <svg class="w-5 h-5 flex-shrink-0 text-[#FF7A18]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        <span>علاقه‌مندی‌های من</span>
                    </a>

                    <a href="{{ route('customer.club') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-black transition-all min-h-[44px] {{ request()->routeIs('customer.club*') ? 'bg-[#3C6E71] text-white shadow-xs' : 'text-[#353535] hover:bg-[#F7F9FA] hover:text-[#284B63]' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>باشگاه و کش‌بک</span>
                    </a>

                    <a href="{{ route('customer.addresses') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-black transition-all min-h-[44px] {{ request()->routeIs('customer.addresses*') ? 'bg-[#3C6E71] text-white shadow-xs' : 'text-[#353535] hover:bg-[#F7F9FA] hover:text-[#284B63]' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>آدرس‌های من (شیراز)</span>
                    </a>

                    <a href="{{ route('customer.profile') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-black transition-all min-h-[44px] {{ request()->routeIs('customer.profile*') ? 'bg-[#3C6E71] text-white shadow-xs' : 'text-[#353535] hover:bg-[#F7F9FA] hover:text-[#284B63]' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>مشخصات و کادوی تولد</span>
                    </a>

                    <a href="{{ route('customer.notifications') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-black transition-all min-h-[44px] {{ request()->routeIs('customer.notifications*') ? 'bg-[#3C6E71] text-white shadow-xs' : 'text-[#353535] hover:bg-[#F7F9FA] hover:text-[#284B63]' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span>پیام‌ها و اعلان‌ها</span>
                    </a>
                </nav>
            </div>

            <!-- Sidebar Bottom Shortcuts -->
            <div class="space-y-2.5 pt-5 border-t border-[#D9D9D9]">
                <a href="{{ route('storefront.menu') }}" class="w-full py-3 bg-[#284B63] hover:bg-[#1E374A] text-white text-xs font-black rounded-xl btn-tactile shadow-xs flex items-center justify-center gap-2 min-h-[44px] transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    <span>سفارش از منو</span>
                </a>

                <div class="flex items-center justify-between gap-2">
                    <a href="{{ route('storefront.about') }}" class="text-[11px] text-[#353535]/65 hover:text-[#284B63] font-bold">
                        داستان بی کافه
                    </a>
                    <form method="POST" action="{{ route('customer.logout') }}">
                        @csrf
                        <button type="submit" class="text-[11px] text-[#B44A4A] hover:underline font-black flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            <span>خروج</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 w-full min-w-0 px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-6 pb-24 md:pb-12">

            @yield('content')
        </main>
    </div>

    <!-- Mobile Bottom Navigation Bar -->
    <x-mobile-nav :active="'panel'"/>

    @include('partials.pwa-prompt', ['pwaPanel' => 'customer'])

    <x-preloader :global="true" />
    <x-toast-container />

    <!-- Auto-Sync LocalStorage Favorites to Customer Account -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        try {
            const rawFavs = localStorage.getItem('bcafe_favorites');
            const localFavs = rawFavs ? JSON.parse(rawFavs) : [];
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            if (token && Array.isArray(localFavs) && localFavs.length > 0) {
                fetch('{{ route("customer.favorites.sync") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({ product_ids: localFavs })
                })
                .then(r => r.json())
                .then(data => {
                    if (data && data.success && Array.isArray(data.favorites)) {
                        localStorage.setItem('bcafe_favorites', JSON.stringify(data.favorites));
                        if (data.synced && data.count > 0 && !sessionStorage.getItem('favs_synced_once')) {
                            sessionStorage.setItem('favs_synced_once', '1');
                            if (window.location.pathname.includes('/favorites') || window.location.pathname.endsWith('/panel')) {
                                window.location.reload();
                            }
                        }
                    }
                })
                .catch(() => {});
            }
        } catch(e) {}
    });
    </script>

    @stack('scripts')
</body>
</html>
