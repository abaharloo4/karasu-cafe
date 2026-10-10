<!DOCTYPE html>
<html lang="fa" dir="rtl" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>وضعیت سفارش {{ $order->order_number }} - بی کافه</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}?v=2.0.2">
    <link rel="alternate icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2.0.2">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}?v=2.0.2">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data="orderStatusTracker('{{ $order->order_number }}', '{{ $order->status }}')"
      class="h-full antialiased selection:bg-[#3C6E71] selection:text-[#F7F9FA] bg-[#F7F9FA]/35 text-[#353535] py-8 sm:py-12 px-4 sm:px-6">

    <div class="max-w-2xl mx-auto space-y-6">

        <!-- Top Header Navigation -->
        <header class="flex items-center justify-between border-b border-[#353535]/10 pb-4">
            <a href="{{ route('storefront.menu') }}" class="text-xs font-bold text-[#353535] hover:text-[#284B63] btn-tactile flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span>بازگشت به منوی بی کافه</span>
            </a>

            <div class="flex items-center gap-2.5">
                <img src="{{ asset('images/logo.jpeg') }}" alt="بی کافه" class="w-8 h-8 rounded-xl object-cover border border-[#353535]/15 shadow-2xs">
                <span class="text-xs font-black text-[#353535]">پیگیری بلادرنگ سفارش</span>
            </div>
        </header>

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-[#3C6E71]/15 border border-[#3C6E71]/30 text-[#353535] text-xs font-bold flex items-center gap-2.5 shadow-xs">
                <svg class="w-5 h-5 flex-shrink-0 text-[#3C6E71]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-2xl bg-[#B44A4A]/10 border border-[#B44A4A]/30 text-[#B44A4A] text-xs font-bold flex items-center gap-2.5 shadow-xs">
                <svg class="w-5 h-5 flex-shrink-0 fill-none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Tailored Thank-You Hero Banner by Payment Method -->
        @if($order->payment_method === 'cash')
            <!-- Cash on Delivery / POS at Cafe -->
            <div class="p-6 rounded-3xl bg-gradient-to-br from-[#F7F9FA] to-[#E9EDC9]/60 border-2 border-[#3C6E71]/40 shadow-sm space-y-3.5">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-[#3C6E71] text-[#F7F9FA] flex items-center justify-center flex-shrink-0 shadow-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div>
                        <span class="text-[11px] font-black text-[#3C6E71] uppercase tracking-wider block">سفارش با موفقیت ثبت شد • تسویه در محل</span>
                        <h2 class="text-base sm:text-lg font-black text-[#353535]">با تشکر از سفارش شما - پرداخت هنگام تحویل</h2>
                    </div>
                </div>
                <p class="text-xs sm:text-sm text-[#353535]/85 leading-relaxed font-medium">
                    سفارش شما در سامانه بی کافه شیراز ثبت شد و به صف آماده‌سازی بار قهوه پیوست. مبلغ
                    <strong class="text-[#284B63] font-black tabular-nums">{{ number_format($order->final_payable) }} تومان</strong>
                    هنگام {{ $order->isDineIn() ? 'سرو در سالن کافه روی میز' : 'تحویل سفارش توسط سفیر پیک در شیراز' }} با کارتخوان سیار یا نقدی دریافت خواهد شد.
                </p>
                <div class="flex items-center gap-2 pt-1 text-[11px] text-[#3C6E71] font-bold">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>نیاز به هیچ پرداخت اضافه‌ای نیست؛ آماده‌سازی طبق زمان تخمینی آغاز شد.</span>
                </div>
            </div>

        @elseif($order->payment_method === 'zarinpal')
            <!-- Zarinpal Online Gateway -->
            <div class="p-6 rounded-3xl bg-gradient-to-br from-[#4F8A4B]/15 to-[#3C6E71]/10 border-2 border-[#4F8A4B]/35 shadow-sm space-y-3.5">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-[#4F8A4B] text-white flex items-center justify-center flex-shrink-0 shadow-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <span class="text-[11px] font-black text-[#4F8A4B] uppercase tracking-wider block">پرداخت امن شاپرک تایید شد</span>
                        <h2 class="text-base sm:text-lg font-black text-[#353535]">با تشکر از پرداخت آنلاین شما</h2>
                    </div>
                </div>
                <p class="text-xs sm:text-sm text-[#353535]/85 leading-relaxed font-medium">
                    تراکنش بانکی شما به مبلغ
                    <strong class="text-[#4F8A4B] font-black tabular-nums">{{ number_format($order->final_payable) }} تومان</strong>
                    با موفقیت تایید و تسویه شد. سفارش شما به عنوان فاکتور پرداخت‌شده با اولویت بار در حال آماده‌سازی است.
                </p>
                <div class="flex items-center gap-2 pt-1 text-[11px] text-[#4F8A4B] font-bold">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>رسید دیجیتال این سفارش در پنل کاربری شما ثبت و ذخیره گردید.</span>
                </div>
            </div>

        @elseif($order->payment_method === 'card_to_card')
            <!-- Card to Card -->
            <div class="p-6 rounded-3xl bg-gradient-to-br from-[#284B63]/15 to-[#3C6E71]/15 border-2 border-[#284B63]/30 shadow-sm space-y-3.5">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-[#284B63] text-white flex items-center justify-center flex-shrink-0 shadow-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <div>
                        <span class="text-[11px] font-black text-[#284B63] uppercase tracking-wider block">رسید بانکی دریافت شد</span>
                        <h2 class="text-base sm:text-lg font-black text-[#353535]">با تشکر از ثبت سفارش - فیش بانکی دریافت شد</h2>
                    </div>
                </div>
                <p class="text-xs sm:text-sm text-[#353535]/85 leading-relaxed font-medium">
                    اطلاعات فیش واریزی کارت‌به‌کارت شما به صندوق‌دار بی کافه ارسال شد. پس از تایید آنی توسط صندوق، وضعیت سفارش روی این صفحه خودکار به‌روزرسانی خواهد شد.
                </p>
                <div class="flex items-center gap-2 pt-1 text-[11px] text-[#284B63] font-bold">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>میانگین زمان تایید رسید: کمتر از ۲ الی ۵ دقیقه</span>
                </div>
            </div>

        @elseif($order->payment_method === 'subscription_credit')
            <!-- Subscription Coffee Quota -->
            <div class="p-6 rounded-3xl bg-[#353535] text-[#F7F9FA] border-2 border-[#3C6E71]/60 shadow-md space-y-3.5">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-[#3C6E71] text-[#353535] flex items-center justify-center flex-shrink-0 shadow-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    </div>
                    <div>
                        <span class="text-[11px] font-black text-[#3C6E71] uppercase tracking-wider block">مشترک ویژه بی کافه (VIP Coffee Club)</span>
                        <h2 class="text-base sm:text-lg font-black text-[#F7F9FA]">با تشکر از عضویت شما - مصرف سهمیه اشتراک روزانه</h2>
                    </div>
                </div>
                <p class="text-xs sm:text-sm text-[#F7F9FA]/85 leading-relaxed font-medium">
                    ۱ فنجان قهوه تخصصی از سهمیه فعال روزانه پلن اشتراک شما کسر گردید. سفارش شما با اولویت بار ویژه مشترکین VIP در حال دم‌آوری و آماده‌سازی است.
                </p>
                <div class="flex items-center gap-2 pt-1 text-[11px] text-[#3C6E71] font-bold">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>سهمیه روزانه شما برای امروز مصرف شد. سهمیه بعدی فردا صبح فعال می‌گردد.</span>
                </div>
            </div>

        @else
            <!-- Wallet Cashback / 100% Coupon -->
            <div class="p-6 rounded-3xl bg-gradient-to-br from-[#3C6E71]/20 to-[#F7F9FA] border-2 border-[#3C6E71]/30 shadow-sm space-y-3.5">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-[#3C6E71] text-white flex items-center justify-center flex-shrink-0 shadow-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <span class="text-[11px] font-black text-[#3C6E71] uppercase tracking-wider block">تسویه کامل از اعتبار / بن تخفیف</span>
                        <h2 class="text-base sm:text-lg font-black text-[#353535]">با تشکر از وفاداری شما - پرداخت با اعتبار باشگاه مشتریان</h2>
                    </div>
                </div>
                <p class="text-xs sm:text-sm text-[#353535]/85 leading-relaxed font-medium">
                    سفارش شما با استفاده از موجودی کیف‌پول و کش‌بک‌های ثبت‌شده بدون نیاز به پرداخت دیگر تسویه شد و آماده‌سازی آغاز گردید.
                </p>
            </div>
        @endif

        @if($order->is_queued_for_next_shift)
            <!-- Outside Operating Hours / Queued for Next Shift Notification -->
            <div class="p-5 rounded-3xl bg-amber-500/10 border-2 border-amber-500/30 text-amber-950 space-y-2 shadow-xs">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <span class="text-[11px] font-black text-amber-800 uppercase tracking-wider block">ثبت سفارش خارج از شیفت کاری کافه</span>
                        <h2 class="text-sm sm:text-base font-black text-amber-950">سفارش شما در نوبت شیفت کاری بعدی قرار گرفت</h2>
                    </div>
                </div>
                <p class="text-xs text-amber-900/85 leading-relaxed font-medium">
                    ساعت سفارش‌گیری بی کافه از ساعت {{ \App\Models\Cafe::first()?->order_start_time ?? '۰۸:۰۰' }} الی {{ \App\Models\Cafe::first()?->order_end_time ?? '۲۲:۰۰' }} می‌باشد. سفارش شما با موفقیت ثبت و رزرو شده است و بلافاصله با شروع شیفت کاری بعدی در اولویت دم‌آوری و ارسال قرار خواهد گرفت.
                </p>
            </div>
        @endif

        <!-- Order Card Header -->
        <div class="card-elevated bg-white border border-[#353535]/10 p-6 rounded-3xl space-y-6 shadow-xs">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#353535]/10 pb-4">
                <div>
                    <span class="text-xs text-[#353535]/60">کد پیگیری سفارش:</span>
                    <span class="text-lg font-black text-[#353535] font-mono mr-1.5">{{ $order->order_number }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold px-3 py-1 rounded-xl {{ $order->isDelivery() ? 'bg-[#3C6E71]/15 text-[#353535] border border-[#3C6E71]/20' : 'bg-[#F7F9FA] text-[#353535] border border-[#353535]/10' }}">
                        {{ $order->getFulfillmentLabel() }}
                    </span>
                    <span class="text-xs text-[#353535]/60 tabular-nums">{{ $order->created_at->format('H:i') }}</span>
                </div>
            </div>

            <!-- Cancelled View -->
            <div x-show="status === 'cancelled'" class="p-4 rounded-2xl bg-[#B44A4A]/10 border border-[#B44A4A]/30 text-[#B44A4A] text-center space-y-1">
                <div class="font-black text-sm">این سفارش لغو شده است.</div>
                <div class="text-xs">در صورت هرگونه سوال با پشتیبانی بی کافه تماس حاصل فرمایید.</div>
            </div>

            <!-- Active / Completed Stepper Progress Tracker -->
            <div x-show="status !== 'cancelled'" class="space-y-4">
                <div class="grid grid-cols-4 text-center text-xs font-black">
                    <span :class="step >= 1 ? 'text-[#353535]' : 'text-[#353535]/40'">۱. ثبت سفارش</span>
                    <span :class="step >= 2 ? 'text-[#3C6E71]' : 'text-[#353535]/40'">۲. آماده‌سازی</span>
                    <span :class="step >= 3 ? 'text-[#284B63]' : 'text-[#353535]/40'">۳. آماده تحویل</span>
                    <span :class="step >= 4 ? 'text-[#353535]' : 'text-[#353535]/40'">۴. تحویل شد</span>
                </div>

                <!-- Visual Progress Bar -->
                <div class="w-full bg-[#353535]/10 h-2.5 rounded-full overflow-hidden">
                    <div class="bg-[#3C6E71] h-full transition-all duration-700 ease-out rounded-full"
                         :style="'width: ' + Math.min(100, Math.max(25, (step / 4) * 100)) + '%;'"></div>
                </div>

                <!-- Current Status Alert -->
                <div class="p-4 rounded-2xl bg-[#3C6E71]/10 border border-[#3C6E71]/20 flex items-center gap-3">
                    <div class="relative flex items-center justify-center">
                        <div class="w-3 h-3 rounded-full bg-[#3C6E71]"></div>
                        <div x-show="isActive" class="absolute w-5 h-5 rounded-full bg-[#3C6E71]/40 animate-ping"></div>
                    </div>
                    <div class="text-xs font-bold text-[#353535] leading-relaxed">
                        <template x-if="status === 'pending'">
                            <span>سفارش شما با موفقیت ثبت شد و در انتظار تایید باریستا است.</span>
                        </template>
                        <template x-if="status === 'processing'">
                            <span>باریستای بی کافه در حال دم‌آوری و آماده‌سازی سفارش شماست (تخمین: {{ $order->estimated_prep_time }} دقیقه).</span>
                        </template>
                        <template x-if="status === 'ready' || status === 'shipping'">
                            <span>{{ $order->isDelivery() ? 'سفارش تحویل سفیر پیک بی کافه شد و در مسیر ارسال به آدرس شما در شیراز است.' : ($order->isDineIn() ? 'سفارش آماده سرو است! لطفاً جهت دریافت به کانتر بار مراجعه فرمایید.' : 'سفارش بیرون‌بر شما آماده تحویل است! لطفاً جهت تحویل به کانتر کافه مراجعه فرمایید.') }}</span>
                        </template>
                        <template x-if="status === 'completed'">
                            <span>سفارش با موفقیت تحویل داده شد. گوارای وجود و نوش جان!</span>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Order Details -->
            <div class="space-y-3 pt-4 border-t border-[#353535]/10">
                <h3 class="text-xs font-black text-[#353535]">اقلام سفارش شما:</h3>
                <div class="divide-y divide-[#353535]/5">
                    @foreach($order->items as $item)
                        <div class="py-2.5 flex items-center justify-between text-xs">
                            <div class="space-y-0.5">
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-lg bg-[#F7F9FA] text-[#353535] font-black text-xs flex items-center justify-center tabular-nums border border-[#353535]/10">{{ $item->quantity }}</span>
                                    <span class="font-bold text-[#353535]">{{ $item->product->name ?? 'محصول' }}</span>
                                </div>
                                @if($item->notes)
                                    <div class="text-[10px] text-[#284B63] font-semibold pr-8 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-[#284B63] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        <span>جزئیات: {{ $item->notes }}</span>
                                    </div>
                                @endif
                            </div>
                            <span class="font-black text-[#353535] tabular-nums">{{ number_format($item->price * $item->quantity) }} تومان</span>
                        </div>
                    @endforeach
                </div>

                <!-- Financial & Address Summary -->
                <div class="pt-3 border-t border-[#353535]/10 space-y-1.5 text-xs text-[#353535]/70">
                    <div class="flex items-center justify-between">
                        <span>روش پرداخت:</span>
                        <span class="font-bold text-[#353535]">
                            @if($order->payment_method === 'zarinpal')
                                پرداخت آنلاین شاپرک (زرین‌پال)
                            @elseif($order->payment_method === 'card_to_card')
                                کارت به کارت بانکی
                            @elseif($order->payment_method === 'cash')
                                پرداخت نقدی / کارتخوان در محل
                            @elseif($order->payment_method === 'subscription_credit')
                                سهمیه اشتراک قهوه روزانه (VIP)
                            @elseif($order->payment_method === 'wallet')
                                مانده اعتبار کیف‌پول
                            @else
                                بن تخفیف ویژه
                            @endif
                        </span>
                    </div>

                    @if($order->isDelivery())
                        <div class="flex items-center justify-between">
                            <span>هزینه پیک شیراز:</span>
                            <span class="tabular-nums font-bold text-[#353535]">{{ number_format($order->delivery_fee) }} تومان</span>
                        </div>
                        <div class="text-[11px] text-[#353535] pt-1">
                            <strong>آدرس تحویل:</strong> {{ $order->deliveryAddress->address_line ?? 'شیراز' }}
                        </div>
                    @elseif($order->isDineIn() && $order->table_number)
                        <div class="text-[11px] text-[#353535]">
                            <strong>شماره میز در سالن:</strong> {{ $order->table_number }}
                        </div>
                    @else
                        <div class="text-[11px] text-[#353535]">
                            <strong>نحوه دریافت:</strong> بیرون‌بر (تحویل حضوری در کافه)
                        </div>
                    @endif

                    <div class="flex items-center justify-between pt-2.5 border-t border-[#353535]/10 text-sm font-black text-[#353535]">
                        <span>مبلغ نهایی فاکتور:</span>
                        <span class="text-[#284B63] text-base tabular-nums">{{ number_format($order->final_payable) }} تومان</span>
                    </div>
                </div>
            </div>

            <!-- Live Status Polling Indicator -->
            <div class="text-center pt-1">
                <span class="text-[11px] text-[#353535]/50 flex items-center justify-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#3C6E71]" :class="isActive ? 'animate-pulse' : ''"></span>
                    <span x-text="isActive ? 'به‌روزرسانی وضعیت سفارش به صورت زنده فعال است.' : 'سفارش پایان یافته است.'"></span>
                </span>
            </div>
        </div>

    </div>

    <!-- Lightweight JSON Polling (No full page reload) -->
    <script>
    function orderStatusTracker(orderNumber, initialStatus) {
        return {
            status: initialStatus,
            step: getStep(initialStatus),
            isActive: ['pending', 'processing', 'ready', 'shipping'].includes(initialStatus),
            timer: null,

            init() {
                // Clear cart only after confirmed landing on order status
                localStorage.removeItem('bcafe_cart');
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: [] }));

                if (this.isActive) {
                    this.timer = setInterval(() => {
                        this.pollFeed();
                    }, 5000);
                }
            },

            async pollFeed() {
                try {
                    const response = await fetch(`/order-status/${orderNumber}/feed`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (!response.ok) return;
                    const data = await response.json();
                    this.status = data.status;
                    this.step = data.status_step;
                    this.isActive = data.is_active;

                    if (!data.is_active && this.timer) {
                        clearInterval(this.timer);
                    }
                } catch (e) {
                    console.error('Polling feed error:', e);
                }
            }
        }
    }

    function getStep(status) {
        switch(status) {
            case 'pending': return 1;
            case 'processing': return 2;
            case 'ready':
            case 'shipping': return 3;
            case 'completed': return 4;
            default: return 1;
        }
    }
    </script>
</body>
</html>
