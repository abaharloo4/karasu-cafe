@extends('layouts.customer')

@section('title', 'تنظیمات پیام‌ها و اعلان‌ها')

@section('content')
<div class="space-y-6 max-w-2xl" x-data="{
    pushEnabled: {{ $hasPushSubscription ? 'true' : 'false' }},
    loading: false,
    async enablePush() {
        this.loading = true;
        try {
            if (window.BCafePwa) {
                const res = await window.BCafePwa.subscribePush();
                if (res && res.success) {
                    this.pushEnabled = true;
                    window.CafeToast.success('دریافت اعلان‌ها با موفقیت در این مرورگر فعال شد.');
                }
            } else {
                window.CafeToast.error('سیستم وب‌اپلیکیشن در دسترس نیست.');
            }
        } catch(e) {
            window.CafeToast.error('خطا در برقراری ارتباط با سرویس پوش');
        } finally {
            this.loading = false;
        }
    }
}">

    <!-- Page Header -->
    <div>
        <h1 class="text-lg sm:text-xl font-black text-[#353535] flex items-center gap-2">
            <svg class="w-6 h-6 text-[#3C6E71]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            <span>تنظیمات پیام‌ها و اعلان‌ها</span>
        </h1>
        <p class="text-xs text-[#353535]/65 mt-1">مدیریت نحوه دریافت اعلان‌های وضعیت سفارش، جوایز باشگاه مشتریان و تخفیف‌ها</p>
    </div>

    <!-- Device Push Subscription Card -->
    <div class="bg-white border border-[#D9D9D9] rounded-2xl p-5 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" :class="pushEnabled ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-black text-[#353535]">اعلان وب در این دستگاه</h2>
                    <p class="text-xs text-[#353535]/65 mt-0.5" x-text="pushEnabled ? 'اعلان‌ها روی این مرورگر فعال هستند و پیام‌های وضعیت سفارش را دریافت می‌کنید.' : 'برای دریافت آنی پیام‌ها هنگام آماده شدن سفارش، اعلان این مرورگر را فعال کنید.'"></p>
                </div>
            </div>

            <button type="button"
                    @click="enablePush()"
                    :disabled="loading || pushEnabled"
                    class="px-4 py-2.5 text-xs font-black rounded-xl transition-all flex items-center justify-center gap-1.5 cursor-pointer flex-shrink-0"
                    :class="pushEnabled ? 'bg-gray-100 text-gray-500 cursor-default' : 'bg-[#3C6E71] hover:bg-[#387780] text-white shadow-xs active:scale-95'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!loading"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span x-text="loading ? 'در حال فعال‌سازی...' : (pushEnabled ? 'فعال است' : 'فعال‌سازی اعلان')"></span>
            </button>
        </div>
    </div>

    <!-- Notification Preferences Form -->
    <form method="POST" action="{{ route('customer.notifications.update') }}" class="bg-white border border-[#D9D9D9] rounded-2xl p-5 shadow-xs space-y-5">
        @csrf

        <div class="border-b border-[#D9D9D9] pb-4">
            <h2 class="text-sm font-black text-[#353535]">دسته‌بندی پیام‌های دریافتی</h2>
            <p class="text-xs text-[#353535]/65 mt-0.5">مشخص کنید کدام پیام‌ها برای شما ارسال شوند.</p>
        </div>

        <div class="space-y-4">
            <!-- Order Status -->
            <label class="flex items-start justify-between gap-4 p-3.5 rounded-xl border border-[#D9D9D9]/70 hover:bg-[#F7F9FA] transition-colors cursor-pointer">
                <div>
                    <span class="text-sm font-black text-[#353535] block">وضعیت سفارش‌ها و فاکتور</span>
                    <span class="text-xs text-[#353535]/65 block mt-0.5">در حال آماده‌سازی، آماده تحویل در کافه، تحویل به پیک و تایید پرداخت</span>
                </div>
                <input type="checkbox" name="order_status" value="1" {{ $preferences['order_status'] ? 'checked' : '' }}
                       class="w-5 h-5 text-[#3C6E71] rounded-md border-gray-300 focus:ring-[#3C6E71] mt-0.5 cursor-pointer">
            </label>

            <!-- Rewards & Loyalty -->
            <label class="flex items-start justify-between gap-4 p-3.5 rounded-xl border border-[#D9D9D9]/70 hover:bg-[#F7F9FA] transition-colors cursor-pointer">
                <div>
                    <span class="text-sm font-black text-[#353535] block">جوایز و باشگاه مشتریان</span>
                    <span class="text-xs text-[#353535]/65 block mt-0.5">کسب امتیاز جدید، شارژ کش‌بک در کیف‌پول و هدیه قهوه روز تولد</span>
                </div>
                <input type="checkbox" name="rewards" value="1" {{ $preferences['rewards'] ? 'checked' : '' }}
                       class="w-5 h-5 text-[#3C6E71] rounded-md border-gray-300 focus:ring-[#3C6E71] mt-0.5 cursor-pointer">
            </label>

            <!-- Marketing & Discounts -->
            <label class="flex items-start justify-between gap-4 p-3.5 rounded-xl border border-[#D9D9D9]/70 hover:bg-[#F7F9FA] transition-colors cursor-pointer">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-black text-[#353535]">تخفیف‌ها و پیشنهادات شگفت‌انگیز</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#3C6E71]/10 text-[#3C6E71]">اختیاری</span>
                    </div>
                    <span class="text-xs text-[#353535]/65 block mt-0.5">کدهای تخفیف مناسبتی، منوی جدید فصل و رویدادهای ویژه بی کافه</span>
                </div>
                <input type="checkbox" name="marketing" value="1" {{ $preferences['marketing'] ? 'checked' : '' }}
                       class="w-5 h-5 text-[#3C6E71] rounded-md border-gray-300 focus:ring-[#3C6E71] mt-0.5 cursor-pointer">
            </label>
        </div>

        <div class="pt-2 flex items-center justify-end">
            <button type="submit" class="px-5 py-2.5 bg-[#284B63] hover:bg-[#1E374A] text-white text-xs font-black rounded-xl transition-all shadow-xs cursor-pointer active:scale-95">
                ذخیره تنظیمات
            </button>
        </div>
    </form>
</div>
@endsection
