@extends('layouts.app')

@section('title', 'تنظیمات و طراحی فاکتور حرارتی')
@section('header_title', 'شخصی‌سازی فاکتور و فیش پرینتر حرارتی (۸۰mm / ۵۸mm)')

@section('content')
<div class="space-y-6" x-data="receiptDesigner()">

    <!-- Header Description Card -->
    <div class="bg-white border border-[#E2E8F0] p-6 rounded-3xl space-y-2 shadow-xs">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-base font-extrabold text-[#19242B] flex items-center gap-2">
                <svg class="w-5 h-5 text-[#387780]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>شخصی‌سازی کامل فاکتور و فیش چاپگر حرارتی</span>
            </h2>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 bg-teal-50 text-[#387780] text-xs font-black rounded-xl border border-teal-200">الگوی استاندارد رستورانی زعفران</span>
                <button type="button" @click="testPrint()" class="px-4 py-2 bg-[#387780] hover:bg-[#2E626A] text-white text-xs font-bold rounded-xl flex items-center gap-1.5 shadow-xs transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>چاپ آزمایشی فاکتور</span>
                </button>
            </div>
        </div>
        <p class="text-xs text-[#64748B] leading-relaxed">
            شما می‌توانید به طور کامل مشخص کنید چه مواردی روی فیش چاپ شود، فونت و عرض کاغذ پرینتر را تعیین کنید و پیش‌نمایش زنده را به صورت همزمان مشاهده فرمایید.
        </p>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold space-y-1">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>خطا در اعتبارسنجی فرم تنظیمات فاکتور:</span>
            </div>
            <ul class="list-disc list-inside ps-7 font-medium text-rose-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- Form Column (7 Cols) -->
        <div class="lg:col-span-7 space-y-6">
            <form method="POST" action="{{ route('settings.receipt.update') }}" class="space-y-6">
                @csrf

                <!-- Section 1: Paper Width & Font Styling -->
                <div class="bg-white border border-[#E2E8F0] p-6 rounded-3xl space-y-4 shadow-xs">
                    <div class="flex items-center gap-3 border-b border-[#E2E8F0] pb-3">
                        <div class="w-8 h-8 rounded-xl bg-teal-50 text-[#387780] flex items-center justify-center font-black">۱</div>
                        <div>
                            <h3 class="text-xs font-black text-[#19242B]">قالب، کاغذ و تایپوگرافی حرفه‌ای</h3>
                            <span class="text-[11px] text-[#64748B]">انتخاب فونت، سایز و ابعاد رول کاغذ</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-[#19242B] mb-1.5">عرض رول حرارتی</label>
                            <select name="paper_width" x-model="form.paper_width" class="w-full px-3 py-2.5 rounded-xl bg-white border border-[#CBD5E1] text-xs font-bold focus:border-[#387780]">
                                <option value="80mm" {{ old('paper_width', $setting->paper_width) === '80mm' ? 'selected' : '' }}>۸۰ میلی‌متر (رول فیش استاندارد)</option>
                                <option value="58mm" {{ old('paper_width', $setting->paper_width) === '58mm' ? 'selected' : '' }}>۵۸ میلی‌متر (رول کوچک)</option>
                                <option value="a4" {{ old('paper_width', $setting->paper_width) === 'a4' ? 'selected' : '' }}>کاغذ A4 / A5 (پرینتر عادی اداری - پر کردن کل کاغذ)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[#19242B] mb-1.5">فونت فاکتور</label>
                            <select name="font_family" x-model="form.font_family" class="w-full px-3 py-2.5 rounded-xl bg-white border border-[#CBD5E1] text-xs font-bold focus:border-[#387780]">
                                <option value="vazirmatn" {{ old('font_family', $setting->font_family) === 'vazirmatn' ? 'selected' : '' }}>وزیرمتن (Vazirmatn - لوکال و آفلاین)</option>
                                <option value="post" {{ old('font_family', $setting->font_family) === 'post' ? 'selected' : '' }}>پست (Post Font - فونت اختصاصی بی کافه)</option>
                                <option value="shabnam" {{ old('font_family', $setting->font_family) === 'shabnam' ? 'selected' : '' }}>شبنم (Shabnam)</option>
                                <option value="iransans" {{ old('font_family', $setting->font_family) === 'iransans' ? 'selected' : '' }}>ایران‌سنس (IRANSans)</option>
                                <option value="b_titr" {{ old('font_family', $setting->font_family) === 'b_titr' ? 'selected' : '' }}>بی‌تیتر (B Titr - پررنگ)</option>
                                <option value="courier" {{ old('font_family', $setting->font_family) === 'courier' ? 'selected' : '' }}>فیش حرارتی منواسپیس (Courier)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[#19242B] mb-1.5">اندازه متن</label>
                            <select name="font_size" x-model="form.font_size" class="w-full px-3 py-2.5 rounded-xl bg-white border border-[#CBD5E1] text-xs font-bold focus:border-[#387780]">
                                <option value="compact" {{ old('font_size', $setting->font_size) === 'compact' ? 'selected' : '' }}>کوچک و فشرده (۱۰px)</option>
                                <option value="medium" {{ old('font_size', $setting->font_size) === 'medium' ? 'selected' : '' }}>متوسط و بهینه (۱۱px)</option>
                                <option value="large" {{ old('font_size', $setting->font_size) === 'large' ? 'selected' : '' }}>درشت و خوانا (۱۲px)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Header Information -->
                <div class="bg-white border border-[#E2E8F0] p-6 rounded-3xl space-y-4 shadow-xs">
                    <div class="flex items-center gap-3 border-b border-[#E2E8F0] pb-3">
                        <div class="w-8 h-8 rounded-xl bg-teal-50 text-[#387780] flex items-center justify-center font-black">۲</div>
                        <div>
                            <h3 class="text-xs font-black text-[#19242B]">سربرگ و عنوان کافه</h3>
                            <span class="text-[11px] text-[#64748B]">متن‌های بالای فیش و لوگو</span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-[#19242B] mb-1">نام یا عنوان کافه</label>
                                <input type="text" name="header_title" value="{{ old('header_title', $setting->header_title) }}" x-model="form.header_title" class="w-full px-3 py-2.5 rounded-xl bg-white border border-[#CBD5E1] text-xs font-bold">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-[#19242B] mb-1">عنوان نوع فاکتور</label>
                                <input type="text" name="invoice_title" value="{{ old('invoice_title', $setting->invoice_title) }}" x-model="form.invoice_title" class="w-full px-3 py-2.5 rounded-xl bg-white border border-[#CBD5E1] text-xs font-bold">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[#19242B] mb-1">زیرنویس سربرگ (شعبه و معرفی)</label>
                            <input type="text" name="header_subtitle" value="{{ old('header_subtitle', $setting->header_subtitle) }}" x-model="form.header_subtitle" class="w-full px-3 py-2.5 rounded-xl bg-white border border-[#CBD5E1] text-xs">
                        </div>

                        <div class="pt-2 border-t border-slate-100 flex items-center gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="show_logo" value="1" {{ old('show_logo', $setting->show_logo) ? 'checked' : '' }} x-model="form.show_logo" class="w-4 h-4 rounded text-[#387780] focus:ring-[#387780]">
                                <span class="text-xs font-bold text-[#19242B]">نمایش لوگوی کافه در ابتدای فیش</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Customer & Order Meta Information -->
                <div class="bg-white border border-[#E2E8F0] p-6 rounded-3xl space-y-4 shadow-xs">
                    <div class="flex items-center gap-3 border-b border-[#E2E8F0] pb-3">
                        <div class="w-8 h-8 rounded-xl bg-teal-50 text-[#387780] flex items-center justify-center font-black">۳</div>
                        <div>
                            <h3 class="text-xs font-black text-[#19242B]">مشخصات سفارش و مشتری</h3>
                            <span class="text-[11px] text-[#64748B]">فیلدهای اطلاعاتی قابل چاپ</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <label class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer">
                            <input type="checkbox" name="show_order_number" value="1" {{ old('show_order_number', $setting->show_order_number) ? 'checked' : '' }} x-model="form.show_order_number" class="w-4 h-4 rounded text-[#387780]">
                            <span class="font-bold text-[#19242B]">شماره فاکتور / سفارش (#BC-...)</span>
                        </label>

                        <label class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer">
                            <input type="checkbox" name="show_date_time" value="1" {{ old('show_date_time', $setting->show_date_time) ? 'checked' : '' }} x-model="form.show_date_time" class="w-4 h-4 rounded text-[#387780]">
                            <span class="font-bold text-[#19242B]">تاریخ و ساعت دقیق شمسی</span>
                        </label>

                        <label class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer">
                            <input type="checkbox" name="show_cashier_name" value="1" {{ old('show_cashier_name', $setting->show_cashier_name) ? 'checked' : '' }} x-model="form.show_cashier_name" class="w-4 h-4 rounded text-[#387780]">
                            <span class="font-bold text-[#19242B]">نام صندوق‌دار / ثبت‌کننده</span>
                        </label>

                        <label class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer">
                            <input type="checkbox" name="show_customer_name" value="1" {{ old('show_customer_name', $setting->show_customer_name) ? 'checked' : '' }} x-model="form.show_customer_name" class="w-4 h-4 rounded text-[#387780]">
                            <span class="font-bold text-[#19242B]">نام مشتری</span>
                        </label>

                        <label class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer">
                            <input type="checkbox" name="show_customer_phone" value="1" {{ old('show_customer_phone', $setting->show_customer_phone) ? 'checked' : '' }} x-model="form.show_customer_phone" class="w-4 h-4 rounded text-[#387780]">
                            <span class="font-bold text-[#19242B]">شماره موبایل مشتری</span>
                        </label>

                        <label class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer">
                            <input type="checkbox" name="show_customer_club_info" value="1" {{ old('show_customer_club_info', $setting->show_customer_club_info) ? 'checked' : '' }} x-model="form.show_customer_club_info" class="w-4 h-4 rounded text-[#387780]">
                            <span class="font-bold text-[#19242B]">اطلاعات باشگاه و اعتبار کیف‌پول</span>
                        </label>

                        <label class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer">
                            <input type="checkbox" name="show_order_type" value="1" {{ old('show_order_type', $setting->show_order_type) ? 'checked' : '' }} x-model="form.show_order_type" class="w-4 h-4 rounded text-[#387780]">
                            <span class="font-bold text-[#19242B]">نوع سفارش (میز سالن / پیک / بیرون‌بر)</span>
                        </label>

                        <label class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer">
                            <input type="checkbox" name="show_delivery_address" value="1" {{ old('show_delivery_address', $setting->show_delivery_address) ? 'checked' : '' }} x-model="form.show_delivery_address" class="w-4 h-4 rounded text-[#387780]">
                            <span class="font-bold text-[#19242B]">آدرس مقصد سفارشات ارسالی</span>
                        </label>
                    </div>
                </div>

                <!-- Section 4: Items & Calculations -->
                <div class="bg-white border border-[#E2E8F0] p-6 rounded-3xl space-y-4 shadow-xs">
                    <div class="flex items-center gap-3 border-b border-[#E2E8F0] pb-3">
                        <div class="w-8 h-8 rounded-xl bg-teal-50 text-[#387780] flex items-center justify-center font-black">۴</div>
                        <div>
                            <h3 class="text-xs font-black text-[#19242B]">جدول اقلام، ستون‌ها و محاسبات مالی</h3>
                            <span class="text-[11px] text-[#64748B]">ستون‌های جدول و مبالغ فاکتور</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <label class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer">
                            <input type="checkbox" name="show_item_row_number" value="1" {{ old('show_item_row_number', $setting->show_item_row_number) ? 'checked' : '' }} x-model="form.show_item_row_number" class="w-4 h-4 rounded text-[#387780]">
                            <span class="font-bold text-[#19242B]">ستون ردیف اقلام (#)</span>
                        </label>

                        <label class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer">
                            <input type="checkbox" name="show_item_unit_price" value="1" {{ old('show_item_unit_price', $setting->show_item_unit_price) ? 'checked' : '' }} x-model="form.show_item_unit_price" class="w-4 h-4 rounded text-[#387780]">
                            <span class="font-bold text-[#19242B]">ستون قیمت واحد (فی)</span>
                        </label>

                        <label class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer">
                            <input type="checkbox" name="show_item_notes" value="1" {{ old('show_item_notes', $setting->show_item_notes) ? 'checked' : '' }} x-model="form.show_item_notes" class="w-4 h-4 rounded text-[#387780]">
                            <span class="font-bold text-[#19242B]">یادداشت اختصاصی هر کالا</span>
                        </label>

                        <label class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer">
                            <input type="checkbox" name="show_subtotal" value="1" {{ old('show_subtotal', $setting->show_subtotal) ? 'checked' : '' }} x-model="form.show_subtotal" class="w-4 h-4 rounded text-[#387780]">
                            <span class="font-bold text-[#19242B]">سطر جمع کل اقلام</span>
                        </label>

                        <label class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer">
                            <input type="checkbox" name="show_discount" value="1" {{ old('show_discount', $setting->show_discount) ? 'checked' : '' }} x-model="form.show_discount" class="w-4 h-4 rounded text-[#387780]">
                            <span class="font-bold text-[#19242B]">سطر تخفیف فاکتور</span>
                        </label>

                        <label class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer">
                            <input type="checkbox" name="show_delivery_fee" value="1" {{ old('show_delivery_fee', $setting->show_delivery_fee) ? 'checked' : '' }} x-model="form.show_delivery_fee" class="w-4 h-4 rounded text-[#387780]">
                            <span class="font-bold text-[#19242B]">سطر هزینه ارسال پیک</span>
                        </label>

                        <label class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer">
                            <input type="checkbox" name="show_amount_in_words" value="1" {{ old('show_amount_in_words', $setting->show_amount_in_words) ? 'checked' : '' }} x-model="form.show_amount_in_words" class="w-4 h-4 rounded text-[#387780]">
                            <span class="font-bold text-[#19242B]">مبلغ نهایی به حروف فارسی</span>
                        </label>

                        <label class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer">
                            <input type="checkbox" name="show_tax" value="1" {{ old('show_tax', $setting->show_tax) ? 'checked' : '' }} x-model="form.show_tax" class="w-4 h-4 rounded text-[#387780]">
                            <span class="font-bold text-[#19242B]">محاسبه مالیات بر ارزش افزوده</span>
                        </label>
                    </div>

                    <div x-show="form.show_tax" class="pt-3 border-t border-slate-100">
                        <label class="block text-xs font-bold text-[#19242B] mb-1">درصد مالیات بر ارزش افزوده (VAT)</label>
                        <input type="number" step="0.5" min="0" max="100" name="tax_percent" value="{{ old('tax_percent', $setting->tax_percent) }}" x-model="form.tax_percent" class="w-48 px-3 py-2 rounded-xl bg-white border border-[#CBD5E1] text-xs font-bold tabular-nums">
                    </div>
                </div>

                <!-- Section 5: Payment, Barcode & QR Code -->
                <div class="bg-white border border-[#E2E8F0] p-6 rounded-3xl space-y-4 shadow-xs">
                    <div class="flex items-center gap-3 border-b border-[#E2E8F0] pb-3">
                        <div class="w-8 h-8 rounded-xl bg-teal-50 text-[#387780] flex items-center justify-center font-black">۵</div>
                        <div>
                            <h3 class="text-xs font-black text-[#19242B]">تسویه، بارکد و کیو‌آر کد (QR Code)</h3>
                            <span class="text-[11px] text-[#64748B]">اطلاعات پرداخت و پیگیری دیجیتال</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <label class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer">
                            <input type="checkbox" name="show_payment_method" value="1" {{ old('show_payment_method', $setting->show_payment_method) ? 'checked' : '' }} x-model="form.show_payment_method" class="w-4 h-4 rounded text-[#387780]">
                            <span class="font-bold text-[#19242B]">نمایش روش تسویه (پوز، نقد، اینترنتی)</span>
                        </label>

                        <label class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer">
                            <input type="checkbox" name="show_payment_status" value="1" {{ old('show_payment_status', $setting->show_payment_status) ? 'checked' : '' }} x-model="form.show_payment_status" class="w-4 h-4 rounded text-[#387780]">
                            <span class="font-bold text-[#19242B]">نمایش وضعیت تسویه شده / معلق</span>
                        </label>

                        <label class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer">
                            <input type="checkbox" name="show_barcode" value="1" {{ old('show_barcode', $setting->show_barcode) ? 'checked' : '' }} x-model="form.show_barcode" class="w-4 h-4 rounded text-[#387780]">
                            <span class="font-bold text-[#19242B]">نمایش بارکد استاندارد سفارش</span>
                        </label>

                        <label class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer">
                            <input type="checkbox" name="show_qr_code" value="1" {{ old('show_qr_code', $setting->show_qr_code) ? 'checked' : '' }} x-model="form.show_qr_code" class="w-4 h-4 rounded text-[#387780]">
                            <span class="font-bold text-[#19242B]">نمایش QR Code اختصاصی</span>
                        </label>
                    </div>

                    <div x-show="form.show_qr_code" class="pt-3 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-[#19242B] mb-1">نوع لینک QR Code</label>
                            <select name="qr_code_type" x-model="form.qr_code_type" class="w-full px-3 py-2 rounded-xl bg-white border border-[#CBD5E1] text-xs font-bold">
                                <option value="order_url" {{ old('qr_code_type', $setting->qr_code_type) === 'order_url' ? 'selected' : '' }}>لینک رهگیری آنلاین سفارش</option>
                                <option value="instagram" {{ old('qr_code_type', $setting->qr_code_type) === 'instagram' ? 'selected' : '' }}>صفحه اینستاگرام کافه</option>
                                <option value="website" {{ old('qr_code_type', $setting->qr_code_type) === 'website' ? 'selected' : '' }}>منوی آنلاین و وب‌سایت کافه</option>
                                <option value="custom" {{ old('qr_code_type', $setting->qr_code_type) === 'custom' ? 'selected' : '' }}>آدرس دلخواه سفارشی</option>
                            </select>
                        </div>
                        <div x-show="form.qr_code_type === 'custom'">
                            <label class="block text-xs font-bold text-[#19242B] mb-1">آدرس اینترنتی سفارشی</label>
                            <input type="text" name="custom_qr_url" value="{{ old('custom_qr_url', $setting->custom_qr_url) }}" x-model="form.custom_qr_url" placeholder="https://..." class="w-full px-3 py-2 rounded-xl bg-white border border-[#CBD5E1] text-xs dir-ltr">
                        </div>
                    </div>
                </div>

                <!-- Section 6: Footer, Wi-Fi, Instagram & Auto-Print -->
                <div class="bg-white border border-[#E2E8F0] p-6 rounded-3xl space-y-4 shadow-xs">
                    <div class="flex items-center gap-3 border-b border-[#E2E8F0] pb-3">
                        <div class="w-8 h-8 rounded-xl bg-teal-50 text-[#387780] flex items-center justify-center font-black">۶</div>
                        <div>
                            <h3 class="text-xs font-black text-[#19242B]">پاورقی، وای‌فای، اینستاگرام و پرینت خودکار</h3>
                            <span class="text-[11px] text-[#64748B]">پیام تشکر و مشخصات اتصال مهمانان</span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-[#19242B] mb-1">پیام پایانی و تشکر فاکتور</label>
                            <textarea name="footer_message" x-model="form.footer_message" rows="3" class="w-full px-3 py-2.5 rounded-xl bg-white border border-[#CBD5E1] text-xs leading-relaxed">{{ old('footer_message', $setting->footer_message) }}</textarea>
                        </div>

                        <div class="p-4 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-3">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="show_wifi" value="1" {{ old('show_wifi', $setting->show_wifi) ? 'checked' : '' }} x-model="form.show_wifi" class="w-4 h-4 rounded text-[#387780]">
                                <span class="text-xs font-bold text-[#19242B]">چاپ مشخصات شبکه اینترنت وای‌فای مهمان کافه</span>
                            </label>

                            <div x-show="form.show_wifi" class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                                <div>
                                    <label class="block text-[11px] font-bold text-[#64748B] mb-1">نام شبکه (SSID)</label>
                                    <input type="text" name="wifi_ssid" value="{{ old('wifi_ssid', $setting->wifi_ssid) }}" x-model="form.wifi_ssid" class="w-full px-3 py-2 rounded-xl bg-white border border-[#CBD5E1] text-xs dir-ltr">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-[#64748B] mb-1">رمز عبور وای‌فای</label>
                                    <input type="text" name="wifi_password" value="{{ old('wifi_password', $setting->wifi_password) }}" x-model="form.wifi_password" class="w-full px-3 py-2 rounded-xl bg-white border border-[#CBD5E1] text-xs dir-ltr">
                                </div>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-3">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="show_instagram" value="1" {{ old('show_instagram', $setting->show_instagram) ? 'checked' : '' }} x-model="form.show_instagram" class="w-4 h-4 rounded text-[#387780]">
                                <span class="text-xs font-bold text-[#19242B]">چاپ آی‌دی صفحه اینستاگرام</span>
                            </label>

                            <div x-show="form.show_instagram" class="pt-1">
                                <input type="text" name="instagram_handle" value="{{ old('instagram_handle', $setting->instagram_handle) }}" x-model="form.instagram_handle" placeholder="@bcafe.shiraz" class="w-full sm:w-64 px-3 py-2 rounded-xl bg-white border border-[#CBD5E1] text-xs dir-ltr">
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-teal-50/50 border border-teal-100 flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold text-[#19242B] block">چاپ خودکار (Auto-Print)</span>
                                <span class="text-[11px] text-[#64748B]">با باز شدن پنجره فاکتور، پنجره پرینت ویندوز بلافاصله فعال شود</span>
                            </div>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="auto_print" value="1" {{ old('auto_print', $setting->auto_print) ? 'checked' : '' }} x-model="form.auto_print" class="w-5 h-5 rounded text-[#387780]">
                                <span class="text-xs font-bold text-[#387780]">فعال</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="sticky bottom-4 z-20">
                    <button type="submit" class="w-full py-4 bg-[#387780] hover:bg-[#2E626A] text-white font-black rounded-2xl text-sm shadow-md transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>ذخیره تغییرات و اعمال در چاپگر فاکتور</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Live Thermal Preview Column (5 Cols) -->
        <div class="lg:col-span-5 sticky top-24 space-y-4">
            <div class="bg-white border border-[#E2E8F0] p-4 rounded-3xl shadow-xs flex items-center justify-between">
                <span class="text-xs font-bold text-[#19242B] flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>پیش‌نمایش زنده فیش حرارتی</span>
                </span>
                <span class="text-[11px] font-mono font-bold text-slate-500" x-text="form.paper_width"></span>
            </div>

            <!-- Simulated Thermal Paper Roll -->
            <div class="bg-slate-200 p-4 sm:p-6 rounded-3xl border border-slate-300 flex justify-center overflow-x-auto shadow-inner">
                
                <div id="receipt-preview-box"
                     class="bg-white text-black p-4 shadow-xl border-y-4 border-dashed border-slate-400 select-none transition-all duration-200"
                     :style="{
                         width: form.paper_width === '58mm' ? '58mm' : '80mm',
                         fontFamily: getFontFamily(),
                         fontSize: getFontSize()
                     }">

                    <!-- Logo -->
                    <template x-if="form.show_logo">
                        <div class="text-center mb-2">
                            <img src="{{ asset('images/logo.jpeg') }}" class="w-12 h-12 mx-auto rounded-full object-cover border border-black grayscale contrast-200">
                        </div>
                    </template>

                    <!-- Header Titles -->
                    <div class="text-center space-y-1">
                        <div class="font-black text-sm leading-tight" x-text="form.header_title"></div>
                        <template x-if="form.header_subtitle">
                            <div class="text-[10px] text-gray-700" x-text="form.header_subtitle"></div>
                        </template>
                        <div class="inline-block border border-black px-2 py-0.5 text-[10px] font-black rounded mt-1" x-text="form.invoice_title"></div>
                    </div>

                    <!-- Dashed Divider -->
                    <div class="border-t border-dashed border-black my-2"></div>

                    <!-- Meta Information -->
                    <div class="text-[10px] space-y-0.5">
                        <template x-if="form.show_order_number">
                            <div class="flex justify-between">
                                <span>شماره سفارش:</span>
                                <span class="font-black font-mono">#BC-98421</span>
                            </div>
                        </template>

                        <template x-if="form.show_date_time">
                            <div class="flex justify-between">
                                <span>تاریخ و ساعت:</span>
                                <span class="tabular-nums">۱۴۰۵/۰۷/۰۸ - ۲۱:۱۵</span>
                            </div>
                        </template>

                        <template x-if="form.show_cashier_name">
                            <div class="flex justify-between">
                                <span>صندوق‌دار:</span>
                                <span>{{ auth()->user()->name ?? 'امیر محمد بهارلو' }}</span>
                            </div>
                        </template>

                        <template x-if="form.show_customer_name">
                            <div class="flex justify-between">
                                <span>مشتری:</span>
                                <span class="font-bold">سارا محمدی</span>
                            </div>
                        </template>

                        <template x-if="form.show_customer_phone">
                            <div class="flex justify-between">
                                <span>تلفن:</span>
                                <span class="font-mono dir-ltr">09171234567</span>
                            </div>
                        </template>

                        <template x-if="form.show_customer_club_info">
                            <div class="flex justify-between text-gray-700">
                                <span>باشگاه مشتریان:</span>
                                <span>عضو طلایی (۵۰,۰۰۰ ت اعتبار)</span>
                            </div>
                        </template>

                        <template x-if="form.show_order_type">
                            <div class="flex justify-between font-bold">
                                <span>نوع سفارش:</span>
                                <span>سرو در سالن (میز شماره ۴)</span>
                            </div>
                        </template>

                        <template x-if="form.show_delivery_address">
                            <div class="text-start pt-0.5">
                                <span class="block text-gray-700">آدرس:</span>
                                <span class="font-bold">شیراز، میدان ارم، کوچه ۵، پلاک ۱۲</span>
                            </div>
                        </template>
                    </div>

                    <!-- Items Table -->
                    <div class="my-2 border-t border-b border-black py-1">
                        <table class="w-full text-start text-[10px]">
                            <thead>
                                <tr class="border-b border-black font-black">
                                    <template x-if="form.show_item_row_number"><th class="text-center w-5">#</th></template>
                                    <th class="text-start">شرح کالا</th>
                                    <th class="text-center w-8">تعداد</th>
                                    <template x-if="form.show_item_unit_price"><th class="text-end">فی</th></template>
                                    <th class="text-end">مبلغ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-dotted divide-gray-400">
                                <tr>
                                    <template x-if="form.show_item_row_number"><td class="text-center">۱</td></template>
                                    <td class="font-bold">
                                        اسپرسو دابل تخصصی
                                        <template x-if="form.show_item_notes"><div class="text-[9px] text-gray-600 font-normal">(دانه کلمبیا تک‌خاستگاه)</div></template>
                                    </td>
                                    <td class="text-center font-bold">۲</td>
                                    <template x-if="form.show_item_unit_price"><td class="text-end tabular-nums">۶۵,۰۰۰</td></template>
                                    <td class="text-end font-bold tabular-nums">۱۳۰,۰۰۰</td>
                                </tr>
                                <tr>
                                    <template x-if="form.show_item_row_number"><td class="text-center">۲</td></template>
                                    <td class="font-bold">
                                        چیزکیک سن سباستین
                                        <template x-if="form.show_item_notes"><div class="text-[9px] text-gray-600 font-normal">(با سس شکلات بلژیکی)</div></template>
                                    </td>
                                    <td class="text-center font-bold">۱</td>
                                    <template x-if="form.show_item_unit_price"><td class="text-end tabular-nums">۱۱۰,۰۰۰</td></template>
                                    <td class="text-end font-bold tabular-nums">۱۱۰,۰۰۰</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Totals -->
                    <div class="text-[10px] space-y-1">
                        <template x-if="form.show_subtotal">
                            <div class="flex justify-between">
                                <span>جمع اقلام:</span>
                                <span class="font-bold tabular-nums">۲۴۰,۰۰۰ تومان</span>
                            </div>
                        </template>

                        <template x-if="form.show_discount">
                            <div class="flex justify-between text-gray-800">
                                <span>تخفیف باشگاه:</span>
                                <span class="font-bold tabular-nums">- ۲۰,۰۰۰ تومان</span>
                            </div>
                        </template>

                        <template x-if="form.show_delivery_fee">
                            <div class="flex justify-between">
                                <span>هزینه پیک:</span>
                                <span class="font-bold tabular-nums">۰ تومان (رایگان)</span>
                            </div>
                        </template>

                        <template x-if="form.show_tax">
                            <div class="flex justify-between">
                                <span>مالیات (<span x-text="form.tax_percent"></span>٪):</span>
                                <span class="font-bold tabular-nums">۰ تومان</span>
                            </div>
                        </template>

                        <div class="flex justify-between border-t border-b border-black py-1 font-black text-xs">
                            <span>مبلغ قابل پرداخت:</span>
                            <span class="tabular-nums">۲۲۰,۰۰۰ تومان</span>
                        </div>
                    </div>

                    <!-- Amount In Words -->
                    <template x-if="form.show_amount_in_words">
                        <div class="bg-gray-100 text-center font-bold text-[9px] p-1 my-1.5 rounded border border-gray-300">
                            دویست و بیست هزار تومان
                        </div>
                    </template>

                    <!-- Settlement Meta -->
                    <div class="text-[10px] space-y-0.5 pt-1">
                        <template x-if="form.show_payment_method">
                            <div class="flex justify-between">
                                <span>روش پرداخت:</span>
                                <span class="font-bold">دستگاه پوز بانکی / نقدی</span>
                            </div>
                        </template>

                        <template x-if="form.show_payment_status">
                            <div class="flex justify-between font-bold">
                                <span>وضعیت تسویه:</span>
                                <span>تسویه شده در صندوق</span>
                            </div>
                        </template>
                    </div>

                    <!-- Barcode -->
                    <template x-if="form.show_barcode">
                        <div class="text-center font-mono tracking-widest text-[9px] my-2 font-bold">
                            * BC-98421 *
                        </div>
                    </template>

                    <!-- QR Code Mock -->
                    <template x-if="form.show_qr_code">
                        <div class="text-center my-2 space-y-1">
                            <div class="w-14 h-14 mx-auto border-2 border-black flex items-center justify-center font-mono text-[8px] bg-white">
                                [ QR Code ]
                            </div>
                            <span class="text-[8px] text-gray-600 block">اسکن جهت پیگیری و امتیاز</span>
                        </div>
                    </template>

                    <!-- Wi-Fi & Social Details -->
                    <template x-if="form.show_wifi">
                        <div class="border-t border-dotted border-gray-400 pt-1 mt-1 text-[9px] text-center space-y-0.5">
                            <div><svg class="w-4 h-4 inline-block align-middle ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/></svg> اینترنت کافه: <span class="font-mono font-bold" x-text="form.wifi_ssid"></span></div>
                            <div>رمز: <span class="font-mono font-bold" x-text="form.wifi_password"></span></div>
                        </div>
                    </template>

                    <template x-if="form.show_instagram">
                        <div class="text-[9px] text-center pt-0.5">
                            <svg class="w-4 h-4 inline-block align-middle ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg> اینستاگرام: <span class="font-mono font-bold" x-text="form.instagram_handle"></span>
                        </div>
                    </template>

                    <!-- Footer Note -->
                    <div class="border-t-2 border-black pt-2 mt-2 text-center text-[9px] text-gray-800 whitespace-pre-line leading-relaxed"
                         x-text="form.footer_message">
                    </div>

                </div>
            </div>

        </div>

    </div>

</div>

<!-- Alpine.js Live Designer Logic -->
<script>
function receiptDesigner() {
    return {
        form: {
            paper_width: {!! json_encode(old('paper_width', $setting->paper_width ?? '80mm')) !!},
            font_family: {!! json_encode(old('font_family', $setting->font_family ?? 'vazirmatn')) !!},
            font_size: {!! json_encode(old('font_size', $setting->font_size ?? 'medium')) !!},
            header_title: {!! json_encode(old('header_title', $setting->header_title ?? 'بی کافه (B CAFE)')) !!},
            header_subtitle: {!! json_encode(old('header_subtitle', $setting->header_subtitle ?? 'شیراز • شعبه مرکزی تخصصی قهوه موج سوم')) !!},
            invoice_title: {!! json_encode(old('invoice_title', $setting->invoice_title ?? 'صورت‌حساب فروش')) !!},
            show_logo: {{ old('show_logo', $setting->show_logo) ? 'true' : 'false' }},
            show_order_number: {{ old('show_order_number', $setting->show_order_number) ? 'true' : 'false' }},
            show_date_time: {{ old('show_date_time', $setting->show_date_time) ? 'true' : 'false' }},
            show_cashier_name: {{ old('show_cashier_name', $setting->show_cashier_name) ? 'true' : 'false' }},
            show_customer_name: {{ old('show_customer_name', $setting->show_customer_name) ? 'true' : 'false' }},
            show_customer_phone: {{ old('show_customer_phone', $setting->show_customer_phone) ? 'true' : 'false' }},
            show_customer_club_info: {{ old('show_customer_club_info', $setting->show_customer_club_info) ? 'true' : 'false' }},
            show_order_type: {{ old('show_order_type', $setting->show_order_type) ? 'true' : 'false' }},
            show_delivery_address: {{ old('show_delivery_address', $setting->show_delivery_address) ? 'true' : 'false' }},
            show_item_row_number: {{ old('show_item_row_number', $setting->show_item_row_number) ? 'true' : 'false' }},
            show_item_unit_price: {{ old('show_item_unit_price', $setting->show_item_unit_price) ? 'true' : 'false' }},
            show_item_notes: {{ old('show_item_notes', $setting->show_item_notes) ? 'true' : 'false' }},
            show_subtotal: {{ old('show_subtotal', $setting->show_subtotal) ? 'true' : 'false' }},
            show_discount: {{ old('show_discount', $setting->show_discount) ? 'true' : 'false' }},
            show_delivery_fee: {{ old('show_delivery_fee', $setting->show_delivery_fee) ? 'true' : 'false' }},
            show_tax: {{ old('show_tax', $setting->show_tax) ? 'true' : 'false' }},
            tax_percent: {{ (float) old('tax_percent', $setting->tax_percent ?? 0) }},
            show_amount_in_words: {{ old('show_amount_in_words', $setting->show_amount_in_words) ? 'true' : 'false' }},
            show_payment_method: {{ old('show_payment_method', $setting->show_payment_method) ? 'true' : 'false' }},
            show_payment_status: {{ old('show_payment_status', $setting->show_payment_status) ? 'true' : 'false' }},
            show_barcode: {{ old('show_barcode', $setting->show_barcode) ? 'true' : 'false' }},
            show_qr_code: {{ old('show_qr_code', $setting->show_qr_code) ? 'true' : 'false' }},
            qr_code_type: {!! json_encode(old('qr_code_type', $setting->qr_code_type ?? 'order_url')) !!},
            custom_qr_url: {!! json_encode(old('custom_qr_url', $setting->custom_qr_url ?? '')) !!},
            footer_message: {!! json_encode(old('footer_message', $setting->footer_message ?? '')) !!},
            show_wifi: {{ old('show_wifi', $setting->show_wifi) ? 'true' : 'false' }},
            wifi_ssid: {!! json_encode(old('wifi_ssid', $setting->wifi_ssid ?? 'B-Cafe-Guest')) !!},
            wifi_password: {!! json_encode(old('wifi_password', $setting->wifi_password ?? 'bcafe2026')) !!},
            show_instagram: {{ old('show_instagram', $setting->show_instagram) ? 'true' : 'false' }},
            instagram_handle: {!! json_encode(old('instagram_handle', $setting->instagram_handle ?? '@bcafe.shiraz')) !!},
            auto_print: {{ old('auto_print', $setting->auto_print) ? 'true' : 'false' }},
        },

        getFontFamily() {
            switch(this.form.font_family) {
                case 'shabnam': return "'Shabnam', Tahoma, sans-serif";
                case 'iransans': return "'IRANSans', Tahoma, sans-serif";
                case 'b_titr': return "'B Titr', 'Vazirmatn', sans-serif";
                case 'courier': return "'Courier New', Courier, monospace";
                default: return "'Vazirmatn', 'Tahoma', Arial, sans-serif";
            }
        },

        getFontSize() {
            switch(this.form.font_size) {
                case 'compact': return '10px';
                case 'large': return '12px';
                default: return '11px';
            }
        },

        testPrint() {
            @if($sampleOrder)
                window.open('{{ route('orders.receipt', $sampleOrder) }}', 'TestReceipt', 'width=420,height=750,toolbar=0,scrollbars=1');
            @else
                window.CafeToast.warning('جهت چاپ آزمایشی ابتدا باید حداقل یک سفارش در سیستم ثبت شده باشد.');
            @endif
        }
    }
}
</script>
@endsection
