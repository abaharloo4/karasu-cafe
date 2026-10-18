@extends('layouts.app')

@section('title', 'گزارش‌های جامع مالی و تحلیلی')
@section('header_title', 'داشبورد ارزیابی و گزارش‌های مالی بی کافه (B Cafe)')

@section('content')
<div class="space-y-6" x-data="{ customDateOpen: {{ $periodKey === 'custom' ? 'true' : 'false' }} }">

    <!-- Header Actions & Exports -->
    <div class="flex flex-wrap items-center justify-between gap-4 bg-white border border-[#E2E8F0] p-5 rounded-2xl shadow-xs">
        <div class="space-y-1">
            <h2 class="text-base font-extrabold text-[#19242B] flex items-center gap-2">
                <svg class="w-5 h-5 text-[#387780]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>خلاصه وضعیت مالی و عملکرد کل سیستم</span>
            </h2>
            <div class="flex items-center gap-2 text-xs text-[#64748B]">
                <span>بازه فعال:</span>
                <span class="font-bold text-[#387780] bg-[#387780]/10 px-2 py-0.5 rounded-md">{{ $periodLabel }}</span>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('reports.export.orders', ['period' => $periodKey, 'start_date' => $rawStart, 'end_date' => $rawEnd]) }}" class="px-3.5 py-2.5 bg-[#387780] hover:bg-[#2E626A] text-white font-bold rounded-xl text-xs transition-colors shadow-xs flex items-center gap-1.5 btn-tactile">
                <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>خروجی اکسل فاکتورها</span>
            </a>

            <a href="{{ route('reports.export.products', ['period' => $periodKey, 'start_date' => $rawStart, 'end_date' => $rawEnd]) }}" class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-[#19242B] font-bold rounded-xl text-xs transition-colors border border-slate-200 flex items-center gap-1.5 btn-tactile">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>خروجی فروش محصولات</span>
            </a>

            <a href="{{ route('reports.export.customers') }}" class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-[#19242B] font-bold rounded-xl text-xs transition-colors border border-slate-200 flex items-center gap-1.5 btn-tactile">
                <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>باشگاه مشتریان</span>
            </a>

            <button onclick="window.print()" class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-[#19242B] font-bold rounded-xl text-xs transition-colors border border-slate-200 flex items-center gap-1.5 btn-tactile">
                <svg class="w-4 h-4 text-[#387780]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>چاپ</span>
            </button>
        </div>
    </div>

    <!-- Date Range Filter Tabs -->
    <div class="bg-white border border-[#E2E8F0] p-4 rounded-2xl shadow-xs space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <span class="text-xs font-bold text-[#19242B] flex items-center gap-1.5">
                <svg class="w-4 h-4 text-[#387780]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>فیلتر زمانی گزارش:</span>
            </span>

            <div class="flex flex-wrap items-center gap-1.5">
                <a href="{{ route('reports.index', ['period' => 'today']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $periodKey === 'today' ? 'bg-[#387780] text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">امروز</a>
                <a href="{{ route('reports.index', ['period' => 'yesterday']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $periodKey === 'yesterday' ? 'bg-[#387780] text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">دیروز</a>
                <a href="{{ route('reports.index', ['period' => '7days']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $periodKey === '7days' ? 'bg-[#387780] text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">۷ روز اخیر</a>
                <a href="{{ route('reports.index', ['period' => '30days']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $periodKey === '30days' ? 'bg-[#387780] text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">۳۰ روز اخیر</a>
                <a href="{{ route('reports.index', ['period' => 'this_month']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $periodKey === 'this_month' ? 'bg-[#387780] text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">این ماه</a>
                <a href="{{ route('reports.index', ['period' => 'last_month']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $periodKey === 'last_month' ? 'bg-[#387780] text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">ماه قبل</a>
                <a href="{{ route('reports.index', ['period' => 'all']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $periodKey === 'all' ? 'bg-[#387780] text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">همه‌زمان</a>
                <button type="button" @click="customDateOpen = !customDateOpen" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1 {{ $periodKey === 'custom' ? 'bg-[#387780] text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    <span>بازه دلخواه</span>
                </button>
            </div>
        </div>

        <!-- Custom Date Range Form -->
        <div x-show="customDateOpen" x-transition.origin.top class="pt-3 border-t border-slate-100">
            <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-end gap-3">
                <input type="hidden" name="period" value="custom">

                <div class="space-y-1">
                    <label class="block text-[11px] font-bold text-slate-600">از تاریخ (میلادی یا شمسی مانند 1403/07/01):</label>
                    <input type="text" name="start_date" value="{{ $rawStart }}" placeholder="مثلاً: 2026-10-01 یا 1403/07/01" class="px-3 py-2 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-[#387780] focus:border-transparent outline-none w-56 font-mono">
                </div>

                <div class="space-y-1">
                    <label class="block text-[11px] font-bold text-slate-600">تا تاریخ (میلادی یا شمسی مانند 1403/07/30):</label>
                    <input type="text" name="end_date" value="{{ $rawEnd }}" placeholder="مثلاً: 2026-10-31 یا 1403/07/30" class="px-3 py-2 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-[#387780] focus:border-transparent outline-none w-56 font-mono">
                </div>

                <button type="submit" class="px-4 py-2 bg-[#19242B] hover:bg-[#284B63] text-white rounded-xl text-xs font-bold transition-colors btn-tactile">
                    اعمال بازه
                </button>

                @if($periodKey === 'custom')
                    <a href="{{ route('reports.index') }}" class="px-3 py-2 text-xs text-rose-600 hover:underline">
                        حذف فیلتر
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Executive KPI Grid (6 High-Impact Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3.5">
        <!-- KPI 1: Total Revenue -->
        <div class="bg-white border border-slate-200/80 p-4 rounded-2xl space-y-1.5 shadow-xs relative overflow-hidden">
            <div class="text-[11px] font-bold text-slate-500">مجموع کل درآمد</div>
            <div class="text-xl font-black text-[#1E2912] tabular-nums">{{ number_format($total_revenue) }}</div>
            <div class="text-[10px] text-slate-400">تومان (اشتراک + سفارشات)</div>
            <div class="absolute top-3 left-3 w-7 h-7 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- KPI 2: Direct Orders Revenue -->
        <div class="bg-white border border-slate-200/80 p-4 rounded-2xl space-y-1.5 shadow-xs relative overflow-hidden">
            <div class="text-[11px] font-bold text-slate-500">فروش فاکتورهای مستقیم</div>
            <div class="text-xl font-black text-[#284B63] tabular-nums">{{ number_format($paid_order_revenue) }}</div>
            <div class="text-[10px] text-slate-500 font-bold tabular-nums">{{ $successful_orders_count }} فاکتور موفق ({{ $cancelled_orders_count }} لغوشده)</div>
            <div class="absolute top-3 left-3 w-7 h-7 rounded-lg bg-sky-500/10 flex items-center justify-center text-sky-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>

        <!-- KPI 3: Subscriptions Revenue -->
        <div class="bg-white border border-slate-200/80 p-4 rounded-2xl space-y-1.5 shadow-xs relative overflow-hidden">
            <div class="text-[11px] font-bold text-slate-500">درآمد اشتراک‌ها</div>
            <div class="text-xl font-black text-[#19242B] tabular-nums">{{ number_format($subscription_revenue) }}</div>
            <div class="text-[10px] text-slate-500 font-bold tabular-nums">{{ $total_subscriptions_count }} پکیج خریداری‌شده</div>
            <div class="absolute top-3 left-3 w-7 h-7 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2H5z"/></svg>
            </div>
        </div>

        <!-- KPI 4: Average Order Value -->
        <div class="bg-white border border-slate-200/80 p-4 rounded-2xl space-y-1.5 shadow-xs relative overflow-hidden">
            <div class="text-[11px] font-bold text-slate-500">میانگین هر فاکتور (AOV)</div>
            <div class="text-xl font-black text-[#387780] tabular-nums">{{ number_format($avg_order_value) }}</div>
            <div class="text-[10px] text-slate-400">تومان متوسط هر سفارش</div>
            <div class="absolute top-3 left-3 w-7 h-7 rounded-lg bg-teal-500/10 flex items-center justify-center text-teal-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
        </div>

        <!-- KPI 5: Free Coffee Delivered -->
        <div class="bg-white border border-slate-200/80 p-4 rounded-2xl space-y-1.5 shadow-xs relative overflow-hidden">
            <div class="text-[11px] font-bold text-slate-500">تحویل رایگان اشتراکی</div>
            <div class="text-xl font-black text-[#9B561B] tabular-nums">{{ number_format($total_free_coffees_delivered) }}</div>
            <div class="text-[10px] text-slate-400">فنجان (ارزش: {{ number_format($estimated_free_coffee_value) }} ت)</div>
            <div class="absolute top-3 left-3 w-7 h-7 rounded-lg bg-amber-500/10 flex items-center justify-center text-amber-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- KPI 6: Total Discounts -->
        <div class="bg-white border border-slate-200/80 p-4 rounded-2xl space-y-1.5 shadow-xs relative overflow-hidden">
            <div class="text-[11px] font-bold text-slate-500">مجموع تخفیف‌های اعطا شده</div>
            <div class="text-xl font-black text-rose-600 tabular-nums">{{ number_format($total_discount_amount) }}</div>
            <div class="text-[10px] text-slate-400">تومان کوپن‌ها و آفرها</div>
            <div class="absolute top-3 left-3 w-7 h-7 rounded-lg bg-rose-500/10 flex items-center justify-center text-rose-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            </div>
        </div>
    </div>

    <!-- Payment Methods & Fulfillment Breakdown Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Payment Methods Card -->
        <div class="bg-white border border-[#E2E8F0] p-5 rounded-2xl shadow-xs space-y-3">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-xs font-extrabold text-[#19242B] flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#387780]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    <span>تفکیک بر اساس روش پرداخت</span>
                </h3>
                <span class="text-[11px] text-slate-400">فاکتورهای پرداخت‌شده</span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                @foreach($paymentStats as $methodKey => $pStat)
                    <div class="p-3 rounded-xl border {{ $pStat['bg_badge'] ?? 'bg-slate-50 border-slate-200' }} space-y-1">
                        <div class="text-[11px] font-bold">{{ $pStat['label'] }}</div>
                        <div class="text-sm font-black tabular-nums">{{ number_format($pStat['sum']) }} <span class="text-[10px] font-normal">ت</span></div>
                        <div class="text-[10px] opacity-80 tabular-nums">{{ number_format($pStat['count']) }} تراکنش</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Fulfillment Types Card -->
        <div class="bg-white border border-[#E2E8F0] p-5 rounded-2xl shadow-xs space-y-3">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-xs font-extrabold text-[#19242B] flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#387780]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                    <span>تفکیک بر اساس نوع تحویل (Fulfillment)</span>
                </h3>
                <span class="text-[11px] text-slate-400">سرو و ارسال</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                @foreach($fulfillmentStats as $fKey => $fStat)
                    <div class="p-3 rounded-xl border border-slate-200 bg-slate-50/70 space-y-1">
                        <div class="text-[11px] font-bold text-slate-700">{{ $fStat['label'] }}</div>
                        <div class="text-sm font-black text-[#19242B] tabular-nums">{{ number_format($fStat['sum']) }} <span class="text-[10px] font-normal text-slate-400">تومان</span></div>
                        <div class="text-[10px] text-slate-500 tabular-nums">{{ number_format($fStat['count']) }} سفارش</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Top 5 Best-Selling Products Carousel / Highlights -->
    @if($top_selling_products->count() > 0)
    <div class="bg-white border border-[#E2E8F0] p-5 rounded-2xl shadow-xs space-y-3">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-xs font-extrabold text-[#19242B] flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                <span>پرفروش‌ترین و محبوب‌ترین آیتم‌های منو در این بازه</span>
            </h3>
            <span class="text-[11px] text-slate-400">۵ آیتم برتر بر اساس تعداد کل سفارشات</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
            @foreach($top_selling_products as $index => $topProd)
                <div class="p-3.5 rounded-xl border border-slate-100 bg-gradient-to-b from-slate-50/50 to-slate-100/50 space-y-2 relative overflow-hidden">
                    <div class="flex items-center justify-between">
                        <span class="w-6 h-6 rounded-full bg-[#387780] text-white text-[11px] font-black flex items-center justify-center">
                            {{ $index + 1 }}
                        </span>
                        <span class="text-[10px] font-bold text-slate-500 bg-white px-2 py-0.5 rounded-md border border-slate-200">
                            {{ $topProd->category_name }}
                        </span>
                    </div>

                    <div>
                        <h4 class="text-xs font-extrabold text-[#19242B] truncate">{{ $topProd->name }}</h4>
                        <p class="text-[10px] text-slate-500 tabular-nums">{{ number_format($topProd->price) }} تومان</p>
                    </div>

                    <div class="pt-1.5 border-t border-slate-200/60 flex items-center justify-between text-[11px]">
                        <span class="font-bold text-emerald-700 tabular-nums">{{ number_format($topProd->total_qty) }} کل</span>
                        <span class="text-[10px] text-slate-500 tabular-nums">({{ number_format($topProd->paid_qty) }} پولی + {{ number_format($topProd->free_qty) }} اشتراک)</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Analytics Charts Section (Chart.js) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Line Chart: Revenue & Free Coffee Trend -->
        <div class="lg:col-span-2 bg-white border border-[#E2E8F0] rounded-2xl p-6 space-y-4 shadow-xs">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h3 class="text-sm font-bold text-[#19242B] flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#387780]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                    <span>روند فروش پولی و تحویل رایگان</span>
                </h3>
                <span class="text-xs font-medium text-slate-500 bg-slate-100 px-3 py-1 rounded-lg">به‌روزرسانی لحظه‌ای</span>
            </div>
            <div class="h-72 w-full">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Product Category Distribution Chart -->
        <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 space-y-4 shadow-xs flex flex-col justify-between">
            <div class="border-b border-slate-100 pb-4">
                <h3 class="text-sm font-bold text-[#19242B] flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#9B561B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                    <span>توزیع روش‌های پرداخت</span>
                </h3>
            </div>
            <div class="h-60 w-full flex items-center justify-center">
                <canvas id="paymentMethodsChart"></canvas>
            </div>
            <div class="text-[11px] text-slate-500 text-center pt-2">
                نسبت مبالغ و تراکنش‌های هر روش پرداخت
            </div>
        </div>
    </div>

    <!-- Product Profitability & Sales Audit Table -->
    <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 space-y-4 shadow-xs" x-data="{ searchQuery: '' }">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
            <h3 class="text-sm font-bold text-[#19242B] flex items-center gap-2">
                <svg class="w-5 h-5 text-[#387780]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>آمار تفکیکی و عملکرد فروش محصولات منو</span>
            </h3>

            <div class="w-full sm:w-64">
                <input type="text" x-model="searchQuery" placeholder="جستجوی نام محصول یا دسته..." class="w-full px-3 py-1.5 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-[#387780] outline-none">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-start text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 border-b border-slate-200">
                        <th class="p-4 font-semibold text-start">عنوان محصول</th>
                        <th class="p-4 font-semibold text-start">دسته‌بندی</th>
                        <th class="p-4 font-semibold text-start">قیمت تک‌فروشی</th>
                        <th class="p-4 font-semibold text-start">فروش پولی</th>
                        <th class="p-4 font-semibold text-start">تحویل اشتراکی</th>
                        <th class="p-4 font-semibold text-start">مجموع کل تحویل</th>
                        <th class="p-4 font-semibold text-start">درآمد ناخالص (تومان)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($product_stats as $prod)
                        <tr class="hover:bg-slate-50/80 transition-colors" x-show="!searchQuery || '{{ $prod->name }} {{ $prod->category_name }}'.toLowerCase().includes(searchQuery.toLowerCase())">
                            <td class="p-4 font-bold text-[#19242B]">
                                {{ $prod->name }}
                            </td>
                            <td class="p-4 text-slate-500">
                                <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded text-[11px]">{{ $prod->category_name }}</span>
                            </td>
                            <td class="p-4 text-slate-600 tabular-nums">{{ number_format($prod->price) }} تومان</td>
                            <td class="p-4 font-bold text-emerald-700 tabular-nums">{{ number_format($prod->paid_qty) }} عدد</td>
                            <td class="p-4 font-bold text-amber-700 tabular-nums">{{ number_format($prod->free_qty) }} فنجان</td>
                            <td class="p-4 font-extrabold text-[#19242B] tabular-nums">{{ number_format($prod->total_qty) }} عدد</td>
                            <td class="p-4 font-black text-[#1E2912] tabular-nums">{{ number_format($prod->revenue) }} تومان</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">محصولی در این بازه یافت نشد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Monthly Performance Summary Table -->
    <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 space-y-4 shadow-xs">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="text-sm font-bold text-[#19242B] flex items-center gap-2">
                <svg class="w-5 h-5 text-[#9B561B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>تراز و گزارش عملکرد ماهانه (۱۲ ماه اخیر)</span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-start text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 border-b border-slate-200">
                        <th class="p-4 font-semibold text-start">ماه / سال (شمسی)</th>
                        <th class="p-4 font-semibold text-start">تعداد تحویل رایگان اشتراکی</th>
                        <th class="p-4 font-semibold text-start">درآمد فروش پولی مستقیم</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($monthly_usage as $row)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="p-4 font-bold text-[#19242B]">{{ $row->jalali_month }}</td>
                            <td class="p-4 font-bold text-amber-700 tabular-nums">{{ number_format($row->total_usage) }} فنجان</td>
                            <td class="p-4 font-bold text-emerald-700 tabular-nums">{{ number_format($row->paid_revenue) }} تومان</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-8 text-center text-slate-400">اطلاعات ماهانه‌ای هنوز ثبت نشده است.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Chart.js Engine -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Revenue & Free Daily Trend Chart
        const ctx1 = document.getElementById('revenueChart');
        if (ctx1) {
            new Chart(ctx1.getContext('2d'), {
                type: 'line',
                data: {
                    labels: @json($chart_labels),
                    datasets: [
                        {
                            label: 'فروش پولی (تومان)',
                            data: @json($chart_revenue_data),
                            borderColor: '#387780',
                            backgroundColor: 'rgba(56, 119, 128, 0.08)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.35,
                            yAxisID: 'y'
                        },
                        {
                            label: 'تحویل رایگان (فنجان)',
                            data: @json($chart_free_data),
                            borderColor: '#9B561B',
                            backgroundColor: 'rgba(155, 86, 27, 0.12)',
                            borderWidth: 2,
                            borderDash: [4, 4],
                            fill: false,
                            tension: 0.35,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { labels: { font: { family: 'Vazirmatn', size: 11 } } }
                    },
                    scales: {
                        x: { ticks: { font: { family: 'Vazirmatn', size: 10 } } },
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            ticks: {
                                font: { family: 'Vazirmatn', size: 10 },
                                callback: function (val) { return Number(val).toLocaleString('fa-IR'); }
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            grid: { drawOnChartArea: false },
                            ticks: {
                                font: { family: 'Vazirmatn', size: 10 },
                                callback: function (val) { return Number(val).toLocaleString('fa-IR'); }
                            }
                        }
                    }
                }
            });
        }

        // 2. Payment Methods Doughnut Chart
        const ctx2 = document.getElementById('paymentMethodsChart');
        if (ctx2) {
            const paymentLabels = @json(array_values(array_map(fn($item) => $item['label'], $paymentStats)));
            const paymentValues = @json(array_values(array_map(fn($item) => $item['sum'], $paymentStats)));
            const paymentColors = @json(array_values(array_map(fn($item) => $item['color'], $paymentStats)));

            new Chart(ctx2.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: paymentLabels,
                    datasets: [{
                        data: paymentValues,
                        backgroundColor: paymentColors,
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { font: { family: 'Vazirmatn', size: 11 } } }
                    }
                }
            });
        }
    });
</script>
@endsection
