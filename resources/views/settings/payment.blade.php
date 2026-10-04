@extends('layouts.app')

@section('title', 'تنظیمات روش‌های پرداخت')
@section('header_title', 'تنظیمات روش‌های پرداخت آنلاین، کارت به کارت و پرداخت در محل')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header info card -->
    <div class="bg-white border border-[#E2E8F0] p-6 rounded-3xl space-y-2 shadow-xs">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-extrabold text-[#19242B] flex items-center gap-2">
                <svg class="w-5 h-5 text-[#387780]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                <span>پیکربندی درگاه‌های پرداخت آنلاین و تسویه فاکتورها</span>
            </h2>
            <span class="px-3 py-1 bg-teal-50 text-[#387780] text-xs font-bold rounded-lg border border-[#387780]/20">مدیریت مالی کافه بی</span>
        </div>
        <p class="text-xs text-[#64748B]">روش‌های پرداخت فعال را انتخاب کنید و اطلاعات حساب‌های بانکی را جهت استفاده مشتریان در ثبت سفارش آنلاین و منوی سالن تنظیم نمایید.</p>
    </div>

    <form method="POST" action="{{ route('settings.payment.update') }}" class="space-y-6">
        @csrf

        <!-- 1. ZarinPal Settings Card -->
        <div class="bg-white border border-[#E2E8F0] p-6 rounded-3xl space-y-4 shadow-xs">
            <div class="flex items-center justify-between border-b border-[#E2E8F0] pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-[#19242B]">درگاه پرداخت اینترنتی زرین‌پال (شاپرک)</h3>
                        <p class="text-xs text-[#64748B]">پرداخت آنلاین آنی و اتصال به شبکه پرداخت شاپرک بانکی</p>
                    </div>
                </div>

                <label class="flex items-center gap-2 cursor-pointer">
                    <span class="text-xs font-bold text-[#19242B]">وضعیت:</span>
                    <input type="checkbox" name="zarinpal_is_active" value="1" {{ old('zarinpal_is_active', $zarinpal->is_active) ? 'checked' : '' }} class="w-5 h-5 rounded text-[#387780] focus:ring-[#387780]">
                    <span class="text-xs font-semibold text-[#475569]">فعال</span>
                </label>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-[#19242B] mb-1.5">کد مرچنت زرین‌پال (Merchant ID) *</label>
                    <input type="text" name="zarinpal_merchant_id" value="{{ old('zarinpal_merchant_id', $zarinpal->settings['merchant_id'] ?? '') }}" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" class="w-full px-4 py-3 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs font-mono dir-ltr text-right focus:outline-none focus:border-[#387780] focus:ring-1 focus:ring-[#387780]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#19242B] mb-1.5">واحد پولی درگاه</label>
                    <select name="zarinpal_currency" class="w-full px-4 py-3 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs font-bold focus:outline-none focus:border-[#387780] focus:ring-1 focus:ring-[#387780]">
                        <option value="IRT" {{ (old('zarinpal_currency', $zarinpal->settings['currency'] ?? 'IRT') === 'IRT') ? 'selected' : '' }}>تومان (IRT - پیش‌فرض)</option>
                        <option value="IRR" {{ (old('zarinpal_currency', $zarinpal->settings['currency'] ?? '') === 'IRR') ? 'selected' : '' }}>ریال (IRR)</option>
                    </select>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0] flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-[#19242B] block">حالت تست و توسعه (Sandbox)</span>
                    <span class="text-[11px] text-[#64748B]">در صورت فعال بودن، تراکنش‌ها در شبیه‌ساز زرین‌پال بدون کسر وجه واقعی انجام می‌شوند.</span>
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="zarinpal_sandbox" value="1" {{ old('zarinpal_sandbox', $zarinpal->settings['sandbox'] ?? true) ? 'checked' : '' }} class="w-5 h-5 rounded text-amber-500 focus:ring-amber-500">
                    <span class="text-xs font-bold text-amber-600">حالت سندباکس</span>
                </label>
            </div>
        </div>

        <!-- 2. Card to Card Settings Card -->
        <div class="bg-white border border-[#E2E8F0] p-6 rounded-3xl space-y-4 shadow-xs">
            <div class="flex items-center justify-between border-b border-[#E2E8F0] pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-teal-50 border border-teal-200 flex items-center justify-center text-[#387780]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-[#19242B]">انتقال کارت به کارت بانکی</h3>
                        <p class="text-xs text-[#64748B]">واریز مستقیم به شماره کارت کافه و آپلود تصویر فیش توسط مشتری</p>
                    </div>
                </div>

                <label class="flex items-center gap-2 cursor-pointer">
                    <span class="text-xs font-bold text-[#19242B]">وضعیت:</span>
                    <input type="checkbox" name="card_to_card_is_active" value="1" {{ old('card_to_card_is_active', $cardToCard->is_active) ? 'checked' : '' }} class="w-5 h-5 rounded text-[#387780] focus:ring-[#387780]">
                    <span class="text-xs font-semibold text-[#475569]">فعال</span>
                </label>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-[#19242B] mb-1.5">نام بانک</label>
                    <input type="text" name="card_bank_name" value="{{ old('card_bank_name', $cardToCard->settings['bank_name'] ?? '') }}" placeholder="مثلاً: بانک سامان" class="w-full px-4 py-3 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs focus:outline-none focus:border-[#387780] focus:ring-1 focus:ring-[#387780]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#19242B] mb-1.5">نام دارنده حساب *</label>
                    <input type="text" name="card_account_owner" value="{{ old('card_account_owner', $cardToCard->settings['account_owner'] ?? '') }}" placeholder="مثلاً: کافه بی" class="w-full px-4 py-3 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs focus:outline-none focus:border-[#387780] focus:ring-1 focus:ring-[#387780]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#19242B] mb-1.5">شماره کارت (۱۶ رقم) *</label>
                    <input type="text" name="card_number" value="{{ old('card_number', $cardToCard->settings['card_number'] ?? '') }}" placeholder="۶۲۱۹-xxxx-xxxx-xxxx" class="w-full px-4 py-3 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs font-mono dir-ltr text-right focus:outline-none focus:border-[#387780] focus:ring-1 focus:ring-[#387780]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#19242B] mb-1.5">شماره شبا (اختیاری)</label>
                    <input type="text" name="card_sheba_number" value="{{ old('card_sheba_number', $cardToCard->settings['sheba_number'] ?? '') }}" placeholder="IR..." class="w-full px-4 py-3 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs font-mono dir-ltr text-right focus:outline-none focus:border-[#387780] focus:ring-1 focus:ring-[#387780]">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#19242B] mb-1.5">متن راهنمای واریز برای مشتری</label>
                <textarea name="card_instructions" rows="2" placeholder="متنی که در زمان انتخاب کارت به کارت به مشتری نمایش داده می‌شود..." class="w-full px-4 py-3 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs focus:outline-none focus:border-[#387780] focus:ring-1 focus:ring-[#387780]">{{ old('card_instructions', $cardToCard->settings['instructions'] ?? '') }}</textarea>
            </div>
        </div>

        <!-- 3. Cash on Delivery / In-Store POS Settings Card -->
        <div class="bg-white border border-[#E2E8F0] p-6 rounded-3xl space-y-4 shadow-xs">
            <div class="flex items-center justify-between border-b border-[#E2E8F0] pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center text-[#19242B]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-[#19242B]">پرداخت در محل / کارتخوان سیار و حضوری (Cash / POS)</h3>
                        <p class="text-xs text-[#64748B]">تسویه نقدی یا با دستگاه کارتخوان روی کانتر یا هنگام تحویل سفارش توسط پیک</p>
                    </div>
                </div>

                <label class="flex items-center gap-2 cursor-pointer">
                    <span class="text-xs font-bold text-[#19242B]">وضعیت:</span>
                    <input type="checkbox" name="cash_is_active" value="1" {{ old('cash_is_active', $cash->is_active ?? true) ? 'checked' : '' }} class="w-5 h-5 rounded text-[#387780] focus:ring-[#387780]">
                    <span class="text-xs font-semibold text-[#475569]">فعال</span>
                </label>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#19242B] mb-1.5">متن راهنمای تسویه برای مشتری</label>
                <textarea name="cash_instructions" rows="2" placeholder="متنی که هنگام انتخاب گزینه پرداخت در محل نمایش داده می‌شود..." class="w-full px-4 py-3 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs focus:outline-none focus:border-[#387780] focus:ring-1 focus:ring-[#387780]">{{ old('cash_instructions', $cash->settings['instructions'] ?? '') }}</textarea>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end pt-2">
            <button type="submit" class="px-8 py-3.5 bg-[#387780] hover:bg-[#2E626A] text-white font-bold rounded-2xl text-xs transition-colors shadow-xs flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>ذخیره تغییرات تنظیمات روش‌های پرداخت</span>
            </button>
        </div>
    </form>
</div>
@endsection
