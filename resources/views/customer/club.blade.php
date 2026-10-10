@extends('layouts.customer')

@section('title', 'باشگاه مشتریان و کش‌بک')

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-[#353535] flex items-center gap-2">
                <svg class="w-6 h-6 text-[#3C6E71]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>باشگاه مشتریان و کش‌بک بی کافه</span>
            </h1>
            <p class="text-xs text-[#353535]/65 mt-1">لول‌های وفاداری، ۵٪ کش‌بک نقدی با هر سفارش و کدهای تخفیف اختصاصی</p>
        </div>

        <a href="{{ route('storefront.menu') }}" class="px-4 py-2.5 bg-[#284B63] hover:bg-[#1E374A] text-white text-xs font-black rounded-xl btn-tactile shadow-xs flex items-center gap-1.5 transition-colors min-h-[44px] self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            <span>سفارش با کیف‌پول</span>
        </a>
    </div>

    <!-- VIP Tier Hero Card -->
    <div class="bg-gradient-to-l from-[#353535] to-[#284B63] rounded-3xl p-6 sm:p-7 text-white shadow-md relative overflow-hidden space-y-5">
        <div class="absolute -end-10 -bottom-10 w-44 h-44 rounded-full bg-white/5 pointer-events-none"></div>

        <div class="flex flex-wrap items-center justify-between gap-4 relative z-10">
            <div class="space-y-1">
                <div class="flex items-center gap-2.5">
                    <span class="px-3 py-1 rounded-xl text-xs font-black bg-white/15 text-[#3C6E71] border border-white/20">
                        سطح {{ $currentTier->name ?? 'برنزی' }}
                    </span>
                    <span class="text-xs text-white/70 font-medium">باشگاه وفاداری بی کافه</span>
                </div>
                <h2 class="text-lg sm:text-xl font-black text-white">
                    باشگاه مشتریان و امتیازهای {{ $customer->name }}
                </h2>
            </div>

            <!-- Loyalty Points & Wallet Summary Badges -->
            <div class="flex items-center gap-2 sm:gap-3 flex-wrap">
                <div class="px-3.5 py-2 rounded-2xl bg-white/10 backdrop-blur-xs border border-white/15 text-center">
                    <span class="text-[10px] text-white/70 block font-medium">امتیاز وفاداری:</span>
                    <span class="text-sm sm:text-base font-black text-[#3C6E71] tabular-nums">{{ number_format($loyaltyPoints) }}</span>
                    <span class="text-[10px] text-white/60 mr-0.5">امتیاز</span>
                </div>

                <div class="px-3.5 py-2 rounded-2xl bg-white/10 backdrop-blur-xs border border-white/15 text-center">
                    <span class="text-[10px] text-white/70 block font-medium">موجودی کیف‌پول:</span>
                    <span class="text-sm sm:text-base font-black text-white tabular-nums">{{ number_format($customer->wallet_balance ?? 0) }}</span>
                    <span class="text-[10px] text-white/60 mr-0.5">تومان</span>
                </div>
            </div>
        </div>

        <!-- Tier Progress Bar & Benefits -->
        <div class="space-y-2 relative z-10 pt-1">
            <div class="flex items-center justify-between text-xs text-white/80 font-bold">
                <span>پیشرفت تا سطح {{ $nextTier->name ?? 'حداکثر (VIP)' }}</span>
                <span class="tabular-nums">{{ $tierProgress }}٪</span>
            </div>
            <div class="w-full bg-black/25 h-3 rounded-full overflow-hidden p-0.5">
                <div class="bg-gradient-to-r from-[#3C6E71] to-emerald-400 h-full rounded-full transition-all duration-700" style="width: {{ $tierProgress }}%"></div>
            </div>
            <div class="flex flex-wrap items-center justify-between text-[11px] text-white/70 pt-0.5 gap-2">
                <span>مجموع خرید تاکنون: <strong class="text-white tabular-nums">{{ number_format($customer->total_spent) }} تومان</strong></span>
                @if($nextTier && $remainingToNextTier > 0)
                    <span class="text-emerald-300">فقط <strong class="text-white tabular-nums">{{ number_format($remainingToNextTier) }} تومان</strong> خرید دیگه تا لول {{ $nextTier->name }} و کش‌بک بیشتر!</span>
                @else
                    <span class="text-emerald-300 font-bold">دمت گرم! تو در بالاترین لول کاربری بی کافه هستی.</span>
                @endif
            </div>
        </div>

        <!-- Tier Perks Pills -->
        <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-white/10 text-xs">
            <div class="flex items-center gap-1.5 px-3 py-1 rounded-xl bg-white/10 border border-white/15 text-white">
                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span>کش‌بک سفارش: {{ $currentTier->discount_percentage ?? 0 }}٪</span>
            </div>
            <div class="flex items-center gap-1.5 px-3 py-1 rounded-xl bg-white/10 border border-white/15 text-white">
                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span>ارسال پیک در شیراز: {{ ($currentTier->free_delivery ?? false) ? 'رایگان' : 'عادی' }}</span>
            </div>
            <div class="flex items-center gap-1.5 px-3 py-1 rounded-xl bg-white/10 border border-white/15 text-white">
                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span>{{ $loyaltySetting->cashback_percent ?? 5 }}٪ کش‌بک نقدی خودکار</span>
            </div>
        </div>
    </div>

    <!-- Tiers Comparison Table -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-[#D9D9D9] shadow-xs space-y-4">
        <h3 class="text-sm font-black text-[#353535] flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-[#3C6E71]"></span>
            <span>لول‌ها و هدیه‌های باشگاه مشتریان</span>
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
            @foreach($allTiers as $tier)
                @php
                    $isCurrent = ($currentTier && $currentTier->id === $tier->id);
                @endphp
                <div class="p-4 rounded-2xl border {{ $isCurrent ? 'border-2 border-[#3C6E71] bg-[#3C6E71]/10 ring-2 ring-[#3C6E71]/20' : 'border-[#D9D9D9] bg-white' }} space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-1 rounded-lg text-xs font-black text-white" style="background-color: {{ $tier->badge_color }}">
                            {{ $tier->name }}
                        </span>
                        @if($isCurrent)
                            <span class="text-[10px] font-black text-[#3C6E71] bg-white px-2 py-0.5 rounded-md border border-[#3C6E71]/30">سطح شما</span>
                        @endif
                    </div>

                    <div class="space-y-1.5 text-xs">
                        <div class="text-[11px] text-[#353535]/65">حداقل خرید برای این لول:</div>
                        <div class="font-black text-sm text-[#353535] tabular-nums">{{ number_format($tier->min_spend) }} تومان</div>
                    </div>

                    <ul class="text-[11px] space-y-1 pt-2 border-t border-[#D9D9D9]/60 text-[#353535]/75">
                        <li class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 inline-block text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>{{ $tier->discount_percentage }}٪ کش‌بک سفارش</span>
                        </li>
                        <li class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 inline-block text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>پیک شیراز: {{ $tier->free_delivery ? 'کاملاً رایگان' : 'عادی' }}</span>
                        </li>
                    </ul>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Active Coupons Section -->
    @if(isset($activeCoupons) && $activeCoupons->isNotEmpty())
        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-[#D9D9D9] shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-[#D9D9D9]/70 pb-3">
                <h3 class="text-sm font-black text-[#353535] flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#284B63]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    <span>کدهای تخفیف آماده استفاده شما</span>
                </h3>
                <span class="text-xs text-[#353535]/65">می‌تونی توی مرحله پرداخت ازشون استفاده کنی</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($activeCoupons as $coupon)
                    <div class="p-3.5 rounded-2xl bg-[#F7F9FA] border border-[#D9D9D9] space-y-2 relative overflow-hidden" x-data="{ copied: false }">
                        <div class="flex items-center justify-between">
                            <span class="font-mono font-black text-xs text-[#284B63] bg-white px-2.5 py-1 rounded-lg border border-[#D9D9D9] select-all">
                                {{ $coupon->code }}
                            </span>
                            <span class="text-xs font-black text-[#3C6E71]">
                                @if($coupon->discount_type === 'percent')
                                    {{ $coupon->discount_value }}٪ تخفیف
                                @else
                                    {{ number_format($coupon->discount_value) }} تومان
                                @endif
                            </span>
                        </div>

                        <div class="text-[11px] text-[#353535]/65 flex justify-between items-center pt-1">
                            <span>حداقل خرید: {{ number_format($coupon->min_order_amount ?? 0) }} تومان</span>
                            <button type="button"
                                    @click="navigator.clipboard.writeText('{{ $coupon->code }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                    class="text-[10px] text-[#3C6E71] font-bold hover:underline">
                                <span x-show="!copied">کپی کد</span>
                                <span x-show="copied" class="text-emerald-600 font-black">کپی شد!</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Cashback Transactions Ledger -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-[#D9D9D9] shadow-xs space-y-4">
        <div class="flex items-center justify-between border-b border-[#D9D9D9]/70 pb-3">
            <h3 class="text-sm font-black text-[#353535] flex items-center gap-2">
                <svg class="w-4 h-4 text-[#284B63]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                <span>گردش حساب کیف‌پول و کش‌بک‌ها</span>
            </h3>
            <span class="text-xs text-[#353535]/65">تاریخچه تراکنش‌ها</span>
        </div>

        @if(isset($cashbacks) && $cashbacks->count() > 0)
            <div class="divide-y divide-[#D9D9D9]/50 text-xs">
                @foreach($cashbacks as $cb)
                    <div class="py-3 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            @if($cb->type === 'credit')
                                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-black">
                                    +
                                </div>
                            @else
                                <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-black">
                                    -
                                </div>
                            @endif
                            <div>
                                <span class="font-bold text-[#353535] block text-xs">
                                    {{ $cb->description ?? ($cb->type === 'credit' ? 'واریز ۵٪ کش‌بک سفارش' : 'کسر اعتبار در سفارش') }}
                                </span>
                                <span class="text-[11px] text-[#353535]/60 tabular-nums">
                                    {{ \App\Helpers\JalaliHelper::format($cb->created_at, 'Y/m/d - H:i') }}
                                </span>
                            </div>
                        </div>

                        <div class="font-black text-sm tabular-nums {{ $cb->type === 'credit' ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $cb->type === 'credit' ? '+' : '-' }}{{ number_format($cb->amount) }} تومان
                        </div>
                    </div>
                @endforeach
            </div>

            @if(method_exists($cashbacks, 'links'))
                <div class="pt-3">
                    {{ $cashbacks->links() }}
                </div>
            @endif
        @else
            <div class="p-8 text-center text-xs text-[#353535]/60 space-y-1">
                <p class="font-bold text-[#353535]">هنوز تراکنشی توی کیف‌پولت ثبت نشده.</p>
                <p>با اولین سفارش، ۵٪ مبلغش نقدی برمی‌گرده به کیف‌پولت.</p>
            </div>
        @endif
    </div>

</div>
@endsection
