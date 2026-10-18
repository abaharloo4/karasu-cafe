<!DOCTYPE html>
<html lang="fa" dir="rtl" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>درباره ما - کافه تخصصی بی کافه (B Cafe) شیراز</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}?v=2.0.2">
    <link rel="alternate icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2.0.2">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}?v=2.0.2">

    <!-- Leaflet CSS for Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    @include('partials.theme-init')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full antialiased bg-[#F8FAFC] dark:bg-[#0F171B] text-[#19242B] dark:text-slate-100 selection:bg-[#387780] selection:text-white transition-colors">
    @php
        $siteContent = \App\Models\SiteContentSetting::getForCafe();
    @endphp

    <!-- Unified Global Navigation Header -->
    <x-storefront-header :activeTab="'about'" :siteContent="$siteContent" />

    <!-- Hero Section -->
    <section class="relative bg-gradient-to-b from-white via-[#F8FAFC] to-[#F1F5F9] border-b border-[#E2E8F0] py-16 sm:py-24 overflow-hidden">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-teal-50 border border-teal-200 text-[#387780] text-xs font-black shadow-xs">
                <span>یه کنج دنج توی شیراز</span>
            </div>

            <h1 class="text-3xl sm:text-5xl font-black text-[#19242B] tracking-tight leading-tight">
                {{ $siteContent->about_title ?? 'داستان بی کافه؛ دانه باکیفیت و روست تازه' }}
            </h1>

            <p class="text-sm sm:text-base text-[#64748B] max-w-2xl mx-auto leading-relaxed">
                {{ $siteContent->about_subtitle ?? 'توی بی کافه شیراز همه‌چیز از یه فکر ساده شروع شد: قهوه تخصصی تازه برشت، بدون ادا و اصول اضافه.' }}
            </p>

            <div class="flex flex-wrap items-center justify-center gap-4 pt-4">
                <a href="#location" class="px-6 py-3.5 bg-[#387780] hover:bg-[#2E626A] text-white font-black rounded-2xl text-xs sm:text-sm transition-all shadow-md flex items-center gap-2">
                    <span>موقعیت مکانی و ساعت کاری</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </a>
                <a href="{{ route('storefront.menu') }}" class="px-6 py-3.5 bg-white hover:bg-slate-50 text-[#19242B] border border-slate-300 font-bold rounded-2xl text-xs sm:text-sm transition-colors shadow-xs">
                    مشاهده منوی آنلاین کافه
                </a>
            </div>
        </div>
    </section>

    <!-- Core Philosophy & Pillars -->
    <section class="py-16 sm:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="text-center space-y-2">
                <span class="text-xs font-bold text-[#387780]">چرا به این کار دل بستیم؟</span>
                <h2 class="text-2xl sm:text-3xl font-black text-[#19242B]">چه چیزهایی برامون مهمه؟</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Pillar 1 -->
                <div class="p-8 rounded-3xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-4 hover:border-[#387780] transition-colors">
                    <div class="w-12 h-12 rounded-2xl bg-teal-50 border border-teal-200 flex items-center justify-center text-[#387780] text-2xl">
                        <svg class="w-6 h-6 inline-block text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    </div>
                    <h3 class="text-lg font-black text-[#19242B]">دانه‌های ۱۰۰٪ عربیکا با روست تازه</h3>
                    <p class="text-xs sm:text-sm text-[#64748B] leading-relaxed">
                        قهوه‌هامون رو از مزارع شناخته‌شده اتیوپی، کلمبیا، گواتمالا و کنیا با گرید تخصصی تهیه می‌کنیم و با پروفایل روست دقیق هفتگی برشته می‌کنیم تا شفافیت و عطر واقعی دانه حفظ بشه.
                    </p>
                </div>

                <!-- Pillar 2 -->
                <div class="p-8 rounded-3xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-4 hover:border-[#387780] transition-colors">
                    <div class="w-12 h-12 rounded-2xl bg-teal-50 border border-teal-200 flex items-center justify-center text-[#387780] text-2xl">
                        <svg class="w-6 h-6 inline-block text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <h3 class="text-lg font-black text-[#19242B]">دستگاه‌های حرفه‌ای و عصاره‌گیری استاندارد</h3>
                    <p class="text-xs sm:text-sm text-[#64748B] leading-relaxed">
                        با دستگاه اسپرسو لامارزوکو و آسیاب‌های دقیق مالکونیگ، هر شات قهوه با دما، وزن و فشار یکنواخت عصاره‌گیری میشه تا فنجانت هر روز همون طعم عالی رو بده.
                    </p>
                </div>

                <!-- Pillar 3 -->
                <div class="p-8 rounded-3xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-4 hover:border-[#387780] transition-colors">
                    <div class="w-12 h-12 rounded-2xl bg-teal-50 border border-teal-200 flex items-center justify-center text-[#387780] text-2xl">
                        <svg class="w-6 h-6 inline-block text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </div>
                    <h3 class="text-lg font-black text-[#19242B]">اشتراک قهوه و احترام به وقت شما</h3>
                    <p class="text-xs sm:text-sm text-[#64748B] leading-relaxed">
                        چه حضوری بیای چه با پیک سفارش بدی، با سیستم اشتراک روزانه و سفارش آنلاین معطل نمی‌شی و قهوه روزانه‌ت همیشه با قیمت به‌صرفه و تازه آماده‌ست.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Ambiance & Workspaces -->
    <section class="py-16 sm:py-20 bg-[#F8FAFC] border-t border-[#E2E8F0]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <span class="text-xs font-bold text-[#387780]">جایی برای کار، گپ‌وگفت و خلوت</span>
                    <h2 class="text-2xl sm:text-4xl font-black text-[#19242B] leading-tight">
                        میزهای دنج، اینترنت پایدار و بوی قهوه تازه
                    </h2>
                    <p class="text-sm text-[#64748B] leading-relaxed">
                        فضای کافه رو جوری چیدیم که اگر لپ‌تاپت همراهته یا کتاب می‌خونی، راحت باشی. اینترنت پرسرعت فیبر، پریز برق کنار اکثر میزها، نور ملایم و موسیقی آروم، بی کافه رو برای کار عمیق و دیدارهای دوستانه دلپذیر کرده.
                    </p>

                    <div class="grid grid-cols-2 gap-4 pt-2">
                        <div class="p-4 rounded-2xl bg-white border border-[#E2E8F0]">
                            <div class="text-[#387780] font-black text-base flex items-center gap-1.5"><svg class="w-5 h-5 inline-block text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/></svg> اینترنت پرسرعت</div>
                            <span class="text-xs text-[#64748B]">فیبر نوری رایگان برای مهمانان</span>
                        </div>
                        <div class="p-4 rounded-2xl bg-white border border-[#E2E8F0]">
                            <div class="text-[#387780] font-black text-base flex items-center gap-1.5"><svg class="w-5 h-5 inline-block text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg> دسترسی برق</div>
                            <span class="text-xs text-[#64748B]">پریز و شارژ کنار بیشتر میزها</span>
                        </div>
                    </div>
                </div>

                <!-- Cafe Visual Card -->
                <div class="p-8 rounded-3xl bg-white border border-[#E2E8F0] shadow-sm space-y-4">
                    <div class="aspect-video w-full rounded-2xl bg-gradient-to-br from-[#19242B] to-[#2E626A] flex flex-col items-center justify-center text-white p-6 text-center space-y-2">
                        <svg class="w-10 h-10 mx-auto text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z M6 1v3 M10 1v3 M14 1v3"/></svg>
                        <span class="text-lg font-black">بی کافه شیراز</span>
                        <span class="text-xs text-teal-200">کافه تخصصی موج سوم و برشته‌کاری</span>
                    </div>
                    <div class="flex items-center justify-between text-xs text-[#64748B] pt-2">
                        <span>انواع نوشیدنی‌های گرم، کلدبرو و دمی</span>
                        <span class="font-bold text-[#387780]">شیرینی‌های روزپخت خانگی</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Location, Hours & Interactive Map Section -->
    <section id="location" class="py-16 sm:py-24 bg-white border-t border-[#E2E8F0]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="text-center space-y-2">
                <span class="text-xs font-bold text-[#387780]">مشتاق دیدارتون هستیم</span>
                <h2 class="text-2xl sm:text-3xl font-black text-[#19242B]">آدرس و ساعت کاری بی کافه</h2>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Info details (5 cols) -->
                <div class="lg:col-span-5 space-y-6">
                    <!-- Address Card -->
                    <div class="p-6 rounded-3xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-3">
                        <div class="flex items-center gap-3 text-[#387780]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <h3 class="text-base font-black text-[#19242B]">نشانی کافه در شیراز</h3>
                        </div>
                        <p class="text-xs sm:text-sm text-[#475569] leading-relaxed">
                            {{ $siteContent->about_address ?? 'شیراز، میدان ارم، ابتدای بلوار دانشجو، کافه تخصصی بی کافه (B Cafe)' }}
                        </p>
                    </div>

                    <!-- Hours Card -->
                    <div class="p-6 rounded-3xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-3">
                        <div class="flex items-center gap-3 text-[#387780]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <h3 class="text-base font-black text-[#19242B]">ساعت کار کافه</h3>
                        </div>
                        <div class="space-y-2 text-xs sm:text-sm text-[#475569]">
                            <div class="flex justify-between border-b border-slate-200 pb-2">
                                <span>ساعات سرویس‌دهی:</span>
                                <span class="font-black text-[#19242B]">{{ $siteContent->footer_hours ?? 'همه‌روزه از ۰۸:۰۰ الی ۲۳:۳۰' }}</span>
                            </div>
                            @if($siteContent->about_instagram)
                            <div class="flex justify-between pt-1">
                                <span>اینستاگرام رسمی:</span>
                                <a href="https://instagram.com/{{ ltrim($siteContent->about_instagram, '@') }}" target="_blank" class="font-black text-[#387780] hover:underline dir-ltr">{{ $siteContent->about_instagram }}</a>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Contact & Navigation Links -->
                    <div class="p-6 rounded-3xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-4">
                        <div class="flex items-center justify-between text-xs sm:text-sm">
                            <span class="text-[#64748B]">شماره تماس و هماهنگی:</span>
                            <a href="tel:{{ preg_replace('/[^0-9]/', '', $siteContent->about_phone ?? '07136280000') }}" class="font-black text-[#387780] hover:underline dir-ltr">
                                {{ $siteContent->about_phone ?? '071-36280000' }}
                            </a>
                        </div>

                        <div class="pt-2 border-t border-slate-200">
                            <span class="text-xs font-bold text-[#19242B] block mb-2">مسیریابی مستقیم با اپلیکیشن‌ها:</span>
                            <div class="grid grid-cols-3 gap-2">
                                <a href="{{ $siteContent->about_map_neshan_url ?: 'https://neshan.org/maps/@' . ($siteContent->about_map_lat ?? 29.6385) . ',' . ($siteContent->about_map_lng ?? 52.5186) . ',16z' }}" target="_blank" class="px-3 py-2 bg-white hover:bg-slate-50 border border-slate-300 rounded-xl text-center text-xs font-black text-blue-600 transition-colors">
                                    نشان
                                </a>
                                <a href="{{ $siteContent->about_map_balad_url ?: 'https://balad.ir/location?lat=' . ($siteContent->about_map_lat ?? 29.6385) . '&lng=' . ($siteContent->about_map_lng ?? 52.5186) }}" target="_blank" class="px-3 py-2 bg-white hover:bg-slate-50 border border-slate-300 rounded-xl text-center text-xs font-black text-emerald-600 transition-colors">
                                    بلد
                                </a>
                                <a href="{{ $siteContent->about_map_google_url ?: 'https://www.google.com/maps/search/?api=1&query=' . ($siteContent->about_map_lat ?? 29.6385) . ',' . ($siteContent->about_map_lng ?? 52.5186) }}" target="_blank" class="px-3 py-2 bg-white hover:bg-slate-50 border border-slate-300 rounded-xl text-center text-xs font-black text-rose-600 transition-colors">
                                    گوگل مپ
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Interactive Leaflet Map (7 cols) -->
                <div class="lg:col-span-7">
                    <div class="rounded-3xl overflow-hidden border border-[#E2E8F0] shadow-sm bg-white p-2">
                        <div id="about-cafe-map" class="h-96 sm:h-[460px] w-full rounded-2xl z-10"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bottom CTA Bar -->
    <section class="py-12 bg-gradient-to-r from-[#19242B] to-[#2E626A] text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-6 text-center sm:text-start">
            <div class="space-y-1">
                <h3 class="text-xl sm:text-2xl font-black">هنوز قهوه بی کافه رو امتحان نکردی؟</h3>
                <p class="text-xs sm:text-sm text-teal-200">منوی آنلاین رو ببین یا با اشتراک ماهانه، هر روز قهوه‌ت رو با تخفیف عالی بنوش.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('storefront.menu') }}" class="px-6 py-3 bg-white text-[#19242B] hover:bg-slate-100 font-black rounded-xl text-xs sm:text-sm transition-colors shadow-sm">
                    سفارش از منو
                </a>
                <a href="{{ route('storefront.subscriptions.index') }}" class="px-6 py-3 bg-[#387780] hover:bg-[#326971] text-white font-black rounded-xl text-xs sm:text-sm transition-colors border border-teal-400/30">
                    خرید اشتراک
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <x-storefront-footer :siteContent="$siteContent" />

    <!-- Leaflet JS for Map -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cafeLat = {{ (float) ($siteContent->about_map_lat ?: 29.6385) }};
            const cafeLng = {{ (float) ($siteContent->about_map_lng ?: 52.5186) }};

            const map = L.map('about-cafe-map', {
                scrollWheelZoom: false
            }).setView([cafeLat, cafeLng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            const marker = L.marker([cafeLat, cafeLng]).addTo(map);
            marker.bindPopup('<div style="font-family: inherit; font-size: 13px; font-weight: bold; text-align: center; padding: 4px;"><svg class="w-4 h-4 inline-block text-[#387780]" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="vertical-align: middle; margin-left: 2px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z M6 1v3 M10 1v3 M14 1v3"/></svg> بی کافه (B Cafe)<br><span style="font-size: 11px; font-weight: normal; color: #64748B;">{{ addslashes($siteContent->about_address ?? 'شیراز') }}</span></div>').openPopup();
        });
    </script>
</body>
</html>
