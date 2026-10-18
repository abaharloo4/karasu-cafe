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
                    <div x-data="{
                        lightboxOpen: false,
                        rejectModalOpen: false,
                        zoom: 1,
                        rotation: 0,
                        rejectAction: 'request_reupload',
                        presetReason: 'مبلغ واریزی کمتر از فاکتور است',
                        customReason: 'مبلغ واریزی کمتر از فاکتور است',
                        copied: ''
                    }" class="pt-3 border-t border-[#353535]/10 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black text-[#353535]">فیش واریز کارت‌به‌کارت:</span>
                            @if($order->latestPayment->status === 'successful')
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">تایید شده</span>
                            @elseif($order->latestPayment->status === 'failed')
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-300">رد شده</span>
                            @else
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">در انتظار تایید</span>
                            @endif
                        </div>

                        @if($order->latestPayment->receipt_image)
                            <div class="relative group cursor-pointer rounded-2xl overflow-hidden border border-[#353535]/15 bg-slate-900" @click="lightboxOpen = true">
                                <img src="{{ route('admin.payments.receipt', $order->latestPayment) }}" alt="رسید کارت‌به‌کارت" class="w-full h-44 object-contain group-hover:scale-105 transition-transform duration-200">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2 text-white text-xs font-bold backdrop-blur-2xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>
                                    <span>مشاهده، زوم و بررسی دقیق</span>
                                </div>
                            </div>
                        @else
                            <div class="p-3.5 rounded-xl bg-[#F7F9FA] text-center text-xs text-[#353535]/70 border border-[#353535]/10">تصویر فیش ضمیمه نشده است.</div>
                        @endif

                        <!-- Fast Copy Information Badges -->
                        <div class="space-y-1.5 text-xs bg-[#F7F9FA] p-3 rounded-xl border border-[#353535]/10">
                            <div class="flex items-center justify-between">
                                <span class="text-[#353535]/65 text-[11px]">مبلغ فاکتور:</span>
                                <div class="flex items-center gap-1.5">
                                    <span class="font-black text-[#284B63] tabular-nums">{{ number_format($order->final_payable) }} ت</span>
                                    <button type="button" @click="navigator.clipboard.writeText('{{ $order->final_payable }}'); copied = 'amt'; setTimeout(() => copied = '', 1500)" class="text-[10px] text-slate-500 hover:text-slate-800 font-bold px-1.5 py-0.5 bg-white rounded border border-slate-200">
                                        <span x-text="copied === 'amt' ? 'کپی شد!' : 'کپی'"></span>
                                    </button>
                                </div>
                            </div>
                            @if($order->latestPayment->tracking_number)
                                <div class="flex items-center justify-between pt-1 border-t border-[#353535]/5">
                                    <span class="text-[#353535]/65 text-[11px]">کد رهگیری:</span>
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-mono font-bold">{{ $order->latestPayment->tracking_number }}</span>
                                        <button type="button" @click="navigator.clipboard.writeText('{{ $order->latestPayment->tracking_number }}'); copied = 'track'; setTimeout(() => copied = '', 1500)" class="text-[10px] text-slate-500 hover:text-slate-800 font-bold px-1.5 py-0.5 bg-white rounded border border-slate-200">
                                            <span x-text="copied === 'track' ? 'کپی شد!' : 'کپی'"></span>
                                        </button>
                                    </div>
                                </div>
                            @endif
                            @if($order->latestPayment->card_pan_mask)
                                <div class="flex items-center justify-between pt-1 border-t border-[#353535]/5">
                                    <span class="text-[#353535]/65 text-[11px]">کارت مبدأ:</span>
                                    <span class="font-mono font-bold" dir="ltr">{{ $order->latestPayment->card_pan_mask }}</span>
                                </div>
                            @endif
                        </div>

                        @if($order->status === 'awaiting_payment_verification' || $order->status === 'pending')
                            <div class="flex gap-2 pt-1">
                                <form method="POST" action="{{ route('admin.live-orders.verify-payment', $order->latestPayment) }}" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="w-full py-2.5 bg-[#3C6E71] hover:bg-[#284B63] text-white rounded-xl text-xs font-bold transition-colors shadow-xs btn-tactile flex items-center justify-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span>تایید فیش</span>
                                    </button>
                                </form>
                                <button type="button" @click="rejectModalOpen = true" class="flex-1 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold transition-colors btn-tactile">
                                    رد فیش / اصلاح...
                                </button>
                            </div>
                        @endif

                        <!-- Inspection Lightbox Modal -->
                        @if($order->latestPayment->receipt_image)
                            <div x-show="lightboxOpen" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;" @keydown.escape.window="lightboxOpen = false">
                                <div class="bg-white rounded-3xl max-w-2xl w-full max-h-[90vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden" @click.outside="lightboxOpen = false">
                                    <div class="shrink-0 flex items-center justify-between px-5 py-3.5 border-b border-slate-100 bg-slate-50/80">
                                        <h4 class="text-xs font-black text-slate-900">بررسی بزرگ‌نمایی فیش سفارش #{{ $order->order_number }}</h4>
                                        <button type="button" @click="lightboxOpen = false" class="inline-flex items-center gap-1 px-3 py-1 rounded-xl bg-slate-200/80 hover:bg-slate-300 text-slate-700 text-xs font-bold transition-colors">
                                            <span>بستن</span>
                                            <span class="text-sm font-black">&times;</span>
                                        </button>
                                    </div>
                                    <div class="flex-1 overflow-y-auto p-5">
                                        <div class="relative bg-slate-950 rounded-2xl overflow-hidden min-h-[300px] max-h-[460px] flex items-center justify-center border border-slate-700 shadow-inner">
                                            <img src="{{ route('admin.payments.receipt', $order->latestPayment) }}" alt="فیش"
                                                 class="max-h-[420px] w-auto object-contain transition-transform duration-200"
                                                 :style="`transform: scale(${zoom}) rotate(${rotation}deg);`">
                                            <div class="absolute top-3 end-3 flex items-center gap-1.5 bg-slate-900/80 backdrop-blur-md p-1.5 rounded-xl border border-white/10 shadow-lg">
                                                <button type="button" @click="zoom = Math.min(zoom + 0.3, 3)" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-bold">+</button>
                                                <button type="button" @click="zoom = Math.max(zoom - 0.3, 0.8)" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-bold">-</button>
                                                <button type="button" @click="rotation = (rotation + 90) % 360" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-bold">↻</button>
                                                <button type="button" @click="zoom = 1; rotation = 0" class="px-2 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-[10px] text-white font-bold">ریست</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="shrink-0 px-5 py-3 border-t border-slate-100 bg-slate-50/80 flex justify-end">
                                        <button type="button" @click="lightboxOpen = false" class="px-5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 rounded-xl text-xs font-bold transition-colors">بستن پنجره</button>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Professional Rejection Modal -->
                        <div x-show="rejectModalOpen" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;" @keydown.escape.window="rejectModalOpen = false">
                            <div class="bg-white rounded-3xl max-w-lg w-full max-h-[90vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden" @click.outside="rejectModalOpen = false">
                                <div class="shrink-0 flex items-center justify-between px-5 py-3.5 border-b border-slate-100 bg-slate-50/80">
                                    <h4 class="text-xs font-black text-rose-800 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        <span>رد فیش سفارش #{{ $order->order_number }}</span>
                                    </h4>
                                    <button type="button" @click="rejectModalOpen = false" class="inline-flex items-center gap-1 px-3 py-1 rounded-xl bg-slate-200/80 hover:bg-slate-300 text-slate-700 text-xs font-bold transition-colors">
                                        <span>بستن</span>
                                        <span class="text-sm font-black">&times;</span>
                                    </button>
                                </div>

                                <form method="POST" action="{{ route('admin.live-orders.verify-payment', $order->latestPayment) }}" class="flex-1 flex flex-col min-h-0">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">

                                    <div class="flex-1 overflow-y-auto p-5 space-y-3.5">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1.5">اقدام سیستم:</label>
                                            <div class="grid grid-cols-2 gap-2 text-xs">
                                                <label :class="rejectAction === 'request_reupload' ? 'bg-amber-100 border-amber-400 font-bold text-amber-950' : 'bg-white border-slate-200 text-slate-700'" class="p-2.5 rounded-xl border flex items-center gap-2 cursor-pointer transition-colors shadow-2xs">
                                                    <input type="radio" name="reject_action" value="request_reupload" x-model="rejectAction" class="text-amber-600">
                                                    <div>
                                                        <span class="block">درخواست فیش جدید</span>
                                                        <span class="text-[10px] text-slate-500">سفارش فعال می‌ماند</span>
                                                    </div>
                                                </label>
                                                <label :class="rejectAction === 'cancel_order' ? 'bg-rose-100 border-rose-400 font-bold text-rose-950' : 'bg-white border-slate-200 text-slate-700'" class="p-2.5 rounded-xl border flex items-center gap-2 cursor-pointer transition-colors shadow-2xs">
                                                    <input type="radio" name="reject_action" value="cancel_order" x-model="rejectAction" class="text-rose-600">
                                                    <div>
                                                        <span class="block">لغو قطعی سفارش</span>
                                                        <span class="text-[10px] text-slate-500">لغو فاکتور و لغو سفارش</span>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">علت رد فیش:</label>
                                            <select x-model="presetReason" @change="if(presetReason !== 'other') customReason = presetReason" class="w-full text-xs rounded-xl border border-slate-300 p-2.5 bg-white text-slate-800">
                                                <option value="مبلغ واریزی کمتر از فاکتور است">مبلغ واریزی کمتر از فاکتور است</option>
                                                <option value="تصویر فیش تار، ناخوانا یا ناقص است">تصویر فیش تار، ناخوانا یا ناقص است</option>
                                                <option value="وجه به حساب کافه ننشسته است">وجه به حساب کافه ننشسته است</option>
                                                <option value="فیش تکراری، نامعتبر یا فاقد کد ارجاع است">فیش تکراری، نامعتبر یا فاقد کد ارجاع است</option>
                                                <option value="other">سایر (توضیح دلخواه)...</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">متن توضیحی برای مشتری (ارسال با پیامک و اعلان):</label>
                                            <textarea name="reason" x-model="customReason" rows="2" class="w-full text-xs rounded-xl border border-rose-300 p-2.5 bg-rose-50/30 focus:border-rose-500 focus:ring-1 focus:ring-rose-500" required></textarea>
                                        </div>
                                    </div>

                                    <div class="shrink-0 px-5 py-3.5 border-t border-slate-100 bg-slate-50/80 flex items-center justify-between gap-2">
                                        <button type="button" @click="rejectModalOpen = false" class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-xl text-xs font-bold transition-colors">انصراف</button>
                                        <button type="submit" :class="rejectAction === 'request_reupload' ? 'bg-amber-600 hover:bg-amber-700' : 'bg-rose-600 hover:bg-rose-700'" class="px-5 py-2 text-white font-black text-xs rounded-xl shadow-xs transition-colors">
                                            <span x-text="rejectAction === 'request_reupload' ? 'ارسال درخواست اصلاح فیش به مشتری' : 'تایید رد فیش و لغو سفارش'"></span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
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
