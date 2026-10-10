@extends('layouts.customer')

@section('title', 'داشبورد کاربری')

@section('content')
<div class="space-y-6">

    <!-- Birthday Celebration Banner -->
    @if(isset($birthdayGiftAwarded) && $birthdayGiftAwarded)
        <div class="p-5 sm:p-6 rounded-3xl bg-gradient-to-r from-amber-500 via-rose-500 to-amber-600 text-white shadow-lg space-y-2 relative overflow-hidden">
            <div class="absolute -end-6 -bottom-6 w-32 h-32 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
            <div class="flex items-center gap-3">
                <span class="text-3xl animate-bounce">🎂</span>
                <div>
                    <h3 class="text-base font-black">زادروزتان خجسته باد، {{ $customer->name }} عزیز! 🎉</h3>
                    <p class="text-xs text-white/90 mt-0.5">
                        کادوی تولد اختصاصی شما 
                        @if($birthdayGiftAwarded['type'] === 'points')
                            به میزان <span class="font-bold underline">{{ number_format($birthdayGiftAwarded['amount']) }} امتیاز باشگاه</span>
                        @else
                            به مبلغ <span class="font-bold underline">{{ number_format($birthdayGiftAwarded['amount']) }} تومان</span>
                        @endif
                        با عشق به حسابتان واریز گردید. روزتان پر از شادی و طعم خوش قهوه!
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Active Live Order Card (If Any) -->
    @if($activeOrder)
        <div class="p-5 sm:p-6 rounded-3xl bg-white border-2 border-[#3C6E71] shadow-xs space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#D9D9D9]/70 pb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-[#3C6E71]/15 flex items-center justify-center text-[#3C6E71] animate-pulse flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <span class="text-xs text-[#353535]/65 font-medium">سفارش در حال آماده‌سازی:</span>
                        <div class="text-sm font-black text-[#353535] font-mono flex items-center gap-2 mt-0.5">
                            <span>{{ $activeOrder->order_number }}</span>
                            <span class="text-xs font-bold text-[#3C6E71]">({{ $activeOrder->isDineIn() ? 'سرو داخل کافه' : 'ارسال با پیک شیراز' }})</span>
                        </div>
                    </div>
                </div>

                <a href="{{ route('storefront.order-status', $activeOrder->order_number) }}" class="px-4 py-2 bg-[#353535] hover:bg-[#1E2912] text-white text-xs font-black rounded-xl btn-tactile shadow-xs flex items-center gap-1.5 min-h-[40px] transition-colors">
                    <span>پیگیری زنده</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
            </div>

            <!-- Order Status Step Indicator -->
            <div class="grid grid-cols-4 gap-2 text-center pt-1 text-xs">
                @php
                    $stepIndex = match($activeOrder->status) {
                        'pending' => 1,
                        'processing' => 2,
                        'ready', 'shipping' => 3,
                        'completed' => 4,
                        default => 1
                    };
                @endphp
                <div class="space-y-1">
                    <div class="h-2 rounded-full {{ $stepIndex >= 1 ? 'bg-[#3C6E71]' : 'bg-[#D9D9D9]' }}"></div>
                    <span class="text-[11px] font-bold {{ $stepIndex >= 1 ? 'text-[#353535]' : 'text-[#353535]/40' }}">ثبت سفارش</span>
                </div>
                <div class="space-y-1">
                    <div class="h-2 rounded-full {{ $stepIndex >= 2 ? 'bg-[#3C6E71]' : 'bg-[#D9D9D9]' }}"></div>
                    <span class="text-[11px] font-bold {{ $stepIndex >= 2 ? 'text-[#353535]' : 'text-[#353535]/40' }}">بار قهوه</span>
                </div>
                <div class="space-y-1">
                    <div class="h-2 rounded-full {{ $stepIndex >= 3 ? 'bg-[#3C6E71]' : 'bg-[#D9D9D9]' }}"></div>
                    <span class="text-[11px] font-bold {{ $stepIndex >= 3 ? 'text-[#353535]' : 'text-[#353535]/40' }}">{{ $activeOrder->isDineIn() ? 'آماده سرو' : 'پیک ارسال' }}</span>
                </div>
                <div class="space-y-1">
                    <div class="h-2 rounded-full {{ $stepIndex >= 4 ? 'bg-[#3C6E71]' : 'bg-[#D9D9D9]' }}"></div>
                    <span class="text-[11px] font-bold {{ $stepIndex >= 4 ? 'text-[#353535]' : 'text-[#353535]/40' }}">تحویل</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Welcome & VIP Tier Banner -->
    <div class="bg-gradient-to-l from-[#353535] to-[#284B63] rounded-3xl p-6 sm:p-7 text-white shadow-md relative overflow-hidden space-y-4">
        <div class="absolute -end-10 -bottom-10 w-44 h-44 rounded-full bg-white/5 pointer-events-none"></div>

        <div class="flex flex-wrap items-center justify-between gap-4 relative z-10">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-lg text-xs font-black bg-white/15 text-[#3C6E71] border border-white/20">
                        سطح {{ $currentTier->name ?? 'برنزی' }}
                    </span>
                    <span class="text-xs text-white/70">عضو باشگاه بی کافه</span>
                </div>
                <h1 class="text-lg sm:text-2xl font-black text-white">
                    سلام، {{ $customer->name ?? 'همراه گرامی' }}!
                </h1>
            </div>

            <a href="{{ route('storefront.menu') }}" class="px-5 py-3 bg-[#3C6E71] hover:bg-[#2E585B] text-white font-black text-xs rounded-2xl btn-tactile shadow-md flex items-center gap-2 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                <span>سفارش از منو</span>
            </a>
        </div>

        <!-- Tier Progress Bar -->
        <div class="space-y-2 relative z-10 pt-2 border-t border-white/10">
            <div class="flex items-center justify-between text-xs text-white/80 font-bold">
                <span>فاصله تا لول {{ $nextTier->name ?? 'VIP' }}</span>
                <span class="tabular-nums">{{ $tierProgress }}٪</span>
            </div>
            <div class="w-full bg-black/25 h-2.5 rounded-full overflow-hidden p-0.5">
                <div class="bg-gradient-to-r from-[#3C6E71] to-emerald-400 h-full rounded-full transition-all duration-700" style="width: {{ $tierProgress }}%"></div>
            </div>
            <div class="flex flex-wrap items-center justify-between text-[11px] text-white/70 gap-2">
                <span>مجموع خرید: <strong class="text-white tabular-nums">{{ number_format($customer->total_spent) }} تومان</strong></span>
                @if($nextTier && $remainingToNextTier > 0)
                    <span class="text-emerald-300">فقط {{ number_format($remainingToNextTier) }} تومن خرید تا لول {{ $nextTier->name }} و {{ $nextTier->discount_percentage }}٪ تخفیف همیشگی!</span>
                @else
                    <span class="text-emerald-300 font-bold">دمت گرم! تو بالاترین لول باشگاه بی کافه هستی.</span>
                @endif
            </div>
        </div>
    </div>

    <!-- 4 KPI Summary Cards Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Wallet Card -->
        <div class="p-5 rounded-3xl bg-white border border-[#D9D9D9] shadow-xs space-y-2 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs text-[#353535]/65 font-bold">موجودی کیف‌پول</span>
                <div class="w-8 h-8 rounded-xl bg-[#284B63]/15 text-[#284B63] flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
            </div>
            <div>
                <div class="text-lg sm:text-xl font-black text-[#284B63] tabular-nums">
                    {{ number_format($customer->wallet_balance ?? 0) }}
                </div>
                <span class="text-[10px] text-[#353535]/50">تومان اعتبار نقد</span>
            </div>
            <a href="{{ route('customer.club') }}" class="text-[11px] text-[#3C6E71] font-bold hover:underline block pt-1 border-t border-[#D9D9D9]/50">
                مشاهده تراکنش‌ها ←
            </a>
        </div>

        <!-- Loyalty Points Card -->
        <div class="p-5 rounded-3xl bg-white border border-[#D9D9D9] shadow-xs space-y-2 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs text-[#353535]/65 font-bold">امتیاز وفاداری</span>
                <div class="w-8 h-8 rounded-xl bg-[#3C6E71]/15 text-[#3C6E71] flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </div>
            </div>
            <div>
                <div class="text-lg sm:text-xl font-black text-[#3C6E71] tabular-nums">
                    {{ number_format($loyaltyPoints) }}
                </div>
                <span class="text-[10px] text-[#353535]/50">امتیاز فعال</span>
            </div>
            <a href="{{ route('customer.club') }}" class="text-[11px] text-[#3C6E71] font-bold hover:underline block pt-1 border-t border-[#D9D9D9]/50">
                کدهای تخفیف باشگاه ←
            </a>
        </div>

        <!-- Active Subscription Card -->
        <div class="p-5 rounded-3xl bg-white border border-[#D9D9D9] shadow-xs space-y-2 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs text-[#353535]/65 font-bold">اشتراک قهوه</span>
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
            </div>
            <div>
                @if($customer->activeSubscription)
                    <div class="text-lg sm:text-xl font-black text-[#353535] tabular-nums">
                        {{ $customer->activeSubscription->remaining_items }} <span class="text-xs font-normal text-[#353535]/60">فنجان</span>
                    </div>
                    <span class="text-[10px] text-emerald-600 font-bold">اشتراک فعاله</span>
                @else
                    <div class="text-sm font-black text-[#353535]/60 mt-1">
                        اشتراک فعال نداری
                    </div>
                    <span class="text-[10px] text-[#353535]/50">۴۰٪ صرفه‌جویی ماهانه</span>
                @endif
            </div>
            <a href="{{ route('customer.subscriptions') }}" class="text-[11px] text-[#3C6E71] font-bold hover:underline block pt-1 border-t border-[#D9D9D9]/50">
                مدیریت اشتراک ←
            </a>
        </div>

        <!-- Total Orders Card -->
        <div class="p-5 rounded-3xl bg-white border border-[#D9D9D9] shadow-xs space-y-2 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs text-[#353535]/65 font-bold">سفارش‌ها</span>
                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
            </div>
            <div>
                <div class="text-lg sm:text-xl font-black text-[#353535] tabular-nums">
                    {{ $totalOrdersCount }}
                </div>
                <span class="text-[10px] text-[#353535]/50">سفارش ثبت‌شده تاکنون</span>
            </div>
            <a href="{{ route('customer.orders') }}" class="text-[11px] text-[#3C6E71] font-bold hover:underline block pt-1 border-t border-[#D9D9D9]/50">
                دیدن سفارش‌ها ←
            </a>
        </div>
    </div>

    <!-- Quick Shortcuts Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('storefront.menu') }}" class="p-4 rounded-2xl bg-white border border-[#D9D9D9] hover:border-[#3C6E71] transition-all flex items-center gap-3.5 shadow-2xs group">
            <div class="w-10 h-10 rounded-xl bg-[#284B63]/10 text-[#284B63] group-hover:bg-[#284B63] group-hover:text-white flex items-center justify-center transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            </div>
            <div>
                <span class="font-black text-xs text-[#353535] block">سفارش آنلاین</span>
                <span class="text-[11px] text-[#353535]/60">قهوه، کیک و دسر تازه</span>
            </div>
        </a>

        <a href="{{ route('customer.addresses') }}" class="p-4 rounded-2xl bg-white border border-[#D9D9D9] hover:border-[#3C6E71] transition-all flex items-center gap-3.5 shadow-2xs group">
            <div class="w-10 h-10 rounded-xl bg-[#3C6E71]/15 text-[#3C6E71] group-hover:bg-[#3C6E71] group-hover:text-white flex items-center justify-center transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
            </div>
            <div>
                <span class="font-black text-xs text-[#353535] block">آدرس‌های من</span>
                <span class="text-[11px] text-[#353535]/60">برای تحویل سریع با پیک در شیراز</span>
            </div>
        </a>

        <a href="{{ route('customer.profile') }}" class="p-4 rounded-2xl bg-white border border-[#D9D9D9] hover:border-[#3C6E71] transition-all flex items-center gap-3.5 shadow-2xs group">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 group-hover:bg-amber-600 group-hover:text-white flex items-center justify-center transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V4a2 2 0 114 0v4m-4 0a2 2 0 10-4 0v4m-5 4h18M5 12a2 2 0 012-2h10a2 2 0 012 2v7a2 2 0 01-2 2H7a2 2 0 01-2-2v-7z"/></svg>
            </div>
            <div>
                <span class="font-black text-xs text-[#353535] block">کادوی تولد</span>
                <span class="text-[11px] text-[#353535]/60">۵۰ تومن شارژ هدیه سالانه</span>
            </div>
        </a>
    </div>

    <!-- Recent Orders Section -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-[#D9D9D9] shadow-xs space-y-4">
        <div class="flex items-center justify-between border-b border-[#D9D9D9]/70 pb-3.5">
            <h3 class="text-sm font-black text-[#353535] flex items-center gap-2">
                <svg class="w-4 h-4 text-[#284B63]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span>سفارش‌های اخیر</span>
            </h3>
            <a href="{{ route('customer.orders') }}" class="text-xs text-[#3C6E71] font-bold hover:underline">
                همه سفارش‌ها ←
            </a>
        </div>

        @if(isset($recentOrders) && $recentOrders->count() > 0)
            <div class="divide-y divide-[#D9D9D9]/50 text-xs">
                @foreach($recentOrders as $order)
                    <div class="py-3.5 flex flex-wrap items-center justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-mono font-black text-xs text-[#353535]">{{ $order->order_number }}</span>
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold {{ $order->isDineIn() ? 'bg-slate-100 text-slate-700' : 'bg-teal-50 text-[#3C6E71]' }}">
                                    {{ $order->isDineIn() ? 'حضوری در کافه' : 'ارسال پیک شیراز' }}
                                </span>
                            </div>
                            <div class="text-[11px] text-[#353535]/65 tabular-nums">
                                {{ \App\Helpers\JalaliHelper::format($order->created_at, 'Y/m/d - H:i') }}
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="text-end">
                                <div class="font-black text-xs sm:text-sm text-[#284B63] tabular-nums">
                                    {{ number_format($order->final_payable) }} تومان
                                </div>
                                @php
                                    $badge = match($order->status) {
                                        'pending' => ['bg-amber-50 text-amber-700 border-amber-200', 'در انتظار بررسی'],
                                        'processing' => ['bg-blue-50 text-blue-700 border-blue-200', 'در حال آماده‌سازی'],
                                        'ready', 'shipping' => ['bg-teal-50 text-[#3C6E71] border-teal-200', 'آماده / در مسیر'],
                                        'completed' => ['bg-emerald-50 text-emerald-700 border-emerald-200', 'تحویل شده'],
                                        'cancelled' => ['bg-rose-50 text-rose-700 border-rose-200', 'لغو شده'],
                                        default => ['bg-slate-100 text-slate-700 border-slate-200', $order->status],
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $badge[0] }}">
                                    {{ $badge[1] }}
                                </span>
                            </div>

                            <a href="{{ route('customer.orders.show', $order) }}" class="px-3 py-1.5 bg-[#F7F9FA] hover:bg-[#3C6E71] hover:text-white text-[#353535] text-xs font-bold rounded-xl border border-[#D9D9D9] transition-colors btn-tactile">
                                فاکتور
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-8 text-center text-xs text-[#353535]/60 space-y-1">
                <p class="font-bold text-[#353535]">هنوز سفارشی ثبت نکردی!</p>
                <p>همین الان یه سر به منو بزن و یه قهوه تازه مهمون ما باش.</p>
            </div>
        @endif
    </div>

    <!-- Favorite Products Section (If Any) -->
    @if(isset($favoriteProducts) && $favoriteProducts->count() > 0)
        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-[#D9D9D9] shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-[#D9D9D9]/70 pb-3.5">
                <h3 class="text-sm font-black text-[#353535] flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#FF7A18]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                    <span>محصولات مورد علاقه شما</span>
                </h3>
                <a href="{{ route('customer.favorites') }}" class="text-xs text-[#3C6E71] font-bold hover:underline">
                    همه علاقه‌مندی‌ها ←
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 gap-3 sm:gap-4">
                @foreach($favoriteProducts as $favProduct)
                    <x-product-card :product="$favProduct" :is-favorite="true" />
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection