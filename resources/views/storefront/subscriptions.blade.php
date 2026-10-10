<!DOCTYPE html>
<html lang="fa" dir="rtl" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>باشگاه اشتراک روزانه قهوه - بی کافه (B Cafe) شیراز</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}?v=2.0.2">
    <link rel="alternate icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2.0.2">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}?v=2.0.2">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full antialiased bg-[#F8FAFC] text-[#19242B] selection:bg-[#387780] selection:text-white" x-data="subscriptionPage()">

    <!-- Top Sticky Navigation Bar -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-[#E2E8F0] shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between">
            <div class="flex items-center gap-8">
                <a href="{{ route('storefront.menu') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.jpeg') }}" alt="بی کافه" class="w-10 h-10 rounded-2xl object-cover border border-[#E2E8F0] shadow-2xs">
                    <div>
                        <span class="text-sm font-black text-[#19242B] block leading-tight">بی کافه (B Cafe)</span>
                        <span class="text-[10px] text-[#64748B] block">باشگاه مشترکین قهوه شیراز</span>
                    </div>
                </a>

                <nav class="hidden md:flex items-center gap-6 text-xs font-bold text-[#475569]">
                    <a href="{{ route('storefront.menu') }}" class="hover:text-[#387780] transition-colors">منوی آنلاین و سفارش</a>
                    <a href="{{ route('storefront.subscriptions.index') }}" class="text-[#387780] font-black border-b-2 border-[#387780] pb-0.5">اشتراک‌های روزانه</a>
                    <a href="{{ route('storefront.about') }}" class="hover:text-[#387780] transition-colors">درباره بی کافه</a>
                </nav>
            </div>

            <div class="flex items-center gap-3">
                @if(auth('customer')->check())
                    <a href="{{ route('customer.dashboard') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-[#19242B] font-bold rounded-xl text-xs transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#387780]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>پنل من ({{ auth('customer')->user()->name }})</span>
                    </a>
                @else
                    <a href="{{ route('customer.login') }}" class="px-4 py-2 bg-[#387780] hover:bg-[#2E626A] text-white font-bold rounded-xl text-xs transition-colors shadow-xs flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        <span>ورود / عضویت</span>
                    </a>
                @endif

                <a href="{{ route('cart.index') }}" class="p-2.5 rounded-xl bg-white border border-[#E2E8F0] hover:border-[#387780] text-[#19242B] transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section with Persuasive Copy -->
    <section class="relative overflow-hidden bg-gradient-to-b from-white via-[#F8FAFC] to-[#F1F5F9] border-b border-[#E2E8F0] py-16 sm:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6 relative z-10">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-teal-50 border border-teal-200 text-[#387780] text-xs font-black shadow-xs">
                <span class="w-2 h-2 rounded-full bg-[#387780] animate-ping"></span>
                <span>اشتراک ماهانه قهوه در شیراز</span>
            </div>

            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-[#19242B] tracking-tight leading-tight max-w-4xl mx-auto">
                هر روز قهوه تخصصی تازه بنوش، <br class="hidden sm:inline">
                <span class="text-[#387780]">تا ۵۰٪ کمتر هزینه کن</span>
            </h1>

            <p class="text-sm sm:text-base text-[#64748B] max-w-2xl mx-auto leading-relaxed">
                با اشتراک بی کافه، دیگه نیازی نیست هر روز کارت بکشی یا معطل بمونی. قهوه‌ت چه توی کافه چه با پیک در شیراز، همیشه با خیال راحت آماده‌ست.
            </p>

            <div class="flex flex-wrap items-center justify-center gap-4 pt-4">
                <a href="#plans-grid" class="px-8 py-4 bg-[#387780] hover:bg-[#2E626A] text-white font-black rounded-2xl text-sm transition-all shadow-md flex items-center gap-2">
                    <span>دیدن بسته‌ها و خرید اشتراک</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </a>
                <a href="#calculator" class="px-6 py-4 bg-white hover:bg-slate-50 text-[#19242B] border border-slate-300 font-bold rounded-2xl text-sm transition-colors shadow-xs">
                    محاسبه سود و صرفه‌جویی
                </a>
            </div>

            <!-- Value Highlights Badges -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto pt-10 text-start">
                <div class="p-4 rounded-2xl bg-white border border-[#E2E8F0] shadow-xs space-y-1">
                    <div class="text-[#387780] font-black text-lg">۱۰۰٪ عربیکا</div>
                    <div class="text-xs font-bold text-[#19242B]">دانه‌های تازه برشته‌شده</div>
                    <div class="text-[11px] text-[#64748B]">اتیوپی، کلمبیا و برزیل</div>
                </div>
                <div class="p-4 rounded-2xl bg-white border border-[#E2E8F0] shadow-xs space-y-1">
                    <div class="text-emerald-600 font-black text-lg">تا ۵۰٪ تخفیف</div>
                    <div class="text-xs font-bold text-[#19242B]">صرفه‌جویی خالص ماهانه</div>
                    <div class="text-[11px] text-[#64748B]">قیمت هر فنجان بسیار اقتصادی</div>
                </div>
                <div class="p-4 rounded-2xl bg-white border border-[#E2E8F0] shadow-xs space-y-1">
                    <div class="text-[#284B63] font-black text-lg">سرو بدون معطلی</div>
                    <div class="text-xs font-bold text-[#19242B]">اولویت آماده‌سازی باریستا</div>
                    <div class="text-[11px] text-[#64748B]">کسر سریع با شماره همراه</div>
                </div>
                <div class="p-4 rounded-2xl bg-white border border-[#E2E8F0] shadow-xs space-y-1">
                    <div class="text-amber-600 font-black text-lg">سراسر شیراز</div>
                    <div class="text-xs font-bold text-[#19242B]">سرو سالن یا ارسال پیک</div>
                    <div class="text-[11px] text-[#64748B]">درب منزل یا شرکت شما</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive Savings Calculator -->
    <section id="calculator" class="py-16 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white border border-[#E2E8F0] rounded-3xl p-6 sm:p-10 shadow-sm space-y-8">
            <div class="text-center space-y-2">
                <span class="text-xs font-bold text-[#387780] bg-teal-50 px-3 py-1 rounded-full border border-teal-200">حساب و کتاب هزینه قهوه</span>
                <h2 class="text-2xl sm:text-3xl font-black text-[#19242B]">با خرید اشتراک، چقدر در هزینه‌هات صرفه‌جویی می‌کنی؟</h2>
                <p class="text-xs text-[#64748B]">تعداد فنجان قهوه‌ای که در طول هفته می‌نوشی رو مشخص کن:</p>
            </div>

            <!-- Cups Selector Slider / Buttons -->
            <div class="space-y-4 max-w-xl mx-auto">
                <div class="flex items-center justify-between text-xs font-bold text-[#475569]">
                    <span>تعداد قهوه در هفته:</span>
                    <span class="text-lg font-black text-[#387780] tabular-nums" x-text="cupsPerWeek + ' فنجان'"></span>
                </div>
                <input type="range" min="1" max="7" step="1" x-model.number="cupsPerWeek"
                       class="w-full h-3 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-[#387780]">
                <div class="flex justify-between text-[11px] text-slate-400 font-bold px-1">
                    <span>۱ فنجان</span>
                    <span>۲ فنجان</span>
                    <span>۳ فنجان</span>
                    <span>۴ فنجان</span>
                    <span>۵ فنجان</span>
                    <span>۶ فنجان</span>
                    <span>۷ فنجان (هر روز)</span>
                </div>
            </div>

            <!-- Comparison Matrix Results -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-slate-100">
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-center space-y-1">
                    <span class="text-xs font-bold text-slate-500 block">هزینه خرید آزاد ماهانه</span>
                    <div class="text-xl font-black text-slate-700 tabular-nums" x-text="regularMonthlyCost.toLocaleString('fa-IR') + ' تومان'"></div>
                    <span class="text-[10px] text-slate-400 block">میانگین منوی کافه</span>
                </div>

                <div class="p-4 rounded-2xl bg-teal-50 border border-teal-200 text-center space-y-1">
                    <span class="text-xs font-bold text-teal-800 block">هزینه با اشتراک بی کافه</span>
                    <div class="text-xl font-black text-[#387780] tabular-nums" x-text="subscriptionMonthlyCost.toLocaleString('fa-IR') + ' تومان'"></div>
                    <span class="text-[10px] text-teal-700 block">با نرخ پلن اقتصادی</span>
                </div>

                <div class="p-4 rounded-2xl bg-emerald-500 text-white text-center space-y-1 shadow-xs">
                    <span class="text-xs font-bold text-emerald-100 block">صرفه‌جویی سالانه شما</span>
                    <div class="text-xl font-black tabular-nums" x-text="annualSavings.toLocaleString('fa-IR') + ' تومان'"></div>
                    <span class="text-[10px] text-emerald-100 block">پس‌انداز یک‌ساله در جیب شما</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Subscription Plans Grid Section -->
    <section id="plans-grid" class="py-16 bg-white border-t border-[#E2E8F0]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="text-center space-y-3">
                <h2 class="text-2xl sm:text-4xl font-black text-[#19242B]">بسته‌های اشتراک قهوه بی کافه</h2>
                <p class="text-xs sm:text-sm text-[#64748B] max-w-xl mx-auto leading-relaxed">
                    بسته دلخواهت رو انتخاب کن و بلافاصله بعد از پرداخت، سهمیه فنجان‌هات فعال میشه.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($plans as $plan)
                    @php
                        $pricePerCup = $plan->total_items > 0 ? (int) round($plan->price / $plan->total_items) : 0;
                    @endphp
                    <div class="bg-[#F8FAFC] border-2 border-[#E2E8F0] hover:border-[#387780] rounded-3xl p-6 sm:p-8 space-y-6 flex flex-col justify-between transition-all duration-200 card-elevated">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="px-3 py-1 bg-teal-50 text-[#387780] text-xs font-black rounded-xl border border-teal-200">
                                    {{ $plan->duration_days }} روزه
                                </span>
                                <span class="text-xs font-bold text-[#64748B]">{{ $plan->total_items }} سهمیه فنجان</span>
                            </div>

                            <div>
                                <h3 class="text-xl font-black text-[#19242B]">{{ $plan->name }}</h3>
                                <p class="text-xs text-[#64748B] mt-1">مناسب برای نوشیدن روزانه قهوه تخصصی و باکیفیت</p>
                            </div>

                            <div class="p-4 rounded-2xl bg-white border border-[#E2E8F0] space-y-1">
                                <div class="text-xs text-[#64748B] flex items-center justify-between">
                                    <span>قیمت کل اشتراک:</span>
                                    <span class="text-xl font-black text-[#387780] tabular-nums">{{ number_format($plan->price) }} تومان</span>
                                </div>
                                <div class="text-[11px] text-slate-400 flex items-center justify-between border-t border-slate-100 pt-1">
                                    <span>قیمت هر فنجان:</span>
                                    <span class="font-bold text-slate-600 tabular-nums">حدود {{ number_format($pricePerCup) }} تومان</span>
                                </div>
                            </div>

                            <!-- Included Coffees List -->
                            <div class="space-y-2 text-xs">
                                <span class="font-bold text-[#19242B] block">اقلام مشمول در این اشتراک:</span>
                                <ul class="space-y-1.5 text-[#475569]">
                                    @forelse($plan->products->take(5) as $prod)
                                        <li class="flex items-center gap-2">
                                            <svg class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            <span>{{ $prod->name }}</span>
                                        </li>
                                    @empty
                                        <li class="text-slate-400">شامل کلیه قهوه‌های منوی اسپرسوبار</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>

                        <!-- Plan Actions -->
                        <div class="space-y-2 pt-4 border-t border-[#E2E8F0]" x-data="{
                            addPlanToCart() {
                                let cart = [];
                                try { cart = JSON.parse(localStorage.getItem('bcafe_cart') || '[]'); } catch(e) {}
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
                                if (window.CafeToast) {
                                    window.CafeToast.success('پلن «{{ addslashes($plan->name) }}» به سبد خرید افزوده شد.', 'سبد خرید');
                                }
                                setTimeout(() => { window.location.href = '{{ route('cart.index') }}'; }, 500);
                            }
                        }">
                            <button type="button"
                                    @click="addPlanToCart()"
                                    class="w-full py-3.5 bg-[#387780] hover:bg-[#2E626A] text-white font-black rounded-xl text-xs flex items-center justify-center gap-2 shadow-xs transition-colors cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                <span>افزودن به سبد خرید</span>
                            </button>
                            <a href="{{ route('storefront.subscriptions.show', $plan) }}"
                               class="w-full py-2.5 bg-white hover:bg-slate-100 text-[#19242B] border border-slate-300 font-bold rounded-xl text-xs flex items-center justify-center transition-colors">
                                جزئیات بیشتر پلن
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center text-slate-500">در حال حاضر پلن فعالی تعریف نشده است.</div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- How It Works Section (۳ گام ساده) -->
    <section class="py-16 bg-[#F8FAFC] border-t border-[#E2E8F0]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12 text-center">
            <div class="space-y-2">
                <span class="text-xs font-bold text-[#387780]">خیلی ساده در ۳ قدم</span>
                <h2 class="text-2xl sm:text-3xl font-black text-[#19242B]">اشتراک قهوه چطوری کار می‌کنه؟</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-start">
                <div class="bg-white p-6 rounded-3xl border border-[#E2E8F0] space-y-3 shadow-xs">
                    <div class="w-12 h-12 rounded-2xl bg-teal-50 text-[#387780] font-black text-lg flex items-center justify-center">۱</div>
                    <h3 class="text-base font-bold text-[#19242B]">انتخاب و خرید آنلاین</h3>
                    <p class="text-xs text-[#64748B] leading-relaxed">بسته دلخواهت رو در همین صفحه انتخاب کن و با شماره موبایل و کارت بانکی خریدت رو انجام بده. اشتراکت در لحظه فعال میشه.</p>
                </div>

                <div class="bg-white p-6 rounded-3xl border border-[#E2E8F0] space-y-3 shadow-xs">
                    <div class="w-12 h-12 rounded-2xl bg-teal-50 text-[#387780] font-black text-lg flex items-center justify-center">۲</div>
                    <h3 class="text-base font-bold text-[#19242B]">سفارش توی کافه یا با پیک</h3>
                    <p class="text-xs text-[#64748B] leading-relaxed">هر روز کافیه شماره همراهت رو به باریستا بگی یا موقع سفارش آنلاین، گزینه «استفاده از سهمیه اشتراک» رو انتخاب کنی.</p>
                </div>

                <div class="bg-white p-6 rounded-3xl border border-[#E2E8F0] space-y-3 shadow-xs">
                    <div class="w-12 h-12 rounded-2xl bg-teal-50 text-[#387780] font-black text-lg flex items-center justify-center">۳</div>
                    <h3 class="text-base font-bold text-[#19242B]">نوشیدن فنجان تازه با خیال راحت</h3>
                    <p class="text-xs text-[#64748B] leading-relaxed">قهوه‌ت با کیفیت همیشگی آماده میشه و وضعیت سهمیه فنجان‌هات لحظه‌ای در پنل کاربری به‌روز میشه.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Accordion -->
    <section class="py-16 bg-white border-t border-[#E2E8F0]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8" x-data="{ openFaq: null }">
            <div class="text-center space-y-2">
                <span class="text-xs font-bold text-[#387780]">پرسش و پاسخ</span>
                <h2 class="text-2xl sm:text-3xl font-black text-[#19242B]">سؤالات متداول درباره اشتراک قهوه</h2>
            </div>

            <div class="space-y-3">
                <div class="border border-[#E2E8F0] rounded-2xl overflow-hidden">
                    <button type="button" @click="openFaq = openFaq === 1 ? null : 1" class="w-full p-4 text-start font-bold text-xs flex items-center justify-between bg-[#F8FAFC]">
                        <span>آیا روزهای تعطیل هم می‌تونم از سهمیه اشتراک استفاده کنم؟</span>
                        <span x-text="openFaq === 1 ? '−' : '+'" class="text-base"></span>
                    </button>
                    <div x-show="openFaq === 1" class="p-4 text-xs text-[#64748B] bg-white leading-relaxed border-t border-[#E2E8F0]">
                        بله! بی کافه همه روزهای هفته حتی روزهای تعطیل بازه و سهمیه اشتراکت همیشه معتبره.
                    </div>
                </div>

                <div class="border border-[#E2E8F0] rounded-2xl overflow-hidden">
                    <button type="button" @click="openFaq = openFaq === 2 ? null : 2" class="w-full p-4 text-start font-bold text-xs flex items-center justify-between bg-[#F8FAFC]">
                        <span>اگر یه روز نتونم بیام، سهمیه فنجان اون روز می‌سوزه؟</span>
                        <span x-text="openFaq === 2 ? '−' : '+'" class="text-base"></span>
                    </button>
                    <div x-show="openFaq === 2" class="p-4 text-xs text-[#64748B] bg-white leading-relaxed border-t border-[#E2E8F0]">
                        نگران نباش! سهمیه فنجان‌هات تا پایان اعتبار پلن باقی می‌مونه و روزهایی که نیای نمی‌سوزه.
                    </div>
                </div>

                <div class="border border-[#E2E8F0] rounded-2xl overflow-hidden">
                    <button type="button" @click="openFaq = openFaq === 3 ? null : 3" class="w-full p-4 text-start font-bold text-xs flex items-center justify-between bg-[#F8FAFC]">
                        <span>می‌تونم قهوه اشتراک رو با پیک در محل کار یا خونه تحویل بگیرم؟</span>
                        <span x-text="openFaq === 3 ? '−' : '+'" class="text-base"></span>
                    </button>
                    <div x-show="openFaq === 3" class="p-4 text-xs text-[#64748B] bg-white leading-relaxed border-t border-[#E2E8F0]">
                        بله! موقع ثبت سفارش آنلاین توی سایت، کافیه گزینه کسر از سهمیه رو بزنی تا قهوه‌ت با پیک ارسال بشه.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-[#19242B] text-white py-12 border-t border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-wrap items-center justify-between gap-6 text-xs text-slate-300">
            <div>
                <span class="font-black text-white text-sm block">بی کافه شیراز (B Cafe)</span>
                <span class="text-[11px] text-slate-400">شیراز، میدان ارم، ابتدای بلوار دانشجو • تلفن: ۰۷۱-۳۶۲۸۰۰۰۰</span>
            </div>
            <div class="flex items-center gap-6">
                <a href="{{ route('storefront.menu') }}" class="hover:underline">منوی کافه</a>
                <a href="{{ route('storefront.subscriptions.index') }}" class="hover:underline">اشتراک‌ها</a>
                <a href="{{ route('storefront.about') }}" class="hover:underline">درباره ما</a>
            </div>
        </div>
    </footer>

    <!-- Alpine.js Script for Calculator -->
    <script>
    function subscriptionPage() {
        return {
            cupsPerWeek: 5,
            cupPriceRegular: 75000,
            cupPriceSub: 38000,

            get regularMonthlyCost() {
                return this.cupsPerWeek * 4 * this.cupPriceRegular;
            },

            get subscriptionMonthlyCost() {
                return this.cupsPerWeek * 4 * this.cupPriceSub;
            },

            get monthlySavings() {
                return this.regularMonthlyCost - this.subscriptionMonthlyCost;
            },

            get annualSavings() {
                return this.monthlySavings * 12;
            }
        };
    }
    </script>
</body>
</html>
