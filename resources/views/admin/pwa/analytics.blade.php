@extends('layouts.app')

@section('title', 'آمار و تحلیل وب‌اپلیکیشن (PWA)')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">

    <!-- Page Header & Filters -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-slate-800 flex items-center gap-2">
                <svg class="w-6 h-6 text-[#387780]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>آمار و تحلیل وب‌اپلیکیشن (PWA Analytics)</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">بررسی نصب‌ها، کاربران فعال، بازدید صفحات، ارسال و نرخ باز شدن اعلان‌ها (Open Rate)</p>
        </div>

        <!-- Filter Controls & Export -->
        <div class="flex flex-wrap items-center gap-2">
            <!-- Filter by Panel -->
            <div class="inline-flex rounded-xl bg-slate-200/80 p-1 text-xs font-bold">
                <a href="{{ route('admin.pwa-analytics', ['panel' => 'all', 'range' => $range]) }}"
                   class="px-3 py-1.5 rounded-lg transition-colors {{ $panel === 'all' ? 'bg-white text-slate-800 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">همه</a>
                <a href="{{ route('admin.pwa-analytics', ['panel' => 'customer', 'range' => $range]) }}"
                   class="px-3 py-1.5 rounded-lg transition-colors {{ $panel === 'customer' ? 'bg-white text-slate-800 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">مشتریان</a>
                <a href="{{ route('admin.pwa-analytics', ['panel' => 'admin', 'range' => $range]) }}"
                   class="px-3 py-1.5 rounded-lg transition-colors {{ $panel === 'admin' ? 'bg-white text-slate-800 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">مدیریت</a>
            </div>

            <!-- Filter by Time Range -->
            <div class="inline-flex rounded-xl bg-slate-200/80 p-1 text-xs font-bold">
                <a href="{{ route('admin.pwa-analytics', ['panel' => $panel, 'range' => '7d']) }}"
                   class="px-3 py-1.5 rounded-lg transition-colors {{ $range === '7d' ? 'bg-white text-slate-800 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">۷ روز</a>
                <a href="{{ route('admin.pwa-analytics', ['panel' => $panel, 'range' => '30d']) }}"
                   class="px-3 py-1.5 rounded-lg transition-colors {{ $range === '30d' ? 'bg-white text-slate-800 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">۳۰ روز</a>
                <a href="{{ route('admin.pwa-analytics', ['panel' => $panel, 'range' => '90d']) }}"
                   class="px-3 py-1.5 rounded-lg transition-colors {{ $range === '90d' ? 'bg-white text-slate-800 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">۹۰ روز</a>
            </div>

            <!-- CSV Export Button -->
            <a href="{{ route('admin.pwa-analytics.export', ['panel' => $panel, 'days' => $range === '7d' ? 7 : ($range === '90d' ? 90 : 30)]) }}"
               class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-xs flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>خروجی CSV</span>
            </a>
        </div>
    </div>

    <!-- Summary KPI Cards Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <!-- Installs -->
        <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500">مجموع نصب وب‌اپ</span>
                <div class="w-8 h-8 rounded-xl bg-[#387780]/10 text-[#387780] flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-800 tabular-nums">{{ number_format($totalInstalls) }}</div>
            <div class="text-[11px] text-slate-400">نصب روی صفحه اصلی دستگاه</div>
        </div>

        <!-- Active Users -->
        <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500">کاربران فعال دوره</span>
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-800 tabular-nums">{{ number_format($activeUsers) }}</div>
            <div class="text-[11px] text-slate-400">کاربران منحصر‌به‌فرد وب‌اپ</div>
        </div>

        <!-- Page Views -->
        <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500">بازدید صفحات</span>
                <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-800 tabular-nums">{{ number_format($totalPageViews) }}</div>
            <div class="text-[11px] text-slate-400">مشاهده صفحات منو و پنل‌ها</div>
        </div>

        <!-- Push Notifications & Open Rate -->
        <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500">نرخ باز شدن اعلان‌ها</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <div class="text-2xl font-black text-emerald-700 tabular-nums">{{ $openRate }}%</div>
                <span class="text-[11px] text-slate-400">({{ number_format($totalNotificationsOpened) }} از {{ number_format($totalNotificationsSent) }})</span>
            </div>
            <div class="text-[11px] text-slate-400">نرخ کلیک و باز کردن پوش‌ها</div>
        </div>
    </div>

    <!-- Daily Breakdown Table -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-black text-slate-800">ریز آمار روزانه</h2>
            <span class="text-xs text-slate-400 font-bold">نمایش تفکیکی بر اساس تاریخ</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 font-bold">
                        <th class="py-3 px-3">تاریخ</th>
                        <th class="py-3 px-3">تعداد نصب</th>
                        <th class="py-3 px-3">کاربران فعال</th>
                        <th class="py-3 px-3">بازدید صفحات</th>
                        <th class="py-3 px-3">اعلان ارسالی</th>
                        <th class="py-3 px-3">اعلان بازشده</th>
                        <th class="py-3 px-3">نرخ باز شدن</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($dailySummary as $day)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-3 font-mono font-bold text-slate-700">{{ $day['date'] }}</td>
                            <td class="py-3 px-3 font-bold text-slate-800 tabular-nums">{{ number_format($day['installs']) }}</td>
                            <td class="py-3 px-3 font-bold text-slate-800 tabular-nums">{{ number_format($day['active_users']) }}</td>
                            <td class="py-3 px-3 font-bold text-slate-800 tabular-nums">{{ number_format($day['page_views']) }}</td>
                            <td class="py-3 px-3 font-bold text-slate-800 tabular-nums">{{ number_format($day['notifications_sent']) }}</td>
                            <td class="py-3 px-3 font-bold text-slate-800 tabular-nums">{{ number_format($day['notifications_opened']) }}</td>
                            <td class="py-3 px-3 font-bold text-emerald-600 tabular-nums">{{ $day['open_rate'] }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                داده‌ای برای این بازه زمانی ثبت نشده است. آمار روزانه ساعت ۰۰:۳۰ بامداد یا پس از رخدادهای کاربری تجمیع می‌شود.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
