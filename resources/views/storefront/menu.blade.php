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

    @include('partials.theme-init')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
@php
    $siteContent = $siteContent ?? \App\Models\SiteContentSetting::getForCafe($cafe->id ?? 1);
@endphp
<body x-data="storefrontMenu()"
      @keydown.escape="closeProductModal(); mobileDrawerOpen = false; closeCartDrawer()"
      class="h-full antialiased selection:bg-[#387780] selection:text-white bg-[#F8FAFC] dark:bg-[#0F171B] text-[#19242B] dark:text-slate-100 pb-36 relative font-sans transition-colors">

    @include('partials.page-preloader')

    <!-- Unified Global Navigation Header -->
    <x-storefront-header :activeTab="'menu'" :siteContent="$siteContent" :cafe="$cafe ?? null" />

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

                    <button type="button" @click="mobileDrawerOpen = false; openCartDrawer()" class="w-full flex items-center justify-between p-3 rounded-xl bg-[#F7F9FA]/50 text-[#353535] font-black text-xs btn-tactile">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-[#284B63]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            <span>سبد خرید</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-[#353535] text-[#F7F9FA] text-[11px] font-mono tabular-nums" x-text="cartTotalCount"></span>
                    </button>

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

        <!-- Cohesive Hero Banner (Brand Aligned with Cafe Logo) -->
        <section id="cafe-features" class="relative overflow-hidden p-6 sm:p-8 lg:p-10 rounded-3xl bg-white dark:bg-[#19242B] border border-[#E2E8F0] dark:border-slate-800 shadow-xs">
            <!-- Subtle Ambient Background Accents -->
            <div class="absolute -end-16 -top-16 w-72 h-72 rounded-full bg-teal-500/5 dark:bg-teal-500/10 blur-3xl pointer-events-none"></div>
            <div class="absolute -start-16 -bottom-16 w-72 h-72 rounded-full bg-amber-500/5 dark:bg-amber-500/10 blur-3xl pointer-events-none"></div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center relative z-10">
                <!-- Content Column (8 cols) -->
                <div class="lg:col-span-8 space-y-4 text-start">
                    <!-- Badge -->
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-[#387780]/10 border border-[#387780]/20 text-[#387780] dark:text-teal-400 text-xs font-black shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-[#387780] dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        <span>بار تخصصی قهوه بی کافه شیراز</span>
                    </div>

                    <!-- Main Headline -->
                    <h1 class="text-2xl sm:text-4xl font-black text-[#19242B] dark:text-white leading-tight tracking-tight">
                        {{ $siteContent->menu_title ?: 'طعم اصیل قهوه تازه‌برشت با عصاره‌گیری استاندارد' }}
                    </h1>

                    <!-- Subtitle -->
                    <p class="text-xs sm:text-sm text-[#475569] dark:text-slate-300 leading-relaxed font-medium max-w-2xl">
                        {{ $siteContent->menu_subtitle ?: 'تجربه سرو روی میز در فضای آرام کافه، یا تحویل داغ و سریع با پیک اختصاصی در تمام مناطق شیراز. با اشتراک‌های ماهانه قهوه، از تخفیف‌های ویژه و سرو روزانه بهره‌مند شوید.' }}
                    </p>

                    <!-- Feature Badges -->
                    <div class="pt-1 flex flex-wrap items-center gap-2.5">
                        <div class="flex items-center gap-2 text-xs font-bold bg-[#F8FAFC] dark:bg-slate-800/80 px-3.5 py-2 rounded-xl border border-[#E2E8F0] dark:border-slate-700 text-slate-700 dark:text-slate-200">
                            <svg class="w-4 h-4 text-[#387780] dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>آماده‌سازی فوری (۱۰ تا ۱۵ دقیقه)</span>
                        </div>

                        <div class="flex items-center gap-2 text-xs font-bold bg-[#F8FAFC] dark:bg-slate-800/80 px-3.5 py-2 rounded-xl border border-[#E2E8F0] dark:border-slate-700 text-slate-700 dark:text-slate-200">
                            <svg class="w-4 h-4 text-[#387780] dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                            <span>پیک اختصاصی و گرم در شیراز</span>
                        </div>

                        <div class="flex items-center gap-2 text-xs font-bold bg-[#F8FAFC] dark:bg-slate-800/80 px-3.5 py-2 rounded-xl border border-[#E2E8F0] dark:border-slate-700 text-slate-700 dark:text-slate-200">
                            <svg class="w-4 h-4 text-[#387780] dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>۵٪ کش‌بک در کیف‌پول باشگاه مشتریان</span>
                        </div>
                    </div>

                    <!-- CTAs -->
                    <div class="pt-3 flex flex-wrap items-center gap-3">
                        <a href="#menu-catalog" class="px-6 py-3 bg-[#387780] hover:bg-[#2E636B] text-white text-xs sm:text-sm font-black rounded-xl btn-tactile shadow-xs flex items-center gap-2">
                            <span>مشاهده منو و ثبت سفارش</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </a>
                        <a href="#coffee-subscriptions" class="px-5 py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-[#19242B] dark:text-slate-100 text-xs sm:text-sm font-bold rounded-xl btn-tactile border border-slate-200 dark:border-slate-700 flex items-center gap-2">
                            <span>پلن‌های اشتراک ماهانه</span>
                            <svg class="w-4 h-4 text-[#387780] dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2H5z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Brand Logo Card (4 cols) -->
                <div class="lg:col-span-4 flex items-center justify-center">
                    <div class="w-full max-w-xs p-6 rounded-3xl bg-slate-50 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 shadow-xs text-center space-y-3.5 relative group">
                        <div class="relative mx-auto w-28 h-28 sm:w-32 sm:h-32">
                            <div class="absolute -inset-2 rounded-2xl bg-[#387780]/15 dark:bg-[#387780]/30 blur-lg"></div>
                            <img src="{{ asset('images/logo.jpeg') }}" alt="لوگوی بی کافه شیراز" class="relative w-full h-full rounded-2xl object-cover border border-[#E2E8F0] dark:border-slate-700 shadow-xs group-hover:scale-105 transition-transform duration-300">
                        </div>
                        <div>
                            <span class="text-base font-black text-[#19242B] dark:text-white block">بی کافه (B Cafe)</span>
                            <span class="text-xs text-[#387780] dark:text-teal-400 font-bold block mt-0.5">شیراز • بار تخصصی قهوه و منوی روز</span>
                        </div>
                        <div class="pt-2 border-t border-slate-200 dark:border-slate-700 flex items-center justify-center gap-2 text-[11px] font-bold text-slate-600 dark:text-slate-300">
                            <span class="inline-block size-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>سرو حضوری در سالن و ارسال با پیک</span>
                        </div>
                    </div>
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
    <x-storefront-footer :siteContent="$siteContent" :cafe="$cafe ?? null" />

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
            <button type="button" @click="openCartDrawer()" class="flex items-center gap-3 text-start focus:outline-none">
                <div class="relative w-10 h-10 rounded-xl bg-[#3C6E71] flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <span class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-amber-400 text-[#353535] font-black text-[11px] flex items-center justify-center tabular-nums" x-text="cartTotalCount"></span>
                </div>
                <div>
                    <div class="text-[11px] text-white/70 font-medium">مجموع سبد خرید (مشاهده اقلام)</div>
                    <div class="text-xs font-black tabular-nums text-white" x-text="(cartTotalPrice.toLocaleString('fa-IR')) + ' تومان'"></div>
                </div>
            </button>
            <a href="{{ route('cart.checkout') }}" @click="syncToStorage()"
               class="px-4 py-2.5 rounded-xl bg-[#3C6E71] hover:bg-[#284B63] text-white text-xs font-black flex items-center gap-1.5 btn-tactile shadow-xs">
                <span>تکمیل سفارش</span>
                <svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <!-- Desktop View (Floating Island Capsule) -->
        <div class="hidden sm:flex items-center gap-4 bg-[#353535]/95 text-[#F7F9FA] rounded-full shadow-2xl px-5 py-3 border border-white/15 backdrop-blur-md">
            <button type="button" @click="openCartDrawer()" class="flex items-center gap-2.5 text-start focus:outline-none hover:opacity-90 transition-opacity">
                <div class="relative w-9 h-9 rounded-full bg-[#3C6E71] flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-amber-400 text-[#353535] font-black text-[10px] flex items-center justify-center tabular-nums" x-text="cartTotalCount"></span>
                </div>
                <div class="text-right">
                    <div class="text-[11px] text-white/70 font-medium"><span x-text="cartTotalCount"></span> آیتم در سبد (کلیک برای نمایش)</div>
                    <div class="text-xs font-black tabular-nums text-white" x-text="(cartTotalPrice.toLocaleString('fa-IR')) + ' تومان'"></div>
                </div>
            </button>
            <div class="h-6 w-px bg-white/20"></div>
            <button type="button" @click="openCartDrawer()"
               class="px-4 py-2 rounded-full bg-[#3C6E71] hover:bg-[#284B63] text-white text-xs font-black flex items-center gap-1.5 btn-tactile shadow-xs transition-colors">
                <span>مشاهده سبد و تسویه</span>
                <svg class="w-3.5 h-3.5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </div>

    <!-- Floating Cart Slide-Over Drawer (Off-Canvas Sheet) -->
    <div x-show="cartDrawerOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex justify-end"
         style="display: none;">

        <div @click.outside="closeCartDrawer()"
             x-show="cartDrawerOpen"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="-translate-x-full rtl:translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full rtl:translate-x-full"
             class="w-full max-w-md bg-white h-full shadow-2xl flex flex-col justify-between overflow-hidden">

            <!-- Drawer Header -->
            <div class="p-5 border-b border-[#353535]/10 flex items-center justify-between bg-[#F7F9FA]">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-[#3C6E71]/15 text-[#3C6E71] flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-[#353535]">سبد خرید شما</h3>
                        <p class="text-[11px] text-[#353535]/65">
                            <span class="tabular-nums" x-text="cartTotalCount"></span> آیتم انتخاب‌شده
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <template x-if="cart.length > 0">
                        <button type="button" @click="clearAllCart()" class="text-[11px] font-bold text-rose-600 hover:bg-rose-50 px-2 py-1 rounded-lg transition-colors" title="خالی کردن سبد">
                            خالی کردن
                        </button>
                    </template>
                    <button type="button" @click="closeCartDrawer()" class="w-8 h-8 rounded-xl bg-white border border-[#353535]/15 text-[#353535] flex items-center justify-center hover:bg-[#F7F9FA] transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <!-- Drawer Body: Items list -->
            <div class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-3 custom-scrollbar">
                <!-- Empty Cart State -->
                <template x-if="cart.length === 0">
                    <div class="py-16 text-center space-y-4">
                        <div class="w-20 h-20 rounded-full bg-[#F7F9FA] border-2 border-dashed border-[#284B63]/30 flex items-center justify-center mx-auto text-[#284B63]/60">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                        <div class="space-y-1">
                            <h4 class="text-sm font-black text-[#353535]">سبد خرید شما خالی است</h4>
                            <p class="text-xs text-[#353535]/60 max-w-xs mx-auto">قهوه‌های تخصصی تازه رست‌شده و دسرهای کافه را انتخاب کنید.</p>
                        </div>
                    </div>
                </template>

                <!-- Items Loop -->
                <template x-for="(item, idx) in cart" :key="item.id + '-' + idx">
                    <div class="p-3.5 rounded-2xl bg-[#F7F9FA]/60 border border-[#353535]/10 space-y-2.5 hover:border-[#3C6E71]/40 transition-colors">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs font-black text-[#353535] truncate" x-text="item.name"></span>
                                    <template x-if="item.type === 'subscription'">
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-[#387780] text-white">اشتراک</span>
                                    </template>
                                </div>
                                <template x-if="item.options && Object.keys(item.options).length > 0">
                                    <div class="text-[10px] text-[#3C6E71] font-bold mt-0.5">
                                        <template x-for="(optVal, optKey) in item.options" :key="optKey">
                                            <span class="inline-block me-1.5" x-text="optKey + ': ' + optVal"></span>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="item.note">
                                    <div class="text-[10px] text-slate-500 mt-0.5" x-text="'یادداشت: ' + item.note"></div>
                                </template>
                            </div>

                            <button type="button" @click="removeCartIndex(idx)" class="text-slate-400 hover:text-rose-500 transition-colors p-1" title="حذف این قلم">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>

                        <div class="flex items-center justify-between pt-1 border-t border-[#353535]/8">
                            <!-- Stepper Quantity Controls -->
                            <div class="flex items-center gap-1.5 bg-white px-2 py-1 rounded-xl border border-[#353535]/15 shadow-2xs">
                                <button type="button" @click="decrementCartIndex(idx)" class="w-6 h-6 rounded-lg bg-[#F7F9FA] hover:bg-slate-200 text-[#353535] flex items-center justify-center font-bold text-xs transition-colors">
                                    <template x-if="item.quantity > 1">
                                        <span>-</span>
                                    </template>
                                    <template x-if="item.quantity <= 1">
                                        <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </template>
                                </button>
                                <span class="w-6 text-center text-xs font-black font-mono tabular-nums text-[#353535]" x-text="item.quantity"></span>
                                <button type="button" @click="incrementCartIndex(idx)" class="w-6 h-6 rounded-lg bg-[#3C6E71]/15 hover:bg-[#3C6E71] hover:text-white text-[#3C6E71] flex items-center justify-center font-bold text-xs transition-colors">
                                    +
                                </button>
                            </div>

                            <!-- Price -->
                            <div class="text-start">
                                <span class="text-xs font-black text-[#284B63] tabular-nums" x-text="(item.price * item.quantity).toLocaleString('fa-IR') + ' ت'"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Drawer Footer: Subtotal & Actions -->
            <div class="p-5 border-t border-[#353535]/10 bg-white space-y-3">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-bold text-[#353535]/70">مجموع کل اقلام:</span>
                    <span class="text-base font-black text-[#284B63] tabular-nums" x-text="cartTotalPrice.toLocaleString('fa-IR') + ' تومان'"></span>
                </div>

                <div class="space-y-2 pt-1">
                    <template x-if="cart.length > 0">
                        <a href="{{ route('cart.checkout') }}" @click="syncToStorage()"
                           class="w-full py-3.5 rounded-2xl bg-[#284B63] hover:bg-[#1E374A] text-white font-black text-xs btn-tactile shadow-md flex items-center justify-center gap-2 transition-colors min-h-[46px]">
                            <span>تکمیل و پرداخت سفارش</span>
                            <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </template>
                    <a href="{{ route('cart.index') }}" @click="syncToStorage()"
                       class="w-full py-2.5 rounded-xl bg-[#F7F9FA] hover:bg-slate-200 text-[#353535] font-bold text-xs text-center block transition-colors border border-[#353535]/12">
                        مشاهده صفحه کامل سبد خرید
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- Alpine.js Storefront Cart & Product Modal Logic -->
    <script>
    function storefrontMenu() {
        return {
            cart: JSON.parse(localStorage.getItem('bcafe_cart') || '[]'),
            searchQuery: '',
            mobileDrawerOpen: false,
            cartDrawerOpen: false,

            openCartDrawer() {
                this.cartDrawerOpen = true;
                document.body.style.overflow = 'hidden';
            },

            closeCartDrawer() {
                this.cartDrawerOpen = false;
                document.body.style.overflow = '';
            },

            clearAllCart() {
                if (confirm('آیا از خالی کردن تمام اقلام سبد خرید مطمئن هستید؟')) {
                    this.cart = [];
                    this.syncToStorage();
                }
            },

            incrementCartIndex(idx) {
                if (this.cart[idx]) {
                    const item = this.cart[idx];
                    const max = (item.stock !== null && item.stock !== undefined) ? item.stock : 99;
                    if (item.quantity < max) {
                        item.quantity++;
                        this.syncToStorage();
                    }
                }
            },

            decrementCartIndex(idx) {
                if (this.cart[idx]) {
                    if (this.cart[idx].quantity > 1) {
                        this.cart[idx].quantity--;
                    } else {
                        this.cart.splice(idx, 1);
                    }
                    this.syncToStorage();
                }
            },

            removeCartIndex(idx) {
                if (this.cart[idx]) {
                    this.cart.splice(idx, 1);
                    this.syncToStorage();
                }
            },

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
