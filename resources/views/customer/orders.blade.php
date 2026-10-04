@extends('layouts.customer')

@section('title', 'تاریخچه سفارش‌های من')

@section('content')
<div class="space-y-6" x-data="{ activeFilter: 'all' }">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-[#353535] flex items-center gap-2">
                <svg class="w-6 h-6 text-[#3C6E71]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                <span>تاریخچه سفارش‌های من</span>
            </h1>
            <p class="text-xs text-[#353535]/65 mt-1">سفارش‌های حضوری توی کافه و سفارش‌های ارسالی با پیک در شیراز</p>
        </div>

        <a href="{{ route('storefront.menu') }}" class="px-4 py-2.5 bg-[#284B63] hover:bg-[#1E374A] text-white text-xs font-black rounded-xl btn-tactile shadow-xs flex items-center gap-1.5 transition-colors min-h-[44px] self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            <span>سفارش جدید از منو</span>
        </a>
    </div>

    <!-- Filter Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 custom-scrollbar text-xs font-black">
        <button type="button"
                @click="activeFilter = 'all'"
                :class="activeFilter === 'all' ? 'bg-[#353535] text-white shadow-xs' : 'bg-white text-[#353535]/70 hover:bg-[#F7F9FA] hover:text-[#353535] border border-[#D9D9D9]'"
                class="px-4 py-2 rounded-xl transition-all min-h-[40px] whitespace-nowrap btn-tactile">
            همه سفارش‌ها
        </button>

        <button type="button"
                @click="activeFilter = 'active'"
                :class="activeFilter === 'active' ? 'bg-[#3C6E71] text-white shadow-xs' : 'bg-white text-[#353535]/70 hover:bg-[#F7F9FA] hover:text-[#353535] border border-[#D9D9D9]'"
                class="px-4 py-2 rounded-xl transition-all min-h-[40px] whitespace-nowrap btn-tactile">
            در حال آماده‌سازی و ارسال
        </button>

        <button type="button"
                @click="activeFilter = 'completed'"
                :class="activeFilter === 'completed' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white text-[#353535]/70 hover:bg-[#F7F9FA] hover:text-[#353535] border border-[#D9D9D9]'"
                class="px-4 py-2 rounded-xl transition-all min-h-[40px] whitespace-nowrap btn-tactile">
            تحویل داده شده
        </button>

        <button type="button"
                @click="activeFilter = 'cancelled'"
                :class="activeFilter === 'cancelled' ? 'bg-rose-600 text-white shadow-xs' : 'bg-white text-[#353535]/70 hover:bg-[#F7F9FA] hover:text-[#353535] border border-[#D9D9D9]'"
                class="px-4 py-2 rounded-xl transition-all min-h-[40px] whitespace-nowrap btn-tactile">
            لغو شده
        </button>
    </div>

    <!-- Orders Cards List -->
    @if($orders->count() > 0)
        <div class="space-y-4">
            @foreach($orders as $order)
                @php
                    $isOrderActive = in_array($order->status, ['pending', 'processing', 'ready', 'shipping']);
                    $filterCategory = $isOrderActive ? 'active' : ($order->status === 'completed' ? 'completed' : ($order->status === 'cancelled' ? 'cancelled' : 'other'));
                    $badge = match($order->status) {
                        'pending' => ['bg-amber-50 text-amber-700 border-amber-200', 'در انتظار بررسی'],
                        'processing' => ['bg-blue-50 text-blue-700 border-blue-200', 'در حال آماده‌سازی'],
                        'ready', 'shipping' => ['bg-teal-50 text-[#3C6E71] border-teal-200 font-bold', 'آماده تحویل / پیک'],
                        'completed' => ['bg-emerald-50 text-emerald-700 border-emerald-200', 'تحویل شده'],
                        'cancelled' => ['bg-rose-50 text-rose-700 border-rose-200', 'لغو شده'],
                        default => ['bg-slate-100 text-slate-700 border-slate-200', $order->status],
                    };
                @endphp
                <div x-show="activeFilter === 'all' || activeFilter === '{{ $filterCategory }}'"
                     x-transition
                     class="bg-white border border-[#D9D9D9] rounded-3xl p-5 sm:p-6 space-y-4 shadow-xs hover:border-[#3C6E71] transition-all">

                    <!-- Order Card Header -->
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#D9D9D9]/70 pb-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-[#F7F9FA] border border-[#D9D9D9] text-[#284B63] flex items-center justify-center font-black">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-black text-sm text-[#353535]">{{ $order->order_number }}</span>
                                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold {{ $order->isDineIn() ? 'bg-slate-100 text-slate-700' : 'bg-teal-50 text-[#3C6E71]' }}">
                                        {{ $order->isDineIn() ? 'سرو در کافه' : 'پیک شیراز' }}
                                    </span>
                                </div>
                                <span class="text-[11px] text-[#353535]/65 tabular-nums">
                                    {{ \App\Helpers\JalaliHelper::format($order->created_at, 'Y/m/d - H:i') }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 rounded-xl text-xs font-bold border {{ $badge[0] }}">
                                {{ $badge[1] }}
                            </span>
                        </div>
                    </div>

                    <!-- Items Preview & Address -->
                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-1.5">
                            @foreach($order->items as $item)
                                <span class="px-3 py-1.5 rounded-xl bg-[#F7F9FA] border border-[#D9D9D9]/80 text-[11px] text-[#353535] font-bold">
                                    {{ $item->product->name ?? 'آیتم کافه' }}
                                    <span class="text-[#284B63] font-mono mr-1">×{{ $item->quantity }}</span>
                                </span>
                            @endforeach
                        </div>

                        @if(!$order->isDineIn() && $order->deliveryAddress)
                            <div class="text-[11px] text-[#353535]/70 flex items-center gap-1.5 pt-1">
                                <svg class="w-3.5 h-3.5 text-[#3C6E71] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                <span class="truncate">آدرس تحویل: {{ $order->deliveryAddress->address_line }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Card Footer Actions -->
                    <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-[#D9D9D9]/70 text-xs">
                        <div>
                            <span class="text-[11px] text-[#353535]/65">مبلغ پرداختی:</span>
                            <span class="text-sm sm:text-base font-black text-[#284B63] tabular-nums mr-1">
                                {{ number_format($order->final_payable) }}
                            </span>
                            <span class="text-[10px] text-[#353535]/60">تومان</span>
                        </div>

                        <div class="flex items-center gap-2">
                            @if($isOrderActive)
                                <a href="{{ route('storefront.order-status', $order->order_number) }}" class="px-3.5 py-2 bg-[#3C6E71] hover:bg-[#2E585B] text-white text-xs font-bold rounded-xl btn-tactile shadow-2xs flex items-center gap-1 min-h-[38px] transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                    <span>پیگیری زنده</span>
                                </a>
                            @endif

                            <a href="{{ route('customer.orders.show', $order) }}" class="px-4 py-2 bg-[#F7F9FA] hover:bg-slate-200 text-[#353535] text-xs font-black rounded-xl border border-[#D9D9D9] transition-colors btn-tactile min-h-[38px] flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>دیدن فاکتور</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-2">
            {{ $orders->links() }}
        </div>
    @else
        <div class="p-12 rounded-3xl bg-white border border-[#D9D9D9] text-center space-y-3 shadow-xs">
            <div class="w-16 h-16 rounded-3xl bg-[#F7F9FA] text-[#353535]/40 flex items-center justify-center mx-auto">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <div class="space-y-1">
                <h3 class="text-sm font-black text-[#353535]">هنوز سفارشی ثبت نکردی!</h3>
                <p class="text-xs text-[#353535]/65">از منوی آنلاین یه نوشیدنی یا خوراکی انتخاب کن تا سفارشت این‌جا ثبت بشه.</p>
            </div>
            <a href="{{ route('storefront.menu') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#284B63] hover:bg-[#1E374A] text-white font-black text-xs rounded-xl btn-tactile shadow-xs transition-colors">
                <span>مشاهده منو و ثبت سفارش</span>
            </a>
        </div>
    @endif

</div>
@endsection
