@extends('layouts.app')

@section('title', 'گزارش‌های مالی و تحلیلی')
@section('header_title', 'داشبورد ارزیابی و گزارش‌های مالی بی کافه (B Cafe)')

@section('content')
<div class="space-y-8">

    <!-- Header Actions & Print Bar -->
    <div class="flex flex-wrap items-center justify-between gap-4 bg-white border border-[#E2E8F0] p-5 rounded-2xl shadow-xs">
        <div class="space-y-1">
            <h2 class="text-base font-extrabold text-[#19242B] flex items-center gap-2">
                <svg class="w-5 h-5 text-[#387780]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>خلاصه وضعیت مالی و عملکرد کل سیستم</span>
            </h2>
            <p class="text-xs text-[#64748B]">محاسبه هوشمند بر اساس فاکتورهای صادره، اشتراک‌های خریداری‌شده و مصارف روزانه</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('reports.export.orders') }}" class="px-3.5 py-2.5 bg-[#387780] hover:bg-[#2E626A] text-white font-bold rounded-xl text-xs transition-colors shadow-xs flex items-center gap-1.5">
                <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>خروجی اکسل سفارشات</span>
            </a>

            <a href="{{ route('reports.export.products') }}" class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-[#19242B] font-bold rounded-xl text-xs transition-colors border border-slate-200 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>خروجی فروش کالاها</span>
            </a>

            <a href="{{ route('reports.export.customers') }}" class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-[#19242B] font-bold rounded-xl text-xs transition-colors border border-slate-200 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>خروجی باشگاه مشتریان</span>
            </a>

            <button onclick="window.print()" class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-[#19242B] font-bold rounded-xl text-xs transition-colors border border-slate-200 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-[#387780]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>چاپ</span>
            </button>
        </div>
    </div>

    <!-- Executive KPI Matrix (5 Metrics) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- KPI 1: Total Revenue -->
        <div class="bg-[#FCFCFB] border border-[#D9E0D6] p-5 rounded-2xl space-y-2 shadow-sm relative overflow-hidden">
            <div class="text-xs font-semibold text-[#6E756F]">مجموع کل درآمد</div>
            <div class="text-xl font-black text-[#3A5232]">{{ number_format($total_revenue) }}</div>
            <div class="text-[10px] text-[#8A918B]">تومان (اشتراک + فروش مستقیم)</div>
            <div class="absolute top-3 left-3 w-8 h-8 rounded-lg bg-[#EAF2E7] flex items-center justify-center text-[#3A5232]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- KPI 2: Subscription Revenue -->
        <div class="bg-[#FCFCFB] border border-[#D9E0D6] p-5 rounded-2xl space-y-2 shadow-sm relative overflow-hidden">
            <div class="text-xs font-semibold text-[#6E756F]">درآمد اشتراک‌ها</div>
            <div class="text-xl font-black text-[#1F2520]">{{ number_format($subscription_revenue) }}</div>
            <div class="text-[10px] text-[#6E756F] font-bold">{{ $total_subscriptions_count }} اشتراک صادرشده</div>
            <div class="absolute top-3 left-3 w-8 h-8 rounded-lg bg-[#EEF2EC] flex items-center justify-center text-[#1F2520]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2H5z"/></svg>
            </div>
        </div>

        <!-- KPI 3: Direct Paid Sales -->
        <div class="bg-[#FCFCFB] border border-[#D9E0D6] p-5 rounded-2xl space-y-2 shadow-sm relative overflow-hidden">
            <div class="text-xs font-semibold text-[#6E756F]">فروش پولی مستقیم</div>
            <div class="text-xl font-black text-[#3A5232]">{{ number_format($paid_order_revenue) }}</div>
            <div class="text-[10px] text-[#6E756F] font-bold">{{ $paid_orders_count }} فاکتور حضوری</div>
            <div class="absolute top-3 left-3 w-8 h-8 rounded-lg bg-[#EAF2E7] flex items-center justify-center text-[#3A5232]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>

        <!-- KPI 4: Free Coffee Delivered -->
        <div class="bg-[#FCFCFB] border border-[#D9E0D6] p-5 rounded-2xl space-y-2 shadow-sm relative overflow-hidden">
            <div class="text-xs font-semibold text-[#6E756F]">تحویل رایگان روزانه</div>
            <div class="text-xl font-black text-[#C8A35A]">{{ number_format($total_free_coffees_delivered) }}</div>
            <div class="text-[10px] text-[#8A918B]">فنجان تحویل‌شده با اشتراک</div>
            <div class="absolute top-3 left-3 w-8 h-8 rounded-lg bg-[#F7F0DE] flex items-center justify-center text-[#C8A35A]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- KPI 5: Average Order Value -->
        <div class="bg-[#FCFCFB] border border-[#D9E0D6] p-5 rounded-2xl space-y-2 shadow-sm relative overflow-hidden">
            <div class="text-xs font-semibold text-[#6E756F]">میانگین هر سفارش</div>
            <div class="text-xl font-black text-[#3A5232]">{{ number_format($avg_order_value) }}</div>
            <div class="text-[10px] text-[#8A918B]">تومان متوسط فروش پولی</div>
            <div class="absolute top-3 left-3 w-8 h-8 rounded-lg bg-[#EAF2E7] flex items-center justify-center text-[#3A5232]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
        </div>
    </div>

    <!-- Analytics Charts Section (Chart.js) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Line Chart: Revenue & Free Coffee Trend -->
        <div class="lg:col-span-2 bg-[#FCFCFB] border border-[#D9E0D6] rounded-3xl p-6 space-y-4 shadow-sm">
            <div class="flex items-center justify-between border-b border-[#E7ECE4] pb-4">
                <h3 class="text-sm font-bold text-[#1F2520] flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#3A5232]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                    <span>روند فروش پولی و تحویل رایگان (۱۴ روز گذشته)</span>
                </h3>
                <span class="text-xs font-medium text-[#6E756F] bg-[#EEF2EC] px-3 py-1 rounded-lg">به‌روزرسانی لحظه‌ای</span>
            </div>
            <div class="h-72 w-full">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Product Category Distribution Chart -->
        <div class="bg-[#FCFCFB] border border-[#D9E0D6] rounded-3xl p-6 space-y-4 shadow-sm flex flex-col justify-between">
            <div class="border-b border-[#E7ECE4] pb-4">
                <h3 class="text-sm font-bold text-[#1F2520] flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#C8A35A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                    <span>توزیع محبوبیت محصولات</span>
                </h3>
            </div>
            <div class="h-60 w-full flex items-center justify-center">
                <canvas id="categoryChart"></canvas>
            </div>
            <div class="text-[11px] text-[#6E756F] text-center pt-2">
                نسبت قهوه اشتراکی در برابر فروش پولی مستقیم
            </div>
        </div>
    </div>

    <!-- Product Profitability & Popularity Audit Table -->
    <div class="bg-[#FCFCFB] border border-[#D9E0D6] rounded-3xl p-6 space-y-4 shadow-sm">
        <div class="flex items-center justify-between border-b border-[#E7ECE4] pb-4">
            <h3 class="text-sm font-bold text-[#1F2520] flex items-center gap-2">
                <svg class="w-5 h-5 text-[#3A5232]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>آمار تفکیکی فروش محصولات منو</span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-start text-xs">
                <thead>
                    <tr class="bg-[#EEF2EC] text-[#6E756F] border-b border-[#D9E0D6]">
                        <th class="p-4 font-semibold">عنوان محصول</th>
                        <th class="p-4 font-semibold">قیمت تک‌فروشی</th>
                        <th class="p-4 font-semibold">تعداد فروش پولی</th>
                        <th class="p-4 font-semibold">تعداد تحویل اشتراکی</th>
                        <th class="p-4 font-semibold">مجموع کل تحویل</th>
                        <th class="p-4 font-semibold">درآمد حاصله (تومان)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E7ECE4] text-[#48504B]">
                    @forelse($product_stats as $prod)
                        <tr class="hover:bg-[#F7F8F5]">
                            <td class="p-4 font-bold text-[#1F2520]">
                                <div>{{ $prod->name }}</div>
                            </td>
                            <td class="p-4 text-[#6E756F]">{{ number_format($prod->price) }} تومان</td>
                            <td class="p-4 font-bold text-[#3A5232]">{{ number_format($prod->paid_qty) }} عدد</td>
                            <td class="p-4 font-bold text-[#C8A35A]">{{ number_format($prod->free_qty) }} فنجان</td>
                            <td class="p-4 font-extrabold text-[#1F2520]">{{ number_format($prod->total_qty) }} عدد</td>
                            <td class="p-4 font-black text-[#3A5232]">{{ number_format($prod->revenue) }} تومان</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-[#8A918B]">محصولی یافت نشد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Monthly Performance Summary Table -->
    <div class="bg-[#FCFCFB] border border-[#D9E0D6] rounded-3xl p-6 space-y-4 shadow-sm">
        <div class="flex items-center justify-between border-b border-[#E7ECE4] pb-4">
            <h3 class="text-sm font-bold text-[#1F2520] flex items-center gap-2">
                <svg class="w-5 h-5 text-[#C8A35A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>تراز و گزارش عملکرد ماهانه</span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-start text-xs">
                <thead>
                    <tr class="bg-[#EEF2EC] text-[#6E756F] border-b border-[#D9E0D6]">
                        <th class="p-4 font-semibold">ماه / سال (شمسی)</th>
                        <th class="p-4 font-semibold">تعداد تحویل رایگان اشتراکی</th>
                        <th class="p-4 font-semibold">درآمد فروش پولی مستقیم</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E7ECE4] text-[#48504B]">
                    @forelse($monthly_usage as $row)
                        <tr class="hover:bg-[#F7F8F5]">
                            <td class="p-4 font-bold text-[#1F2520]">{{ $row->jalali_month }}</td>
                            <td class="p-4 font-bold text-[#C8A35A]">{{ number_format($row->total_usage) }} فنجان</td>
                            <td class="p-4 font-bold text-[#3A5232]">{{ number_format($row->paid_revenue) }} تومان</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-8 text-center text-[#8A918B]">اطلاعات ماهانه‌ای هنوز ثبت نشده است.</td>
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
        const ctx1 = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: @json($chart_labels),
                datasets: [
                    {
                        label: 'فروش پولی (تومان)',
                        data: @json($chart_revenue_data),
                        borderColor: '#3A5232',
                        backgroundColor: 'rgba(58, 82, 50, 0.08)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.35,
                        yAxisID: 'y'
                    },
                    {
                        label: 'تحویل رایگان (فنجان)',
                        data: @json($chart_free_data),
                        borderColor: '#C8A35A',
                        backgroundColor: 'rgba(200, 163, 90, 0.15)',
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
                        type: 'linear', display: true, position: 'right',
                        ticks: { font: { family: 'Vazirmatn', size: 10 } }
                    },
                    y1: {
                        type: 'linear', display: true, position: 'left',
                        grid: { drawOnChartArea: false },
                        ticks: { font: { family: 'Vazirmatn', size: 10 } }
                    }
                }
            }
        });

        // 2. Category / Distribution Doughnut Chart
        const ctx2 = document.getElementById('categoryChart').getContext('2d');
        const prodData = @json($product_stats);
        const topProds = prodData.slice(0, 5);

        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: topProds.map(p => p.name),
                datasets: [{
                    data: topProds.map(p => p.total_qty),
                    backgroundColor: ['#3A5232', '#C8A35A', '#6A8C60', '#B78F44', '#48504B'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { family: 'Vazirmatn', size: 10 } } }
                }
            }
        });
    });
</script>
@endsection
