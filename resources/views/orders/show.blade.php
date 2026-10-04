@extends('layouts.app')

@section('title', 'جزئیات سفارش ' . ($order->order_number ?? '#' . $order->id))
@section('header_title', 'مدیریت و جزئیات سفارش ' . ($order->order_number ?? '#' . $order->id))

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    <!-- Header & Navigation -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('orders.index') }}" class="w-10 h-10 rounded-xl bg-white border border-[#353535]/15 hover:bg-[#F7F9FA] text-[#353535] flex items-center justify-center transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-black text-[#353535]">سفارش {{ $order->order_number ?? ('#' . $order->id) }}</h2>
                    <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold
                        @if($order->status === 'completed') bg-[#3C6E71]/15 text-[#353535]
                        @elseif($order->status === 'cancelled') bg-[#B44A4A]/10 text-[#B44A4A]
                        @elseif($order->status === 'awaiting_payment_verification') bg-[#284B63]/15 text-[#284B63]
                        @elseif($order->status === 'preparing') bg-[#284B63]/15 text-[#284B63]
                        @else bg-[#F7F9FA] text-[#353535] border border-[#353535]/15 @endif">
                        {{ match($order->status) {
                            'pending' => 'در انتظار تایید',
                            'awaiting_payment_verification' => 'در انتظار تایید فیش کارت‌به‌کارت',
                            'preparing' => 'در حال آماده‌سازی در بار',
                            'ready' => 'آماده تحویل / ارسال',
                            'shipping', 'out_for_delivery' => 'در مسیر ارسال با پیک',
                            'delivered', 'completed' => 'تکمیل و تحویل شده',
                            'cancelled' => 'لغو شده',
                            default => $order->status
                        } }}
                    </span>
                </div>
                <p class="text-xs text-[#353535]/60 mt-0.5">ثبت شده در: {{ $order->created_at->format('Y/m/d - H:i') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('orders.receipt', $order) }}" target="_blank" class="px-4 py-2 bg-[#387780] text-white rounded-xl text-xs font-bold hover:bg-[#2E636B] transition-colors flex items-center gap-1.5 shadow-xs btn-tactile">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>چاپ فیش حرارتی (80mm)</span>
            </a>
            <a href="{{ route('admin.live-orders.index') }}" class="px-4 py-2 bg-slate-800 text-white rounded-xl text-xs font-bold hover:bg-slate-900 transition-colors flex items-center gap-1.5 shadow-xs btn-tactile">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <span>بورد سفارشات زنده</span>
            </a>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: Order Items & Customer -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Customer & Fulfillment Mode Card -->
            <div class="bg-white border border-[#353535]/12 rounded-3xl p-6 shadow-xs space-y-4">
                <h3 class="text-xs font-black text-[#353535] border-b border-[#353535]/10 pb-3 flex items-center justify-between">
                    <span>اطلاعات مشتری و نوع تحویل</span>
                    <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full {{ $order->isDelivery() ? 'bg-[#284B63]/15 text-[#284B63]' : 'bg-[#3C6E71]/15 text-[#353535]' }}">
                        {{ $order->getFulfillmentLabel() }}
                    </span>
                </h3>

                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-[#353535]/60 block mb-0.5">نام مشتری:</span>
                        <span class="font-bold text-[#353535]">{{ $order->customer->name ?? 'مشتری عبوری' }}</span>
                    </div>
                    <div>
                        <span class="text-[#353535]/60 block mb-0.5">شماره تماس:</span>
                        <span class="font-mono font-bold text-[#353535]" dir="ltr">{{ $order->customer->phone ?? '-' }}</span>
                    </div>

                    @if($order->isDelivery())
                        @php
                            $deliveryLat = $order->deliveryAddress?->latitude ?? $order->delivery_latitude;
                            $deliveryLng = $order->deliveryAddress?->longitude ?? $order->delivery_longitude;
                            $deliveryAddrText = $order->deliveryAddress?->address_line ?? $order->delivery_address ?? 'آدرس مشخص نشده';
                        @endphp
                        <div class="col-span-2 pt-2 border-t border-[#353535]/8 space-y-3">
                            <div>
                                <span class="text-[#353535]/60 block mb-0.5">آدرس تحویل در شیراز:</span>
                                <span class="font-bold text-[#353535] text-sm block">{{ $deliveryAddrText }}</span>
                                @if($order->deliveryAddress?->building_unit)
                                    <span class="text-[11px] text-[#353535]/70 block mt-0.5">پلاک / واحد: {{ $order->deliveryAddress->building_unit }}</span>
                                @endif
                                @if($order->deliveryAddress?->deliveryZone)
                                    <span class="text-[11px] text-[#284B63] font-bold block mt-1">منطقه تحت پوشش: {{ $order->deliveryAddress->deliveryZone->name }}</span>
                                @endif
                            </div>

                            @if($deliveryLat && $deliveryLng)
                                <div class="p-3 bg-slate-50 border border-slate-200 rounded-2xl space-y-2.5">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-[#387780]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                            <span>موقعیت دقیق ثبت‌شده روی نقشه نشان:</span>
                                        </span>
                                        <div class="flex items-center gap-1.5">
                                            <a href="https://neshan.org/maps/@ {{ $deliveryLat }},{{ $deliveryLng }},16z" target="_blank" class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-lg text-[11px] font-bold transition-colors">مسیریابی نشان</a>
                                            <a href="https://balad.ir/location?latitude={{ $deliveryLat }}&longitude={{ $deliveryLng }}" target="_blank" class="px-2.5 py-1 bg-sky-50 hover:bg-sky-100 text-sky-700 border border-sky-200 rounded-lg text-[11px] font-bold transition-colors">مسیریابی بلد</a>
                                            <a href="https://www.google.com/maps?q={{ $deliveryLat }},{{ $deliveryLng }}" target="_blank" class="px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 rounded-lg text-[11px] font-bold transition-colors">Google Maps</a>
                                        </div>
                                    </div>
                                    <div id="order-admin-map" class="h-64 w-full rounded-xl border border-slate-200 shadow-inner z-0"></div>
                                    <div class="text-[10px] text-slate-500 font-mono text-start dir-ltr">
                                        {{ $deliveryLat }}, {{ $deliveryLng }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="col-span-2 pt-2 border-t border-[#353535]/8">
                            <span class="text-[#353535]/60 block mb-0.5">نحوه تحویل:</span>
                            <span class="font-bold text-[#353535]">{{ $order->isDineIn() ? ($order->table_number ? ('سرو در سالن (میز شماره ' . $order->table_number . ')') : 'سرو در سالن (کانتر)') : 'بیرون‌بر (تحویل حضوری در کافه)' }}</span>
                        </div>
                    @endif

                    @if($order->notes)
                        <div class="col-span-2 p-3 rounded-xl bg-[#F7F9FA]/50 border border-[#353535]/10 text-xs">
                            <span class="font-bold text-[#284B63] block mb-0.5">یادداشت مشتری:</span>
                            <p class="text-[#353535]">{{ $order->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Items Table Card -->
            <div class="bg-white border border-[#353535]/12 rounded-3xl overflow-hidden shadow-xs">
                <div class="p-5 border-b border-[#353535]/10">
                    <h3 class="text-xs font-black text-[#353535]">اقلام سفارش ({{ $order->items->count() }} قلم)</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-start text-xs">
                        <thead>
                            <tr class="bg-[#F7F9FA]/40 text-[#353535]/70 border-b border-[#353535]/10">
                                <th class="p-4 font-black">ردیف</th>
                                <th class="p-4 font-black">محصول</th>
                                <th class="p-4 font-black text-center">تعداد</th>
                                <th class="p-4 font-black">قیمت واحد</th>
                                <th class="p-4 font-black">قیمت کل</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#353535]/8 text-[#353535]">
                            @foreach($order->items as $idx => $item)
                                <tr>
                                    <td class="p-4 text-[#353535]/60 font-mono">{{ $idx + 1 }}</td>
                                    <td class="p-4">
                                        <div class="font-bold">{{ $item->product->name ?? 'محصول حذف شده' }}</div>
                                        @if($item->notes)
                                            <div class="text-[11px] text-[#284B63] mt-0.5">{{ $item->notes }}</div>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center font-bold font-mono">{{ $item->quantity }}</td>
                                    <td class="p-4 tabular-nums">{{ number_format($item->price) }} تومان</td>
                                    <td class="p-4 font-bold tabular-nums">{{ number_format($item->total_price) }} تومان</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Right 1 Col: Payment & Status Controls -->
        <div class="space-y-6">

            <!-- Payment Summary Card -->
            <div class="bg-white border border-[#353535]/12 rounded-3xl p-6 shadow-xs space-y-4">
                <h3 class="text-xs font-black text-[#353535] border-b border-[#353535]/10 pb-3">اطلاعات مالی و پرداخت</h3>

                <div class="space-y-2.5 text-xs">
                    <div class="flex justify-between text-[#353535]/70">
                        <span>مبلغ سفارش:</span>
                        <span class="font-bold tabular-nums text-[#353535]">{{ number_format($order->total_price) }} تومان</span>
                    </div>

                    @if($order->delivery_fee > 0)
                        <div class="flex justify-between text-[#353535]/70">
                            <span>هزینه پیک شیراز:</span>
                            <span class="font-bold tabular-nums text-[#353535]">{{ number_format($order->delivery_fee) }} تومان</span>
                        </div>
                    @endif

                    @if($order->discount_amount > 0)
                        <div class="flex justify-between text-[#3C6E71]">
                            <span>تخفیف اعمال شده:</span>
                            <span class="font-bold tabular-nums">-{{ number_format($order->discount_amount) }} تومان</span>
                        </div>
                    @endif

                    <div class="pt-3 border-t border-[#353535]/10 flex justify-between items-center text-sm">
                        <span class="font-black text-[#353535]">مبلغ نهایی پرداختی:</span>
                        <span class="font-black text-[#284B63] tabular-nums">{{ number_format($order->final_payable) }} تومان</span>
                    </div>

                    <div class="pt-2 text-[11px] text-[#353535]/65">
                        <span>روش پرداخت: </span>
                        <span class="font-bold text-[#353535]">
                            {{ match($order->payment_method) {
                                'zarinpal' => 'درگاه پرداخت آنلاین زرین‌پال',
                                'card_to_card' => 'کارت به کارت (واریز بانکی)',
                                'subscription_credit' => 'سهمیه اعتبار اشتراک روزانه',
                                'cash' => 'نقدی / کارتخوان در کافه',
                                default => $order->payment_method
                            } }}
                        </span>
                    </div>
                </div>

                <!-- Card-to-Card Receipt Section -->
                @if($order->payment_method === 'card_to_card' && $order->latestPayment)
                    <div class="pt-3 border-t border-[#353535]/10 space-y-2">
                        <span class="text-xs font-bold text-[#353535] block">فیش واریزی ارسال‌شده:</span>
                        @if($order->latestPayment->receipt_image)
                            <a href="{{ route('admin.payments.receipt', $order->latestPayment) }}" target="_blank" class="block border border-[#353535]/15 rounded-xl overflow-hidden hover:opacity-90 transition-opacity">
                                <img src="{{ route('admin.payments.receipt', $order->latestPayment) }}" alt="رسید کارت‌به‌کارت" class="w-full h-40 object-cover">
                            </a>
                            <span class="text-[10px] text-[#353535]/60 block text-center">برای مشاهده اندازه اصلی روی تصویر کلیک کنید</span>
                        @else
                            <div class="p-3 rounded-xl bg-[#F7F9FA] text-center text-xs text-[#353535]/70">تصویر فیش ضمیمه نشده است.</div>
                        @endif

                        @if($order->latestPayment->tracking_number)
                            <div class="text-xs">
                                <span class="text-[#353535]/60">کد رهگیری: </span>
                                <span class="font-mono font-bold">{{ $order->latestPayment->tracking_number }}</span>
                            </div>
                        @endif

                        @if($order->status === 'awaiting_payment_verification' || $order->status === 'pending')
                            <div class="flex gap-2 pt-2">
                                <form method="POST" action="{{ route('admin.live-orders.verify-payment', $order->latestPayment) }}" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="w-full py-2 bg-[#3C6E71] hover:bg-[#4d572d] text-white rounded-xl text-xs font-bold transition-colors">
                                        تایید فیش
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.live-orders.verify-payment', $order->latestPayment) }}" class="flex-1" onsubmit="return confirm('آیا از رد فیش واریزی اطمینان دارید؟');">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="w-full py-2 bg-[#B44A4A] hover:bg-[#962f2f] text-white rounded-xl text-xs font-bold transition-colors">
                                        رد فیش
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Actions & Order Progression Card -->
            <div class="bg-white border border-[#353535]/12 rounded-3xl p-6 shadow-xs space-y-3">
                <h3 class="text-xs font-black text-[#353535] border-b border-[#353535]/10 pb-3">عملیات سفارش</h3>

                <form method="POST" action="{{ route('admin.live-orders.update-status', $order) }}" class="space-y-2">
                    @csrf
                    @if($order->status === 'pending')
                        <button type="submit" name="action" value="start_prep" class="w-full py-3 bg-[#284B63] hover:bg-[#1d374a] text-white font-bold rounded-xl text-xs transition-colors shadow-xs">
                            شروع آماده‌سازی در بار
                        </button>
                    @endif

                    @if($order->status === 'preparing' || $order->status === 'processing')
                        <button type="submit" name="action" value="ready" class="w-full py-3 bg-[#3C6E71] hover:bg-[#2e5557] text-white font-bold rounded-xl text-xs transition-colors shadow-xs">
                            {{ $order->isDelivery() ? 'آماده تحویل به پیک' : 'آماده تحویل به مشتری' }}
                        </button>
                    @endif

                    @if($order->status === 'ready' || $order->status === 'shipping' || $order->status === 'out_for_delivery')
                        <button type="submit" name="action" value="complete" class="w-full py-3 bg-[#3C6E71] hover:bg-[#4d572d] text-white font-bold rounded-xl text-xs transition-colors shadow-xs">
                            تکمیل سفارش و تحویل نهایی
                        </button>
                    @endif

                    @if(!in_array($order->status, ['completed', 'cancelled']))
                        <button type="submit" name="action" value="cancel" class="w-full py-2 bg-white hover:bg-[#B44A4A]/10 text-[#B44A4A] border border-[#B44A4A]/20 font-bold rounded-xl text-xs transition-colors" onclick="return confirm('آیا از لغو این سفارش اطمینان دارید؟');">
                            لغو سفارش
                        </button>
                    @endif
                </form>
            </div>

        </div>

    </div>

</div>
@endsection

@php
    $scriptLat = $order->deliveryAddress?->latitude ?? $order->delivery_latitude;
    $scriptLng = $order->deliveryAddress?->longitude ?? $order->delivery_longitude;
    $scriptAddr = $order->deliveryAddress?->address_line ?? $order->delivery_address ?? 'آدرس تحویل سفارش';
@endphp

@if($order->isDelivery() && $scriptLat && $scriptLng)
@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var lat = {{ (float) $scriptLat }};
    var lng = {{ (float) $scriptLng }};
    var map = L.map('order-admin-map', {
        center: [lat, lng],
        zoom: 16,
        scrollWheelZoom: false
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© نشان / OpenStreetMap'
    }).addTo(map);

    L.marker([lat, lng])
        .addTo(map)
        .bindPopup('<b>محل تحویل سفارش:</b><br>{{ addslashes($scriptAddr) }}')
        .openPopup();
});
</script>
@endpush
@endif
