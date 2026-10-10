@extends('layouts.app')

@section('title', 'ویرایش کد تخفیف ' . $coupon->code)
@section('header_title', 'ویرایش کد تخفیف: ' . $coupon->code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Card -->
    <div class="bg-white border border-[#D9D9D9] p-5 sm:p-6 rounded-3xl shadow-xs flex items-center justify-between">
        <div>
            <h2 class="text-base font-black text-[#353535] flex items-center gap-2">
                <svg class="w-5 h-5 text-[#3C6E71]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>ویرایش کد تخفیف «{{ $coupon->code }}»</span>
            </h2>
            <p class="text-xs text-[#353535]/65 mt-1">تاکنون <strong class="text-[#284B63] font-mono">{{ $coupon->times_used }} بار</strong> استفاده شده است.</p>
        </div>

        <a href="{{ route('admin.coupons.index') }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-[#353535] font-black text-xs rounded-xl transition-colors">
            ← بازگشت به لیست
        </a>
    </div>

    <form method="POST" action="{{ route('admin.coupons.update', $coupon) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- 1. General Info Card -->
        <div class="bg-white border border-[#D9D9D9] p-6 rounded-3xl shadow-xs space-y-5">
            <h3 class="text-xs font-black text-[#284B63] flex items-center gap-2 border-b border-gray-100 pb-3">
                <span class="w-2 h-2 rounded-full bg-[#3C6E71]"></span>
                <span>۱. اطلاعات عمومی کوپن</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-black text-[#353535] mb-1.5">کد تخفیف (کوپن) *</label>
                    <input type="text" name="code" value="{{ old('code', $coupon->code) }}" required dir="ltr"
                           class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-xs font-mono font-bold text-[#353535] focus:outline-none focus:border-[#3C6E71] focus:bg-white uppercase">
                    @error('code') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-black text-[#353535] mb-1.5">عنوان / مناسبت تخفیف *</label>
                    <input type="text" name="title" value="{{ old('title', $coupon->title) }}" required
                           class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-[#353535] focus:outline-none focus:border-[#3C6E71] focus:bg-white">
                    @error('title') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-black text-[#353535] mb-1.5">توضیحات کوتاه (اختیاری)</label>
                    <textarea name="description" rows="2"
                              class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-[#353535] focus:outline-none focus:border-[#3C6E71] focus:bg-white">{{ old('description', $coupon->description) }}</textarea>
                    @error('description') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- 2. Discount Type & Value Card -->
        <div class="bg-white border border-[#D9D9D9] p-6 rounded-3xl shadow-xs space-y-5" x-data="{ discType: '{{ old('discount_type', $coupon->discount_type) }}' }">
            <h3 class="text-xs font-black text-[#284B63] flex items-center gap-2 border-b border-gray-100 pb-3">
                <span class="w-2 h-2 rounded-full bg-[#3C6E71]"></span>
                <span>۲. نوع و مقدار تخفیف</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-black text-[#353535] mb-1.5">نوع تخفیف *</label>
                    <select name="discount_type" x-model="discType" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-[#353535] focus:outline-none focus:border-[#3C6E71] focus:bg-white min-h-[46px]">
                        <option value="percent">درصد تخفیف (درصدی %)</option>
                        <option value="fixed">مبلغ ثابت تخفیف (تومان)</option>
                    </select>
                    @error('discount_type') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-black text-[#353535] mb-1.5" x-text="discType === 'percent' ? 'درصد تخفیف (۱ تا ۱۰۰) *' : 'مبلغ تخفیف (تومان) *'">میزان تخفیف *</label>
                    <input type="number" name="discount_value" value="{{ old('discount_value', (float)$coupon->discount_value) }}" required min="1" step="any"
                           class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-[#353535] focus:outline-none focus:border-[#3C6E71] focus:bg-white dir-ltr text-right tabular-nums">
                    @error('discount_value') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div x-show="discType === 'percent'">
                    <label class="block text-xs font-black text-[#353535] mb-1.5">حداکثر سقف تخفیف (تومان)</label>
                    <input type="number" name="max_discount" value="{{ old('max_discount', $coupon->max_discount ? (float)$coupon->max_discount : '') }}" min="0" step="1000" placeholder="بدون سقف"
                           class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-[#353535] focus:outline-none focus:border-[#3C6E71] focus:bg-white dir-ltr text-right tabular-nums">
                    <p class="text-[11px] text-gray-400 mt-1">سقف ریالی برای تخفیف‌های درصدی.</p>
                    @error('max_discount') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- 3. Usage Restrictions & Limits (WooCommerce Style) -->
        <div class="bg-white border border-[#D9D9D9] p-6 rounded-3xl shadow-xs space-y-5">
            <h3 class="text-xs font-black text-[#284B63] flex items-center gap-2 border-b border-gray-100 pb-3">
                <span class="w-2 h-2 rounded-full bg-[#3C6E71]"></span>
                <span>۳. محدودیت‌های مالی و دفعات استفاده</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-black text-[#353535] mb-1.5">حداقل مبلغ سفارش (تومان)</label>
                    <input type="number" name="min_spend" value="{{ old('min_spend', $coupon->min_spend ? (float)$coupon->min_spend : '') }}" min="0" step="1000" placeholder="بدون حداقل"
                           class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-[#353535] focus:outline-none focus:border-[#3C6E71] focus:bg-white dir-ltr text-right tabular-nums">
                    @error('min_spend') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-black text-[#353535] mb-1.5">محدودیت تعداد کل استفاده</label>
                    <input type="number" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}" min="1" placeholder="نامحدود"
                           class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-[#353535] focus:outline-none focus:border-[#3C6E71] focus:bg-white dir-ltr text-right tabular-nums">
                    @error('usage_limit') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-black text-[#353535] mb-1.5">محدودیت استفاده برای هر کاربر</label>
                    <input type="number" name="usage_limit_per_user" value="{{ old('usage_limit_per_user', $coupon->usage_limit_per_user) }}" min="1"
                           class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-[#353535] focus:outline-none focus:border-[#3C6E71] focus:bg-white dir-ltr text-right tabular-nums">
                    @error('usage_limit_per_user') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- First Order Toggle -->
            <div class="pt-3 border-t border-gray-100">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="first_order_only" value="1" {{ old('first_order_only', $coupon->first_order_only) ? 'checked' : '' }}
                           class="w-5 h-5 rounded bg-gray-50 border-gray-300 text-[#3C6E71] focus:ring-[#3C6E71]">
                    <div>
                        <span class="text-xs font-black text-[#353535]">فقط مخصوص اولین خرید مشتری (کاربران جدید)</span>
                        <p class="text-[11px] text-gray-400">کاربرانی که قبلاً سفارشی در کافه ثبت کرده‌اند نمی‌توانند از این کد استفاده کنند.</p>
                    </div>
                </label>
            </div>
        </div>

        <!-- 4. Schedule & Status Card -->
        <div class="bg-white border border-[#D9D9D9] p-6 rounded-3xl shadow-xs space-y-5">
            <h3 class="text-xs font-black text-[#284B63] flex items-center gap-2 border-b border-gray-100 pb-3">
                <span class="w-2 h-2 rounded-full bg-[#3C6E71]"></span>
                <span>۴. زمان‌بندی و وضعیت انتشار</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <x-persian-datetime-picker name="starts_at" :value="old('starts_at', $coupon->starts_at ? $coupon->starts_at->format('Y-m-d\TH:i') : '')" label="تاریخ و ساعت شروع اعتبار" placeholder="انتخاب تاریخ تقویم و ساعت..." />
                    @error('starts_at') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-persian-datetime-picker name="expires_at" :value="old('expires_at', $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : '')" label="تاریخ و ساعت انقضا" placeholder="انتخاب تاریخ تقویم و ساعت..." />
                    @error('expires_at') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-black text-[#353535] mb-1.5">وضعیت کد تخفیف *</label>
                    <select name="status" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-[#353535] focus:outline-none focus:border-[#3C6E71] focus:bg-white min-h-[46px]">
                        <option value="active" {{ old('status', $coupon->status) === 'active' ? 'selected' : '' }}>فعال و آماده مصرف</option>
                        <option value="inactive" {{ old('status', $coupon->status) === 'inactive' ? 'selected' : '' }}>غیرفعال (پیش‌نویس)</option>
                    </select>
                    @error('status') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('admin.coupons.index') }}" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-[#353535] font-black rounded-xl text-xs transition-colors">
                انصراف
            </a>
            <button type="submit" class="px-8 py-3.5 bg-[#3C6E71] hover:bg-[#284B63] text-white font-black rounded-xl text-xs shadow-md transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>ذخیره تغییرات کد تخفیف</span>
            </button>
        </div>
    </form>

</div>
@endsection
