@extends('layouts.app')

@section('title', 'تنظیمات سامانه پیامکی ملی پیامک')
@section('header_title', 'تنظیمات پیامک و قالب‌های ارسال پترن (ملی پیامک)')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header info card -->
    <div class="bg-[#FCFCFB] border border-[#D9E0D6] p-6 rounded-3xl space-y-2 shadow-sm">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-extrabold text-[#1F2520] flex items-center gap-2">
                <svg class="w-5 h-5 text-[#3A5232]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                <span>تنظیمات درگاه ملی پیامک (Melli Payamak)</span>
            </h2>
            <span class="px-3 py-1 bg-[#EAF2E7] text-[#3A5232] text-xs font-bold rounded-lg border border-[#3A5232]/20">ویرایش کامل قالب‌های متنی و پترن</span>
        </div>
        <p class="text-xs text-[#6E756F]">اطلاعات اکانت ملی پیامک، متون قالب‌ها و پترن‌های دلخواه را تنظیم کنید تا پیامک‌ها ارسال شوند.</p>
    </div>

    <form method="POST" action="{{ route('settings.sms.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- 1. Melli Payamak Credentials -->
        <div class="bg-[#FCFCFB] border border-[#D9E0D6] p-6 rounded-3xl space-y-4 shadow-sm">
            <h3 class="text-sm font-bold text-[#1F2520] border-b border-[#E7ECE4] pb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-[#C8A35A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                <span>مشخصات حساب کاربری ملی پیامک</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#1F2520] mb-1.5">نام کاربری ملی پیامک *</label>
                    <input type="text" name="username" value="{{ old('username', $setting->username) }}" placeholder="مثلاً: 09121234567" class="w-full px-4 py-3 rounded-xl bg-[#FFFFFF] border border-[#CCD5C8] text-[#1F2520] text-xs dir-ltr text-right focus:outline-none focus:border-[#6A8C60]">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#1F2520] mb-1.5">کلمه عبور (یا API Key)</label>
                    <input type="password" name="password" placeholder="••••••••" class="w-full px-4 py-3 rounded-xl bg-[#FFFFFF] border border-[#CCD5C8] text-[#1F2520] text-xs dir-ltr text-right focus:outline-none focus:border-[#6A8C60]">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[#1F2520] mb-1.5">شماره خط اختصاصی / عمومی</label>
                    <input type="text" name="from_number" value="{{ old('from_number', $setting->from_number) }}" placeholder="مثلاً: 50004000..." class="w-full px-4 py-3 rounded-xl bg-[#FFFFFF] border border-[#CCD5C8] text-[#1F2520] text-xs dir-ltr text-right focus:outline-none focus:border-[#6A8C60]">
                </div>
            </div>
        </div>

        <!-- 2. Daily Reminder & Automation Timing -->
        <div class="bg-[#FCFCFB] border border-[#D9E0D6] p-6 rounded-3xl space-y-4 shadow-sm">
            <h3 class="text-sm font-bold text-[#1F2520] border-b border-[#E7ECE4] pb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-[#3A5232]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>تنظیمات یادآوری روزانه و فعال‌سازی پیامک‌ها</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-semibold text-[#1F2520] mb-1.5">ساعت ارسال پیامک یادآوری روزانه (ساعت X) *</label>
                    <select name="reminder_hour" class="w-full px-4 py-3 rounded-xl bg-[#FFFFFF] border border-[#CCD5C8] text-[#1F2520] text-xs font-bold focus:outline-none focus:border-[#6A8C60]">
                        @for($h = 8; $h <= 22; $h++)
                            <option value="{{ $h }}" {{ (int)old('reminder_hour', $setting->reminder_hour) === $h ? 'selected' : '' }}>
                                ساعت {{ sprintf('%02d:00', $h) }} ({{ $h < 12 ? 'صبح' : ($h < 17 ? 'بعدازظهر' : 'عصر/شب') }})
                            </option>
                        @endfor
                    </select>
                    <p class="text-[11px] text-[#6E756F] mt-1.5">هر کس تا این ساعت سهمیه امروز قهوه خود را دریافت نکرده باشد، پیامک یادآوری دریافت می‌کند.</p>
                </div>

                <div class="space-y-3 pt-2">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_reminder_enabled" value="1" {{ $setting->is_reminder_enabled ? 'checked' : '' }} class="w-5 h-5 rounded bg-[#FCFCFB] border-[#CCD5C8] text-[#3A5232] focus:ring-[#3A5232]">
                        <span class="text-xs font-bold text-[#1F2520]">فعال‌سازی ارسال خودکار یادآوری روزانه (ساعت X)</span>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_welcome_enabled" value="1" {{ $setting->is_welcome_enabled ? 'checked' : '' }} class="w-5 h-5 rounded bg-[#FCFCFB] border-[#CCD5C8] text-[#3A5232] focus:ring-[#3A5232]">
                        <span class="text-xs font-bold text-[#1F2520]">فعال‌سازی پیامک خوش‌آمدگویی و خرید/فعال‌سازی اشتراک</span>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_daily_usage_enabled" value="1" {{ $setting->is_daily_usage_enabled ? 'checked' : '' }} class="w-5 h-5 rounded bg-[#FCFCFB] border-[#CCD5C8] text-[#3A5232] focus:ring-[#3A5232]">
                        <span class="text-xs font-bold text-[#1F2520]">فعال‌سازی پیامک تایید تحویل سهمیه روزانه</span>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_order_sms_enabled" value="1" {{ $setting->is_order_sms_enabled ? 'checked' : '' }} class="w-5 h-5 rounded bg-[#FCFCFB] border-[#CCD5C8] text-[#3A5232] focus:ring-[#3A5232]">
                        <span class="text-xs font-bold text-[#1F2520]">فعال‌سازی ارسال پیامک‌های چرخه حیات سفارشات (وضعیت‌های ۶ گانه)</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- 3. Dynamic Text Templates & Pattern Codes -->
        <div class="bg-[#FCFCFB] border border-[#D9E0D6] p-6 rounded-3xl space-y-6 shadow-sm">
            <div>
                <h3 class="text-sm font-bold text-[#1F2520] flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#C8A35A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>ویرایش متون قالب‌های پیامک و کدهای پترن</span>
                </h3>
                <p class="text-xs text-[#6E756F] mt-1">مدیر می‌تواند متن متغیر تمام پیامک‌ها را ویرایش کند. متغیرها به صورت خودکار جایگزین می‌شوند.</p>
            </div>

            <div class="space-y-6 border-t border-[#E7ECE4] pt-4">

                <!-- Template 1: Welcome & Subscription -->
                <div class="p-5 rounded-2xl bg-[#FFFFFF] border border-[#CCD5C8] space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-[#1F2520]">۱. قالب پیامک خوش‌آمدگویی و فعال‌سازی اشتراک (Welcome)</label>
                        <span class="text-[11px] text-[#3A5232] font-semibold bg-[#EAF2E7] px-2.5 py-0.5 rounded-lg">متغیرها: {name} , {plan_name} , {expire_date}</span>
                    </div>

                    <div>
                        <label class="block text-[11px] text-[#6E756F] mb-1">متن پیامک (ارسال معمولی / متنی):</label>
                        <textarea name="template_text_welcome" rows="2" class="w-full p-3 rounded-xl bg-[#FCFCFB] border border-[#CCD5C8] text-[#1F2520] text-xs leading-relaxed focus:outline-none focus:border-[#6A8C60]">{{ old('template_text_welcome', $setting->template_text_welcome) }}</textarea>
                    </div>

                    <div class="pt-1">
                        <label class="block text-[11px] text-[#6E756F] mb-1">کد پترن ملی پیامک (در صورت استفاده از خدمت پترن BodyID):</label>
                        <input type="text" name="pattern_welcome" value="{{ old('pattern_welcome', $setting->pattern_welcome) }}" placeholder="کد پترن مثلاً: 12345" class="w-full px-4 py-2 rounded-xl bg-[#FCFCFB] border border-[#CCD5C8] text-[#3A5232] text-xs font-mono font-bold">
                    </div>
                </div>

                <!-- Template 2: Daily Usage Confirmation -->
                <div class="p-5 rounded-2xl bg-[#FFFFFF] border border-[#CCD5C8] space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-[#1F2520]">۲. قالب پیامک تایید تحویل سهمیه روزانه (Daily Usage)</label>
                        <span class="text-[11px] text-[#3A5232] font-semibold bg-[#EAF2E7] px-2.5 py-0.5 rounded-lg">متغیرها: {name} , {product_name} , {remaining_items}</span>
                    </div>

                    <div>
                        <label class="block text-[11px] text-[#6E756F] mb-1">متن پیامک (ارسال معمولی / متنی):</label>
                        <textarea name="template_text_daily_usage" rows="2" class="w-full p-3 rounded-xl bg-[#FCFCFB] border border-[#CCD5C8] text-[#1F2520] text-xs leading-relaxed focus:outline-none focus:border-[#6A8C60]">{{ old('template_text_daily_usage', $setting->template_text_daily_usage) }}</textarea>
                    </div>

                    <div class="pt-1">
                        <label class="block text-[11px] text-[#6E756F] mb-1">کد پترن ملی پیامک (در صورت استفاده از خدمت پترن BodyID):</label>
                        <input type="text" name="pattern_daily_usage" value="{{ old('pattern_daily_usage', $setting->pattern_daily_usage) }}" placeholder="کد پترن مثلاً: 12346" class="w-full px-4 py-2 rounded-xl bg-[#FCFCFB] border border-[#CCD5C8] text-[#3A5232] text-xs font-mono font-bold">
                    </div>
                </div>

                <!-- Template 3: Daily Reminder -->
                <div class="p-5 rounded-2xl bg-[#FFFFFF] border border-[#CCD5C8] space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-[#1F2520]">۳. قالب پیامک یادآوری روزانه عدم مراجعه (Daily Reminder - ساعت X)</label>
                        <span class="text-[11px] text-[#3A5232] font-semibold bg-[#EAF2E7] px-2.5 py-0.5 rounded-lg">متغیرها: {name} , {plan_name}</span>
                    </div>

                    <div>
                        <label class="block text-[11px] text-[#6E756F] mb-1">متن پیامک (ارسال معمولی / متنی):</label>
                        <textarea name="template_text_reminder" rows="2" class="w-full p-3 rounded-xl bg-[#FCFCFB] border border-[#CCD5C8] text-[#1F2520] text-xs leading-relaxed focus:outline-none focus:border-[#6A8C60]">{{ old('template_text_reminder', $setting->template_text_reminder) }}</textarea>
                    </div>

                    <div class="pt-1">
                        <label class="block text-[11px] text-[#6E756F] mb-1">کد پترن ملی پیامک (در صورت استفاده از خدمت پترن BodyID):</label>
                        <input type="text" name="pattern_reminder" value="{{ old('pattern_reminder', $setting->pattern_reminder) }}" placeholder="کد پترن مثلاً: 12347" class="w-full px-4 py-2 rounded-xl bg-[#FCFCFB] border border-[#CCD5C8] text-[#3A5232] text-xs font-mono font-bold">
                    </div>
                </div>

                <!-- Template 4: OTP Verification -->
                <div class="p-5 rounded-2xl bg-[#FFFFFF] border border-[#CCD5C8] space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-[#1F2520]">۴. قالب پیامک کد تایید ورود (OTP Code)</label>
                        <span class="text-[11px] text-[#3A5232] font-semibold bg-[#EAF2E7] px-2.5 py-0.5 rounded-lg">متغیر: {code}</span>
                    </div>

                    <div>
                        <label class="block text-[11px] text-[#6E756F] mb-1">متن پیامک (ارسال معمولی / متنی):</label>
                        <textarea name="template_text_otp" rows="2" class="w-full p-3 rounded-xl bg-[#FCFCFB] border border-[#CCD5C8] text-[#1F2520] text-xs leading-relaxed focus:outline-none focus:border-[#6A8C60]">{{ old('template_text_otp', $setting->template_text_otp) }}</textarea>
                    </div>

                    <div class="pt-1">
                        <label class="block text-[11px] text-[#6E756F] mb-1">کد پترن ملی پیامک (در صورت استفاده از خدمت پترن BodyID):</label>
                        <input type="text" name="pattern_otp" value="{{ old('pattern_otp', $setting->pattern_otp) }}" placeholder="کد پترن مثلاً: 12348" class="w-full px-4 py-2 rounded-xl bg-[#FCFCFB] border border-[#CCD5C8] text-[#3A5232] text-xs font-mono font-bold">
                    </div>
                </div>

                <!-- Template 5: Order Submitted -->
                <div class="p-5 rounded-2xl bg-[#FFFFFF] border border-[#CCD5C8] space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-[#1F2520]">۵. قالب پیامک ثبت موفق سفارش (Order Submitted)</label>
                        <span class="text-[11px] text-[#3A5232] font-semibold bg-[#EAF2E7] px-2.5 py-0.5 rounded-lg">متغیرها: {name} , {order_number} , {total_amount}</span>
                    </div>

                    <div>
                        <label class="block text-[11px] text-[#6E756F] mb-1">متن پیامک (ارسال متنی):</label>
                        <textarea name="template_text_order_submitted" rows="2" class="w-full p-3 rounded-xl bg-[#FCFCFB] border border-[#CCD5C8] text-[#1F2520] text-xs leading-relaxed focus:outline-none focus:border-[#6A8C60]">{{ old('template_text_order_submitted', $setting->template_text_order_submitted) }}</textarea>
                    </div>

                    <div class="pt-1">
                        <label class="block text-[11px] text-[#6E756F] mb-1">کد پترن ملی پیامک:</label>
                        <input type="text" name="pattern_order_submitted" value="{{ old('pattern_order_submitted', $setting->pattern_order_submitted) }}" placeholder="کد پترن مثلاً: 10001" class="w-full px-4 py-2 rounded-xl bg-[#FCFCFB] border border-[#CCD5C8] text-[#3A5232] text-xs font-mono font-bold">
                    </div>
                </div>

                <!-- Template 6: Order Preparing -->
                <div class="p-5 rounded-2xl bg-[#FFFFFF] border border-[#CCD5C8] space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-[#1F2520]">۶. قالب پیامک در حال آماده‌سازی (Preparing)</label>
                        <span class="text-[11px] text-[#3A5232] font-semibold bg-[#EAF2E7] px-2.5 py-0.5 rounded-lg">متغیرها: {name} , {order_number}</span>
                    </div>

                    <div>
                        <label class="block text-[11px] text-[#6E756F] mb-1">متن پیامک (ارسال متنی):</label>
                        <textarea name="template_text_order_preparing" rows="2" class="w-full p-3 rounded-xl bg-[#FCFCFB] border border-[#CCD5C8] text-[#1F2520] text-xs leading-relaxed focus:outline-none focus:border-[#6A8C60]">{{ old('template_text_order_preparing', $setting->template_text_order_preparing) }}</textarea>
                    </div>

                    <div class="pt-1">
                        <label class="block text-[11px] text-[#6E756F] mb-1">کد پترن ملی پیامک:</label>
                        <input type="text" name="pattern_order_preparing" value="{{ old('pattern_order_preparing', $setting->pattern_order_preparing) }}" placeholder="کد پترن مثلاً: 10002" class="w-full px-4 py-2 rounded-xl bg-[#FCFCFB] border border-[#CCD5C8] text-[#3A5232] text-xs font-mono font-bold">
                    </div>
                </div>

                <!-- Template 7: Order Ready -->
                <div class="p-5 rounded-2xl bg-[#FFFFFF] border border-[#CCD5C8] space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-[#1F2520]">۷. قالب پیامک آماده تحویل یا ارسال (Ready)</label>
                        <span class="text-[11px] text-[#3A5232] font-semibold bg-[#EAF2E7] px-2.5 py-0.5 rounded-lg">متغیرها: {name} , {order_number}</span>
                    </div>

                    <div>
                        <label class="block text-[11px] text-[#6E756F] mb-1">متن پیامک (ارسال متنی):</label>
                        <textarea name="template_text_order_ready" rows="2" class="w-full p-3 rounded-xl bg-[#FCFCFB] border border-[#CCD5C8] text-[#1F2520] text-xs leading-relaxed focus:outline-none focus:border-[#6A8C60]">{{ old('template_text_order_ready', $setting->template_text_order_ready) }}</textarea>
                    </div>

                    <div class="pt-1">
                        <label class="block text-[11px] text-[#6E756F] mb-1">کد پترن ملی پیامک:</label>
                        <input type="text" name="pattern_order_ready" value="{{ old('pattern_order_ready', $setting->pattern_order_ready) }}" placeholder="کد پترن مثلاً: 10003" class="w-full px-4 py-2 rounded-xl bg-[#FCFCFB] border border-[#CCD5C8] text-[#3A5232] text-xs font-mono font-bold">
                    </div>
                </div>

                <!-- Template 8: Order On The Way -->
                <div class="p-5 rounded-2xl bg-[#FFFFFF] border border-[#CCD5C8] space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-[#1F2520]">۸. قالب پیامک تحویل به پیک / در راه (On The Way)</label>
                        <span class="text-[11px] text-[#3A5232] font-semibold bg-[#EAF2E7] px-2.5 py-0.5 rounded-lg">متغیرها: {name} , {order_number}</span>
                    </div>

                    <div>
                        <label class="block text-[11px] text-[#6E756F] mb-1">متن پیامک (ارسال متنی):</label>
                        <textarea name="template_text_order_on_the_way" rows="2" class="w-full p-3 rounded-xl bg-[#FCFCFB] border border-[#CCD5C8] text-[#1F2520] text-xs leading-relaxed focus:outline-none focus:border-[#6A8C60]">{{ old('template_text_order_on_the_way', $setting->template_text_order_on_the_way) }}</textarea>
                    </div>

                    <div class="pt-1">
                        <label class="block text-[11px] text-[#6E756F] mb-1">کد پترن ملی پیامک:</label>
                        <input type="text" name="pattern_order_on_the_way" value="{{ old('pattern_order_on_the_way', $setting->pattern_order_on_the_way) }}" placeholder="کد پترن مثلاً: 10004" class="w-full px-4 py-2 rounded-xl bg-[#FCFCFB] border border-[#CCD5C8] text-[#3A5232] text-xs font-mono font-bold">
                    </div>
                </div>

                <!-- Template 9: Order Delivered -->
                <div class="p-5 rounded-2xl bg-[#FFFFFF] border border-[#CCD5C8] space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-[#1F2520]">۹. قالب پیامک تحویل موفق نهایی (Delivered)</label>
                        <span class="text-[11px] text-[#3A5232] font-semibold bg-[#EAF2E7] px-2.5 py-0.5 rounded-lg">متغیرها: {name} , {order_number}</span>
                    </div>

                    <div>
                        <label class="block text-[11px] text-[#6E756F] mb-1">متن پیامک (ارسال متنی):</label>
                        <textarea name="template_text_order_delivered" rows="2" class="w-full p-3 rounded-xl bg-[#FCFCFB] border border-[#CCD5C8] text-[#1F2520] text-xs leading-relaxed focus:outline-none focus:border-[#6A8C60]">{{ old('template_text_order_delivered', $setting->template_text_order_delivered) }}</textarea>
                    </div>

                    <div class="pt-1">
                        <label class="block text-[11px] text-[#6E756F] mb-1">کد پترن ملی پیامک:</label>
                        <input type="text" name="pattern_order_delivered" value="{{ old('pattern_order_delivered', $setting->pattern_order_delivered) }}" placeholder="کد پترن مثلاً: 10005" class="w-full px-4 py-2 rounded-xl bg-[#FCFCFB] border border-[#CCD5C8] text-[#3A5232] text-xs font-mono font-bold">
                    </div>
                </div>

                <!-- Template 10: Order Cancelled -->
                <div class="p-5 rounded-2xl bg-[#FFFFFF] border border-[#CCD5C8] space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-[#1F2520]">۱۰. قالب پیامک لغو سفارش (Cancelled)</label>
                        <span class="text-[11px] text-[#3A5232] font-semibold bg-[#EAF2E7] px-2.5 py-0.5 rounded-lg">متغیرها: {name} , {order_number}</span>
                    </div>

                    <div>
                        <label class="block text-[11px] text-[#6E756F] mb-1">متن پیامک (ارسال متنی):</label>
                        <textarea name="template_text_order_cancelled" rows="2" class="w-full p-3 rounded-xl bg-[#FCFCFB] border border-[#CCD5C8] text-[#1F2520] text-xs leading-relaxed focus:outline-none focus:border-[#6A8C60]">{{ old('template_text_order_cancelled', $setting->template_text_order_cancelled) }}</textarea>
                    </div>

                    <div class="pt-1">
                        <label class="block text-[11px] text-[#6E756F] mb-1">کد پترن ملی پیامک:</label>
                        <input type="text" name="pattern_order_cancelled" value="{{ old('pattern_order_cancelled', $setting->pattern_order_cancelled) }}" placeholder="کد پترن مثلاً: 10006" class="w-full px-4 py-2 rounded-xl bg-[#FCFCFB] border border-[#CCD5C8] text-[#3A5232] text-xs font-mono font-bold">
                    </div>
                </div>

            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <x-button variant="primary" type="submit" class="px-8 py-3.5 shadow-lg">
                <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>ذخیره متون قالب‌ها و تنظیمات ملی پیامک</span>
            </x-button>
        </div>
    </form>

    <!-- Manual SMS Test Card (Pre-Production Verification) -->
    <div class="bg-white border border-[#3C6E71]/30 p-6 rounded-3xl space-y-4 shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
            <h3 class="text-sm font-black text-[#284B63] flex items-center gap-2">
                <svg class="w-5 h-5 text-[#3C6E71]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span>تست ارسال دستی و زنده پیامک (راستی‌آزمایی پیش از پروداکشن)</span>
            </h3>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-teal-50 text-teal-700 border border-teal-200">تست بلادرنگ</span>
        </div>
        <p class="text-xs text-gray-500 leading-relaxed">
            جهت اطمینان از صحت اطلاعات اکانت ملی‌پیامک و اتصال به پنل، می‌توانید شماره موبایل خود را وارد نموده و پیامک آزمایشی (کد تایید یا اعلان) دریافت نمایید.
        </p>

        <form method="POST" action="{{ route('settings.sms.test') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            @csrf
            <div class="sm:col-span-5">
                <label class="block text-xs font-bold text-[#353535] mb-1.5">شماره موبایل گیرنده تست</label>
                <input type="tel" name="test_phone" placeholder="09171234567" required dir="ltr" class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-mono font-bold text-[#353535] focus:outline-none focus:border-[#3C6E71] text-center">
            </div>

            <div class="sm:col-span-4">
                <label class="block text-xs font-bold text-[#353535] mb-1.5">نوع تست پیامکی</label>
                <select name="test_type" class="w-full px-3 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-[#353535] focus:outline-none focus:border-[#3C6E71]">
                    <option value="otp">ارسال کد تایید OTP (با پترن / ساده)</option>
                    <option value="notification">ارسال پیامک اطلاع‌رسانی معمولی</option>
                </select>
            </div>

            <div class="sm:col-span-3">
                <button type="submit" class="w-full py-2.5 bg-[#284B63] hover:bg-[#1f3a4d] text-white text-xs font-black rounded-xl transition-colors shadow-sm flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    <span>ارسال پیامک تست</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
