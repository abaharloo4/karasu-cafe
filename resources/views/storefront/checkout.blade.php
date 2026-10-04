<!DOCTYPE html>
<html lang="fa" dir="rtl" class="h-full bg-[#F7F9FA]/35 text-[#353535]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تکمیل و پرداخت سفارش - بی کافه (B Cafe)</title>

    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.jpeg') }}">
    <link rel="shortcut icon" type="image/jpeg" href="{{ asset('images/logo.jpeg') }}">

    <!-- Leaflet for Neshan Maps integration -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data="checkoutPage()" class="min-h-full antialiased selection:bg-[#3C6E71] selection:text-[#F7F9FA] pt-4 sm:pt-8 pb-36 lg:pb-16 px-4 sm:px-6 lg:px-8">

    <div class="max-w-6xl mx-auto space-y-6">

        <!-- Top Header Navigation -->
        <header class="flex items-center justify-between border-b border-[#353535]/10 pb-4">
            <a href="{{ route('storefront.menu') }}" class="inline-flex items-center gap-2 text-xs font-bold text-[#353535] hover:text-[#284B63] btn-tactile min-h-[44px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span>بازگشت به منوی بی کافه</span>
            </a>

            <div class="flex items-center gap-3">
                <div class="text-end hidden sm:block">
                    <span class="text-xs font-black text-[#353535] block">بی کافه (B Cafe)</span>
                    <span class="text-[10px] text-[#353535]/65">شعبه شیراز • ثبت آنلاین سفارش</span>
                </div>
                <img src="{{ asset('images/logo.jpeg') }}" alt="بی کافه" class="w-10 h-10 rounded-2xl object-cover border border-[#353535]/15 shadow-2xs">
            </div>
        </header>

        @php
            $currentCafe = \App\Models\Cafe::first();
            $isShiftOpen = $currentCafe ? $currentCafe->isOrderingOpen() : true;
        @endphp

        @if(!$isShiftOpen)
            <div class="p-4 rounded-2xl bg-amber-500/10 border-2 border-amber-500/30 text-amber-950 text-xs font-bold flex items-center gap-3 shadow-xs">
                <svg class="w-5 h-5 flex-shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="space-y-0.5">
                    <span class="block text-amber-900 font-black">توجه: هم‌اکنون خارج از ساعات کاری شیفت کافه ({{ $currentCafe?->getFormattedShiftHours() ?? '۰۸:۰۰ الی ۲۲:۰۰' }}) است.</span>
                    <span class="block text-amber-900/80 font-normal">می‌توانید سفارش خود را ثبت نمایید؛ سفارش شما ثبت و برای شروع شیفت کاری بعدی رزرو و آماده خواهد شد.</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-2xl bg-[#B44A4A]/10 border border-[#B44A4A]/30 text-[#B44A4A] text-xs font-bold flex items-center gap-3 shadow-xs">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 rounded-2xl bg-[#B44A4A]/10 border border-[#B44A4A]/30 text-[#B44A4A] text-xs font-bold space-y-1.5 shadow-xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>لطفاً خطاهای زیر را برطرف فرمایید:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 pr-6 font-semibold">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('cart.order') }}" enctype="multipart/form-data" @submit="handleSubmit($event)">
            @csrf

            <!-- 2-Column Responsive Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                <!-- Start Column (lg: 7 cols) - Form Steps -->
                <div class="lg:col-span-7 space-y-6">

                    <!-- Step 1: Customer Personal Information -->
                    <div class="bg-white border border-[#353535]/10 p-5 sm:p-6 rounded-3xl space-y-4 shadow-xs">
                        <div class="flex items-center justify-between border-b border-[#353535]/10 pb-3.5">
                            <h2 class="text-sm font-black text-[#353535] flex items-center gap-2.5">
                                <span class="w-6 h-6 rounded-xl bg-[#353535] text-[#F7F9FA] flex items-center justify-center text-xs font-bold">۱</span>
                                <span>اطلاعات تماس و مشتری</span>
                            </h2>
                            <span class="text-[11px] text-[#353535]/65">برای هماهنگی تحویل یا سرو</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-[#353535] mb-1.5">نام و نام خانوادگی <span class="text-[#B44A4A]">*</span></label>
                                <input type="text" name="customer_name" x-model="customerName" required placeholder="مثلاً: علیرضا رضایی"
                                       class="w-full px-4 py-3 rounded-xl bg-[#F7F9FA]/25 border border-[#353535]/15 text-[#353535] placeholder-[#353535]/45 text-xs focus:outline-none focus:border-[#3C6E71] focus:bg-white transition-colors min-h-[48px]">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-[#353535] mb-1.5">شماره موبایل <span class="text-[#B44A4A]">*</span></label>
                                <input type="tel" name="customer_phone" x-model="customerPhone" required placeholder="09171234567" dir="ltr"
                                       class="w-full px-4 py-3 rounded-xl bg-[#F7F9FA]/25 border border-[#353535]/15 text-[#353535] placeholder-[#353535]/45 text-xs font-mono text-start tabular-nums focus:outline-none focus:border-[#3C6E71] focus:bg-white transition-colors min-h-[48px]">
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Order Mode (Dine-in vs Courier Delivery in Shiraz) -->
                    <!-- Step 2: Order Mode (Dine-in vs Courier Delivery in Shiraz) -->
                    <div class="bg-white border border-[#353535]/10 p-5 sm:p-6 rounded-3xl space-y-5 shadow-xs">
                        <div class="flex items-center justify-between border-b border-[#353535]/10 pb-3.5">
                            <h2 class="text-sm font-black text-[#353535] flex items-center gap-2.5">
                                <span class="w-6 h-6 rounded-xl bg-[#353535] text-[#F7F9FA] flex items-center justify-center text-xs font-bold">۲</span>
                                <span>نحوه تحویل سفارش</span>
                            </h2>
                            <span class="text-[11px] text-[#284B63] font-bold">حضوری یا ارسال با پیک در شیراز</span>
                        </div>

                        <!-- Mode Switcher Cards -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                            <!-- Dine-in Option -->
                            <label :class="orderType === 'dine_in' ? 'border-[#353535] bg-[#3C6E71]/10 ring-1 ring-[#353535]' : 'border-[#353535]/15 bg-white hover:border-[#3C6E71]/50'"
                                   class="p-4 rounded-2xl border-2 cursor-pointer btn-tactile flex items-start gap-3 transition-colors min-h-[72px]">
                                <input type="radio" name="order_type" value="dine_in" x-model="orderType" class="mt-1 text-[#353535] focus:ring-[#3C6E71]">
                                <div class="space-y-0.5">
                                    <span class="text-xs font-black text-[#353535] block">سرو در سالن کافه</span>
                                    <span class="text-[11px] text-[#353535]/65 block">نشستن در سالن یا پشت میز</span>
                                </div>
                            </label>

                            <!-- Takeaway Option -->
                            <label :class="orderType === 'takeaway' ? 'border-[#353535] bg-[#3C6E71]/10 ring-1 ring-[#353535]' : 'border-[#353535]/15 bg-white hover:border-[#3C6E71]/50'"
                                   class="p-4 rounded-2xl border-2 cursor-pointer btn-tactile flex items-start gap-3 transition-colors min-h-[72px]">
                                <input type="radio" name="order_type" value="takeaway" x-model="orderType" class="mt-1 text-[#353535] focus:ring-[#3C6E71]">
                                <div class="space-y-0.5">
                                    <span class="text-xs font-black text-[#353535] block">بیرون‌بر (Takeaway)</span>
                                    <span class="text-[11px] text-[#353535]/65 block">تحویل حضوری از کانتر برای بردن</span>
                                </div>
                            </label>

                            <!-- Courier Option -->
                            <label :class="orderType === 'courier' ? 'border-[#353535] bg-[#3C6E71]/10 ring-1 ring-[#353535]' : 'border-[#353535]/15 bg-white hover:border-[#3C6E71]/50'"
                                   class="p-4 rounded-2xl border-2 cursor-pointer btn-tactile flex items-start gap-3 transition-colors min-h-[72px]">
                                <input type="radio" name="order_type" value="courier" x-model="orderType" class="mt-1 text-[#353535] focus:ring-[#3C6E71]">
                                <div class="space-y-0.5">
                                    <span class="text-xs font-black text-[#353535] block">ارسال با پیک (شیراز)</span>
                                    <span class="text-[11px] text-[#353535]/65 block">بسته‌بندی محکم و ارسال به آدرس شما</span>
                                </div>
                            </label>
                        </div>

                        <!-- Conditional: Dine-in Table Number -->
                        <div x-show="orderType === 'dine_in'" x-transition class="p-4 rounded-2xl bg-[#F7F9FA]/40 border border-[#353535]/10 space-y-2">
                            <label class="block text-xs font-bold text-[#353535]">شماره میز در سالن کافه (اختیاری):</label>
                            <input type="text" name="table_number" placeholder="مثلاً: میز شماره ۵ (یا بگذارید خالی بماند برای تحویل از کانتر)"
                                   class="w-full sm:w-80 px-4 py-2.5 rounded-xl bg-white border border-[#353535]/15 text-[#353535] text-xs focus:outline-none focus:border-[#3C6E71] focus:ring-1 focus:ring-[#3C6E71] transition-colors min-h-[44px]">
                        </div>

                        <!-- Conditional: Shiraz Courier Delivery with Neshan Map -->
                        <div x-show="orderType === 'courier'" x-transition class="space-y-4 pt-1">
                            @if(isset($savedAddresses) && $savedAddresses->isNotEmpty())
                            <!-- Saved Addresses Quick Pick -->
                            <div class="p-3.5 rounded-2xl bg-[#F7F9FA]/70 border border-[#3C6E71]/40 space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-black text-[#353535] flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-[#284B63]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                        <span>انتخاب از آدرس‌های قبلی شما در شیراز:</span>
                                    </label>
                                    <span class="text-[10px] text-[#284B63] font-black">انتخاب سریع</span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach($savedAddresses as $addr)
                                        <button type="button"
                                                @click="applySavedAddress({
                                                    id: {{ $addr->id }},
                                                    address_line: '{{ addslashes($addr->address_line) }}',
                                                    zone_id: '{{ $addr->delivery_zone_id }}',
                                                    lat: {{ $addr->latitude ?? 29.6293 }},
                                                    lng: {{ $addr->longitude ?? 52.5186 }},
                                                    unit: '{{ addslashes($addr->building_unit ?? '') }}'
                                                })"
                                                :class="addressId === {{ $addr->id }} ? 'border-[#3C6E71] bg-[#3C6E71]/15 ring-2 ring-[#3C6E71]' : 'border-[#353535]/15 bg-white hover:border-[#3C6E71]'"
                                                class="p-2.5 rounded-xl border text-start btn-tactile text-xs space-y-0.5 shadow-2xs transition-all">
                                            <div class="flex items-center justify-between">
                                                <span class="font-bold text-[#353535] block text-[11px]">{{ $addr->title ?? 'آدرس شیراز' }}</span>
                                                <span x-show="addressId === {{ $addr->id }}" class="text-[9px] font-black bg-[#3C6E71] text-white px-2 py-0.5 rounded-full">انتخاب شده</span>
                                            </div>
                                            <span class="text-[10px] text-[#353535]/70 block truncate">{{ $addr->address_line }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <div>
                                <label class="block text-xs font-bold text-[#353535] mb-1.5">انتخاب منطقه ارسال در شیراز <span class="text-[#B44A4A]">*</span></label>
                                <select name="delivery_zone_id" x-model="selectedZoneId" @change="updateDeliveryFee()"
                                        class="w-full px-4 py-3 rounded-xl bg-white border border-[#353535]/15 text-[#353535] text-xs font-bold focus:outline-none focus:border-[#3C6E71] focus:ring-1 focus:ring-[#3C6E71] transition-colors min-h-[48px]">
                                    @foreach($deliveryZones as $zone)
                                        <option value="{{ $zone->id }}" data-fee="{{ (int) $zone->delivery_price }}">
                                            {{ $zone->name }} - هزینه پیک: {{ number_format($zone->delivery_price) }} تومان
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Neshan Map Pin Drop Container -->
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-bold text-[#353535]">انتخاب دقیق لوکیشن روی نقشه شیراز:</label>
                                    <span class="text-[11px] text-[#353535]/65 font-mono text-start tabular-nums" dir="ltr" x-text="'مختصات: ' + latitude + ', ' + longitude"></span>
                                </div>

                                <div class="relative w-full h-64 rounded-2xl overflow-hidden border border-[#353535]/15 shadow-inner bg-[#F7F9FA]/20">
                                    <div id="neshan-map" class="w-full h-full"></div>
                                    <!-- Fixed Center Pin -->
                                    <div class="absolute inset-0 pointer-events-none flex items-center justify-center z-[1000]">
                                        <div class="transform -translate-y-5 flex flex-col items-center">
                                            <div class="w-10 h-10 rounded-full bg-[#353535] text-[#F7F9FA] flex items-center justify-center shadow-lg border-2 border-white animate-bounce">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                            </div>
                                            <div class="w-2.5 h-1 bg-black/40 rounded-full blur-[1px]"></div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="latitude" x-model="latitude">
                                <input type="hidden" name="longitude" x-model="longitude">
                                <input type="hidden" name="address_id" :value="addressId">
                                <p class="text-[11px] text-[#353535]/65">نقشه رو تکون بده تا پین وسط دقیقاً بیفته روی در ساختمون یا محل تحویل.</p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-bold text-[#353535] mb-1.5">آدرس دقیق در شیراز <span class="text-[#B44A4A]">*</span></label>
                                    <input type="text" name="address_line" x-model="addressLine" @input="addressId = null" placeholder="مثلاً: شیراز، خیابان ارم، کوچه ..."
                                           class="w-full px-4 py-3 rounded-xl bg-white border border-[#353535]/15 text-[#353535] text-xs focus:outline-none focus:border-[#3C6E71] focus:ring-1 focus:ring-[#3C6E71] transition-colors min-h-[48px]">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-[#353535] mb-1.5">پلاک / واحد / طبقه</label>
                                    <input type="text" name="building_unit" placeholder="مثلاً: پلاک ۱۲، واحد ۳"
                                           class="w-full px-4 py-3 rounded-xl bg-white border border-[#353535]/15 text-[#353535] text-xs focus:outline-none focus:border-[#3C6E71] focus:ring-1 focus:ring-[#3C6E71] transition-colors min-h-[48px]">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Payment Method Selection -->
                    <div class="bg-white border border-[#353535]/10 p-5 sm:p-6 rounded-3xl space-y-4 shadow-xs">
                        <div class="flex items-center justify-between border-b border-[#353535]/10 pb-3.5">
                            <h2 class="text-sm font-black text-[#353535] flex items-center gap-2.5">
                                <span class="w-6 h-6 rounded-xl bg-[#353535] text-[#F7F9FA] flex items-center justify-center text-xs font-bold">۳</span>
                                <span>روش پرداخت</span>
                            </h2>
                            <span class="text-[11px] text-[#3C6E71] font-bold">امن و مطمئن</span>
                        </div>

                        <div class="space-y-3">
                            <!-- Zarinpal Gateway -->
                            @if($gateways['zarinpal']->is_active ?? true)
                            <label :class="paymentMethod === 'zarinpal' ? 'border-[#353535] bg-[#3C6E71]/10 ring-1 ring-[#353535]' : 'border-[#353535]/15 bg-white hover:border-[#3C6E71]/50'"
                                   class="p-4 rounded-2xl border-2 cursor-pointer btn-tactile flex items-center justify-between transition-colors min-h-[64px]">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="payment_method" value="zarinpal" x-model="paymentMethod" class="text-[#353535] focus:ring-[#3C6E71]">
                                    <div>
                                        <span class="text-xs font-black text-[#353535] block">درگاه پرداخت اینترنتی زرین‌پال (شاپرک)</span>
                                        <span class="text-[11px] text-[#353535]/65">پرداخت امن آنلاین با کلیه کارت‌های بانکی عضو شتاب</span>
                                    </div>
                                </div>
                                <span class="text-[11px] text-[#3C6E71] font-black bg-[#3C6E71]/15 px-2.5 py-1 rounded-lg border border-[#3C6E71]/20">تایید آنی</span>
                            </label>
                            @endif

                            <!-- Card to Card -->
                            @if($gateways['card_to_card']->is_active ?? true)
                            <div :class="paymentMethod === 'card_to_card' ? 'border-[#353535] bg-white ring-1 ring-[#353535]' : 'border-[#353535]/15 bg-white hover:border-[#3C6E71]/50'"
                                 class="p-4 rounded-2xl border-2 space-y-3 transition-colors">
                                <label class="flex items-center justify-between cursor-pointer">
                                    <div class="flex items-center gap-3">
                                        <input type="radio" name="payment_method" value="card_to_card" x-model="paymentMethod" class="text-[#353535] focus:ring-[#3C6E71]">
                                        <div>
                                            <span class="text-xs font-black text-[#353535] block">کارت به کارت بانکی</span>
                                            <span class="text-[11px] text-[#353535]/65">واریز مستقیم به شماره کارت کافه و آپلود تصویر فیش واریزی</span>
                                        </div>
                                    </div>
                                    <span class="text-[11px] text-[#284B63] font-black bg-[#284B63]/15 px-2.5 py-1 rounded-lg border border-[#284B63]/30">تایید باریستا</span>
                                </label>

                                <!-- Card to Card Details & Upload Form -->
                                <div x-show="paymentMethod === 'card_to_card'" x-transition class="pt-3 border-t border-[#353535]/10 space-y-3">
                                    <div class="p-3.5 rounded-xl bg-[#F7F9FA]/80 border border-[#3C6E71]/40 text-xs space-y-1.5">
                                        <div class="flex items-center justify-between">
                                            <span class="font-black text-[#353535]">شماره کارت بی کافه:</span>
                                            <button type="button" @click="navigator.clipboard.writeText('{{ $gateways['card_to_card']->settings['card_number'] ?? '۶۲۱۹-xxxx-xxxx-xxxx' }}'); copiedCard = true; setTimeout(() => copiedCard = false, 2500)" class="text-[11px] font-bold flex items-center gap-1 min-h-[36px] transition-colors" :class="copiedCard ? 'text-[#3C6E71]' : 'text-[#284B63] hover:underline'">
                                                <span x-text="copiedCard ? 'شماره کارت کپی شد ✓' : 'کپی شماره کارت'"></span>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                            </button>
                                        </div>
                                        <div class="font-mono text-sm font-black text-[#353535] dir-ltr text-center tracking-widest bg-white py-1.5 rounded-lg border border-[#3C6E71]/30">
                                            {{ $gateways['card_to_card']->settings['card_number'] ?? '۶۲۱۹-xxxx-xxxx-xxxx' }}
                                        </div>
                                        <div class="text-[11px] text-[#353535]/65 flex items-center justify-between">
                                            <span>به نام: {{ $gateways['card_to_card']->settings['account_owner'] ?? 'کافه بی' }}</span>
                                            <span>بانک: {{ $gateways['card_to_card']->settings['bank_name'] ?? 'بانک سامان' }}</span>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-bold text-[#353535] mb-1">شماره پیگیری واریز</label>
                                            <input type="text" name="tracking_number" placeholder="مثلاً: ۱۲۳۴۵۶" dir="ltr"
                                                   class="w-full px-3 py-2.5 rounded-xl bg-white border border-[#353535]/15 text-[#353535] text-xs font-mono text-start tabular-nums focus:outline-none focus:border-[#3C6E71] focus:ring-1 focus:ring-[#3C6E71] transition-colors min-h-[44px]">
                                        </div>
                                        <div x-data="{ fileName: '', preview: '', dragging: false }">
                                            <label class="block text-xs font-bold text-[#353535] mb-1">عکس فیش یا رسید واریز</label>
                                            <div class="relative"
                                                 @dragover.prevent="dragging = true"
                                                 @dragleave.prevent="dragging = false"
                                                 @drop.prevent="dragging = false; const f = $event.dataTransfer.files[0]; if(f) { $refs.receiptInput.files = $event.dataTransfer.files; fileName = f.name; if(f.type.startsWith('image/')) { const r = new FileReader(); r.onload = e => preview = e.target.result; r.readAsDataURL(f); } }">
                                                <input type="file" name="receipt_image" accept="image/*" x-ref="receiptInput"
                                                       @change="const f = $event.target.files[0]; if(f) { fileName = f.name; if(f.type.startsWith('image/')) { const r = new FileReader(); r.onload = e => preview = e.target.result; r.readAsDataURL(f); } } else { fileName = ''; preview = ''; }"
                                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                                                <!-- Empty state -->
                                                <div x-show="!fileName"
                                                     :class="dragging ? 'border-[#3C6E71] bg-[#3C6E71]/5' : 'border-[#353535]/15 bg-white hover:border-[#3C6E71]/50'"
                                                     class="w-full p-4 rounded-xl border-2 border-dashed text-center transition-colors min-h-[80px] flex flex-col items-center justify-center gap-1.5">
                                                    <svg class="w-6 h-6 text-[#284B63]/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    <span class="text-[11px] font-bold text-[#353535]/55">عکس فیش رو اینجا بنداز یا کلیک کن</span>
                                                </div>

                                                <!-- File selected state -->
                                                <div x-show="fileName" x-cloak class="w-full p-3 rounded-xl border border-[#3C6E71]/40 bg-[#3C6E71]/5 min-h-[80px]">
                                                    <div class="flex items-center gap-3">
                                                        <template x-if="preview">
                                                            <img :src="preview" class="w-14 h-14 object-cover rounded-lg border border-[#D9D9D9] flex-shrink-0">
                                                        </template>
                                                        <div class="flex-1 min-w-0">
                                                            <div class="text-xs font-bold text-[#353535] truncate" x-text="fileName"></div>
                                                            <div class="text-[10px] text-emerald-600 font-bold mt-0.5 flex items-center gap-1">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                                <span>آماده ارسال</span>
                                                            </div>
                                                        </div>
                                                        <button type="button" @click.stop="$refs.receiptInput.value = ''; fileName = ''; preview = '';"
                                                                class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-rose-50 text-[#353535]/40 hover:text-rose-600 transition-colors flex-shrink-0 relative z-20">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Cash on delivery / In-store POS -->
                            @if($gateways['cash']->is_active ?? true)
                            <div :class="paymentMethod === 'cash' ? 'border-[#353535] bg-white ring-1 ring-[#353535]' : 'border-[#353535]/15 bg-white hover:border-[#3C6E71]/50'"
                                 class="p-4 rounded-2xl border-2 transition-colors">
                                <label class="flex items-center justify-between cursor-pointer">
                                    <div class="flex items-center gap-3">
                                        <input type="radio" name="payment_method" value="cash" x-model="paymentMethod" class="text-[#353535] focus:ring-[#3C6E71]">
                                        <div>
                                            <span class="text-xs font-black text-[#353535] block">پرداخت در محل / کارتخوان سیار و حضوری</span>
                                            <span class="text-[11px] text-[#353535]/65" x-text="orderType === 'dine_in' ? 'پرداخت نقدی یا با دستگاه کارتخوان روی کانتر کافه' : 'پرداخت با کارتخوان سیار پیک هنگام تحویل سفارش در شیراز'"></span>
                                        </div>
                                    </div>
                                    <span class="text-[11px] text-[#3C6E71] font-black bg-[#3C6E71]/15 px-2.5 py-1 rounded-lg border border-[#3C6E71]/30">تسویه در محل</span>
                                </label>
                            </div>
                            @endif

                            @if(!($gateways['zarinpal']->is_active ?? true) && !($gateways['card_to_card']->is_active ?? true) && !($gateways['cash']->is_active ?? true))
                            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs text-center font-bold">
                                در حال حاضر کلیه روش‌های پرداخت عمومی توسط کافه غیرفعال شده‌اند.
                            </div>
                            @endif

                            <!-- Subscription Credit (Strictly in-person / takeaway) -->
                            @if($activeSubscription ?? false)
                            <div x-show="orderType !== 'courier'" x-transition>
                                <label :class="paymentMethod === 'subscription_credit' ? 'border-[#353535] bg-[#3C6E71]/10 ring-1 ring-[#353535]' : 'border-[#353535]/15 bg-white hover:border-[#3C6E71]/50'"
                                       class="p-4 rounded-2xl border-2 cursor-pointer btn-tactile flex items-center justify-between transition-colors min-h-[64px]">
                                    <div class="flex items-center gap-3">
                                        <input type="radio" name="payment_method" value="subscription_credit" x-model="paymentMethod" class="text-[#353535] focus:ring-[#3C6E71]">
                                        <div>
                                            <span class="text-xs font-black text-[#353535] block">استفاده از سهمیه اشتراک قهوه روزانه</span>
                                            <span class="text-[11px] text-[#353535]/65">اعتبار باقی‌مانده: {{ $activeSubscription->remaining_items }} آیتم (کسر هزینه ۱ فنجان از فاکتور)</span>
                                        </div>
                                    </div>
                                    <span class="text-[11px] text-[#3C6E71] font-bold bg-[#3C6E71]/15 px-2.5 py-1 rounded-lg border border-[#3C6E71]/30">سهمیه روزانه</span>
                                </label>
                            </div>
                            <div x-show="orderType === 'courier'" class="p-3 rounded-xl bg-slate-100 text-slate-500 text-[11px] flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>سهمیه اشتراک قهوه روزانه فقط مختص مراجعه حضوری و بیرون‌بر است و برای ارسال با پیک اعمال نمی‌شود.</span>
                            </div>
                            @endif
                        </div>
                    </div>

                </div>

                <!-- End Column (lg: 5 cols) - Sticky Invoice Summary -->
                <div class="lg:col-span-5 sticky top-6 space-y-4">

                    <div class="bg-white border border-[#353535]/10 p-5 sm:p-6 rounded-3xl space-y-4 shadow-xs">
                        <div class="flex items-center justify-between border-b border-[#353535]/10 pb-3">
                            <h2 class="text-sm font-black text-[#353535] flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#353535]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                <span>خلاصه فاکتور سفارش</span>
                            </h2>
                            <span class="text-[11px] text-[#353535]/65 tabular-nums" x-text="cart.length + ' قلم کالا'"></span>
                        </div>

                        <!-- Items Breakdown -->
                        <div class="space-y-2.5 max-h-56 overflow-y-auto custom-scrollbar">
                            <template x-for="(item, index) in cart" :key="index">
                                <div class="py-2 border-b border-dashed border-[#353535]/10 space-y-1">
                                    <div class="flex items-center justify-between text-xs">
                                        <div class="flex items-center gap-2">
                                            <span class="w-6 h-6 rounded-lg bg-[#F7F9FA] text-[#353535] font-black text-xs flex items-center justify-center tabular-nums border border-[#353535]/10" x-text="item.quantity"></span>
                                            <span class="font-bold text-[#353535]" x-text="item.name"></span>
                                        </div>
                                        <span class="font-black text-[#284B63] tabular-nums" x-text="(item.price * item.quantity).toLocaleString('fa-IR') + ' تومان'"></span>
                                    </div>
                                    <template x-if="item.addons && item.addons.length > 0">
                                        <div class="text-[10px] text-[#3C6E71] font-bold ps-8">
                                            افزودنی‌ها: <span x-text="item.addons.map(a => a.name).join('، ')"></span>
                                        </div>
                                    </template>
                                    <template x-if="item.note">
                                        <div class="text-[10px] text-[#284B63] font-semibold flex items-center gap-1.5 ps-8">
                                            <svg class="w-3 h-3 text-[#284B63]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>یادداشت:</span>
                                            <span x-text="item.note"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <template x-if="cart.length === 0">
                                <div class="py-6 text-center text-xs text-[#353535]/50">
                                    سبد خرید شما خالی است!
                                </div>
                            </template>
                        </div>

                        <!-- Coupon Code Section -->
                        <div class="p-3.5 rounded-2xl bg-[#F7F9FA]/40 border border-[#353535]/10 space-y-2">
                            <label class="block text-xs font-bold text-[#353535] flex items-center justify-between">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-[#284B63]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    <span>کد تخفیف:</span>
                                </span>
                                <template x-if="appliedCoupon">
                                    <button type="button" @click="removeCoupon()" class="text-[10px] text-[#B44A4A] hover:underline font-bold">حذف کد</button>
                                </template>
                            </label>

                            <div class="flex items-center gap-2">
                                <input type="text"
                                       id="coupon_code"
                                       name="coupon_code"
                                       x-model="couponInput"
                                       :disabled="appliedCoupon !== null"
                                       placeholder="مثلاً: BDAY-2026"
                                       class="flex-1 px-3 py-2 rounded-xl bg-white border border-[#353535]/15 text-[#353535] text-xs font-mono uppercase focus:outline-none focus:border-[#3C6E71] disabled:bg-gray-100 min-h-[44px]">
                                <button type="button"
                                        @click="applyCoupon()"
                                        :disabled="!couponInput || appliedCoupon !== null || couponLoading"
                                        class="px-4 py-2 bg-[#353535] hover:bg-[#1E2912] disabled:opacity-50 text-[#F7F9FA] text-xs font-bold rounded-xl btn-tactile transition-all">
                                    <span x-show="!couponLoading">اعمال</span>
                                    <span x-show="couponLoading">...</span>
                                </button>
                            </div>

                            <template x-if="couponMessage">
                                <p class="text-[10px] font-bold" :class="appliedCoupon ? 'text-[#4F8A4B]' : 'text-[#B44A4A]'" x-text="couponMessage"></p>
                            </template>
                        </div>

                        <!-- Customer Cashback Wallet Redemption (If balance > 0) -->
                        @if(($customer->wallet_balance ?? 0) > 0)
                        <div class="p-3.5 rounded-2xl bg-[#3C6E71]/10 border border-[#3C6E71]/25 space-y-2">
                            <label class="flex items-center justify-between cursor-pointer select-none">
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" x-model="useWallet" class="w-4 h-4 rounded text-[#3C6E71] focus:ring-0">
                                    <span class="text-xs font-black text-[#353535]">استفاده از موجودی کیف‌پول (کش‌بک)</span>
                                </div>
                                <span class="text-xs font-black text-[#284B63] tabular-nums">{{ number_format($customer->wallet_balance) }} تومان</span>
                            </label>
                            <p class="text-[10px] text-[#353535]/65 ps-6">موجودی نقدی حاصل از کش‌بک‌های سفارشات قبلی شما کسر خواهد شد.</p>
                        </div>
                        @endif

                        <!-- Price Calculations -->
                        <div class="space-y-2.5 pt-2 text-xs border-t border-[#353535]/10">
                            <div class="flex items-center justify-between text-[#353535]/70">
                                <span>جمع اقلام سفارش:</span>
                                <span class="tabular-nums font-bold text-[#353535]" x-text="subtotal.toLocaleString('fa-IR') + ' تومان'"></span>
                            </div>

                            <div x-show="orderType === 'courier'" class="flex items-center justify-between text-[#353535]/70">
                                <span>هزینه پیک شیراز:</span>
                                <span class="tabular-nums font-bold text-[#353535]" x-text="deliveryFee.toLocaleString('fa-IR') + ' تومان'"></span>
                            </div>

                            <div x-show="orderType === 'dine_in'" class="flex items-center justify-between text-[#353535]/70">
                                <span>هزینه سرو در کافه:</span>
                                <span class="font-bold text-[#3C6E71]">رایگان</span>
                            </div>

                            <div x-show="couponDiscount > 0" class="flex items-center justify-between text-[#4F8A4B] font-bold">
                                <span>تخفیف کوپن:</span>
                                <span class="tabular-nums" x-text="'- ' + couponDiscount.toLocaleString('fa-IR') + ' تومان'"></span>
                            </div>

                            <div x-show="walletDeduction > 0" class="flex items-center justify-between text-[#284B63] font-bold">
                                <span>کسر از موجودی کیف‌پول:</span>
                                <span class="tabular-nums" x-text="'- ' + walletDeduction.toLocaleString('fa-IR') + ' تومان'"></span>
                            </div>

                            <div class="flex items-center justify-between pt-3 border-t-2 border-[#353535] text-sm font-black text-[#353535]">
                                <span>مبلغ قابل پرداخت:</span>
                                <span class="text-[#284B63] text-lg tabular-nums" x-text="finalPayable.toLocaleString('fa-IR') + ' تومان'"></span>
                            </div>
                        </div>

                        <!-- Dynamic Hidden Inputs for Cart Items -->
                        <div id="hidden-cart-items"></div>

                        <!-- Submit Final Order Button -->
                        <button type="submit" :disabled="cart.length === 0 || isSubmitting"
                                class="w-full py-4 bg-[#284B63] hover:bg-[#9B561B] disabled:opacity-50 text-[#F7F9FA] font-black rounded-2xl text-sm btn-tactile shadow-xs flex items-center justify-center gap-2 mt-4 transition-all min-h-[48px]">
                            <template x-if="!isSubmitting">
                                <span class="flex items-center gap-2">
                                    <span>تایید و پرداخت نهایی سفارش</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                </span>
                            </template>
                            <template x-if="isSubmitting">
                                <span class="flex items-center gap-2">
                                    <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                    <span>در حال پردازش و ثبت سفارش...</span>
                                </span>
                            </template>
                        </button>
                    </div>

                    <!-- Trust & Guarantee Badges -->
                    <div class="p-4 rounded-2xl bg-white border border-[#353535]/10 space-y-2 text-[11px] text-[#353535]/70 shadow-xs">
                        <div class="flex items-center gap-2 text-[#353535] font-bold">
                            <svg class="w-4 h-4 flex-shrink-0 text-[#3C6E71]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>خیالت از سفارشت راحت باشه</span>
                        </div>
                        <p class="leading-relaxed">به محض ثبت، سفارشت روی مانیتور باریستا میاد و می‌تونی مرحله به مرحله سفارشت رو آنلاین پیگیری کنی.</p>
                    </div>

                </div>

            </div>

            <!-- Mobile Sticky Bottom Bar (Only visible on screens < 1024px) -->
            <div class="lg:hidden fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur-md border-t border-[#353535]/15 px-4 pt-3 pb-[max(0.75rem,env(safe-area-inset-bottom))] z-40 shadow-2xl">
                <div class="flex items-center justify-between gap-3 max-w-lg mx-auto">
                    <div class="space-y-0.5">
                        <span class="text-[11px] text-[#353535]/65 font-bold block">مبلغ نهایی پرداخت:</span>
                        <div class="text-base font-black text-[#284B63] tabular-nums" x-text="finalPayable.toLocaleString('fa-IR') + ' تومان'"></div>
                    </div>
                    <button type="submit" :disabled="cart.length === 0 || isSubmitting"
                            class="flex-1 max-w-[220px] py-3.5 bg-[#284B63] hover:bg-[#9B561B] disabled:opacity-50 text-[#F7F9FA] font-black rounded-xl text-xs btn-tactile shadow-md flex items-center justify-center gap-2 min-h-[48px]">
                        <template x-if="!isSubmitting">
                            <span class="flex items-center gap-1.5">
                                <span>تایید و پرداخت</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                            </span>
                        </template>
                        <template x-if="isSubmitting">
                            <span class="flex items-center gap-1.5">
                                <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                <span>در حال ثبت...</span>
                            </span>
                        </template>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Leaflet & Checkout Controller Script -->
    <script>
    function checkoutPage() {
        return {
            cart: JSON.parse(localStorage.getItem('bcafe_cart') || '[]'),
            customerName: '{{ $customer->name ?? '' }}',
            customerPhone: '{{ $customer->phone ?? '' }}',
            orderType: 'dine_in',
            paymentMethod: '{{ ($gateways['zarinpal']->is_active ?? false) ? 'zarinpal' : (($gateways['card_to_card']->is_active ?? false) ? 'card_to_card' : (($gateways['cash']->is_active ?? false) ? 'cash' : 'zarinpal')) }}',
            selectedZoneId: '{{ $deliveryZones->first()->id ?? 1 }}',
            deliveryFee: 0,
            addressId: null,
            addressLine: '',
            latitude: 29.6293,
            longitude: 52.5186,
            map: null,
            copiedCard: false,
            isSubmitting: false,

            init() {
                this.updateDeliveryFee();
                this.$nextTick(() => {
                    this.initNeshanMap();
                });
                this.$watch('orderType', (val) => {
                    this.updateDeliveryFee();
                    if (val === 'courier' && this.map) {
                        setTimeout(() => { this.map.invalidateSize(); }, 200);
                    }
                });
            },

            initNeshanMap() {
                const mapEl = document.getElementById('neshan-map');
                if (!mapEl) return;

                const isMobile = window.innerWidth < 768 || ('ontouchstart' in window);

                // Center on Shiraz (قصردشت / ارم)
                this.map = L.map('neshan-map', {
                    center: [this.latitude, this.longitude],
                    zoom: 14,
                    zoomControl: true,
                    scrollWheelZoom: false,
                    dragging: !isMobile,
                    tap: false
                });

                if (isMobile) {
                    mapEl.addEventListener('click', () => {
                        if (this.map && !this.map.dragging.enabled()) {
                            this.map.dragging.enable();
                        }
                    });
                    mapEl.addEventListener('touchstart', () => {
                        if (this.map && !this.map.dragging.enabled()) {
                            this.map.dragging.enable();
                        }
                    }, { passive: true });
                }

                // OpenStreetMap / Neshan Tiles
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© بی کافه شیراز'
                }).addTo(this.map);

                // On Map Move End -> Update center coordinates
                this.map.on('moveend', () => {
                    const center = this.map.getCenter();
                    const newLat = parseFloat(center.lat.toFixed(7));
                    const newLng = parseFloat(center.lng.toFixed(7));
                    if (Math.abs(this.latitude - newLat) > 0.0003 || Math.abs(this.longitude - newLng) > 0.0003) {
                        this.addressId = null;
                    }
                    this.latitude = newLat;
                    this.longitude = newLng;
                });
            },

            updateDeliveryFee() {
                if (this.orderType === 'dine_in') {
                    this.deliveryFee = 0;
                    return;
                }
                const selectEl = document.querySelector('select[name="delivery_zone_id"]');
                if (selectEl) {
                    const selectedOpt = selectEl.options[selectEl.selectedIndex];
                    this.deliveryFee = selectedOpt ? parseInt(selectedOpt.dataset.fee || '30000') : 30000;
                } else {
                    this.deliveryFee = 30000;
                }
            },

            couponInput: '',
            appliedCoupon: null,
            couponLoading: false,
            couponMessage: '',
            useWallet: false,
            customerWalletBalance: {{ (float)($customer->wallet_balance ?? 0) }},

            get subtotal() {
                return this.cart.reduce((sum, i) => sum + (i.price * i.quantity), 0);
            },

            applyCoupon() {
                if (!this.couponInput) return;
                this.couponLoading = true;
                this.couponMessage = '';
                fetch('{{ route('cart.validate-coupon') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ code: this.couponInput })
                })
                .then(r => r.json().then(data => ({ status: r.status, body: data })))
                .then(({ status, body }) => {
                    this.couponLoading = false;
                    if (status === 200 && body.valid) {
                        this.appliedCoupon = body;
                        this.couponMessage = body.message;
                    } else {
                        this.appliedCoupon = null;
                        this.couponMessage = body.message || 'کد تخفیف معتبر نیست.';
                    }
                })
                .catch(() => {
                    this.couponLoading = false;
                    this.couponMessage = 'خطا در بررسی کد تخفیف.';
                });
            },

            removeCoupon() {
                this.appliedCoupon = null;
                this.couponInput = '';
                this.couponMessage = '';
            },

            get couponDiscount() {
                if (!this.appliedCoupon) return 0;
                if (this.appliedCoupon.discount_type === 'percent') {
                    return Math.round((this.subtotal * this.appliedCoupon.discount_value) / 100);
                }
                return Math.min(this.subtotal, this.appliedCoupon.discount_value);
            },

            get payableBeforeWallet() {
                return Math.max(0, (this.subtotal - this.couponDiscount) + this.deliveryFee);
            },

            get walletDeduction() {
                if (!this.useWallet || this.customerWalletBalance <= 0) return 0;
                return Math.min(this.customerWalletBalance, this.payableBeforeWallet);
            },

            get finalPayable() {
                return Math.max(0, this.payableBeforeWallet - this.walletDeduction);
            },

            applySavedAddress(addr) {
                this.addressId = addr.id || null;
                this.addressLine = addr.address_line;
                if (addr.zone_id) {
                    this.selectedZoneId = addr.zone_id;
                    this.updateDeliveryFee();
                }
                if (addr.lat && addr.lng) {
                    this.latitude = addr.lat;
                    this.longitude = addr.lng;
                    if (this.map) {
                        this.map.setView([addr.lat, addr.lng], 15);
                    }
                }
                const unitInput = document.querySelector('input[name="building_unit"]');
                if (unitInput && addr.unit) {
                    unitInput.value = addr.unit;
                }
            },

            handleSubmit(e) {
                if (this.isSubmitting) {
                    e.preventDefault();
                    return;
                }
                if (this.cart.length === 0) {
                    e.preventDefault();
                    window.CafeToast.error('سبد خرید شما خالی است! لطفاً ابتدا آیتمی از منو انتخاب فرمایید.', 'سبد خرید خالی');
                    return;
                }
                if (!this.customerName || !this.customerName.trim()) {
                    e.preventDefault();
                    window.CafeToast.error('لطفاً نام و نام خانوادگی خود را وارد فرمایید.', 'اطلاعات ناقص');
                    document.querySelector('input[name="customer_name"]')?.focus();
                    return;
                }
                const phoneClean = (this.customerPhone || '').trim();
                if (!/^09[0-9]{9}$/.test(phoneClean)) {
                    e.preventDefault();
                    window.CafeToast.error('لطفاً شماره تماس معتبر ۱۱ رقمی (مثلاً ۰۹۱۷۱۲۳۴۵۶۷) وارد فرمایید.', 'شماره همراه نادرست');
                    document.querySelector('input[name="customer_phone"]')?.focus();
                    return;
                }
                if (this.orderType === 'courier' && (!this.addressLine || !this.addressLine.trim())) {
                    e.preventDefault();
                    window.CafeToast.error('لطفاً آدرس دقیق تحویل سفارش در شیراز را وارد فرمایید.', 'آدرس نامشخص');
                    document.querySelector('input[name="address_line"]')?.focus();
                    return;
                }

                // Inject dynamic inputs
                this.populateForm(e);

                // Safe to show spinner now as native submit will proceed
                this.isSubmitting = true;
            },

            populateForm(e) {
                const container = document.getElementById('hidden-cart-items');
                if (!container) return;
                container.innerHTML = '';

                this.cart.forEach((item, idx) => {
                    const isSub = (item.type === 'subscription' || !!item.subscription_plan_id);

                    const typeInput = document.createElement('input');
                    typeInput.type = 'hidden';
                    typeInput.name = `items[${idx}][type]`;
                    typeInput.value = isSub ? 'subscription' : 'product';
                    container.appendChild(typeInput);

                    if (isSub) {
                        const subPlanInput = document.createElement('input');
                        subPlanInput.type = 'hidden';
                        subPlanInput.name = `items[${idx}][subscription_plan_id]`;
                        subPlanInput.value = item.subscription_plan_id || (typeof item.id === 'string' ? item.id.replace('sub-', '') : item.id);
                        container.appendChild(subPlanInput);
                    } else {
                        const pInput = document.createElement('input');
                        pInput.type = 'hidden';
                        pInput.name = `items[${idx}][product_id]`;
                        pInput.value = item.product_id || item.id;
                        container.appendChild(pInput);
                    }

                    const qInput = document.createElement('input');
                    qInput.type = 'hidden';
                    qInput.name = `items[${idx}][quantity]`;
                    qInput.value = item.quantity || 1;
                    container.appendChild(qInput);

                    // Addons array
                    if (item.addons && Array.isArray(item.addons)) {
                        item.addons.forEach((addon, aIdx) => {
                            const aInput = document.createElement('input');
                            aInput.type = 'hidden';
                            aInput.name = `items[${idx}][addons][${aIdx}]`;
                            aInput.value = addon.name;
                            container.appendChild(aInput);
                        });
                    }

                    const noteVal = item.note || item.notes;
                    if (noteVal) {
                        const nInput = document.createElement('input');
                        nInput.type = 'hidden'; nInput.name = `items[${idx}][notes]`; nInput.value = noteVal;
                        container.appendChild(nInput);
                    }
                });

                if (this.appliedCoupon) {
                    const cInput = document.createElement('input');
                    cInput.type = 'hidden'; cInput.name = 'coupon_code'; cInput.value = this.appliedCoupon.code;
                    container.appendChild(cInput);
                }
                if (this.useWallet && this.walletDeduction > 0) {
                    const wInput = document.createElement('input');
                    wInput.type = 'hidden'; wInput.name = 'use_wallet'; wInput.value = '1';
                    container.appendChild(wInput);
                }
            }
        }
    }
    </script>

    <x-toast-container />
</body>
</html>
