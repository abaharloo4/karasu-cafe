@extends('layouts.app')

@section('title', 'تنظیمات وب‌اپلیکیشن (PWA) و اعلان‌ها')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto" x-data="{
    activeTab: 'customer',
    pushSubscribed: false,
    async enablePush() {
        if (window.BCafePwa) {
            const res = await window.BCafePwa.subscribePush();
            if (res && res.success) {
                this.pushSubscribed = true;
                alert('اعلان‌های مرورگر مدیریت با موفقیت فعال شد.');
            }
        }
    }
}">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-slate-800 flex items-center gap-2">
                <svg class="w-6 h-6 text-[#387780]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span>تنظیمات وب‌اپلیکیشن (PWA) و اعلان‌ها</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">مدیریت مانیفست، تم رنگی، آیکون‌ها، کلیدهای قطع پوش نوتیفیکیشن و گوگل آنالیتیکس ۴ برای هر ۳ پنل</p>
        </div>

        <div class="flex items-center gap-2">
            <!-- Test Push Button -->
            <form method="POST" action="{{ route('admin.pwa-settings.test-push') }}" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl transition-all btn-tactile shadow-xs flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>ارسال پوش تستی</span>
                </button>
            </form>

            <button type="button"
                    @click="enablePush()"
                    class="px-4 py-2.5 bg-[#387780] hover:bg-[#2E636B] text-white text-xs font-bold rounded-xl transition-all btn-tactile shadow-xs flex items-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span>فعال‌سازی اعلان در این مرورگر</span>
            </button>
        </div>
    </div>


    <!-- Tabs Header -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-3">
        <button type="button"
                @click="activeTab = 'customer'"
                :class="activeTab === 'customer' ? 'bg-[#387780] text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200'"
                class="px-5 py-2.5 rounded-xl text-xs font-black transition-all btn-tactile">
            ۱. وب‌اپ مشتریان (Storefront)
        </button>

        <button type="button"
                @click="activeTab = 'admin'"
                :class="activeTab === 'admin' ? 'bg-[#387780] text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200'"
                class="px-5 py-2.5 rounded-xl text-xs font-black transition-all btn-tactile">
            ۲. وب‌اپ مدیریت و بار (Admin POS)
        </button>
    </div>

    <!-- Panel 1: Customer PWA Settings -->
    <div x-show="activeTab === 'customer'" class="space-y-6">
        <form method="POST" action="{{ route('admin.pwa-settings.update') }}" class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-6">
            @csrf
            <input type="hidden" name="panel" value="customer">

            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-sm font-black text-slate-800">مشخصات مانیفست وب‌اپ مشتریان</h2>
                <p class="text-xs text-slate-400 mt-0.5">ظاهر اپلیکیشن هنگام نصب روی صفحه اصلی گوشی و تبلت مشتریان</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">نام کامل اپلیکیشن</label>
                    <input type="text" name="app_name" value="{{ old('app_name', $customerSetting->app_name) }}" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:bg-white focus:border-[#387780] focus:ring-1 focus:ring-[#387780]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">نام کوتاه (زیر آیکون در هوم‌اسکرین)</label>
                    <input type="text" name="short_name" value="{{ old('short_name', $customerSetting->short_name) }}" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:bg-white focus:border-[#387780] focus:ring-1 focus:ring-[#387780]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">رنگ تم (Theme Color - نوار بالای مرورگر)</label>
                    <div class="flex items-center gap-2" x-data="{ cColor: '{{ old('theme_color', $customerSetting->theme_color ?? '#387780') }}' }">
                        <input type="color" name="theme_color" x-model="cColor"
                               class="w-10 h-10 p-1 rounded-xl border border-slate-200 cursor-pointer bg-slate-50">
                        <input type="text" x-model="cColor"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-600">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">رنگ پس‌زمینه اسپلش (Background Color)</label>
                    <div class="flex items-center gap-2" x-data="{ cBg: '{{ old('background_color', $customerSetting->background_color ?? '#0F172A') }}' }">
                        <input type="color" name="background_color" x-model="cBg"
                               class="w-10 h-10 p-1 rounded-xl border border-slate-200 cursor-pointer bg-slate-50">
                        <input type="text" x-model="cBg"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-600">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">شناسه گوگل آنالیتیکس ۴ (GA4 Measurement ID)</label>
                    <input type="text" name="ga4_measurement_id" value="{{ old('ga4_measurement_id', $customerSetting->ga4_measurement_id) }}" placeholder="G-XXXXXXXXXX"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-800 focus:bg-white focus:border-[#387780] dir-ltr text-left">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">آدرس آیکون ۱۹۲×۱۹۲ (اختیاری)</label>
                    <input type="text" name="icon_192" value="{{ old('icon_192', $customerSetting->icon_192) }}" placeholder="پیش‌فرض: /icons/icon-192x192.png"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-[#387780] dir-ltr text-left">
                </div>
            </div>

            <div class="border-t border-slate-100 pt-5 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xs font-black text-slate-800">کلید قطع اضطراری اعلان‌ها (Kill-Switch)</h3>
                        <p class="text-[11px] text-slate-400">در صورت غیرفعال بودن، هیچ پیام پوشی به مشتریان ارسال نخواهد شد.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="push_enabled" value="1" {{ $customerSetting->push_enabled ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#387780]"></div>
                    </label>
                </div>

                <div>
                    <span class="block text-xs font-bold text-slate-700 mb-2">دسته‌بندی‌های مجاز اعلان برای مشتریان:</span>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                            <input type="checkbox" name="allowed_categories[]" value="order_status" {{ in_array('order_status', $customerSetting->allowed_categories ?? []) ? 'checked' : '' }}
                                   class="w-4 h-4 text-[#387780] rounded border-slate-300 focus:ring-[#387780]">
                            <span class="text-xs font-bold text-slate-700">وضعیت سفارش‌ها</span>
                        </label>
                        <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                            <input type="checkbox" name="allowed_categories[]" value="rewards" {{ in_array('rewards', $customerSetting->allowed_categories ?? []) ? 'checked' : '' }}
                                   class="w-4 h-4 text-[#387780] rounded border-slate-300 focus:ring-[#387780]">
                            <span class="text-xs font-bold text-slate-700">جوایز و باشگاه</span>
                        </label>
                        <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                            <input type="checkbox" name="allowed_categories[]" value="marketing" {{ in_array('marketing', $customerSetting->allowed_categories ?? []) ? 'checked' : '' }}
                                   class="w-4 h-4 text-[#387780] rounded border-slate-300 focus:ring-[#387780]">
                            <span class="text-xs font-bold text-slate-700">تخفیف‌ها و بازاریابی</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-6 py-2.5 bg-[#387780] hover:bg-[#2E636B] text-white text-xs font-bold rounded-xl transition-all btn-tactile shadow-xs">
                    ذخیره تنظیمات پنل مشتریان
                </button>
            </div>
        </form>
    </div>

    <!-- Panel 2: Admin PWA Settings -->
    <div x-show="activeTab === 'admin'" class="space-y-6" style="display: none;">
        <form method="POST" action="{{ route('admin.pwa-settings.update') }}" class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-6">
            @csrf
            <input type="hidden" name="panel" value="admin">

            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-sm font-black text-slate-800">مشخصات مانیفست وب‌اپ مدیریت کافه</h2>
                <p class="text-xs text-slate-400 mt-0.5">ظاهر اپلیکیشن بار، آشپزخانه و صندوقداران</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">نام کامل اپلیکیشن</label>
                    <input type="text" name="app_name" value="{{ old('app_name', $adminSetting->app_name) }}" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:bg-white focus:border-[#387780]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">نام کوتاه</label>
                    <input type="text" name="short_name" value="{{ old('short_name', $adminSetting->short_name) }}" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:bg-white focus:border-[#387780]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">رنگ تم (Theme Color)</label>
                    <div class="flex items-center gap-2" x-data="{ aColor: '{{ old('theme_color', $adminSetting->theme_color ?? '#141D22') }}' }">
                        <input type="color" name="theme_color" x-model="aColor"
                               class="w-10 h-10 p-1 rounded-xl border border-slate-200 cursor-pointer bg-slate-50">
                        <input type="text" x-model="aColor"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-600">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">رنگ پس‌زمینه اسپلش</label>
                    <div class="flex items-center gap-2" x-data="{ aBg: '{{ old('background_color', $adminSetting->background_color ?? '#0F172A') }}' }">
                        <input type="color" name="background_color" x-model="aBg"
                               class="w-10 h-10 p-1 rounded-xl border border-slate-200 cursor-pointer bg-slate-50">
                        <input type="text" x-model="aBg"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-600">
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100 pt-5 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xs font-black text-slate-800">کلید قطع اضطراری اعلان‌های مدیریت</h3>
                        <p class="text-[11px] text-slate-400">غیرفعال‌سازی هشدارهای صوتی و پوش نوتیفیکیشن برای پرسنل و باریستاها</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="push_enabled" value="1" {{ $adminSetting->push_enabled ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#387780]"></div>
                    </label>
                </div>

                <div>
                    <span class="block text-xs font-bold text-slate-700 mb-2">دسته‌بندی‌های فعال اعلان مدیریت:</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                            <input type="checkbox" name="allowed_categories[]" value="new_order" {{ in_array('new_order', $adminSetting->allowed_categories ?? []) ? 'checked' : '' }}
                                   class="w-4 h-4 text-[#387780] rounded border-slate-300 focus:ring-[#387780]">
                            <span class="text-xs font-bold text-slate-700">ثبت سفارش آنلاین جدید</span>
                        </label>
                        <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                            <input type="checkbox" name="allowed_categories[]" value="payment_pending" {{ in_array('payment_pending', $adminSetting->allowed_categories ?? []) ? 'checked' : '' }}
                                   class="w-4 h-4 text-[#387780] rounded border-slate-300 focus:ring-[#387780]">
                            <span class="text-xs font-bold text-slate-700">فیش کارت‌به‌کارت نیازمند تایید</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-6 py-2.5 bg-[#387780] hover:bg-[#2E636B] text-white text-xs font-bold rounded-xl transition-all btn-tactile shadow-xs">
                    ذخیره تنظیمات پنل مدیریت
                </button>
            </div>
        </form>
    </div>



</div>
@endsection
