@extends('layouts.customer')

@section('title', 'فاکتور سفارش ' . $order->order_number)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Print & Back bar -->
    <div class="flex items-center justify-between no-print">
        <a href="{{ route('customer.orders') }}" class="text-xs font-bold text-[#353535] hover:text-[#284B63] flex items-center gap-1.5 btn-tactile min-h-[44px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span>بازگشت به تاریخچه سفارش‌ها</span>
        </a>

        <button type="button" onclick="window.print()" class="px-4 py-2 bg-white border border-[#353535]/15 hover:border-[#3C6E71] text-[#353535] text-xs font-bold rounded-xl btn-tactile flex items-center gap-1.5 shadow-xs min-h-[44px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            <span>چاپ فاکتور</span>
        </button>
    </div>

    <!-- Official Invoice Paper Card -->
    <div class="bg-white border border-[#353535]/10 p-8 rounded-3xl shadow-xs space-y-6">
        <!-- Invoice Header -->
        <div class="flex items-center justify-between border-b border-[#353535]/10 pb-6">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo.jpeg') }}" alt="بی کافه" class="w-14 h-14 rounded-2xl object-cover border border-[#353535]/15 shadow-2xs">
                <div>
                    <h2 class="text-base font-black text-[#353535]">بی کافه (B Cafe)</h2>
                    <span class="text-xs text-[#353535]/65">فاکتور رسمی سفارش مشتری</span>
                </div>
            </div>

            <div class="text-end text-xs space-y-1">
                <div>شماره سفارش: <span class="font-mono font-bold text-[#353535]">{{ $order->order_number }}</span></div>
                <div class="text-[#353535]/65 tabular-nums">تاریخ: {{ \App\Helpers\JalaliHelper::format($order->created_at, 'Y/m/d - H:i') }}</div>
            </div>
        </div>

        <!-- Customer & Delivery Info -->
        <div class="grid grid-cols-2 gap-4 text-xs bg-[#F7F9FA]/40 p-4 rounded-2xl border border-[#353535]/10">
            <div class="space-y-1">
                <span class="text-[#353535]/65 block">مشخصات خریدار:</span>
                <div class="font-bold text-[#353535]">{{ $order->customer->name ?? ($order->customer_name ?? 'مشتری گرامی') }}</div>
                <div class="text-[#353535]/65 font-mono text-start tabular-nums" dir="ltr">{{ $order->customer->phone ?? ($order->customer_phone ?? '') }}</div>
            </div>

            <div class="space-y-1">
                <span class="text-[#353535]/65 block">نحوه تحویل:</span>
                <div class="font-bold text-[#3C6E71]">
                    {{ $order->getFulfillmentLabel() }}
                </div>
                @if($order->isDineIn() && $order->table_number)
                    <div class="text-[#353535]">شماره میز: {{ $order->table_number }}</div>
                @endif
                @if($order->isCourier() && $order->deliveryAddress)
                    <div class="text-[#353535]/65 text-[11px]">{{ $order->deliveryAddress->address_line }}</div>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-start text-xs">
                <thead>
                    <tr class="bg-[#F7F9FA]/60 text-[#353535]/70 border-b border-[#353535]/10">
                        <th class="p-3 font-bold">شرح کالا / خدمات</th>
                        <th class="p-3 font-bold w-20 text-center">تعداد</th>
                        <th class="p-3 font-bold">قیمت واحد</th>
                        <th class="p-3 font-bold text-end">مبلغ کل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#353535]/5 text-[#353535]">
                    @foreach($order->items as $item)
                        <tr>
                            <td class="p-3 font-bold text-[#353535]">{{ $item->product->name ?? 'آیتم' }}</td>
                            <td class="p-3 text-center tabular-nums font-bold">{{ $item->quantity }}</td>
                            <td class="p-3 text-[#353535]/65 tabular-nums">{{ number_format($item->price) }} تومان</td>
                            <td class="p-3 font-black text-[#284B63] text-end tabular-nums">{{ number_format($item->total_price) }} تومان</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals & Payment Breakdown -->
        <div class="space-y-2 pt-4 border-t border-[#353535]/10 text-xs">
            <div class="flex items-center justify-between text-[#353535]/70">
                <span>جمع کل اقلام:</span>
                <span class="tabular-nums font-bold">{{ number_format($order->total_price) }} تومان</span>
            </div>

            @if($order->discount_amount > 0)
                <div class="flex items-center justify-between text-[#3C6E71]">
                    <span>تخفیف باشگاه مشتریان / اشتراک:</span>
                    <span class="tabular-nums font-bold">-{{ number_format($order->discount_amount) }} تومان</span>
                </div>
            @endif

            @if($order->isCourier())
                <div class="flex items-center justify-between text-[#353535]/70">
                    <span>هزینه ارسال با پیک:</span>
                    <span class="tabular-nums font-bold">{{ number_format($order->delivery_fee) }} تومان</span>
                </div>
            @endif

            <div class="flex items-center justify-between pt-3 border-t border-[#353535]/10 text-sm font-black text-[#353535]">
                <span>مبلغ نهایی پرداخت شده:</span>
                <span class="text-[#284B63] text-base tabular-nums">{{ number_format($order->final_payable) }} تومان</span>
            </div>
        </div>

        <!-- Footer Note -->
        <div class="text-center pt-4 border-t border-dashed border-[#353535]/10 text-[11px] text-[#353535]/50">
            با سپاس از انتخاب بی کافه • شیراز، بلوار ارم • تلفن پشتیبانی کافه
        </div>
    </div>

</div>
@endsection
