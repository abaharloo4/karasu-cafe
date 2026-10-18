<!DOCTYPE html>
<html lang="fa" dir="rtl" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>سبد خرید شما | بی کافه شیراز (B Cafe)</title>
    <meta name="description" content="بررسی و مدیریت سبد خرید در بی کافه شیراز">

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css">

    <!-- Scripts & Styles via Vite -->
    @include('partials.theme-init')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col antialiased selection:bg-[#387780] selection:text-white bg-[#F8FAFC] dark:bg-[#0F171B] text-[#19242B] dark:text-slate-100 pb-44 lg:pb-12 transition-colors"
      x-data="cartPage()"
      x-init="init()">

    <!-- Unified Global Navigation Header -->
    <x-storefront-header :activeTab="'cart'" />

    <!-- Main Content Container -->
    <main class="max-w-6xl mx-auto px-4 sm:px-6 py-6 sm:py-8 flex-1 w-full">
        <!-- Empty Cart State -->
        <template x-if="cart.length === 0">
            <div class="max-w-md mx-auto text-center py-16 px-4 space-y-6">
                <div class="w-24 h-24 mx-auto rounded-full bg-[#F7F9FA] border-2 border-dashed border-[#284B63]/40 flex items-center justify-center text-[#284B63]">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
                <div class="space-y-2">
                    <h2 class="text-xl font-black text-[#353535]">سبد خرید شما در حال حاضر خالی است</h2>
                    <p class="text-xs sm:text-sm text-[#353535]/70 leading-relaxed">
                        قهوه‌های تخصصی تازه رست‌شده، دسرهای فرانسوی و شیک‌های دست‌ساز بی‌کافه منتظر شما هستند!
                    </p>
                </div>
                <div class="pt-2">
                    <a href="{{ route('storefront.menu') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-[#353535] hover:bg-[#1E2912] text-[#F7F9FA] font-black text-sm rounded-2xl shadow-md btn-tactile">
                        <span>مشاهده منو و انتخاب قهوه</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                </div>
            </div>
        </template>

        <!-- Cart Content (Items + Summary) -->
        <template x-if="cart.length > 0">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 items-start">

                <!-- Left Column: Items List -->
                <div class="lg:col-span-7 space-y-4">
                    <div class="bg-white rounded-3xl p-4 sm:p-6 border border-[#353535]/10 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-[#353535]/10 pb-3">
                            <span class="text-sm font-black text-[#353535]">اقلام سفارش شما</span>
                            <span class="text-xs text-[#353535]/60 tabular-nums" x-text="cart.length + ' ردیف کالا'"></span>
                        </div>

                        <!-- Item Cards Loop -->
                        <div class="space-y-3 divide-y divide-[#353535]/5">
                            <template x-for="(item, index) in cart" :key="item.id + '-' + index">
                                <div class="pt-3 first:pt-0 space-y-2">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="space-y-1 flex-1">
                                            <template x-if="item.type === 'subscription' || item.subscription_plan_id">
                                                <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-black bg-[#3C6E71]/15 text-[#3C6E71] border border-[#3C6E71]/25">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                                    <span>پلن اشتراک روزانه قهوه</span>
                                                </div>
                                            </template>
                                            <h3 class="text-sm font-black text-[#353535]" x-text="item.name"></h3>

                                            <!-- Display Options if any -->
                                            <template x-if="item.options && Object.keys(item.options).length > 0">
                                                <div class="flex flex-wrap gap-1 pt-0.5">
                                                    <template x-for="(optVal, optKey) in item.options" :key="optKey">
                                                        <span class="inline-flex items-center text-[10px] bg-[#F7F9FA] text-[#3C6E71] font-bold px-2 py-0.5 rounded-md border border-[#353535]/10">
                                                            <span x-text="optKey + ': ' + optVal"></span>
                                                        </span>
                                                    </template>
                                                </div>
                                            </template>

                                            <!-- Display Note if any -->
                                            <template x-if="item.note && item.note.trim()">
                                                <div class="text-[11px] text-[#284B63] font-semibold flex items-center gap-1">
                                                    <span class="text-[#353535]/50">یادداشت:</span>
                                                    <span x-text="item.note"></span>
                                                </div>
                                            </template>

                                            <!-- Unit Price -->
                                            <div class="text-xs text-[#353535]/60">
                                                قیمت واحد: <span class="font-bold tabular-nums" x-text="formatNumber(item.price)"></span> تومان
                                            </div>
                                        </div>

                                        <!-- Quantity Controls & Line Total -->
                                        <div class="flex flex-col items-end gap-2">
                                            <!-- Line Total -->
                                            <div class="text-sm font-black text-[#284B63] tabular-nums">
                                                <span x-text="formatNumber(item.price * item.quantity)"></span> تومان
                                            </div>

                                            <!-- Quantity Stepper -->
                                            <div class="flex items-center border border-[#353535]/20 rounded-xl bg-white p-0.5 shadow-2xs">
                                                <button @click="increment(index)" type="button" class="w-11 h-11 min-h-[44px] min-w-[44px] flex items-center justify-center text-[#353535] hover:bg-[#3C6E71]/10 rounded-lg btn-tactile">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                                </button>
                                                <span class="w-8 text-center text-xs font-black text-[#353535] tabular-nums" x-text="item.quantity"></span>
                                                <button @click="decrement(index)" type="button" class="w-11 h-11 min-h-[44px] min-w-[44px] flex items-center justify-center text-[#353535] hover:bg-[#3C6E71]/10 rounded-lg btn-tactile">
                                                    <template x-if="item.quantity > 1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                                                    </template>
                                                    <template x-if="item.quantity <= 1">
                                                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </template>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Item Notes Input -->
                                    <div class="pt-1">
                                        <input type="text"
                                               x-model="item.notes"
                                               @input="saveCart()"
                                               placeholder="یادداشت برای این آیتم (مثلاً کم‌شکر، خیلی داغ، بدون یخ...)"
                                               class="w-full text-[11px] bg-[#F7F9FA]/40 border border-[#353535]/10 rounded-xl px-3 py-1.5 focus:bg-white focus:ring-1 focus:ring-[#3C6E71] focus:border-[#3C6E71] outline-hidden placeholder:text-[#353535]/35 transition-colors">
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Recommended Upsell Items -->
                    @if(isset($suggestedProducts) && $suggestedProducts->isNotEmpty())
                    <div class="bg-white rounded-3xl p-4 sm:p-5 border border-[#353535]/10 shadow-xs space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-4 rounded-full bg-[#284B63]"></span>
                            <h3 class="text-xs sm:text-sm font-black text-[#353535]">پیشنهاد باریستا در کنار سفارش شما</h3>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($suggestedProducts as $suggested)
                            <div class="p-3 rounded-2xl bg-[#F7F9FA]/50 border border-[#353535]/10 flex items-center justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-xs font-black text-[#353535] truncate">{{ $suggested->name }}</p>
                                    <p class="text-[11px] text-[#284B63] font-bold tabular-nums">{{ number_format($suggested->price) }} تومان</p>
                                </div>
                                <button type="button"
                                        @click="addSuggested({{ $suggested->id }}, '{{ addslashes($suggested->name) }}', {{ $suggested->price }})"
                                        class="px-2.5 py-1.5 bg-[#353535] hover:bg-[#1E2912] text-[#F7F9FA] rounded-xl text-xs font-bold btn-tactile flex-shrink-0 flex items-center gap-1 shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                    <span>افزودن</span>
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Right Column: Order Summary & Checkout Action -->
                <div class="lg:col-span-5 space-y-4 lg:sticky lg:top-24">
                    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-[#353535]/10 shadow-xs space-y-5">
                        <h2 class="text-base font-black text-[#353535] border-b border-[#353535]/10 pb-3 flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#284B63]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <span>خلاصه فاکتور سفارش</span>
                        </h2>

                        <!-- Cost Breakdown -->
                        <div class="space-y-3 text-xs sm:text-sm">
                            <div class="flex items-center justify-between text-[#353535]/70">
                                <span>مجموع اقلام (<span class="tabular-nums font-bold" x-text="cartCount()"></span> عدد):</span>
                                <span class="font-bold text-[#353535] tabular-nums"><span x-text="formatNumber(subtotal())"></span> تومان</span>
                            </div>

                            <div class="flex items-center justify-between text-[#353535]/70">
                                <span>هزینه بسته‌بندی ویژه بی کافه:</span>
                                <span class="font-bold text-[#3C6E71]">رایگان</span>
                            </div>

                            <div class="flex items-center justify-between text-[#353535]/70">
                                <span>ارسال پیک در شیراز:</span>
                                <span class="font-bold text-[#353535]/60 text-[11px]">محاسبه در مرحله بعد</span>
                            </div>

                            <div class="pt-3 border-t border-[#353535]/10 flex items-baseline justify-between">
                                <span class="text-sm font-black text-[#353535]">مبلغ قابل پرداخت:</span>
                                <div class="text-left">
                                    <span class="text-xl sm:text-2xl font-black text-[#284B63] tabular-nums" x-text="formatNumber(subtotal())"></span>
                                    <span class="text-xs font-bold text-[#353535]/60 mr-1">تومان</span>
                                </div>
                            </div>
                        </div>

                        <!-- Checkout Button -->
                        <div class="pt-2 space-y-2">
                            <a href="{{ route('cart.checkout') }}"
                               class="w-full py-4 bg-[#284B63] hover:bg-[#9B561B] text-[#F7F9FA] font-black rounded-2xl text-sm btn-tactile shadow-md flex items-center justify-center gap-2 min-h-[48px]">
                                <span>ادامه و تکمیل مشخصات ارسال</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            </a>

                            <a href="{{ route('storefront.menu') }}"
                               class="w-full py-3 bg-[#F7F9FA] hover:bg-[#F7F9FA]/80 text-[#353535] font-bold rounded-2xl text-xs btn-tactile flex items-center justify-center gap-1.5 border border-[#353535]/15">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                                <span>مشاهده منو و انتخاب آیتم‌های بیشتر</span>
                            </a>
                        </div>

                        <!-- Trust Badges -->
                        <div class="grid grid-cols-2 gap-2 pt-2 border-t border-[#353535]/8 text-[11px] text-[#353535]/70">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-[#3C6E71]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>دانه‌های تازه برشت</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-[#3C6E71]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>آماده‌سازی فوری</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </main>

    <!-- Mobile Sticky Checkout Bottom Bar -->
    <template x-if="cart.length > 0">
        <div class="fixed bottom-[calc(4rem+env(safe-area-inset-bottom))] inset-x-0 z-30 md:hidden bg-white/95 backdrop-blur-md border-t border-[#353535]/15 p-3 px-4 shadow-lg">
            <div class="flex items-center justify-between gap-3 max-w-lg mx-auto">
                <div class="min-w-0">
                    <p class="text-[11px] text-[#353535]/60">مبلغ قابل پرداخت (<span x-text="cartCount()"></span> آیتم):</p>
                    <p class="text-base font-black text-[#284B63] tabular-nums">
                        <span x-text="formatNumber(subtotal())"></span>
                        <span class="text-[10px] font-bold text-[#353535]/70 mr-0.5">تومان</span>
                    </p>
                </div>
                <a href="{{ route('cart.checkout') }}"
                   class="flex-1 max-w-[200px] py-3 bg-[#284B63] hover:bg-[#9B561B] text-[#F7F9FA] font-black rounded-xl text-xs btn-tactile shadow-sm flex items-center justify-center gap-1.5 min-h-[44px]">
                    <span>تکمیل و پرداخت</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
            </div>
        </div>
    </template>

    <!-- Mobile Bottom Navigation Bar -->
    <x-mobile-nav :active="'cart'"/>

    <!-- Alpine.js Storefront Cart Logic -->
    <script>
    function cartPage() {
        return {
            cart: [],

            init() {
                try {
                    const raw = localStorage.getItem('bcafe_cart');
                    this.cart = raw ? JSON.parse(raw) : [];
                } catch (e) {
                    this.cart = [];
                }

                @if(isset($autoAddPlan) && $autoAddPlan)
                    const pId = {{ $autoAddPlan->id }};
                    const existing = this.cart.find(i => (i.type === 'subscription' || i.subscription_plan_id === pId) && (i.id === 'sub-' + pId || i.subscription_plan_id === pId));
                    if (existing) {
                        existing.quantity = (existing.quantity || 1) + 1;
                    } else {
                        this.cart.push({
                            id: 'sub-' + pId,
                            product_id: null,
                            type: 'subscription',
                            subscription_plan_id: pId,
                            name: '{{ addslashes($autoAddPlan->name) }} (اشتراک قهوه)',
                            price: {{ (float) $autoAddPlan->price }},
                            base_price: {{ (float) $autoAddPlan->price }},
                            quantity: 1,
                            options: {
                                'مدت اعتبار': '{{ $autoAddPlan->duration_days }} روز',
                                'سهمیه کل': '{{ $autoAddPlan->total_items }} فنجان قهوه'
                            },
                            note: 'پلن اشتراک روزانه'
                        });
                    }
                    this.saveCart();
                @endif
            },

            saveCart() {
                try {
                    localStorage.setItem('bcafe_cart', JSON.stringify(this.cart));
                } catch (e) {
                    console.error('Failed to save cart:', e);
                }
            },

            cartCount() {
                return this.cart.reduce((sum, item) => sum + (parseInt(item.quantity) || 1), 0);
            },

            subtotal() {
                return this.cart.reduce((sum, item) => sum + ((parseFloat(item.price) || 0) * (parseInt(item.quantity) || 1)), 0);
            },

            increment(index) {
                if (this.cart[index]) {
                    this.cart[index].quantity = (parseInt(this.cart[index].quantity) || 1) + 1;
                    this.saveCart();
                }
            },

            decrement(index) {
                if (this.cart[index]) {
                    if (this.cart[index].quantity > 1) {
                        this.cart[index].quantity -= 1;
                    } else {
                        this.cart.splice(index, 1);
                    }
                    this.saveCart();
                }
            },

            clearCart() {
                if (confirm('آیا از خالی کردن سبد خرید خود اطمینان دارید؟')) {
                    this.cart = [];
                    localStorage.removeItem('bcafe_cart');
                }
            },

            addSuggested(id, name, price) {
                const existing = this.cart.find(item => item.id === id);
                if (existing) {
                    existing.quantity += 1;
                } else {
                    this.cart.push({
                        id: id,
                        name: name,
                        price: price,
                        quantity: 1,
                        options: {},
                        notes: ''
                    });
                }
                this.saveCart();
            },

            formatNumber(num) {
                return new Intl.NumberFormat('fa-IR').format(num || 0);
            }
        };
    }
    </script>

    <x-storefront-footer />
    <x-toast-container />
</body>
</html>
