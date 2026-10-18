@extends('layouts.app')

@section('title', 'باشگاه مشتریان')
@section('header_title', 'مدیریت باشگاه مشتریان بی کافه')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-black text-[#353535] flex items-center gap-2">
                <svg class="w-6 h-6 text-[#C8A35A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>باشگاه مشتریان</span>
            </h1>
            <p class="text-xs text-[#353535]/60 mt-1">لول‌بندی مشتریان، کش‌بک، و تنظیمات پاداش</p>
        </div>
            <a href="{{ route('admin.loyalty.tiers') }}" class="px-4 py-2.5 bg-[#284B63] hover:bg-[#1E374A] text-white font-bold rounded-xl text-xs transition-colors flex items-center gap-2 min-h-[44px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span>مدیریت لول‌ها</span>
            </a>
            <a href="{{ route('admin.loyalty.settings') }}" class="px-4 py-2.5 bg-[#3C6E71] hover:bg-[#2E5B5E] text-white font-bold rounded-xl text-xs transition-colors flex items-center gap-2 min-h-[44px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>تنظیمات</span>
            </a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Customers -->
        <div class="bg-white border border-[#D9D9D9] p-5 rounded-2xl space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#353535]/60">کل مشتریان</span>
                <div class="w-9 h-9 rounded-xl bg-[#284B63]/10 text-[#284B63] flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-[#353535] tabular-nums">{{ number_format($totalCustomers) }}</div>
        </div>

        <!-- Without Tier -->
        <div class="bg-white border border-[#D9D9D9] p-5 rounded-2xl space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#353535]/60">بدون لول</span>
                <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-[#353535] tabular-nums">{{ number_format($noTierCount) }}</div>
        </div>

        <!-- Total Cashback -->
        <div class="bg-white border border-[#D9D9D9] p-5 rounded-2xl space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#353535]/60">کل کش‌بک داده‌شده</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-[#353535] tabular-nums">{{ number_format($totalCashbackIssued) }} <span class="text-xs font-bold text-[#353535]/50">تومان</span></div>
        </div>

        <!-- Status -->
        <div class="bg-white border border-[#D9D9D9] p-5 rounded-2xl space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#353535]/60">وضعیت باشگاه</span>
                <div class="w-9 h-9 rounded-xl {{ $settings->is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $settings->is_active ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' : 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z' }}"/></svg>
                </div>
            </div>
            <div class="text-lg font-black {{ $settings->is_active ? 'text-emerald-600' : 'text-rose-600' }}">
                {{ $settings->is_active ? 'فعال' : 'غیرفعال' }}
            </div>
            <div class="text-[11px] text-[#353535]/50">نرخ کش‌بک: {{ $settings->cashback_percent }}٪</div>
        </div>
    </div>

    <!-- Tier Distribution -->
    <div class="bg-white border border-[#D9D9D9] p-6 rounded-2xl space-y-4">
        <h2 class="text-sm font-black text-[#353535] flex items-center gap-2">
            <svg class="w-5 h-5 text-[#C8A35A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span>توزیع مشتریان در لول‌ها</span>
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            @foreach($tierDistribution as $item)
                <div class="p-4 rounded-xl border border-[#D9D9D9] flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-white text-sm" style="background-color: {{ $item['tier']->badge_color ?? '#6B7280' }}">
                        {{ mb_substr($item['tier']->name, 0, 1) }}
                    </div>
                    <div>
                        <div class="text-xs font-black text-[#353535]">{{ $item['tier']->name }}</div>
                        <div class="text-lg font-black text-[#353535] tabular-nums">{{ number_format($item['count']) }} <span class="text-[10px] font-bold text-[#353535]/50">مشتری</span></div>
                    </div>
                </div>
            @endforeach
        </div>

        @if(count($tierDistribution) === 0)
            <div class="text-center py-8 text-[#353535]/50 text-xs">
                هنوز هیچ لولی تعریف نشده.
                <a href="{{ route('admin.loyalty.tiers.create') }}" class="text-[#284B63] hover:underline font-bold">اولین لول رو بساز</a>
            </div>
        @endif
    </div>

</div>
@endsection
