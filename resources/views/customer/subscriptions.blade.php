@extends('layouts.customer')

@section('title', 'اشتراک‌های روزانه قهوه')

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-[#353535] flex items-center gap-2">
                <svg class="w-6 h-6 text-[#3C6E71]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>اشتراک‌های روزانه قهوه تخصصی</span>
            </h1>
            <p class="text-xs text-[#353535]/65 mt-1">سهمیه روزانه، فنجان‌های باقی‌مونده و خرید اشتراک تازه</p>
        </div>

        <a href="{{ route('storefront.subscriptions.index') }}" class="px-4 py-2.5 bg-[#284B63] hover:bg-[#1E374A] text-white text-xs font-black rounded-xl btn-tactile shadow-xs flex items-center gap-1.5 transition-colors min-h-[44px] self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            <span>خرید اشتراک تازه</span>
        </a>
    </div>

    <!-- Active Subscription Status Card -->
    @if($customer->activeSubscription)
        @php
            $sub = $customer->activeSubscription;
            $percent = $sub->total_items > 0 ? round(($sub->remaining_items / $sub->total_items) * 100) : 0;
            $usedItems = max(0, $sub->total_items - $sub->remaining_items);
        @endphp
        <div class="p-6 sm:p-7 rounded-3xl bg-gradient-to-l from-[#353535] to-[#284B63] text-white shadow-md relative overflow-hidden space-y-6">
            <div class="absolute -end-10 -bottom-10 w-48 h-48 rounded-full bg-white/5 pointer-events-none"></div>

            <div class="flex flex-wrap items-center justify-between gap-4 relative z-10">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 rounded-xl text-xs font-black bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>اشتراک فعال</span>
                        </span>
                        <span class="text-xs text-white/70">بی کافه شیراز</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-white mt-1">
                        {{ $sub->plan->name ?? 'اشتراک ماهانه قهوه' }}
                    </h2>
                </div>

                <a href="{{ route('storefront.menu') }}" class="px-5 py-3 bg-[#3C6E71] hover:bg-[#2E585B] text-white font-black text-xs rounded-2xl btn-tactile shadow-md flex items-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    <span>سفارش قهوه امروز (سهمیه)</span>
                </a>
            </div>

            <!-- Stats Bar -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 relative z-10">
                <div class="p-3.5 rounded-2xl bg-white/10 backdrop-blur-xs border border-white/15">
                    <span class="text-[10px] text-white/70 block">فنجان‌های باقی‌مونده:</span>
                    <div class="text-lg sm:text-xl font-black text-emerald-300 tabular-nums mt-0.5">
                        {{ $sub->remaining_items }} <span class="text-xs text-white/60">فنجان</span>
                    </div>
                </div>

                <div class="p-3.5 rounded-2xl bg-white/10 backdrop-blur-xs border border-white/15">
                    <span class="text-[10px] text-white/70 block">نوشیده شده:</span>
                    <div class="text-lg sm:text-xl font-black text-white tabular-nums mt-0.5">
                        {{ $usedItems }} <span class="text-xs text-white/60">فنجان</span>
                    </div>
                </div>

                <div class="p-3.5 rounded-2xl bg-white/10 backdrop-blur-xs border border-white/15">
                    <span class="text-[10px] text-white/70 block">کل سهمیه پلن:</span>
                    <div class="text-lg sm:text-xl font-black text-white tabular-nums mt-0.5">
                        {{ $sub->total_items }} <span class="text-xs text-white/60">فنجان</span>
                    </div>
                </div>

                <div class="p-3.5 rounded-2xl bg-white/10 backdrop-blur-xs border border-white/15">
                    <span class="text-[10px] text-white/70 block">اعتبار تا:</span>
                    <div class="text-sm font-black text-white tabular-nums mt-1.5" dir="ltr">
                        {{ $sub->expire_date }}
                    </div>
                </div>
            </div>

            <!-- Progress Meter -->
            <div class="space-y-2 relative z-10 pt-1">
                <div class="flex items-center justify-between text-xs text-white/80 font-bold">
                    <span>وضعیت مصرف سهمیه</span>
                    <span class="tabular-nums">{{ $percent }}٪ باقی‌مانده</span>
                </div>
                <div class="w-full bg-black/30 h-3 rounded-full overflow-hidden p-0.5">
                    <div class="bg-gradient-to-r from-emerald-400 to-[#3C6E71] h-full rounded-full transition-all duration-700" style="width: {{ $percent }}%"></div>
                </div>
            </div>
        </div>
    @else
        <!-- No Active Subscription State -->
        <div class="p-8 rounded-3xl bg-white border border-[#D9D9D9] shadow-xs text-center space-y-4">
            <div class="w-16 h-16 rounded-3xl bg-[#3C6E71]/15 text-[#3C6E71] flex items-center justify-center mx-auto shadow-inner">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <div class="max-w-md mx-auto space-y-1.5">
                <h3 class="text-base font-black text-[#353535]">در حال حاضر اشتراک فعالی نداری</h3>
                <p class="text-xs text-[#353535]/65 leading-relaxed">
                    با اشتراک ماهانه یا فصلی بی کافه، هر روز یه فنجان قهوه تخصصی تازه با تا ۴۰٪ تخفیف مهمون ما باش؛ چه حضوری در کافه چه با پیک در شیراز.
                </p>
            </div>
            <a href="{{ route('storefront.subscriptions.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#284B63] hover:bg-[#1E374A] text-white font-black text-xs rounded-xl btn-tactile shadow-xs transition-colors">
                <span>دیدن و انتخاب اشتراک‌ها</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
        </div>
    @endif

    <!-- Available Plans Quick Catalog -->
    @if(isset($availablePlans) && $availablePlans->isNotEmpty())
        <div class="space-y-3">
            <h3 class="text-sm font-black text-[#353535] flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#3C6E71]"></span>
                <span>بسته‌های اشتراک قهوه بی کافه</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($availablePlans as $plan)
                    <div class="p-5 rounded-3xl bg-white border border-[#D9D9D9] space-y-4 shadow-xs flex flex-col justify-between hover:border-[#3C6E71] transition-all">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <h4 class="font-black text-sm text-[#353535]">{{ $plan->name }}</h4>
                                <span class="px-2 py-0.5 rounded-lg bg-[#3C6E71]/15 text-[#3C6E71] text-[10px] font-black border border-[#3C6E71]/25">
                                    {{ $plan->days_valid }} روزه
                                </span>
                            </div>

                            <p class="text-[11px] text-[#353535]/65 leading-relaxed">{{ $plan->description }}</p>

                            <div class="p-3 rounded-2xl bg-[#F7F9FA] space-y-1.5 text-xs">
                                <div class="flex justify-between">
                                    <span class="text-[#353535]/65">تعداد فنجان:</span>
                                    <span class="font-bold text-[#353535] tabular-nums">{{ $plan->total_cups }} فنجان</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-[#353535]/65">قیمت هر فنجان:</span>
                                    <span class="font-bold text-[#284B63] tabular-nums">{{ number_format($plan->price_per_cup ?? ($plan->price / max(1, $plan->total_cups))) }} تومان</span>
                                </div>
                                <div class="flex justify-between border-t border-[#D9D9D9]/60 pt-1.5 font-black">
                                    <span class="text-[#353535]">قیمت نهایی:</span>
                                    <span class="text-[#284B63] tabular-nums text-sm">{{ number_format($plan->price) }} تومان</span>
                                </div>
                            </div>
                        </div>

                        <div x-data="{
                            addToCartAndRedirect() {
                                let cart = [];
                                try { cart = JSON.parse(localStorage.getItem('bcafe_cart') || '[]'); } catch(e) {}
                                const pId = {{ $plan->id }};
                                const existing = cart.find(i => (i.type === 'subscription' || i.subscription_plan_id === pId) && (i.id === 'sub-' + pId || i.subscription_plan_id === pId));
                                if (existing) {
                                    existing.quantity = (existing.quantity || 1) + 1;
                                } else {
                                    cart.push({
                                        id: 'sub-' + pId,
                                        product_id: null,
                                        type: 'subscription',
                                        subscription_plan_id: pId,
                                        name: '{{ addslashes($plan->name) }} (اشتراک قهوه)',
                                        price: {{ (float) $plan->price }},
                                        base_price: {{ (float) $plan->price }},
                                        quantity: 1,
                                        options: {
                                            'مدت اعتبار': '{{ $plan->days_valid ?? $plan->duration_days ?? 30 }} روز',
                                            'سهمیه کل': '{{ $plan->total_cups ?? $plan->total_items ?? 30 }} فنجان'
                                        },
                                        note: 'پلن اشتراک روزانه قهوه'
                                    });
                                }
                                localStorage.setItem('bcafe_cart', JSON.stringify(cart));
                                window.location.href = '{{ route('cart.index') }}';
                            }
                        }">
                            <button type="button"
                                    @click="addToCartAndRedirect()"
                                    class="w-full py-2.5 bg-[#3C6E71] hover:bg-[#2E585B] text-white font-black text-xs rounded-xl btn-tactile text-center block shadow-2xs transition-colors min-h-[42px] flex items-center justify-center gap-1.5 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                <span>افزودن این اشتراک به سبد خرید</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Recent Cup Usages Log -->
    <div class="bg-white border border-[#D9D9D9] rounded-3xl p-5 sm:p-6 space-y-4 shadow-xs">
        <div class="flex items-center justify-between border-b border-[#D9D9D9]/70 pb-3.5">
            <h3 class="text-sm font-black text-[#353535] flex items-center gap-2">
                <svg class="w-4 h-4 text-[#284B63]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span>تاریخچه فنجان‌های نوشیده‌شده</span>
            </h3>
            <span class="text-xs text-[#353535]/65">سفارش‌های اشتراکی اخیر</span>
        </div>

        @if(isset($usages) && $usages->count() > 0)
            <div class="divide-y divide-[#D9D9D9]/50 text-xs">
                @foreach($usages as $usage)
                    <div class="py-3 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-[#3C6E71]/15 text-[#3C6E71] flex items-center justify-center font-black">
                                <svg class="w-5 h-5 inline-block text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z M6 1v3 M10 1v3 M14 1v3"/></svg>
                            </div>
                            <div>
                                <span class="font-bold text-[#353535] block text-xs">{{ $usage->product->name ?? 'فنجان قهوه اشتراکی' }}</span>
                                <span class="text-[11px] text-[#353535]/60">{{ $usage->subscription->plan->name ?? 'اشتراک قهوه' }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 text-start sm:text-end">
                            <div class="text-[11px] text-[#353535]/65 tabular-nums">
                                {{ \App\Helpers\JalaliHelper::format($usage->created_at, 'Y/m/d - H:i') }}
                            </div>
                            <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 font-black text-[11px] border border-emerald-200">
                                ۱ فنجان ثبت شد
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-8 text-center text-xs text-[#353535]/60 space-y-1">
                <p class="font-bold text-[#353535]">هنوز قهوه‌ای از اشتراکت سفارش ندادی.</p>
                <p>هر وقت از سهمیه‌ت قهوه سفارش بدی، تاریخ و فنجان‌های ثبت‌شده این‌جا برات میاد.</p>
            </div>
        @endif
    </div>

</div>
@endsection
