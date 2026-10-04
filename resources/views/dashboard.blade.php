@extends('layouts.app')

@section('title', 'داشبورد اصلی مدیریت')
@section('header_title', 'خلاصه عملکرد کافه در یک نگاه')

@section('content')
<div class="space-y-6">

    <!-- Top Dashboard Header with Title & Quick Shift Status -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-black text-slate-800">داشبورد</h1>
                <span class="text-xs text-[#387780] font-bold">| بی کافه (B Cafe)</span>
            </div>
            <p class="text-xs sm:text-sm text-slate-400 mt-0.5">خلاصه عملکرد کافه در یک نگاه • میز کار شیفت جاری کافه</p>
        </div>

        <div class="flex items-center gap-2.5">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-teal-50 text-[#387780] border border-teal-200/60 shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-[#387780] animate-pulse"></span>
                <span>شیفت فعال • آنلاین</span>
            </span>
            <a href="{{ route('daily-usage.index') }}" class="px-4 py-2 bg-[#387780] hover:bg-[#2E636B] text-white text-xs font-bold rounded-xl btn-tactile shadow-xs flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>سفارش جدید</span>
            </a>
        </div>
    </div>

    <!-- 4 KPI Stat Cards Matrix (Matching Screen 1 of Mockup) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- KPI 1: فروش امروز -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs relative overflow-hidden flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-medium text-slate-400 block mb-1">فروش امروز</span>
                    <div class="text-2xl font-black text-slate-800 tabular-nums">
                        {{ number_format($stats['today_paid_revenue']) }} <span class="text-xs font-normal text-slate-400">تومان</span>
                    </div>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-teal-50 border border-teal-100 flex items-center justify-center text-[#387780] flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 mt-2 border-t border-slate-100">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-600">
                    <span>+۲۲٪</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                </span>
                <!-- SVG Sparkline -->
                <svg class="w-24 h-6 text-[#387780]" viewBox="0 0 100 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M0 18 Q 20 22, 35 12 T 70 8 T 100 4" />
                </svg>
            </div>
        </div>

        <!-- KPI 2: سفارش‌های امروز -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs relative overflow-hidden flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-medium text-slate-400 block mb-1">سفارش‌های امروز</span>
                    <div class="text-2xl font-black text-slate-800 tabular-nums">
                        {{ number_format($stats['today_orders_count']) }}
                    </div>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-600 flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 mt-2 border-t border-slate-100">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-600">
                    <span>+۱۸٪</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                </span>
                <!-- SVG Sparkline -->
                <svg class="w-24 h-6 text-sky-500" viewBox="0 0 100 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M0 20 Q 25 15, 45 18 T 75 10 T 100 6" />
                </svg>
            </div>
        </div>

        <!-- KPI 3: مشتریان امروز -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs relative overflow-hidden flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-medium text-slate-400 block mb-1">مشتریان امروز</span>
                    <div class="text-2xl font-black text-slate-800 tabular-nums">
                        {{ number_format($stats['today_new_customers'] > 0 ? $stats['today_new_customers'] : $stats['total_customers']) }}
                    </div>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 mt-2 border-t border-slate-100">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-600">
                    <span>+۱۲٪</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                </span>
                <!-- SVG Sparkline -->
                <svg class="w-24 h-6 text-indigo-500" viewBox="0 0 100 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M0 16 Q 30 22, 50 14 T 80 8 T 100 5" />
                </svg>
            </div>
        </div>

        <!-- KPI 4: میانگین سفارش -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs relative overflow-hidden flex flex-col justify-between hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-medium text-slate-400 block mb-1">میانگین سفارش</span>
                    <div class="text-2xl font-black text-slate-800 tabular-nums">
                        {{ number_format($stats['avg_order_value']) }} <span class="text-xs font-normal text-slate-400">تومان</span>
                    </div>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 mt-2 border-t border-slate-100">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-600">
                    <span>+۵٪</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                </span>
                <!-- SVG Sparkline -->
                <svg class="w-24 h-6 text-amber-500" viewBox="0 0 100 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M0 22 Q 25 10, 50 16 T 75 8 T 100 5" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Main Two-Column Layout (Screen 1: Weekly Sales Chart + Recent Orders Table) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Column 1: فروش هفتگی (Weekly Sales Chart) - 5 Cols on desktop -->
        <div class="lg:col-span-5 bg-white border border-slate-200/80 rounded-2xl p-5 sm:p-6 shadow-xs space-y-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h2 class="text-sm sm:text-base font-black text-slate-800">فروش هفتگی</h2>
                </div>
                <div class="relative">
                    <button type="button" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 flex items-center gap-1.5 btn-tactile">
                        <span>این هفته</span>
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                </div>
            </div>

            <!-- Bar Chart Component -->
            <div class="pt-4 space-y-4">
                <div class="h-56 flex items-end justify-between gap-2 sm:gap-3 px-2 border-b border-slate-100 pb-2">
                    @foreach($weeklySales as $wDay)
                        @php
                            $heightPercent = $maxDailySales > 0 ? max(12, min(100, round(($wDay['amount'] / $maxDailySales) * 100))) : 15;
                        @endphp
                        <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end group">
                            <!-- Tooltip -->
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity text-[10px] bg-slate-800 text-white px-2 py-0.5 rounded-md whitespace-nowrap tabular-nums shadow-sm pointer-events-none mb-1">
                                {{ number_format($wDay['amount']) }} ت
                            </div>
                            <!-- Bar -->
                            <div class="w-full max-w-[28px] rounded-t-lg transition-all duration-300 {{ $wDay['is_today'] ? 'bg-[#387780] shadow-sm' : 'bg-slate-200 hover:bg-[#387780]/60' }}"
                                 style="height: {{ $heightPercent }}%;"></div>
                            <!-- Day Label -->
                            <span class="text-[11px] font-medium {{ $wDay['is_today'] ? 'text-[#387780] font-bold' : 'text-slate-400' }} truncate">
                                {{ $wDay['day'] }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="flex items-center justify-between text-[11px] text-slate-400 pt-1">
                    <span>مجموع عملکرد هفته:</span>
                    <span class="font-bold text-slate-700 tabular-nums">
                        {{ number_format(collect($weeklySales)->sum('amount')) }} تومان
                    </span>
                </div>
            </div>
        </div>

        <!-- Column 2: سفارش‌های اخیر (Recent Orders Table) - 7 Cols on desktop -->
        <div class="lg:col-span-7 bg-white border border-slate-200/80 rounded-2xl p-5 sm:p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="text-sm sm:text-base font-black text-slate-800">سفارش‌های اخیر</h2>
                <a href="{{ route('orders.index') }}" class="text-xs text-[#387780] font-bold hover:underline flex items-center gap-1">
                    <span>مشاهده همه</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-start text-xs">
                    <thead>
                        <tr class="text-slate-400 border-b border-slate-100 font-medium">
                            <th class="pb-3 text-start font-medium">شماره</th>
                            <th class="pb-3 text-start font-medium">مشتری</th>
                            <th class="pb-3 text-start font-medium">آیتم‌ها</th>
                            <th class="pb-3 text-start font-medium">مبلغ</th>
                            <th class="pb-3 text-start font-medium">زمان</th>
                            <th class="pb-3 text-start font-medium">وضعیت</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($recentOrders as $order)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3 font-mono font-bold text-slate-800">
                                    <a href="{{ route('orders.show', $order) }}" class="hover:text-[#387780]">
                                        #{{ $order->order_number }}
                                    </a>
                                </td>
                                <td class="py-3 font-bold text-slate-800">
                                    {{ $order->customer->name ?? ($order->customer_name ?? 'مشتری حضوری') }}
                                </td>
                                <td class="py-3 text-slate-500">
                                    @php
                                        $firstItem = $order->items->first();
                                        $itemsCount = $order->items->count();
                                    @endphp
                                    @if($firstItem)
                                        <span>{{ $firstItem->product->name ?? 'کالا' }} ×{{ $firstItem->quantity }}</span>
                                        @if($itemsCount > 1)
                                            <span class="text-[10px] text-slate-400">و {{ $itemsCount - 1 }} قلم دیگر</span>
                                        @endif
                                    @else
                                        <span>-</span>
                                    @endif
                                </td>
                                <td class="py-3 font-bold text-slate-900 tabular-nums">
                                    {{ number_format($order->final_payable) }}
                                </td>
                                <td class="py-3 text-slate-400 tabular-nums text-[11px]">
                                    {{ $order->created_at->format('H:i') }}
                                </td>
                                <td class="py-3">
                                    @php
                                        $statusConfig = match($order->status) {
                                            'pending' => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200/60', 'label' => 'در انتظار'],
                                            'processing' => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200/60', 'label' => 'در حال آماده‌سازی'],
                                            'ready' => ['bg' => 'bg-sky-50 text-sky-700 border-sky-200/60', 'label' => 'آماده'],
                                            'completed' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200/60', 'label' => 'تحویل شده'],
                                            'shipping' => ['bg' => 'bg-purple-50 text-purple-700 border-purple-200/60', 'label' => 'ارسال با پیک'],
                                            'cancelled' => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200/60', 'label' => 'لغو شد'],
                                            default => ['bg' => 'bg-slate-100 text-slate-700 border-slate-200', 'label' => $order->status],
                                        };
                                    @endphp
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $statusConfig['bg'] }}">
                                        {{ $statusConfig['label'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    هنوز سفارشی برای این شیفت ثبت نشده است.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Secondary Operations Section: Subscriptions & Free Usages -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Today's Free Subscriptions Usages -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-teal-50 text-[#387780] border border-teal-100 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">سهمیه‌های اشتراک امروز</h3>
                        <span class="text-[11px] text-slate-400">{{ $todayUsages->count() }} فنجان مصرف‌شده</span>
                    </div>
                </div>

                <a href="{{ route('daily-usage.index') }}" class="text-xs text-[#387780] font-bold hover:underline">
                    ثبت سهمیه +
                </a>
            </div>

            <div class="space-y-2 max-h-60 overflow-y-auto custom-scrollbar">
                @forelse($todayUsages as $usage)
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100 flex items-center justify-between gap-2">
                        <div>
                            <span class="text-xs font-bold text-slate-800 block">{{ $usage->customer->name ?? 'مشتری' }}</span>
                            <span class="text-[11px] text-slate-500">{{ $usage->product->name ?? 'قهوه' }}</span>
                        </div>
                        <div class="text-end">
                            <span class="px-2 py-0.5 rounded-md bg-teal-50 text-[#387780] text-[10px] font-bold block">سهمیه روزانه</span>
                            <span class="text-[10px] text-slate-400 tabular-nums">{{ \App\Helpers\JalaliHelper::format($usage->created_at, 'H:i') }}</span>
                        </div>
                    </div>
                @empty
                    <div class="py-6 text-center text-slate-400 text-xs">
                        امروز هنوز هیچ سهمیه اشتراکی ثبت نشده است.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Subscriptions -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-teal-50 text-[#387780] border border-teal-100 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2H5z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">آخرین اشتراک‌های صادرشده</h3>
                        <span class="text-[11px] text-slate-400">مانده سهمیه مشترکین</span>
                    </div>
                </div>

                <a href="{{ route('subscriptions.index') }}" class="text-xs text-[#387780] font-bold hover:underline">
                    همه اشتراک‌ها
                </a>
            </div>

            <div class="space-y-2 max-h-60 overflow-y-auto custom-scrollbar">
                @forelse($recentSubscriptions as $sub)
                    <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-800">{{ $sub->customer->name ?? 'مشتری' }}</span>
                            <span class="text-xs font-bold text-[#387780] tabular-nums">{{ $sub->remaining_items }} از {{ $sub->total_items }}</span>
                        </div>
                        @php
                            $subPercent = $sub->total_items > 0 ? round(($sub->remaining_items / $sub->total_items) * 100) : 0;
                        @endphp
                        <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-[#387780] h-full rounded-full transition-all" style="width: {{ $subPercent }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="py-6 text-center text-slate-400 text-xs">
                        هنوز اشتراکی صادر نشده است.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
