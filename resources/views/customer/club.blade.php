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

    <!-- Lucky Wheel (گردونه شانس روزانه) -->
    @if(isset($wheelPrizes) && $wheelPrizes->isNotEmpty())
    <div x-data="luckyWheelComponent()" x-init="init()" class="bg-white rounded-3xl p-5 sm:p-7 border border-[#D9D9D9] shadow-xs space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-[#D9D9D9]/60 pb-4">
            <div>
                <h3 class="text-base font-black text-[#353535] flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#E29578] animate-pulse"></span>
                    <span>گردونه شانس روزانه بی کافه</span>
                </h3>
                <p class="text-xs text-[#353535]/65 mt-1">هر روز یک‌بار می‌تونی گردونه رو بچرخونی و امتیاز باشگاه یا هدیه شگفت‌انگیز ببری!</p>
            </div>
            <div class="flex items-center gap-2">
                <span x-show="hasSpun" class="px-3 py-1.5 rounded-xl bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>شانس امروز استفاده شده</span>
                </span>
                <span x-show="!hasSpun" class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>۱ شانس چرخش فعال</span>
                </span>
            </div>
        </div>

        <div class="flex flex-col md:flex-row items-center justify-around gap-8">
            <!-- Wheel Container -->
            <div class="relative flex items-center justify-center p-3">
                <!-- Pointer Arrow at top -->
                <div class="absolute -top-1 z-20 transform -translate-x-1/2 left-1/2 drop-shadow-md">
                    <div class="w-0 h-0 border-x-8 border-x-transparent border-t-[18px] border-t-rose-600"></div>
                </div>

                <!-- Canvas Wheel with CSS Transform rotation -->
                <div class="relative w-[280px] h-[280px] sm:w-[320px] sm:h-[320px] rounded-full shadow-lg border-4 border-[#353535]/15 overflow-hidden bg-slate-100">
                    <canvas id="luckyWheelCanvas"
                            width="640"
                            height="640"
                            class="w-full h-full transform"
                            :style="`transform: rotate(${rotation}deg); transition: transform ${spinning ? '4.5s' : '0s'} cubic-bezier(0.15, 0.9, 0.2, 1);`">
                    </canvas>
                    
                    <!-- Center decorative pin -->
                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white shadow-md border-4 border-[#3C6E71] flex items-center justify-center font-black text-sm text-[#353535] pointer-events-none select-none">
                        ☕
                    </div>
                </div>
            </div>

            <!-- Controls & Results -->
            <div class="w-full md:w-80 space-y-4 text-center md:text-right">
                <!-- Status & Result Box -->
                <div x-show="resultMessage" x-cloak class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs space-y-1">
                    <div class="font-black text-sm text-emerald-900" x-text="resultTitle"></div>
                    <div class="text-xs" x-text="resultMessage"></div>
                </div>

                <div x-show="errorMessage" x-cloak class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                    <span x-text="errorMessage"></span>
                </div>

                <div class="bg-[#F7F9FA] border border-[#D9D9D9] p-4 rounded-2xl space-y-2 text-xs">
                    <div class="font-black text-[#353535] flex items-center gap-1.5 justify-center md:justify-start">
                        <svg class="w-4 h-4 text-[#3C6E71]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>قوانین گردونه شانس:</span>
                    </div>
                    <ul class="text-[11px] text-[#353535]/70 space-y-1 list-disc list-inside">
                        <li>هر روز یک‌بار می‌تونی شانس خودت رو امتحان کنی.</li>
                        <li>امتیازهای برنده شده بلافاصله به حسابت افزوده میشن.</li>
                        <li>جوایز شامل امتیاز هدیه، تخفیف و جوایز نقدیه!</li>
                    </ul>
                </div>

                <button type="button"
                        @click="spin()"
                        :disabled="spinning || hasSpun"
                        class="w-full py-3.5 px-6 rounded-2xl font-black text-sm text-white shadow-md transition-all flex items-center justify-center gap-2 btn-tactile min-h-[48px]"
                        :class="(spinning || hasSpun) ? 'bg-[#353535]/30 cursor-not-allowed opacity-60' : 'bg-gradient-to-r from-[#3C6E71] to-[#284B63] hover:opacity-95'">
                    <svg x-show="spinning" class="animate-spin w-5 h-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                    <span x-show="!spinning && !hasSpun">بچرخون و جایزه ببر! 🎯</span>
                    <span x-show="spinning">در حال چرخیدن گردونه...</span>
                    <span x-show="!spinning && hasSpun">شانس امروز استفاده شده ✓</span>
                </button>
            </div>
        </div>
    </div>
    @endif

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

@push('scripts')
<script>
function luckyWheelComponent() {
    return {
        prizes: @json($wheelPrizes ?? []),
        hasSpun: @json($hasSpunToday ?? false),
        spinning: false,
        rotation: 0,
        resultTitle: '',
        resultMessage: '',
        errorMessage: '',
        colors: [
            '#3C6E71', '#284B63', '#4F46E5', '#059669', 
            '#D97706', '#E11D48', '#0891B2', '#7C3AED',
            '#2563EB', '#0D9488', '#B45309'
        ],
        
        init() {
            this.$nextTick(() => {
                this.drawWheel();
            });
        },

        drawWheel() {
            const canvas = document.getElementById('luckyWheelCanvas');
            if (!canvas || !this.prizes || !this.prizes.length) return;
            const ctx = canvas.getContext('2d');
            const numSlices = this.prizes.length;
            const sliceAngle = (2 * Math.PI) / numSlices;
            const radius = canvas.width / 2;

            ctx.clearRect(0, 0, canvas.width, canvas.height);

            for (let i = 0; i < numSlices; i++) {
                const angle = i * sliceAngle;
                ctx.beginPath();
                ctx.moveTo(radius, radius);
                ctx.arc(radius, radius, radius, angle, angle + sliceAngle);
                ctx.closePath();
                ctx.fillStyle = this.colors[i % this.colors.length];
                ctx.fill();
                ctx.lineWidth = 3;
                ctx.strokeStyle = '#ffffff';
                ctx.stroke();

                // Draw Text
                ctx.save();
                ctx.translate(radius, radius);
                ctx.rotate(angle + sliceAngle / 2);
                ctx.textAlign = 'right';
                ctx.fillStyle = '#ffffff';
                ctx.font = 'bold 22px Vazirmatn, Tahoma, sans-serif';
                ctx.shadowColor = 'rgba(0, 0, 0, 0.45)';
                ctx.shadowBlur = 4;
                const text = this.prizes[i].title;
                ctx.fillText(text, radius - 28, 8);
                ctx.restore();
            }
        },

        async spin() {
            if (this.spinning || this.hasSpun) return;
            this.spinning = true;
            this.errorMessage = '';
            this.resultMessage = '';
            this.resultTitle = '';

            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const res = await fetch('{{ route('customer.club.spin') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token || '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await res.json();
                if (!res.ok || !data.success) {
                    this.errorMessage = data.message || 'خطا در چرخش گردونه شانس.';
                    this.spinning = false;
                    return;
                }

                // Locate prize slice
                let prizeIndex = 0;
                if (data.prize && data.prize.id) {
                    const found = this.prizes.findIndex(p => p.id === data.prize.id);
                    if (found !== -1) prizeIndex = found;
                }

                const numSlices = this.prizes.length;
                const sliceDeg = 360 / numSlices;
                const prizeCenterDeg = (prizeIndex * sliceDeg) + (sliceDeg / 2);
                
                // Align prize center with top pointer (270 deg)
                const baseFullRounds = 360 * 5;
                const currentMod = this.rotation % 360;
                let delta = (270 - prizeCenterDeg - currentMod) % 360;
                if (delta < 0) delta += 360;

                this.rotation += baseFullRounds + delta;

                setTimeout(() => {
                    this.spinning = false;
                    this.hasSpun = true;
                    this.resultTitle = data.prize ? 'تبریک فراوان! 🎉' : 'شانس بعدی!';
                    this.resultMessage = data.message || (data.prize ? `شما برنده «${data.prize.title}» شدید.` : 'متأسفانه این بار برنده نشدید.');
                }, 4700);

            } catch (err) {
                this.spinning = false;
                this.errorMessage = 'خطای ارتباط با سرور. لطفاً دوباره امتحان کنید.';
            }
        }
    };
}
</script>
@endpush
