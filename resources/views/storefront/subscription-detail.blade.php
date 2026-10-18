<!DOCTYPE html>
<html lang="fa" dir="rtl" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>جزئیات پلن {{ $plan->name }} - باشگاه اشتراک قهوه بی کافه شیراز</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}?v=2.0.2">
    <link rel="alternate icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2.0.2">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}?v=2.0.2">

    @include('partials.theme-init')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full antialiased bg-[#F8FAFC] dark:bg-[#0F171B] text-[#19242B] dark:text-slate-100 selection:bg-[#387780] selection:text-white transition-colors">

    <!-- Unified Global Navigation Header -->
    <x-storefront-header :activeTab="'subscriptions'" />

    @php
        $pricePerCup = $plan->total_items > 0 ? (int) round($plan->price / $plan->total_items) : 0;
        $standardAvgPrice = 75000;
        $totalRegularValue = $plan->total_items * $standardAvgPrice;
        $savingsAmount = max(0, $totalRegularValue - $plan->price);
        $savingsPercent = $totalRegularValue > 0 ? round(($savingsAmount / $totalRegularValue) * 100) : 0;
    @endphp

    <!-- Breadcrumb -->
    <div class="bg-white border-b border-[#E2E8F0]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5">
            <nav class="flex items-center gap-2 text-xs text-[#64748B]">
                <a href="{{ route('storefront.menu') }}" class="hover:text-[#387780] transition-colors">بی کافه</a>
                <span>/</span>
                <a href="{{ route('storefront.subscriptions.index') }}" class="hover:text-[#387780] transition-colors">اشتراک‌های روزانه</a>
                <span>/</span>
                <span class="font-bold text-[#19242B]">{{ $plan->name }}</span>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- Right / Center Column: Plan Overview & Details (8 Cols) -->
            <div class="lg:col-span-8 space-y-8">
                
                <!-- Plan Hero Card -->
                <div class="p-6 sm:p-10 rounded-3xl bg-white border border-[#E2E8F0] shadow-xs space-y-6">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-2.5">
                            <span class="px-3.5 py-1.5 bg-teal-50 text-[#387780] text-xs font-black rounded-xl border border-teal-200 shadow-2xs flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-[#387780]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>اعتبار: {{ $plan->duration_days }} روز</span>
                            </span>
                            <span class="px-3.5 py-1.5 bg-emerald-50 text-emerald-700 text-xs font-black rounded-xl border border-emerald-200">
                                <div class="flex items-center gap-1.5"><svg class="w-4 h-4 inline-block text-[#387780]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z M6 1v3 M10 1v3 M14 1v3"/></svg> سهمیه: {{ $plan->total_items }} فنجان</div>
                            </span>
                            @if($plan->daily_limit)
                                <span class="px-3.5 py-1.5 bg-slate-100 text-slate-700 text-xs font-bold rounded-xl border border-slate-200">
                                    سقف: {{ $plan->daily_limit }} فنجان در روز
                                </span>
                            @endif
                        </div>

                        @if($savingsPercent > 0)
                            <div class="px-3.5 py-1.5 bg-rose-50 text-rose-600 text-xs font-black rounded-xl border border-rose-200 flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                <span>{{ $savingsPercent }}٪ صرفه‌جویی اقتصادی</span>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-3">
                        <h1 class="text-2xl sm:text-4xl font-black text-[#19242B]">{{ $plan->name }}</h1>
                        <p class="text-sm sm:text-base text-[#64748B] leading-relaxed">
                            این پلن ویژه دوستداران قهوه و حرفه‌ای‌هایی طراحی شده است که روز کاری خود را با عطر و طعم یک فنجان قهوه تخصصی و تازه آغاز می‌کنند. با تهیه این اشتراک، هر روز بدون معطلی و با هزینه بسیار کمتر قهوه خود را بنوشید.
                        </p>
                    </div>

                    <!-- Highlight Metrics Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4 border-t border-[#E2E8F0]">
                        <div class="p-3.5 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0] text-center space-y-1">
                            <span class="text-[11px] text-[#64748B] font-medium block">کل سهمیه</span>
                            <span class="text-lg font-black text-[#19242B] tabular-nums">{{ $plan->total_items }} فنجان</span>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0] text-center space-y-1">
                            <span class="text-[11px] text-[#64748B] font-medium block">قیمت هر فنجان</span>
                            <span class="text-lg font-black text-[#387780] tabular-nums">{{ number_format($pricePerCup) }} ت</span>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0] text-center space-y-1">
                            <span class="text-[11px] text-[#64748B] font-medium block">مدت اعتبار</span>
                            <span class="text-lg font-black text-[#19242B] tabular-nums">{{ $plan->duration_days }} روز</span>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0] text-center space-y-1">
                            <span class="text-[11px] text-[#64748B] font-medium block">سود کل شما</span>
                            <span class="text-lg font-black text-emerald-600 tabular-nums">{{ number_format($savingsAmount) }} ت</span>
                        </div>
                    </div>
                </div>

                <!-- Included Coffees & Products -->
                <div class="p-6 sm:p-8 rounded-3xl bg-white border border-[#E2E8F0] shadow-xs space-y-6">
                    <div>
                        <h2 class="text-lg sm:text-xl font-black text-[#19242B]">منوی قهوه‌های قابل انتخاب در این اشتراک</h2>
                        <p class="text-xs text-[#64748B] mt-1">با این پلن، شما می‌توانید در هر بار مراجعه یا سفارش آنلاین، از بین اقلام زیر به دلخواه فنجان خود را تحویل بگیرید:</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @forelse($plan->products as $product)
                            <div class="p-4 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0] flex items-center justify-between hover:border-[#387780] transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-teal-50 border border-teal-100 flex items-center justify-center text-[#387780] font-black">
                                        <svg class="w-5 h-5 inline-block text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z M6 1v3 M10 1v3 M14 1v3"/></svg>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-black text-[#19242B]">{{ $product->name }}</h4>
                                        <span class="text-[10px] text-[#64748B]">{{ $product->category?->name ?? 'اسپرسوبار تخصصی' }}</span>
                                    </div>
                                </div>
                                <div class="text-start">
                                    <span class="text-[11px] font-bold text-slate-400 line-through tabular-nums">{{ number_format($product->price) }}</span>
                                    <span class="text-xs font-black text-emerald-600 block">رایگان با اشتراک</span>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full p-4 rounded-2xl bg-teal-50/50 border border-teal-100 text-xs text-teal-800">
                                این اشتراک شامل تمامی قهوه‌های بر پایه اسپرسو (اسپرسو، آمریکانو، لاته، کاپوچینو، فلت وایت) از دانه‌های تخصصی ۱۰۰٪ عربیکا می‌باشد.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- How it Works 3 Steps -->
                <div class="p-6 sm:p-8 rounded-3xl bg-white border border-[#E2E8F0] shadow-xs space-y-6">
                    <h2 class="text-lg sm:text-xl font-black text-[#19242B]">فرآیند تحویل و استفاده چگونه است؟</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-3 p-4 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0]">
                            <div class="w-8 h-8 rounded-xl bg-[#387780] text-white flex items-center justify-center font-black text-sm">۱</div>
                            <h3 class="text-xs font-black text-[#19242B]">خرید و فعال‌سازی آنی</h3>
                            <p class="text-[11px] text-[#64748B] leading-relaxed">
                                پلن را آنلاین پرداخت کنید؛ سهمیه فنجان‌های شما بلافاصله روی شماره موبایلتان شارژ می‌شود.
                            </p>
                        </div>

                        <div class="space-y-3 p-4 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0]">
                            <div class="w-8 h-8 rounded-xl bg-[#387780] text-white flex items-center justify-center font-black text-sm">۲</div>
                            <h3 class="text-xs font-black text-[#19242B]">مراجعه حضوری به کافه</h3>
                            <p class="text-[11px] text-[#64748B] leading-relaxed">
                                سهمیه اشتراک فقط حضوری در کافه بی کافه شیراز قابل استفاده‌ست. کافیه بیای و شماره موبایلت رو بگی.
                            </p>
                        </div>

                        <div class="space-y-3 p-4 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0]">
                            <div class="w-8 h-8 rounded-xl bg-[#387780] text-white flex items-center justify-center font-black text-sm">۳</div>
                            <h3 class="text-xs font-black text-[#19242B]">کسر خودکار سهمیه</h3>
                            <p class="text-[11px] text-[#64748B] leading-relaxed">
                                هنگام سفارش در کافه (یا ثبت سفارش آنلاین به‌صورت بیرون‌بر و سالن)، ۱ سهمیه از پلن شما کسر شده و صورتحساب ۰ تومان می‌شود.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Terms and FAQs -->
                <div class="p-6 sm:p-8 rounded-3xl bg-white border border-[#E2E8F0] shadow-xs space-y-4">
                    <h2 class="text-lg font-black text-[#19242B]">قوانین و سوالات متداول</h2>
                    
                    <div class="space-y-3 text-xs">
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="font-black text-[#19242B] block mb-1">آیا امکان استفاده در هر روز بیشتر از یک فنجان وجود دارد؟</span>
                            <p class="text-[#64748B] leading-relaxed">
                                @if($plan->daily_limit)
                                    در این پلن حداکثر می‌توانید تا {{ $plan->daily_limit }} فنجان در هر روز سفارش دهید تا سهمیه روزهای بعد حفظ شود.
                                @else
                                    بله، در این پلن محدودیتی در تعداد سفارش در یک روز وجود ندارد و تا پایان سقف {{ $plan->total_items }} فنجان آزاد هستید.
                                @endif
                            </p>
                        </div>

                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="font-black text-[#19242B] block mb-1">آیا بعد از اتمام {{ $plan->duration_days }} روز سهمیه‌های باقی‌مانده می‌سوزد؟</span>
                            <p class="text-[#64748B] leading-relaxed">
                                اعتبار پلن‌ها {{ $plan->duration_days }} روز از تاریخ خرید است؛ در صورت تمدید مجدد قبل از انقضا، اعتبار شما منتقل خواهد شد.
                            </p>
                        </div>

                        @php $siteContent = \App\Models\SiteContentSetting::getForCafe(); @endphp
                        @if($siteContent->subscriptions_rules)
                        <div class="p-3.5 rounded-xl bg-teal-50/50 border border-teal-200 text-[#19242B]">
                            <span class="font-black text-[#387780] block mb-1">ضوابط و شرایط عمومی اشتراک‌های بی کافه:</span>
                            <p class="text-xs text-slate-700 leading-relaxed whitespace-pre-line">
                                {{ $siteContent->subscriptions_rules }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

            </div>

            <!-- Left / Sidebar Column: Pricing & Purchase Card (4 Cols) -->
            <div class="lg:col-span-4 sticky top-24 space-y-6">
                
                <div class="p-6 sm:p-8 rounded-3xl bg-white border-2 border-[#387780] shadow-md space-y-6">
                    <div class="space-y-2">
                        <span class="text-xs font-black text-[#387780] block">خرید مستقیم پلن</span>
                        <h3 class="text-xl font-black text-[#19242B]">{{ $plan->name }}</h3>
                    </div>

                    <div class="p-5 rounded-2xl bg-teal-50/50 border border-teal-100 space-y-3">
                        <div class="flex items-center justify-between text-xs text-[#64748B]">
                            <span>ارزش واقعی منو:</span>
                            <span class="line-through tabular-nums">{{ number_format($totalRegularValue) }} تومان</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black text-[#19242B]">مبلغ قابل پرداخت:</span>
                            <span class="text-2xl font-black text-[#387780] tabular-nums">{{ number_format($plan->price) }} تومان</span>
                        </div>
                        <div class="pt-2 border-t border-teal-200/60 flex items-center justify-between text-xs font-bold text-emerald-700">
                            <span>سود و تخفیف شما:</span>
                            <span class="tabular-nums">{{ number_format($savingsAmount) }} تومان ({{ $savingsPercent }}٪)</span>
                        </div>
                    </div>

                    <ul class="space-y-2.5 text-xs text-[#475569]">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>{{ $plan->total_items }} فنجان قهوه تخصصی ۱۰۰٪ عربیکا</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>{{ $plan->duration_days }} روز اعتبار از زمان فعال‌سازی</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>استفاده حضوری در سالن کافه بی کافه شیراز</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>اولویت آماده‌سازی ویژه مشترکین باشگاه</span>
                        </li>
                    </ul>

                    <div class="space-y-3 pt-2" x-data="{
                        added: false,
                        addPlanToCart() {
                            let cart = [];
                            try { cart = JSON.parse(localStorage.getItem('bcafe_cart') || '[]'); } catch(e) {}
                            // Check if plan already exists in cart
                            const existing = cart.find(i => (i.type === 'subscription' || i.subscription_plan_id === {{ $plan->id }}) && (i.id === 'sub-{{ $plan->id }}' || i.subscription_plan_id === {{ $plan->id }}));
                            if (existing) {
                                existing.quantity = (existing.quantity || 1) + 1;
                            } else {
                                cart.push({
                                    id: 'sub-{{ $plan->id }}',
                                    product_id: null,
                                    type: 'subscription',
                                    subscription_plan_id: {{ $plan->id }},
                                    name: '{{ addslashes($plan->name) }} (اشتراک قهوه)',
                                    price: {{ (float) $plan->price }},
                                    base_price: {{ (float) $plan->price }},
                                    quantity: 1,
                                    options: { 'اعتبار': '{{ $plan->duration_days }} روز', 'سهمیه': '{{ $plan->total_items }} فنجان' },
                                    note: 'پلن اشتراک روزانه'
                                });
                            }
                            localStorage.setItem('bcafe_cart', JSON.stringify(cart));
                            this.added = true;
                            if (window.CafeToast) {
                                window.CafeToast.success('پلن «{{ addslashes($plan->name) }}» به سبد خرید افزوده شد.', 'سبد خرید');
                            }
                            setTimeout(() => { window.location.href = '{{ route('cart.index') }}'; }, 500);
                        }
                    }">
                        <button type="button"
                                @click="addPlanToCart()"
                                class="w-full py-4 bg-[#387780] hover:bg-[#2E626A] text-white font-black rounded-2xl text-sm flex items-center justify-center gap-2 shadow-md transition-all active:scale-[0.98] cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            <span>افزودن این اشتراک به سبد خرید</span>
                        </button>

                        <a href="{{ route('storefront.subscriptions.index') }}"
                           class="w-full py-3 bg-white hover:bg-slate-50 text-[#64748B] hover:text-[#19242B] border border-slate-200 font-bold rounded-xl text-xs flex items-center justify-center transition-colors">
                            ← بازگشت به لیست همه پلن‌ها
                        </a>
                    </div>
                </div>

                <!-- Support Box -->
                <div class="p-5 rounded-2xl bg-white border border-[#E2E8F0] shadow-2xs space-y-2 text-xs">
                    <span class="font-black text-[#19242B] block">پشتیبانی و مشاوره تلفنی</span>
                    <p class="text-[#64748B] leading-relaxed">
                        سوالی درباره پلن‌ها دارید؟ تیم پشتیبانی بی کافه همواره آماده پاسخگویی به شماست.
                    </p>
                    <a href="tel:07136280000" class="font-black text-[#387780] hover:underline block pt-1 dir-ltr text-right">
                        071-36280000
                    </a>
                </div>

            </div>

        </div>
    </main>

    <!-- Footer -->
    <x-storefront-footer />

</body>
</html>
