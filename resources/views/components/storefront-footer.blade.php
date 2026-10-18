@php
    $siteContent = $siteContent ?? \App\Models\SiteContentSetting::getForCafe($cafe->id ?? 1);
@endphp

<!-- Unified Global Karasu B Cafe Footer -->
<footer id="footer-contact" class="mt-20 border-t border-slate-200/80 dark:border-slate-800 bg-white dark:bg-[#141D22] pt-12 pb-16 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 lg:gap-10">
            <!-- Brand & Bio -->
            <div class="md:col-span-2 space-y-4">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.jpeg') }}" 
                         alt="بی کافه" 
                         class="w-12 h-12 rounded-2xl object-cover border border-slate-200 dark:border-slate-700 shadow-xs">
                    <div>
                        <span class="text-base sm:text-lg font-black text-[#19242B] dark:text-slate-100 block">بی کافه (B Cafe)</span>
                        <span class="text-xs text-[#387780] dark:text-teal-400 font-bold">شیراز • بار تخصصی قهوه و منوی روز</span>
                    </div>
                </div>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed font-medium max-w-md">
                    {{ $siteContent->footer_text ?: 'بی کافه، فضایی تخصصی برای علاقه‌مندان به عصاره‌گیری استاندارد، قهوه‌های تک‌خاستگاه و دم‌آوری حرفه‌ای در شهر شیراز. ارائه سرو حضوری با کیفیت بالا و تحویل با پیک اختصاصی در سراسر شهر.' }}
                </p>
                <div class="flex items-center gap-3 pt-2">
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300">ما را دنبال کنید:</span>
                    <a href="{{ $siteContent->instagram_url ?: 'https://instagram.com' }}" 
                       target="_blank" 
                       rel="noopener noreferrer" 
                       class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-[#387780] hover:text-white dark:bg-slate-800 dark:hover:bg-teal-500 dark:hover:text-[#19242B] text-slate-700 dark:text-slate-300 flex items-center justify-center transition-colors shadow-2xs"
                       aria-label="صفحه اینستاگرام بی کافه">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                </div>
            </div>

            <!-- Shiraz Location & Hours -->
            <div class="space-y-3">
                <h4 class="text-xs font-black text-[#19242B] dark:text-slate-200 border-b border-slate-200 dark:border-slate-800 pb-2">اطلاعات شعبه شیراز</h4>
                <div class="space-y-2.5 text-xs text-slate-600 dark:text-slate-400 font-medium">
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-[#387780] dark:text-teal-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        <span>{{ $siteContent->address ?: 'شیراز، میدان ارم، ابتدای بلوار دانشجو' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#387780] dark:text-teal-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>ساعات سرویس‌دهی: {{ $siteContent->opening_hours ?: '۸:۰۰ الی ۲۳:۳۰' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#387780] dark:text-teal-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span>تلفن تماس: {{ $siteContent->contact_phone ?: '۰۷۱-۳۶۲۸۰۰۰۰' }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Navigation Links -->
            <div class="space-y-3">
                <h4 class="text-xs font-black text-[#19242B] dark:text-slate-200 border-b border-slate-200 dark:border-slate-800 pb-2">دسترسی سریع</h4>
                <ul class="space-y-2 text-xs text-slate-600 dark:text-slate-400 font-bold">
                    <li><a href="{{ route('storefront.menu') }}" class="hover:text-[#387780] dark:hover:text-teal-400 transition-colors">منوی آنلاین و سفارش</a></li>
                    <li><a href="{{ route('storefront.subscriptions.index') }}" class="hover:text-[#387780] dark:hover:text-teal-400 transition-colors">باشگاه اشتراک روزانه</a></li>
                    <li><a href="{{ route('storefront.about') }}" class="hover:text-[#387780] dark:hover:text-teal-400 transition-colors">درباره ما و شعبه شیراز</a></li>
                    <li><a href="{{ route('customer.login') }}" class="hover:text-[#387780] dark:hover:text-teal-400 transition-colors">ورود به حساب کاربری</a></li>
                    <li><a href="{{ route('cart.index') }}" class="hover:text-[#387780] dark:hover:text-teal-400 transition-colors">سبد خرید و تسویه</a></li>
                </ul>
            </div>
        </div>

        <div class="mt-12 pt-6 border-t border-slate-200/80 dark:border-slate-800 flex flex-wrap items-center justify-between gap-4 text-[11px] text-slate-500 dark:text-slate-400 font-medium">
            <div>تمامی حقوق این وب‌سایت متعلق به کافه تخصصی بی (B Cafe) شیراز است.</div>
            <div>طراحی و توسعه توسط امیرمحمد بهارلو</div>
        </div>
    </div>
</footer>
