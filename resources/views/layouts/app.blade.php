<!DOCTYPE html>
<html lang="fa" dir="rtl" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'بی کافه') }} - @yield('title', 'پنل مدیریت')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}?v=2.0.2">
    <link rel="alternate icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2.0.2">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}?v=2.0.2">

    @include('partials.pwa-meta', ['pwaPanel' => 'admin'])

    <!-- Bundled Offline Assets (Vazirmatn + Tailwind CSS v4 + Alpine.js) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body x-data="{ sidebarOpen: false, searchOpen: false, searchQuery: '' }"
      @keydown.window.prevent.ctrl.k="searchOpen = !searchOpen"
      @keydown.window.prevent.cmd.k="searchOpen = !searchOpen"
      @keydown.escape="searchOpen = false"
      class="h-full flex flex-col md:flex-row antialiased selection:bg-[#3C6E71] selection:text-white text-[#353535] relative overflow-x-hidden">

    @include('partials.page-preloader')

    <!-- Mobile Backdrop Overlay for Sidebar -->
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/60 backdrop-blur-xs z-40 md:hidden"
         style="display: none;"></div>

    <!-- Sidebar Navigation Drawer (Dark Charcoal Slate #19242B) -->
    <aside :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full md:translate-x-0'"
           class="fixed inset-y-0 start-0 z-50 w-72 md:w-64 bg-[#19242B] text-white border-e border-slate-800/80 flex flex-col justify-between shadow-2xl md:shadow-none md:static md:z-auto transition-transform duration-250 ease-out select-none">
        <div class="flex flex-col h-full">
            <!-- Brand Logo Header (B Cafe Icon) -->
            <div class="h-20 flex items-center justify-between px-5 border-b border-slate-800 bg-[#141D22]">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#387780]/20 text-[#387780] border border-[#387780]/40 flex items-center justify-center shadow-inner flex-shrink-0">
                        <svg class="w-6 h-6 text-[#387780]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8h1a4 4 0 0 1 0 8h-1"></path>
                            <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path>
                            <line x1="6" y1="1" x2="6" y2="4"></line>
                            <line x1="10" y1="1" x2="10" y2="4"></line>
                            <line x1="14" y1="1" x2="14" y2="4"></line>
                        </svg>
                    </div>
                    <div>
                        <span class="font-black text-base text-white tracking-wide block leading-tight">بی کافه (B Cafe)</span>
                        <span class="text-[11px] text-[#387780] font-bold">سامانه مدیریت کافه</span>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="md:hidden p-2 text-slate-400 hover:text-white rounded-xl min-w-[44px] min-h-[44px] flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="p-3.5 space-y-1.5 overflow-y-auto flex-1 custom-scrollbar">
                <!-- Group 1: Core Operations -->
                <div class="px-3 pt-2 pb-1 text-[11px] font-bold text-slate-400/80">میز کار و عملیات روزانه</div>

                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-bold text-xs btn-tactile min-h-[42px] {{ request()->routeIs('dashboard') ? 'bg-[#387780] text-white shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>داشبورد</span>
                </a>

                <a href="{{ route('daily-usage.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-bold text-xs btn-tactile min-h-[42px] {{ request()->routeIs('daily-usage.*') ? 'bg-[#387780] text-white shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>صندوق و ثبت سفارش</span>
                    </div>
                    <span class="px-2 py-0.5 text-[10px] rounded-md bg-[#387780]/30 text-teal-300 font-bold">سریع</span>
                </a>

                <a href="{{ route('admin.live-orders.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-bold text-xs btn-tactile min-h-[42px] {{ request()->routeIs('admin.live-orders.*') || request()->routeIs('orders.live') ? 'bg-[#387780] text-white shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0 text-amber-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span>مانیتور آشپزخانه (۴ مرحله)</span>
                    </div>
                    <span class="w-2 h-2 rounded-full bg-teal-400 animate-ping"></span>
                </a>

                <!-- Group 2: Management & Catalog -->
                <div class="px-3 pt-3 pb-1 text-[11px] font-bold text-slate-400/80">مدیریت سفارشات، مشتریان و کاتالوگ</div>

                <a href="{{ route('orders.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-xs btn-tactile min-h-[42px] {{ request()->routeIs('orders.*') && !request()->routeIs('orders.live') ? 'bg-[#387780] text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>تاریخچه سفارش‌ها</span>
                </a>

                <a href="{{ route('products.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-xs btn-tactile min-h-[42px] {{ request()->routeIs('products.*') ? 'bg-[#387780] text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <span>محصولات</span>
                </a>

                <a href="{{ route('categories.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-xs btn-tactile min-h-[42px] {{ request()->routeIs('categories.*') ? 'bg-[#387780] text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    <span>دسته‌بندی‌های منو</span>
                </a>

                <a href="{{ route('customers.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-xs btn-tactile min-h-[42px] {{ request()->routeIs('customers.*') ? 'bg-[#387780] text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>مشتریان</span>
                </a>

                <a href="{{ route('subscriptions.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-xs btn-tactile min-h-[42px] {{ request()->routeIs('subscriptions.*') ? 'bg-[#387780] text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2H5z"/></svg>
                    <span>اشتراک‌ها و پلن‌ها</span>
                </a>

                <a href="{{ route('admin.media.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-xs btn-tactile min-h-[42px] {{ request()->routeIs('admin.media.*') ? 'bg-[#387780] text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>کتابخانه تصاویر</span>
                </a>

                <a href="{{ route('admin.delivery-zones.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-xs btn-tactile min-h-[42px] {{ request()->routeIs('admin.delivery-zones.*') ? 'bg-[#387780] text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    <span>مناطق ارسال شیراز</span>
                </a>



                @if(auth()->user() && auth()->user()->isAdmin())
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-xs btn-tactile min-h-[42px] {{ request()->routeIs('admin.users.*') ? 'bg-[#387780] text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>کاربران و پرسنل سیستم</span>
                </a>
                @endif

                <!-- Group 3: Financial, Loyalty & Settings (Admin Only) -->
                @if(auth()->user() && auth()->user()->isAdmin())
                <div class="px-3 pt-3 pb-1 text-[11px] font-bold text-slate-400/80">تنظیمات، مالی و باشگاه</div>

                <a href="{{ route('admin.loyalty.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-xs btn-tactile min-h-[42px] {{ request()->routeIs('admin.loyalty.*') || request()->routeIs('loyalty.*') ? 'bg-[#387780] text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>باشگاه مشتریان و سطوح (Tiers)</span>
                </a>

                <a href="{{ route('admin.coupons.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-xs btn-tactile min-h-[42px] {{ request()->routeIs('admin.coupons.*') ? 'bg-[#387780] text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    <span>کدهای تخفیف (کوپن‌ها)</span>
                </a>

                <a href="{{ route('reports.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium text-xs btn-tactile min-h-[42px] {{ request()->routeIs('reports.*') ? 'bg-[#387780] text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span>گزارش‌ها و اکسل</span>
                    </div>
                    <span class="px-1.5 py-0.5 text-[9px] rounded bg-emerald-500/20 text-emerald-300 font-mono font-bold">XLS</span>
                </a>

                <a href="{{ route('settings.payment.edit') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-xs btn-tactile min-h-[42px] {{ request()->routeIs('settings.payment.*') ? 'bg-[#387780] text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    <span>روش‌های پرداخت (فعال/غیرفعال)</span>
                </a>

                <a href="{{ route('settings.sms.edit') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-xs btn-tactile min-h-[42px] {{ request()->routeIs('settings.sms.*') ? 'bg-[#387780] text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    <span>تنظیمات پیامک و پترن</span>
                </a>

                <a href="{{ route('settings.receipt.edit') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-xs btn-tactile min-h-[42px] {{ request()->routeIs('settings.receipt.*') ? 'bg-[#387780] text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>طراحی و تنظیمات فاکتور (۸۰mm)</span>
                </a>

                <a href="{{ route('settings.cafe.edit') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-xs btn-tactile min-h-[42px] {{ request()->routeIs('settings.cafe.*') ? 'bg-[#387780] text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>ساعات کاری و شیفت کافه</span>
                </a>

                <a href="{{ route('admin.deployments.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-xs btn-tactile min-h-[42px] {{ request()->routeIs('admin.deployments.*') ? 'bg-[#387780] text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>به‌روزرسانی و استقرار</span>
                </a>

                <a href="{{ route('admin.pwa-settings') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-xs btn-tactile min-h-[42px] {{ request()->routeIs('admin.pwa-settings*') ? 'bg-[#387780] text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <span>تنظیمات وب‌اپلیکیشن (PWA)</span>
                </a>

                @endif
            </nav>

            <!-- User Profile Footer Card -->
            <div class="p-3.5 border-t border-slate-800 bg-[#141D22]">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-[#387780] text-white flex items-center justify-center font-black text-sm flex-shrink-0 shadow-sm border border-white/10">
                            {{ mb_substr(auth()->user()->name ?? 'U', 0, 1) }}
                        </div>
                        <div class="truncate">
                            <div class="text-xs font-bold text-white leading-snug truncate">{{ auth()->user()->name ?? 'امیر محمد بهارلو' }}</div>
                            <div class="text-[11px]">
                                @if(auth()->user() && auth()->user()->isAdmin())
                                    <span class="text-slate-400 font-medium">مدیر کافه</span>
                                @else
                                    <span class="text-slate-400 font-medium">پرسنل</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="خروج از حساب" class="w-9 h-9 text-slate-400 hover:text-rose-400 hover:bg-white/5 rounded-xl btn-tactile flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Area (Clean White Canvas) -->
    <main class="flex-1 flex flex-col min-w-0 overflow-y-auto pb-24 md:pb-0 bg-[#F8FAFC]">
        <!-- Top Responsive Navbar (Header) -->
        <header class="h-16 sm:h-20 border-b border-slate-200/80 bg-white px-4 sm:px-6 flex items-center justify-between sticky top-0 z-20 shadow-xs gap-3">
            <div class="flex items-center gap-2 sm:gap-4 min-w-0 flex-1">
                <!-- Hamburger Button for Mobile -->
                <button @click="sidebarOpen = true" class="md:hidden w-10 h-10 rounded-xl text-slate-700 hover:bg-slate-100 btn-tactile flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <!-- Global Search Trigger Pill (Ctrl + K) -->
                <div class="w-full max-w-md hidden sm:block">
                    <button @click="searchOpen = true"
                            type="button"
                            class="w-full flex items-center justify-between px-4 py-2.5 bg-slate-50 hover:bg-slate-100/80 border border-slate-200 rounded-full text-xs text-slate-500 hover:text-slate-800 transition-all btn-tactile min-h-[42px]">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <span>جستجو در سفارشات، محصولات، مشتریان...</span>
                        </div>
                        <kbd class="px-2 py-0.5 text-[10px] font-mono bg-white border border-slate-200 rounded-full text-slate-500 shadow-2xs">Ctrl + K</kbd>
                    </button>
                </div>
            </div>

            <!-- Header Quick Action Buttons & Profile -->
            <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
                <!-- Notification Bell with Badge (3) -->
                <a href="{{ route('admin.live-orders.index') }}" title="سفارشات زنده" class="relative w-10 h-10 rounded-full border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 flex items-center justify-center btn-tactile shadow-2xs">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <span class="absolute top-1.5 start-2 w-2 h-2 rounded-full bg-rose-500 ring-2 ring-white"></span>
                </a>

                <!-- Storefront Public Link -->
                <a href="{{ route('storefront.menu') }}" target="_blank" class="hidden md:flex px-3.5 py-2.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 font-bold rounded-xl text-xs btn-tactile items-center gap-2 shadow-2xs min-h-[42px]">
                    <svg class="w-4 h-4 text-[#387780]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>منوی آنلاین</span>
                </a>

                <!-- Primary POS CTA: ثبت سفارش جدید (Teal #387780) -->
                <a href="{{ route('daily-usage.index') }}" class="px-3.5 sm:px-4 py-2 sm:py-2.5 bg-[#387780] hover:bg-[#2E636B] text-white font-bold rounded-xl text-xs btn-tactile shadow-xs flex items-center gap-1.5 min-h-[42px]">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>ثبت سفارش</span>
                </a>

                <!-- Topbar User Avatar -->
                <div class="flex items-center gap-2.5 ps-2 border-s border-slate-200">
                    <div class="w-9 h-9 rounded-full bg-[#19242B] text-white flex items-center justify-center font-bold text-xs ring-2 ring-slate-100">
                        {{ mb_substr(auth()->user()->name ?? 'A', 0, 1) }}
                    </div>
                    <div class="hidden xl:block text-start leading-tight">
                        <div class="text-xs font-bold text-slate-800">{{ auth()->user()->name ?? 'امیر محمد بهارلو' }}</div>
                        <div class="text-[10px] text-slate-400 font-medium">مدیر</div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dynamic Content Body -->
        <div class="p-4 sm:p-6 md:p-8 space-y-6 flex-1 max-w-full overflow-x-hidden">
            <!-- Flash Messages (Validation errors inline, session flash alerts handled by Toast system) -->

            @if($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm shadow-xs space-y-2">
                    <div class="font-extrabold flex items-center gap-2">
                        <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>لطفاً خطاهای زیر را برطرف نمایید:</span>
                    </div>
                    <ul class="list-disc list-inside ps-7 space-y-1 font-medium text-rose-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Mobile Bottom Navigation Bar -->
    <nav class="md:hidden fixed bottom-0 inset-x-0 bg-[#353535] text-white border-t border-[#D9D9D9]/20 z-30 flex justify-around items-center h-16 px-2 shadow-2xl">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 text-[11px] min-w-[48px] min-h-[48px] justify-center {{ request()->routeIs('dashboard') ? 'text-[#3C6E71] font-bold' : 'text-[#D9D9D9]/70' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>داشبورد</span>
        </a>

        <a href="{{ route('admin.live-orders.index') }}" class="flex flex-col items-center gap-1 text-[11px] min-w-[48px] min-h-[48px] justify-center {{ request()->routeIs('admin.live-orders.*') ? 'text-[#3C6E71] font-bold' : 'text-[#D9D9D9]/70' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            <span>آشپزخانه</span>
        </a>

        <!-- Middle FAB: New Order (Yale Blue, 52px target) -->
        <a href="{{ route('daily-usage.index') }}" class="-mt-7 w-14 h-14 rounded-2xl bg-[#284B63] hover:bg-[#1E374A] text-white flex items-center justify-center shadow-lg border-2 border-white btn-tactile">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        </a>

        <a href="{{ route('customers.index') }}" class="flex flex-col items-center gap-1 text-[11px] min-w-[48px] min-h-[48px] justify-center {{ request()->routeIs('customers.*') ? 'text-[#3C6E71] font-bold' : 'text-[#D9D9D9]/70' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <span>مشتریان</span>
        </a>

        <a href="{{ route('orders.index') }}" class="flex flex-col items-center gap-1 text-[11px] min-w-[48px] min-h-[48px] justify-center {{ request()->routeIs('orders.*') ? 'text-[#3C6E71] font-bold' : 'text-[#D9D9D9]/70' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>سفارش‌ها</span>
        </a>
    </nav>

    <!-- Global Command Palette Modal (Ctrl + K) -->
    <div x-show="searchOpen"
         x-transition:enter="transition-opacity ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-start justify-center pt-20 px-4 bg-black/60 backdrop-blur-xs"
         style="display: none;">

        <div @click.outside="searchOpen = false"
             class="w-full max-w-xl bg-white rounded-3xl shadow-2xl border border-[#D9D9D9] overflow-hidden">
            <!-- Search Input Box -->
            <div class="p-4 border-b border-[#D9D9D9] flex items-center gap-3 bg-[#F7F9FA]">
                <svg class="w-5 h-5 text-[#3C6E71]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input x-model="searchQuery"
                       type="text"
                       placeholder="جستجو در بخش‌ها، سفارشات، مشتریان..."
                       class="w-full bg-transparent border-0 focus:outline-none text-sm font-bold text-[#353535] placeholder:text-[#353535]/40"
                       autofocus>
                <button @click="searchOpen = false" class="px-2.5 py-1 text-xs text-[#353535]/60 hover:text-[#353535] rounded-lg">
                    Esc
                </button>
            </div>

            <!-- Quick Navigation Shortcuts -->
            <div class="p-3 max-h-96 overflow-y-auto space-y-1 custom-scrollbar">
                <div class="px-3 py-1.5 text-xs font-black text-[#353535]/50">دسترسی سریع عملیاتی</div>

                <a href="{{ route('daily-usage.index') }}" class="flex items-center justify-between p-3 rounded-2xl hover:bg-[#EEF2F3] text-[#353535] transition-colors min-h-[44px]">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-[#284B63]/15 text-[#284B63] flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold">صندوق و ثبت سفارش جدید</div>
                            <div class="text-[11px] text-[#353535]/60">ثبت مصرف اشتراک یا سفارش مستقیم کافه</div>
                        </div>
                    </div>
                    <span class="text-xs text-[#284B63] font-bold flex items-center gap-1">
                        <span>ورود به صندوق</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </span>
                </a>

                <a href="{{ route('admin.live-orders.index') }}" class="flex items-center justify-between p-3 rounded-2xl hover:bg-[#EEF2F3] text-[#353535] transition-colors min-h-[44px]">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-[#3C6E71]/15 text-[#3C6E71] flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold">مانیتور زنده آشپزخانه و بار</div>
                            <div class="text-[11px] text-[#353535]/60">مشاهده و تغییر وضعیت سفارشات در حال آماده‌سازی</div>
                        </div>
                    </div>
                    <span class="text-xs text-[#3C6E71] font-bold flex items-center gap-1">
                        <span>مشاهده بورد</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </span>
                </a>

                <a href="{{ route('customers.index') }}" class="flex items-center justify-between p-3 rounded-2xl hover:bg-[#EEF2F3] text-[#353535] transition-colors min-h-[44px]">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-[#353535]/10 text-[#353535] flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold">لیست مشتریان و اشتراک‌ها</div>
                            <div class="text-[11px] text-[#353535]/60">جستجوی مشتری با شماره موبایل یا نام</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('products.index') }}" class="flex items-center justify-between p-3 rounded-2xl hover:bg-[#EEF2F3] text-[#353535] transition-colors min-h-[44px]">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-[#353535]/10 text-[#353535] flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold">محصولات و منو</div>
                            <div class="text-[11px] text-[#353535]/60">مدیریت قیمت‌ها، موجودی و دسته‌بندی‌ها</div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('reports.index') }}" class="flex items-center justify-between p-3 rounded-2xl hover:bg-[#EEF2F3] text-[#353535] transition-colors min-h-[44px]">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-[#353535]/10 text-[#353535] flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold">گزارش‌های مالی و عملکرد</div>
                            <div class="text-[11px] text-[#353535]/60">نمودارهای فروش، میزان مصرف و آمار کلیدی</div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Footer hint -->
            <div class="px-4 py-3 bg-[#F7F9FA] border-t border-[#D9D9D9] flex items-center justify-between text-xs text-[#353535]/60">
                <span>استفاده از <kbd class="px-1.5 py-0.5 bg-white border border-[#D9D9D9] rounded-md text-[10px]">Esc</kbd> برای بستن</span>
                <span class="font-bold text-[#3C6E71]">بی کافه شیراز</span>
            </div>
        </div>
    </div>

    @include('partials.pwa-prompt', ['pwaPanel' => 'admin'])

    <x-preloader :global="true" />
    <x-toast-container />

    @stack('scripts')
</body>
</html>
