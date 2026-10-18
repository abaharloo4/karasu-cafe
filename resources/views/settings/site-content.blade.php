@extends('layouts.app')

@section('title', 'مدیریت محتوا، متون صفحات و سایز عکس منو')
@section('header_title', 'شخصی‌سازی متون سایت و ابعاد تصاویر')

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="{ activeTab: 'menu' }">

    <!-- Header Description Card -->
    <div class="bg-white border border-[#E2E8F0] p-6 rounded-3xl space-y-2 shadow-xs">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-extrabold text-[#19242B] flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#387780]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>مدیریت یکپارچه محتوای سایت، متون صفحات و نسبت ابعاد تصاویر منو</span>
                </h2>
                <p class="text-xs text-[#64748B] mt-1">
                    در این بخش می‌توانید تمامی متون نمایش‌داده‌شده در صفحات (درباره ما، اشتراک‌ها، فوتر، نقشه) و همچنین سایز کادر عکس محصولات را مستقیماً تعیین فرمایید.
                </p>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex items-center gap-2 pt-3 border-t border-[#E2E8F0] overflow-x-auto text-xs font-bold">
            <button type="button" @click="activeTab = 'menu'" :class="activeTab === 'menu' ? 'bg-[#387780] text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-4 py-2 rounded-xl transition-colors shrink-0">
                ابعاد تصاویر و سربرگ منو
            </button>
            <button type="button" @click="activeTab = 'about'" :class="activeTab === 'about' ? 'bg-[#387780] text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-4 py-2 rounded-xl transition-colors shrink-0">
                صفحه درباره ما و نقشه
            </button>
            <button type="button" @click="activeTab = 'subscriptions'" :class="activeTab === 'subscriptions' ? 'bg-[#387780] text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-4 py-2 rounded-xl transition-colors shrink-0">
                صفحه اشتراک‌ها و قوانین
            </button>
            <button type="button" @click="activeTab = 'footer'" :class="activeTab === 'footer' ? 'bg-[#387780] text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-4 py-2 rounded-xl transition-colors shrink-0">
                فوتر، کپی‌رایت و ساعات کاری
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('settings.site-content.update') }}" class="space-y-6">
        @csrf

        <!-- 1. Menu Aspect Ratio & Header -->
        <div x-show="activeTab === 'menu'" class="space-y-6">
            <div class="bg-white border border-[#E2E8F0] p-6 rounded-3xl space-y-5 shadow-xs">
                <div class="border-b border-[#E2E8F0] pb-3">
                    <h3 class="text-sm font-bold text-[#19242B]">نسبت ابعاد کادر تصاویر محصولات منو</h3>
                    <p class="text-xs text-[#64748B]">کادر نمایش عکس محصولات در منوی کافه بر اساس این انتخاب برای تمام کاربران تنظیم می‌شود.</p>
                </div>

                <!-- Aspect Ratio Selector Cards -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5" x-data="{ aspect: '{{ old('menu_image_aspect', $setting->menu_image_aspect) }}' }">
                    <!-- Standard 4:3 -->
                    <label :class="aspect === 'standard' ? 'border-[#387780] bg-teal-50/50 ring-2 ring-[#387780]/20' : 'border-[#CBD5E1] bg-white hover:bg-slate-50'"
                           class="p-4 rounded-2xl border flex flex-col items-center text-center gap-2.5 cursor-pointer transition-all">
                        <input type="radio" name="menu_image_aspect" value="standard" x-model="aspect" class="sr-only">
                        <div class="w-16 h-12 bg-slate-200 rounded-lg flex items-center justify-center text-[10px] font-mono font-bold text-slate-600 border border-slate-300">
                            4 : 3
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-[#19242B]">استاندارد (۴:۳)</span>
                            <span class="text-[10px] text-[#64748B]">پیش‌فرض منوی کافه</span>
                        </div>
                    </label>

                    <!-- Square 1:1 -->
                    <label :class="aspect === 'square' ? 'border-[#387780] bg-teal-50/50 ring-2 ring-[#387780]/20' : 'border-[#CBD5E1] bg-white hover:bg-slate-50'"
                           class="p-4 rounded-2xl border flex flex-col items-center text-center gap-2.5 cursor-pointer transition-all">
                        <input type="radio" name="menu_image_aspect" value="square" x-model="aspect" class="sr-only">
                        <div class="w-14 h-14 bg-slate-200 rounded-lg flex items-center justify-center text-[10px] font-mono font-bold text-slate-600 border border-slate-300">
                            1 : 1
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-[#19242B]">مربع (۱:۱)</span>
                            <span class="text-[10px] text-[#64748B]">مشابه پست‌های اینستاگرام</span>
                        </div>
                    </label>

                    <!-- Portrait 3:4 -->
                    <label :class="aspect === 'portrait' ? 'border-[#387780] bg-teal-50/50 ring-2 ring-[#387780]/20' : 'border-[#CBD5E1] bg-white hover:bg-slate-50'"
                           class="p-4 rounded-2xl border flex flex-col items-center text-center gap-2.5 cursor-pointer transition-all">
                        <input type="radio" name="menu_image_aspect" value="portrait" x-model="aspect" class="sr-only">
                        <div class="w-12 h-16 bg-slate-200 rounded-lg flex items-center justify-center text-[10px] font-mono font-bold text-slate-600 border border-slate-300">
                            3 : 4
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-[#19242B]">پرتره عمودی (۳:۴)</span>
                            <span class="text-[10px] text-[#64748B]">عکس‌های قدی و بطری‌ها</span>
                        </div>
                    </label>

                    <!-- Video 16:9 -->
                    <label :class="aspect === 'video' ? 'border-[#387780] bg-teal-50/50 ring-2 ring-[#387780]/20' : 'border-[#CBD5E1] bg-white hover:bg-slate-50'"
                           class="p-4 rounded-2xl border flex flex-col items-center text-center gap-2.5 cursor-pointer transition-all">
                        <input type="radio" name="menu_image_aspect" value="video" x-model="aspect" class="sr-only">
                        <div class="w-20 h-11 bg-slate-200 rounded-lg flex items-center justify-center text-[10px] font-mono font-bold text-slate-600 border border-slate-300">
                            16 : 9
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-[#19242B]">افقی عریض (۱۶:۹)</span>
                            <span class="text-[10px] text-[#64748B]">نمای سینمایی و واید</span>
                        </div>
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-3 border-t border-[#E2E8F0]">
                    <div>
                        <label class="block text-xs font-bold text-[#19242B] mb-1.5">عنوان بالای صفحه منو</label>
                        <input type="text" name="menu_title" value="{{ old('menu_title', $setting->menu_title) }}" class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs font-bold focus:border-[#387780]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#19242B] mb-1.5">زیرعنوان بالای صفحه منو</label>
                        <input type="text" name="menu_subtitle" value="{{ old('menu_subtitle', $setting->menu_subtitle) }}" class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs focus:border-[#387780]">
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. About Us & Map -->
        <div x-show="activeTab === 'about'" class="space-y-6" style="display: none;">
            <div class="bg-white border border-[#E2E8F0] p-6 rounded-3xl space-y-4 shadow-xs">
                <div class="border-b border-[#E2E8F0] pb-3">
                    <h3 class="text-sm font-bold text-[#19242B]">محتوای متنی صفحه «درباره ما»</h3>
                    <p class="text-xs text-[#64748B]">عنوان، داستان شکل‌گیری کافه و معرفی برای مشتریان</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-[#19242B] mb-1.5">عنوان صفحه درباره ما</label>
                        <input type="text" name="about_title" value="{{ old('about_title', $setting->about_title) }}" class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#19242B] mb-1.5">زیرعنوان صفحه درباره ما</label>
                        <input type="text" name="about_subtitle" value="{{ old('about_subtitle', $setting->about_subtitle) }}" class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs">
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-xs font-bold text-[#19242B] mb-1.5">متن تفصیلی داستان و معرفی کافه</label>
                        <textarea name="about_story" rows="5" class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs leading-relaxed">{{ old('about_story', $setting->about_story) }}</textarea>
                    </div>
                </div>

                <div class="border-t border-[#E2E8F0] pt-4 space-y-4">
                    <h4 class="text-xs font-bold text-[#19242B]">اطلاعات تماس، آدرس و شبکه‌های اجتماعی</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-[#19242B] mb-1.5">آدرس کامل متنی کافه</label>
                            <input type="text" name="about_address" value="{{ old('about_address', $setting->about_address) }}" class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#19242B] mb-1.5">تلفن سفارشات و هماهنگی</label>
                            <input type="text" name="about_phone" value="{{ old('about_phone', $setting->about_phone) }}" dir="ltr" class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#19242B] mb-1.5">آیدی اینستاگرام</label>
                            <input type="text" name="about_instagram" value="{{ old('about_instagram', $setting->about_instagram) }}" dir="ltr" class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs font-mono">
                        </div>
                    </div>
                </div>

                <div class="border-t border-[#E2E8F0] pt-4 space-y-4">
                    <h4 class="text-xs font-bold text-[#19242B]">موقعیت روی نقشه و لینک‌های مسیریاب</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-[#19242B] mb-1.5">عرض جغرافیایی (Latitude)</label>
                            <input type="text" name="about_map_lat" value="{{ old('about_map_lat', $setting->about_map_lat) }}" dir="ltr" class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#19242B] mb-1.5">طول جغرافیایی (Longitude)</label>
                            <input type="text" name="about_map_lng" value="{{ old('about_map_lng', $setting->about_map_lng) }}" dir="ltr" class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#19242B] mb-1.5">لینک نشان (Neshan Map URL)</label>
                            <input type="url" name="about_map_neshan_url" value="{{ old('about_map_neshan_url', $setting->about_map_neshan_url) }}" dir="ltr" class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#19242B] mb-1.5">لینک بلد (Balad Map URL)</label>
                            <input type="url" name="about_map_balad_url" value="{{ old('about_map_balad_url', $setting->about_map_balad_url) }}" dir="ltr" class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs font-mono">
                        </div>
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-xs font-bold text-[#19242B] mb-1.5">لینک گوگل مپ (Google Maps URL)</label>
                            <input type="url" name="about_map_google_url" value="{{ old('about_map_google_url', $setting->about_map_google_url) }}" dir="ltr" class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs font-mono">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Subscriptions & Rules -->
        <div x-show="activeTab === 'subscriptions'" class="space-y-6" style="display: none;">
            <div class="bg-white border border-[#E2E8F0] p-6 rounded-3xl space-y-4 shadow-xs">
                <div class="border-b border-[#E2E8F0] pb-3">
                    <h3 class="text-sm font-bold text-[#19242B]">متون صفحه اشتراک‌ها و شرایط مصرف</h3>
                    <p class="text-xs text-[#64748B]">توضیحات و قوانین استفاده از اشتراک‌های قهوه برای مشتریان</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-[#19242B] mb-1.5">عنوان صفحه اشتراک‌ها</label>
                        <input type="text" name="subscriptions_title" value="{{ old('subscriptions_title', $setting->subscriptions_title) }}" class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#19242B] mb-1.5">زیرعنوان صفحه اشتراک‌ها</label>
                        <input type="text" name="subscriptions_subtitle" value="{{ old('subscriptions_subtitle', $setting->subscriptions_subtitle) }}" class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs">
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-xs font-bold text-[#19242B] mb-1.5">توضیحات و بنر راهنمای خرید اشتراک</label>
                        <textarea name="subscriptions_description" rows="3" class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs leading-relaxed">{{ old('subscriptions_description', $setting->subscriptions_description) }}</textarea>
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-xs font-bold text-[#19242B] mb-1.5">قوانین و شرایط استفاده از اشتراک (در صفحه جزئیات طرح)</label>
                        <textarea name="subscriptions_rules" rows="4" class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs leading-relaxed">{{ old('subscriptions_rules', $setting->subscriptions_rules) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Footer & Hours -->
        <div x-show="activeTab === 'footer'" class="space-y-6" style="display: none;">
            <div class="bg-white border border-[#E2E8F0] p-6 rounded-3xl space-y-4 shadow-xs">
                <div class="border-b border-[#E2E8F0] pb-3">
                    <h3 class="text-sm font-bold text-[#19242B]">تنظیمات فوتر، کپی‌رایت و ساعات کاری سایت</h3>
                    <p class="text-xs text-[#64748B]">متونی که در پایین تمامی صفحات یا هدر کافه نمایش داده می‌شوند</p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-[#19242B] mb-1.5">متن کپی‌رایت فوتر</label>
                        <input type="text" name="footer_text" value="{{ old('footer_text', $setting->footer_text) }}" class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#19242B] mb-1.5">متن ساعات کاری در فوتر</label>
                        <input type="text" name="footer_hours" value="{{ old('footer_hours', $setting->footer_hours) }}" class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs">
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <button type="submit" class="px-8 py-3.5 bg-[#387780] hover:bg-[#2E626A] text-white font-bold rounded-2xl text-xs transition-colors shadow-xs flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>ذخیره کلیه تغییرات محتوا</span>
            </button>
        </div>
    </form>
</div>
@endsection
