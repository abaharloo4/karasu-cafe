@extends('layouts.app')

@section('title', 'سفارش‌ها')
@section('header_title', 'مدیریت و تاریخچه سفارش‌ها - بی کافه (B Cafe)')

@section('content')
<div class="space-y-6">

    <!-- Actions Header -->
    <div class="bg-white border border-[#353535]/10 p-4 sm:p-5 rounded-3xl flex flex-wrap items-center justify-between gap-4 shadow-xs">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-[#353535] text-[#F7F9FA] flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <h2 class="text-base font-black text-[#353535]">تاریخچه و دفتر سفارش‌ها</h2>
                <p class="text-xs text-[#353535]/65 mt-0.5">فهرست تمام سفارش‌های حضوری، بیرون‌بر و ارسالی با پیک</p>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('reports.export.orders') }}" class="px-3.5 py-2.5 bg-white hover:bg-slate-50 text-[#19242B] border border-slate-200 font-bold rounded-xl text-xs flex items-center gap-1.5 shadow-xs min-h-[44px]">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>خروجی اکسل</span>
            </a>

            <a href="{{ route('admin.live-orders.index') }}" class="px-3.5 py-2.5 bg-[#F8FAFC] hover:bg-slate-100 text-[#19242B] border border-slate-200 font-bold rounded-xl text-xs flex items-center gap-2 shadow-xs min-h-[44px]">
                <span class="w-2 h-2 rounded-full bg-[#387780] animate-ping"></span>
                <span>مانیتور آشپزخانه (زنده)</span>
            </a>

            <a href="{{ route('daily-usage.index') }}" class="px-4 py-2.5 bg-[#387780] hover:bg-[#2E626A] text-white font-bold rounded-xl text-xs shadow-xs flex items-center gap-2 min-h-[44px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>ثبت سفارش جدید</span>
            </a>
        </div>
    </div>

    <!-- Orders Container -->
    <div class="bg-white border border-[#353535]/10 rounded-3xl overflow-hidden shadow-xs">
        <!-- Mobile Card View (sm:hidden) -->
        <div class="sm:hidden divide-y divide-[#353535]/10">
            @forelse($orders as $order)
                <div class="p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('orders.show', $order) }}" class="font-mono font-black text-xs text-[#284B63] hover:underline">
                            {{ $order->order_number ?? ('#' . $order->id) }}
                        </a>
                        @php
                            $stBadge = match($order->status) {
                                'pending' => ['bg-[#3C6E71]/15 text-[#8F5B1E] border border-[#3C6E71]/30', 'در انتظار تایید'],
                                'processing' => ['bg-[#3B729E]/15 text-[#2A577C] border border-[#3B729E]/30', 'در حال آماده‌سازی'],
                                'ready' => ['bg-[#3C6E71]/15 text-[#353535] border border-[#3C6E71]/30 font-bold', 'آماده تحویل'],
                                'shipping' => ['bg-[#3C6E71]/25 text-[#734A1B] border border-[#3C6E71]/40', 'ارسال با پیک'],
                                'completed' => ['bg-[#353535]/10 text-[#353535] border border-[#353535]/20', 'تکمیل شد'],
                                'cancelled' => ['bg-[#B44A4A]/10 text-[#B44A4A] border border-[#B44A4A]/30', 'لغو شد'],
                                default => ['bg-[#353535]/10 text-[#353535]', $order->status],
                            };
                        @endphp
                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-bold {{ $stBadge[0] }}">
                            {{ $stBadge[1] }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <div>
                            <span class="font-black text-[#353535] block">{{ $order->customer->name ?? ($order->customer_name ?? 'مشتری عبوری') }}</span>
                            @if($order->customer?->phone || $order->customer_phone)
                                <span class="text-[10px] text-[#353535]/60 font-mono" dir="ltr">{{ $order->customer?->phone ?? $order->customer_phone }}</span>
                            @endif
                        </div>
                        <span class="font-black text-[#284B63] tabular-nums text-sm">
                            {{ number_format($order->final_payable > 0 ? $order->final_payable : $order->total_price) }} <span class="text-[10px] font-normal text-[#353535]/60">تومان</span>
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-[11px] text-[#353535]/60 pt-1 border-t border-[#353535]/5">
                        <div class="flex items-center gap-1.5">
                            <span>{{ $order->order_type === 'courier' ? 'پیک شیراز' : ($order->isDineIn() ? 'حضوری' : 'بیرون‌بر') }}</span>
                            <span>•</span>
                            <span>{{ $order->payment_method === 'zarinpal' ? 'زرین‌پال' : ($order->payment_method === 'card_to_card' ? 'کارت به کارت' : 'نقدی/کارتخوان') }}</span>
                        </div>
                        <span class="tabular-nums text-[10px]">{{ \App\Helpers\JalaliHelper::format($order->created_at, 'Y/m/d H:i') }}</span>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-xs text-[#353535]/50">هیچ سفارشی ثبت نشده است.</div>
            @endforelse
        </div>

        <!-- Desktop Table View (hidden sm:block) -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-start text-xs">
                <thead>
                    <tr class="bg-[#F7F9FA]/60 text-[#353535]/70 border-b border-[#353535]/10">
                        <th class="p-4 font-bold">شماره سفارش</th>
                        <th class="p-4 font-bold">مشتری</th>
                        <th class="p-4 font-bold">نحوه دریافت</th>
                        <th class="p-4 font-bold">روش پرداخت</th>
                        <th class="p-4 font-bold">مبلغ نهایی</th>
                        <th class="p-4 font-bold">وضعیت سفارش</th>
                        <th class="p-4 font-bold">تاریخ و ساعت</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#353535]/5 text-[#353535]">
                    @forelse($orders as $order)
                        <tr class="hover:bg-[#F7F9FA]/35 transition-colors">
                            <td class="p-4 font-mono font-bold">
                                <a href="{{ route('orders.show', $order) }}" class="text-[#353535] hover:text-[#284B63] underline decoration-dotted">
                                    {{ $order->order_number ?? ('#' . $order->id) }}
                                </a>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-[#353535]">{{ $order->customer->name ?? ($order->customer_name ?? 'مشتری عبوری') }}</div>
                                @if($order->customer?->phone || $order->customer_phone)
                                    <div class="text-[11px] text-[#353535]/60 font-mono text-start tabular-nums" dir="ltr">{{ $order->customer?->phone ?? $order->customer_phone }}</div>
                                @endif
                            </td>
                            <td class="p-4">
                                @if($order->isCourier())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-[#3C6E71]/15 text-[#353535] text-[11px] font-bold border border-[#3C6E71]/20">
                                        <svg class="w-3 h-3 text-[#3C6E71]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                                        <span>پیک شیراز</span>
                                    </span>
                                @elseif($order->isDineIn())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-[#F7F9FA] text-[#353535] text-[11px] font-bold border border-[#353535]/10">
                                        <span>حضوری</span>
                                        @if($order->table_number)
                                            <span>(میز {{ $order->table_number }})</span>
                                        @else
                                            <span>(کانتر)</span>
                                        @endif
                                    </span>
                                @elseif($order->isTakeaway())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-amber-50 text-amber-800 text-[11px] font-bold border border-amber-200">
                                        <span>بیرون‌بر</span>
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-lg bg-[#353535]/5 text-[#353535]/70 text-[11px] font-bold">عادی</span>
                                @endif
                            </td>
                            <td class="p-4">
                                @if($order->payment_method === 'zarinpal')
                                    <span class="text-xs font-bold text-[#3C6E71]">زرین‌پال (آنلاین)</span>
                                @elseif($order->payment_method === 'card_to_card')
                                    <span class="text-xs font-bold text-[#284B63]">کارت به کارت</span>
                                @elseif($order->payment_method === 'subscription_credit')
                                    <span class="text-xs font-bold text-[#3C6E71]">سهمیه اشتراک</span>
                                @else
                                    <span class="text-xs font-bold text-[#353535]/70">نقدی / کارتخوان</span>
                                @endif
                            </td>
                            <td class="p-4 font-black text-[#284B63] text-sm tabular-nums">
                                {{ number_format($order->final_payable > 0 ? $order->final_payable : $order->total_price) }} <span class="text-[10px] text-[#353535]/60 font-normal">تومان</span>
                            </td>
                            <td class="p-4">
                                @php
                                    $stBadge = match($order->status) {
                                        'pending' => ['bg-[#3C6E71]/15 text-[#8F5B1E] border border-[#3C6E71]/30', 'در انتظار تایید'],
                                        'processing' => ['bg-[#3B729E]/15 text-[#2A577C] border border-[#3B729E]/30', 'در حال آماده‌سازی'],
                                        'ready' => ['bg-[#3C6E71]/15 text-[#353535] border border-[#3C6E71]/30 font-bold', 'آماده تحویل'],
                                        'shipping' => ['bg-[#3C6E71]/25 text-[#734A1B] border border-[#3C6E71]/40', 'ارسال با پیک'],
                                        'completed' => ['bg-[#353535]/10 text-[#353535] border border-[#353535]/20', 'تکمیل شد'],
                                        'cancelled' => ['bg-[#B44A4A]/10 text-[#B44A4A] border border-[#B44A4A]/30', 'لغو شد'],
                                        default => ['bg-[#353535]/10 text-[#353535]', $order->status],
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $stBadge[0] }}">
                                    {{ $stBadge[1] }}
                                </span>
                            </td>
                            <td class="p-4 text-xs text-[#353535]/60 tabular-nums">
                                {{ \App\Helpers\JalaliHelper::format($order->created_at, 'Y/m/d - H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-[#353535]/50">هیچ سفارشی ثبت نشده است.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="p-4 border-t border-[#353535]/10 bg-[#F7F9FA]/20">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
