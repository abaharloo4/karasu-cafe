@extends('layouts.app')

@section('title', 'مانیتور زنده سفارشات کافه (آشپزخانه و بار)')
@section('header_title', 'مانیتور زنده سفارشات کافه - ۴ مرحله')

@section('content')
<script>
    window.__INITIAL_LIVE_ORDERS__ = @json($initialOrders ?? []);
</script>
<div class="space-y-6" x-data="liveKitchenBoard(window.__INITIAL_LIVE_ORDERS__)">

    <!-- Header Actions Bar -->
    <div class="bg-white border border-slate-200/80 p-4 sm:p-5 rounded-2xl flex flex-wrap items-center justify-between gap-4 shadow-xs">
        <div class="flex items-center gap-3">
            <div class="relative flex items-center justify-center">
                <div class="w-3 h-3 rounded-full bg-[#387780]"></div>
                <div class="absolute w-6 h-6 rounded-full bg-[#387780]/30 animate-ping"></div>
            </div>
            <div>
                <h2 class="text-sm sm:text-base font-black text-slate-800 flex items-center gap-2">
                    <span>تابلوی زنده سفارشات بی کافه</span>
                    <span class="text-[11px] font-bold text-slate-400">| بروزرسانی لحظه‌ای هر ۵ ثانیه</span>
                </h2>
                <span class="text-xs text-slate-400">پایش و تغییر وضعیت سفارش‌های آنلاین و حضوری در ۴ مرحله کاری</span>
            </div>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <!-- Search Input -->
            <div class="relative">
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" x-model="searchQuery" placeholder="جستجوی سفارش یا مشتری..." class="block w-full min-h-[44px] pl-3 pr-10 text-xs text-slate-800 bg-slate-50 border border-slate-200 rounded-xl focus:ring-[#387780] focus:border-[#387780] placeholder-slate-400 font-bold transition-all">
            </div>
            <!-- Audio Alert Toggle -->
            <button type="button" @click="toggleSound()"
                    :class="soundEnabled ? 'bg-teal-50 text-[#387780] border-teal-200 ring-1 ring-teal-200' : 'bg-rose-50 text-rose-600 border-rose-200'"
                    class="px-3.5 py-2 rounded-xl border text-xs font-bold btn-tactile flex items-center gap-2 transition-all">
                <template x-if="soundEnabled">
                    <svg class="w-4 h-4 text-[#387780]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
                </template>
                <template x-if="!soundEnabled">
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"/></svg>
                </template>
                <span x-text="soundEnabled ? 'صدای زنگ: فعال' : 'صدای زنگ: قطع'"></span>
            </button>

            <!-- Test Chime Button -->
            <button type="button" @click="playChime()" class="px-3 py-2 bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-xl text-xs font-bold btn-tactile inline-flex items-center gap-1.5">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span>تست صدا</span>
            </button>
        </div>
    </div>

    <!-- 4 Tabs Navigation Bar -->
    <div class="bg-white border border-slate-200/80 p-2 rounded-2xl shadow-xs flex items-center gap-2 overflow-x-auto custom-scrollbar">
        <!-- Tab 1: New & Pending Orders -->
        <button type="button" @click="activeTab = 'pending'"
                :class="activeTab === 'pending' ? 'bg-amber-500 text-white shadow-xs' : 'bg-slate-50 hover:bg-slate-100 text-slate-600'"
                class="flex-1 py-3 px-4 rounded-xl font-bold text-xs btn-tactile flex items-center justify-center gap-2 whitespace-nowrap min-h-[46px]">
            <span>۱. سفارشات جدید و در انتظار</span>
            <span :class="activeTab === 'pending' ? 'bg-white text-amber-700' : 'bg-amber-100 text-amber-800'"
                  class="px-2 py-0.5 rounded-full text-[11px] font-black tabular-nums"
                  x-text="pendingOrders.length"></span>
        </button>

        <!-- Tab 2: Preparing Orders -->
        <button type="button" @click="activeTab = 'preparing'"
                :class="activeTab === 'preparing' ? 'bg-[#387780] text-white shadow-xs' : 'bg-slate-50 hover:bg-slate-100 text-slate-600'"
                class="flex-1 py-3 px-4 rounded-xl font-bold text-xs btn-tactile flex items-center justify-center gap-2 whitespace-nowrap min-h-[46px]">
            <span>۲. در حال آماده‌سازی</span>
            <span :class="activeTab === 'preparing' ? 'bg-white text-teal-800' : 'bg-teal-100 text-teal-800'"
                  class="px-2 py-0.5 rounded-full text-[11px] font-black tabular-nums"
                  x-text="preparingOrders.length"></span>
        </button>

        <!-- Tab 3: Ready Orders -->
        <button type="button" @click="activeTab = 'ready'"
                :class="activeTab === 'ready' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-50 hover:bg-slate-100 text-slate-600'"
                class="flex-1 py-3 px-4 rounded-xl font-bold text-xs btn-tactile flex items-center justify-center gap-2 whitespace-nowrap min-h-[46px]">
            <span>۳. آماده تحویل و ارسال</span>
            <span :class="activeTab === 'ready' ? 'bg-white text-emerald-800' : 'bg-emerald-100 text-emerald-800'"
                  class="px-2 py-0.5 rounded-full text-[11px] font-black tabular-nums"
                  x-text="readyOrders.length"></span>
        </button>

        <!-- Tab 4: Completed Orders -->
        <button type="button" @click="activeTab = 'completed'"
                :class="activeTab === 'completed' ? 'bg-slate-800 text-white shadow-xs' : 'bg-slate-50 hover:bg-slate-100 text-slate-600'"
                class="flex-1 py-3 px-4 rounded-xl font-bold text-xs btn-tactile flex items-center justify-center gap-2 whitespace-nowrap min-h-[46px]">
            <span>۴. تکمیل شده / تحویل داده شده</span>
            <span :class="activeTab === 'completed' ? 'bg-white text-slate-800' : 'bg-slate-200 text-slate-700'"
                  class="px-2 py-0.5 rounded-full text-[11px] font-black tabular-nums"
                  x-text="completedOrders.length"></span>
        </button>
    </div>

    <!-- Active Tab Orders Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <template x-for="order in currentTabOrders" :key="order.id">
            <div class="bg-white border border-slate-200/90 rounded-2xl p-5 shadow-xs hover:shadow-md transition-all space-y-4 flex flex-col justify-between">
                
                <!-- Order Card Top Bar -->
                <div class="space-y-3">
                    <div class="flex items-start justify-between gap-2 border-b border-slate-100 pb-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-base font-black text-slate-900" x-text="order.order_number"></span>
                                <span class="text-[11px] text-slate-400 tabular-nums" x-text="order.created_at_time"></span>
                            </div>
                            <div class="text-xs font-bold text-slate-800 mt-1 flex items-center gap-1.5">
                                <span x-text="order.customer_name"></span>
                                <template x-if="order.customer_phone">
                                    <a :href="'tel:' + order.customer_phone" class="text-slate-400 hover:text-[#387780] font-mono text-[11px]" x-text="'(' + order.customer_phone + ')'"></a>
                                </template>
                            </div>
                        </div>

                        <!-- Type Badge -->
                        <div class="text-end">
                            <span :class="order.is_delivery ? 'bg-teal-50 text-[#387780] border-teal-200' : 'bg-slate-100 text-slate-700 border-slate-200'"
                                  class="px-2.5 py-1 rounded-lg text-xs font-bold block border"
                                  x-text="order.fulfillment_label || (order.is_dine_in ? ('حضوری • ' + (order.table_number ? 'میز ' + order.table_number : 'کانتر')) : (order.is_takeaway ? 'بیرون‌بر (حضوری)' : 'پیک ارسال شیراز'))">
                            </span>
                            <span class="text-xs font-black text-slate-900 tabular-nums block mt-1" x-text="order.final_payable_formatted + ' ت'"></span>
                        </div>
                    </div>

                    <!-- Delivery Full Address Box (Only if order is delivery AND has address/coordinates) -->
                    <template x-if="order.is_delivery && (order.address || (order.latitude && order.longitude))">
                        <div class="bg-teal-50/60 p-3.5 rounded-2xl border border-teal-200/70 space-y-2 text-xs">
                            <div class="flex items-center justify-between text-teal-900 font-extrabold">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-[#387780]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                    <span>موقعیت و آدرس تحویل سفارش:</span>
                                </span>
                                <span class="text-[11px] bg-white px-2.5 py-0.5 rounded-md border border-teal-200 font-bold" x-text="order.delivery_zone || 'محدوده شیراز'"></span>
                            </div>

                            <template x-if="order.address">
                                <p class="text-slate-800 leading-relaxed font-semibold" x-text="order.address"></p>
                            </template>
                            <template x-if="!order.address">
                                <p class="text-amber-800 text-[11px] font-bold bg-amber-50/80 p-2 rounded-lg border border-amber-200">
                                    آدرس متنی در سفارش ثبت نشده است (جهت دریافت آدرس دقیق با مشتری تماس بگیرید).
                                </p>
                            </template>

                            <template x-if="order.unit || order.building_number">
                                <div class="text-[11px] text-slate-600 font-medium flex items-center gap-2">
                                    <span x-show="order.building_number" x-text="'پلاک: ' + order.building_number"></span>
                                    <span x-show="order.unit" x-text="'واحد: ' + order.unit"></span>
                                </div>
                            </template>

                            <template x-if="order.order_notes">
                                <div class="text-[11px] text-slate-700 bg-white p-2 rounded-lg border border-teal-100">
                                    <span class="font-bold text-slate-900">یادداشت سفارش: </span>
                                    <span x-text="order.order_notes"></span>
                                </div>
                            </template>

                            <!-- Map coordinates and interactive location trigger -->
                            <template x-if="order.latitude && order.longitude">
                                <div class="pt-2 border-t border-teal-200/50 flex flex-wrap items-center justify-between gap-1.5">
                                    <button type="button" @click="openMapModal(order)"
                                            class="px-2.5 py-1.5 bg-[#387780] hover:bg-[#2E626A] text-white rounded-xl text-[11px] font-bold flex items-center gap-1.5 shadow-xs transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                        <span>مشاهده پین روی نقشه</span>
                                    </button>
                                    <div class="flex items-center gap-1">
                                        <a :href="order.neshan_url" target="_blank" class="px-2.5 py-1 bg-white hover:bg-slate-50 text-teal-800 border border-teal-300 rounded-lg text-[10px] font-bold transition-colors">نشان</a>
                                        <a :href="order.google_maps_url" target="_blank" class="px-2.5 py-1 bg-white hover:bg-slate-50 text-teal-800 border border-teal-300 rounded-lg text-[10px] font-bold transition-colors">گوگل مپ</a>
                                        <a :href="order.balad_url" target="_blank" class="px-2.5 py-1 bg-white hover:bg-slate-50 text-teal-800 border border-teal-300 rounded-lg text-[10px] font-bold transition-colors">بلد</a>
                                    </div>
                                </div>
                            </template>

                            <template x-if="order.is_delivery && (!order.latitude || !order.longitude)">
                                <div class="text-[10px] text-amber-700 bg-amber-50 px-2 py-1 rounded-md border border-amber-200 flex items-center gap-1 mt-1">
                                    <svg class="w-3.5 h-3.5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    <span>موقعیت نقشه برای این سفارش ثبت نشده است.</span>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- Items List -->
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 space-y-2 text-xs">
                        <div class="font-bold text-slate-500 text-[11px]">اقلام سفارش:</div>
                        <template x-for="item in order.items" :key="item.name">
                            <div class="space-y-1 pb-1 border-b border-slate-200/40 last:border-0 last:pb-0">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-800" x-text="item.name"></span>
                                    <span class="font-black text-slate-900 bg-white px-2 py-0.5 rounded border border-slate-200 tabular-nums" x-text="'× ' + item.quantity"></span>
                                </div>
                                <template x-if="item.notes">
                                    <div class="text-[10px] text-amber-800 font-bold bg-amber-50 px-2 py-0.5 rounded border border-amber-200" x-text="'یادداشت: ' + item.notes"></div>
                                </template>
                            </div>
                        </template>
                    </div>

                    <!-- Payment Information & Card-to-Card Receipt -->
                    <div class="flex items-center justify-between text-xs px-1 text-slate-500">
                        <span>روش پرداخت: <strong class="text-slate-800" x-text="order.payment_method_label"></strong></span>
                        <span :class="order.payment_status === 'paid' ? 'text-emerald-600 font-bold' : 'text-amber-600 font-bold'"
                              x-text="order.payment_status === 'paid' ? 'پرداخت شده' : 'در انتظار تسویه'"></span>
                    </div>

                    <!-- Card-to-Card Receipt Display & Verification -->
                    <template x-if="order.payment_method === 'card_to_card'">
                        <div class="space-y-2">
                            <template x-if="order.receipt_image">
                                <div class="p-3 rounded-2xl bg-amber-50/70 border border-amber-200/80 space-y-2.5">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-bold text-amber-900 flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                            <span>فیش واریزی کارت‌به‌کارت:</span>
                                        </span>
                                        <span class="text-[10px] bg-amber-100/90 text-amber-800 font-bold px-2 py-0.5 rounded-md">پیوست شده</span>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <!-- Thumbnail with click-to-enlarge -->
                                        <div @click="openReceiptModal(order)"
                                             class="relative w-16 h-16 rounded-xl overflow-hidden border-2 border-amber-300 shadow-2xs cursor-pointer group shrink-0 bg-white">
                                            <img :src="order.receipt_image" alt="فیش واریزی" class="w-full h-full object-cover group-hover:scale-110 transition-transform">
                                            <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                            </div>
                                        </div>

                                        <div class="flex-1 min-w-0 space-y-1">
                                            <template x-if="order.card_pan">
                                                <div class="text-[11px] text-slate-700 font-mono" dir="ltr">
                                                    <span class="text-slate-400 font-sans">کارت: </span><span class="font-bold" x-text="order.card_pan"></span>
                                                </div>
                                            </template>
                                            <template x-if="order.tracking_number">
                                                <div class="text-[11px] text-slate-700 font-mono" dir="ltr">
                                                    <span class="text-slate-400 font-sans">پیگیری: </span><span class="font-bold" x-text="order.tracking_number"></span>
                                                </div>
                                            </template>

                                            <!-- Receipt Action buttons -->
                                            <template x-if="order.status === 'awaiting_payment_verification' || order.status === 'pending'">
                                                <div class="flex items-center gap-1.5 pt-1">
                                                    <button type="button" @click="verifyPayment(order.payment_id, 'approve')"
                                                            class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[10px] font-black transition-colors flex items-center gap-1 shadow-2xs">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                        <span>تایید فیش</span>
                                                    </button>
                                                    <button type="button" @click="openReceiptModal(order, 'reject')"
                                                            class="px-2 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-lg text-[10px] font-bold transition-colors">
                                                        رد فیش
                                                    </button>
                                                </div>
                                            </template>
                                            <template x-if="order.status !== 'awaiting_payment_verification' && order.status !== 'pending' && order.payment_status === 'paid'">
                                                <div class="text-[10px] font-bold text-emerald-700 flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    <span>فیش تایید شده است</span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template x-if="!order.receipt_image">
                                <div class="p-2.5 rounded-xl bg-amber-50/70 border border-dashed border-amber-300 text-amber-800 text-xs flex items-center justify-between gap-2">
                                    <span class="flex items-center gap-1.5 font-bold text-[11px]">
                                        <svg class="w-4 h-4 text-amber-500 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        <span>کارت به کارت: در انتظار آپلود فیش توسط مشتری</span>
                                    </span>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Action Buttons Footer -->
                <div class="pt-3 border-t border-slate-100 flex items-center gap-2">
                    <!-- Print 80mm Receipt Button -->
                    <button type="button" @click="printReceipt(order.id)"
                            class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold btn-tactile flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>چاپ فیش</span>
                    </button>

                    <!-- Stage Transition Button -->
                    <template x-if="activeTab === 'pending'">
                        <button type="button" @click="changeStatus(order.id, 'start_prep')"
                                class="flex-1 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold btn-tactile shadow-xs flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>تایید و شروع آماده‌سازی</span>
                        </button>
                    </template>

                    <template x-if="activeTab === 'preparing'">
                        <button type="button" @click="changeStatus(order.id, 'ready')"
                                class="flex-1 py-2.5 bg-[#387780] hover:bg-[#2E636B] text-white rounded-xl text-xs font-bold btn-tactile shadow-xs flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>آماده تحویل شد</span>
                        </button>
                    </template>

                    <template x-if="activeTab === 'ready'">
                        <button type="button" @click="changeStatus(order.id, 'complete')"
                                class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold btn-tactile shadow-xs flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>تحویل و اتمام سفارش</span>
                        </button>
                    </template>

                    <template x-if="activeTab === 'completed'">
                        <span class="flex-1 text-center py-2 text-xs font-bold text-emerald-600 bg-emerald-50 rounded-xl">
                            <svg class="w-4 h-4 inline-block align-middle ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> سفارش تحویل شد
                        </span>
                    </template>
                </div>
            </div>
        </template>

        <!-- Empty State in Tab -->
        <template x-if="currentTabOrders.length === 0">
            <div class="col-span-full py-16 text-center space-y-3 bg-white border border-dashed border-slate-200 rounded-2xl">
                <div class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div class="text-xs font-bold text-slate-600">سفارشی در این مرحله کاری وجود ندارد.</div>
            </div>
        </template>
    </div>

    <!-- Interactive Map Modal for Customer Location -->
    <div x-show="mapModalOpen" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;" @click.self="mapModalOpen = false">
        <div class="bg-white rounded-3xl p-5 max-w-xl w-full space-y-4 shadow-2xl border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <div class="space-y-0.5">
                    <h4 class="text-xs font-black text-slate-800 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-[#387780]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        <span>موقعیت مکانی سفارش <span class="font-mono" x-text="selectedOrder ? selectedOrder.order_number : ''"></span></span>
                    </h4>
                    <p class="text-[11px] text-slate-500 truncate max-w-md" x-text="selectedOrder ? selectedOrder.address : ''"></p>
                </div>
                <button @click="mapModalOpen = false" class="w-8 h-8 rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center font-bold text-sm transition-colors">✕</button>
            </div>

            <!-- Leaflet Map Container -->
            <div id="live-order-map" class="h-80 w-full rounded-2xl border border-slate-200 shadow-inner z-0 overflow-hidden"></div>

            <div class="flex flex-wrap items-center justify-between gap-2 pt-1 border-t border-slate-100">
                <span class="text-[11px] text-slate-500 font-mono dir-ltr" x-text="selectedOrder ? (Number(selectedOrder.latitude).toFixed(5) + ' , ' + Number(selectedOrder.longitude).toFixed(5)) : ''"></span>
                <div class="flex items-center gap-2">
                    <a :href="selectedOrder?.neshan_url" target="_blank" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-xl text-xs font-bold transition-colors">مسیریابی نشان</a>
                    <a :href="selectedOrder?.balad_url" target="_blank" class="px-3 py-1.5 bg-sky-50 hover:bg-sky-100 text-sky-800 border border-sky-200 rounded-xl text-xs font-bold transition-colors">مسیریابی بلد</a>
                    <a :href="selectedOrder?.google_maps_url" target="_blank" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-800 border border-blue-200 rounded-xl text-xs font-bold transition-colors">Google Maps</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Receipt Preview & Verification Modal (Phase 2) -->
    <div x-show="receiptModalOpen" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;" @keydown.escape.window="receiptModalOpen = false">
        <div class="bg-white rounded-3xl p-6 max-w-lg w-full space-y-4 shadow-2xl border border-slate-200 animate-in fade-in zoom-in-95 duration-150" @click.away="receiptModalOpen = false">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h4 class="text-xs font-black text-slate-900 flex items-center gap-2">
                    <span class="w-2.5 h-4 rounded-full bg-amber-500"></span>
                    <span>بررسی فیش واریز کارت‌به‌کارت سفارش <span class="font-mono text-amber-700" x-text="selectedReceiptOrder ? selectedReceiptOrder.order_number : ''"></span></span>
                </h4>
                <button type="button" @click="receiptModalOpen = false" class="text-slate-400 hover:text-slate-700 font-bold text-lg leading-none">&times;</button>
            </div>

            <template x-if="selectedReceiptOrder">
                <div class="space-y-4">
                    <!-- Receipt Large Image Display -->
                    <div class="relative bg-slate-950 rounded-2xl overflow-hidden max-h-[380px] flex items-center justify-center border border-slate-200">
                        <img :src="selectedReceiptOrder.receipt_image" alt="فیش واریز" class="max-h-[380px] w-auto object-contain">
                        <a :href="selectedReceiptOrder.receipt_image" target="_blank" class="absolute bottom-3 end-3 px-3 py-1.5 bg-black/60 hover:bg-black/80 text-white text-[11px] font-bold rounded-xl backdrop-blur-xs flex items-center gap-1.5 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            <span>مشاهده اندازه اصلی</span>
                        </a>
                    </div>

                    <!-- Receipt Metadata Grid -->
                    <div class="grid grid-cols-2 gap-3 text-xs bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                        <div>
                            <span class="text-slate-400 block text-[11px]">مشتری:</span>
                            <span class="font-bold text-slate-800" x-text="selectedReceiptOrder.customer_name"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">مبلغ سفارش:</span>
                            <span class="font-black text-slate-900 tabular-nums" x-text="selectedReceiptOrder.final_payable_formatted + ' تومان'"></span>
                        </div>
                        <template x-if="selectedReceiptOrder.card_pan">
                            <div>
                                <span class="text-slate-400 block text-[11px]">شماره کارت واریزکننده:</span>
                                <span class="font-mono font-bold text-slate-800" dir="ltr" x-text="selectedReceiptOrder.card_pan"></span>
                            </div>
                        </template>
                        <template x-if="selectedReceiptOrder.tracking_number">
                            <div>
                                <span class="text-slate-400 block text-[11px]">شماره پیگیری / مرجع:</span>
                                <span class="font-mono font-bold text-slate-800" dir="ltr" x-text="selectedReceiptOrder.tracking_number"></span>
                            </div>
                        </template>
                    </div>

                    <!-- Reject Reason Input (Conditional) -->
                    <div x-show="receiptActionMode === 'reject'" class="space-y-2 pt-2 border-t border-slate-100">
                        <label class="block text-xs font-black text-rose-700">علت رد فیش واریزی:</label>
                        <input type="text" x-model="rejectReason" placeholder="مثال: مبلغ واریزی ناقص است یا تصویر فیش خوانا نیست."
                               class="w-full text-xs rounded-xl border border-rose-300 px-3 py-2 bg-rose-50/40 focus:border-rose-500 focus:ring-1 focus:ring-rose-500">
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="receiptModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-colors">
                            بستن
                        </button>

                        <template x-if="receiptActionMode === 'reject'">
                            <button type="button" @click="verifyPayment(selectedReceiptOrder.payment_id, 'reject', rejectReason)"
                                    class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-black transition-colors shadow-xs">
                                تایید رد فیش و لغو سفارش
                            </button>
                        </template>

                        <template x-if="receiptActionMode !== 'reject'">
                            <div class="flex items-center gap-2">
                                <button type="button" @click="receiptActionMode = 'reject'"
                                        class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold transition-colors">
                                    رد فیش...
                                </button>
                                <button type="button" @click="verifyPayment(selectedReceiptOrder.payment_id, 'approve')"
                                        class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black transition-colors shadow-xs flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>تایید فیش و شروع آماده‌سازی</span>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>

</div>

<!-- Alpine.js Live 4-Tab Polling Script -->
<script>
function liveKitchenBoard(initialOrders = []) {
    return {
        activeTab: 'pending',
        orders: Array.isArray(initialOrders) ? initialOrders : [],
        previousPendingCount: 0,
        searchQuery: '',
        soundEnabled: true,
        audioCtx: null,

        receiptModalOpen: false,
        selectedReceiptOrder: null,
        receiptActionMode: 'view',
        rejectReason: '',

        openReceiptModal(order, mode = 'view') {
            this.selectedReceiptOrder = order;
            this.receiptActionMode = mode;
            this.rejectReason = '';
            this.receiptModalOpen = true;
        },

        init() {
            this.fetchFeed();
            setInterval(() => {
                this.fetchFeed();
            }, 5000);
        },

        get pendingOrders() {
            return this.orders.filter(o => o.status === 'pending' || o.status === 'awaiting_payment_verification');
        },

        get preparingOrders() {
            return this.orders.filter(o => o.status === 'preparing' || o.status === 'processing');
        },

        get readyOrders() {
            return this.orders.filter(o => o.status === 'ready' || o.status === 'shipping' || o.status === 'out_for_delivery');
        },

        get completedOrders() {
            return this.orders.filter(o => o.status === 'completed');
        },

        get currentTabOrders() {
            let tabOrders = [];
            if (this.activeTab === 'pending') tabOrders = this.pendingOrders;
            else if (this.activeTab === 'preparing') tabOrders = this.preparingOrders;
            else if (this.activeTab === 'ready') tabOrders = this.readyOrders;
            else if (this.activeTab === 'completed') tabOrders = this.completedOrders;

            if (this.searchQuery.trim() !== '') {
                const query = this.searchQuery.trim().toLowerCase();
                tabOrders = tabOrders.filter(o => 
                    String(o.order_number).toLowerCase().includes(query) || 
                    String(o.customer_name).toLowerCase().includes(query)
                );
            }
            return tabOrders;
        },

        toggleSound() {
            this.soundEnabled = !this.soundEnabled;
            if (this.soundEnabled) this.playChime();
        },

        playChime() {
            try {
                if (!this.audioCtx) {
                    this.audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                }
                const ctx = this.audioCtx;
                if (ctx.state === 'suspended') {
                    ctx.resume();
                }
                const now = ctx.currentTime;
                const osc1 = ctx.createOscillator();
                const gain1 = ctx.createGain();
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(587.33, now);
                gain1.gain.setValueAtTime(0.3, now);
                gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.4);
                osc1.connect(gain1);
                gain1.connect(ctx.destination);
                osc1.start(now);
                osc1.stop(now + 0.4);

                const osc2 = ctx.createOscillator();
                const gain2 = ctx.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(880, now + 0.15);
                gain2.gain.setValueAtTime(0.35, now + 0.15);
                gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.7);
                osc2.connect(gain2);
                gain2.connect(ctx.destination);
                osc2.start(now + 0.15);
                osc2.stop(now + 0.7);
            } catch (e) {
                console.warn('Audio error', e);
            }
        },

        fetchFeed() {
            fetch('{{ route('admin.live-orders.feed') }}')
                .then(res => {
                    if (res.status === 304) return null;
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    return res.json();
                })
                .then(data => {
                    if (!data) return;
                    this.orders = Array.isArray(data.orders) ? data.orders : (data.orders ? Object.values(data.orders) : []);
                    if (data.pending_count > this.previousPendingCount && this.soundEnabled) {
                        this.playChime();
                    }
                    this.previousPendingCount = data.pending_count || 0;
                })
                .catch(err => console.error('Feed error:', err));
        },

        changeStatus(orderId, action) {
            fetch(`/admin/live-orders/${orderId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ action: action })
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    this.fetchFeed();
                }
            })
            .catch(err => console.error('Status change error:', err));
        },

        verifyPayment(paymentId, action = 'approve', reason = '') {
            if (!paymentId) return;
            fetch(`/admin/live-orders/payment/${paymentId}/verify`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ action: action, reason: reason })
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    this.receiptModalOpen = false;
                    this.fetchFeed();
                }
            });
        },

        printReceipt(orderId) {
            const printUrl = `/orders/${orderId}/receipt`;
            window.open(printUrl, 'ReceiptPrint', 'width=420,height=750,toolbar=0,scrollbars=1,status=0');
        },

        mapModalOpen: false,
        selectedOrder: null,
        liveMap: null,
        mapMarker: null,

        openMapModal(order) {
            this.selectedOrder = order;
            this.mapModalOpen = true;
            this.$nextTick(() => {
                if (typeof L === 'undefined') {
                    console.warn('Leaflet map library is still loading...');
                    return;
                }
                const lat = Number(order.latitude) || 29.6293;
                const lng = Number(order.longitude) || 52.5186;

                if (!this.liveMap) {
                    this.liveMap = L.map('live-order-map', {
                        center: [lat, lng],
                        zoom: 16
                    });

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© نشان / OpenStreetMap'
                    }).addTo(this.liveMap);

                    this.mapMarker = L.marker([lat, lng]).addTo(this.liveMap);
                } else {
                    this.liveMap.setView([lat, lng], 16);
                    this.mapMarker.setLatLng([lat, lng]);
                }

                this.mapMarker.bindPopup('<b>سفارش ' + order.order_number + ' (' + order.customer_name + ')</b><br>' + (order.address || '')).openPopup();

                setTimeout(() => {
                    this.liveMap.invalidateSize();
                }, 200);
            });
        }
    }
}
</script>

@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush
@endsection
