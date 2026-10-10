@extends('layouts.app')

@section('title', 'ساعات کاری و شیفت کافه')
@section('header_title', 'تنظیمات شیفت کاری، ساعات سفارش‌گیری و پیام‌های وضعیت کافه')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header info card -->
    <div class="bg-white border border-[#E2E8F0] p-6 rounded-3xl space-y-2 shadow-xs">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-base font-extrabold text-[#19242B] flex items-center gap-2">
                <svg class="w-5 h-5 text-[#387780]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>تنظیم ساعات شیفت کاری و دریافت سفارشات بی کافه</span>
            </h2>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-500">وضعیت کنونی سیستم:</span>
                @if($cafe->isOrderingOpen())
                    <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-black rounded-lg flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>سفارش‌گیری باز است</span>
                    </span>
                @else
                    <span class="px-3 py-1 bg-amber-50 text-amber-800 border border-amber-200 text-xs font-black rounded-lg flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span>خارج از شیفت (رزرو نوبت بعدی)</span>
                    </span>
                @endif
            </div>
        </div>
        <p class="text-xs text-[#64748B]">
            ساعات سفارش‌گیری کافه را تعیین نمایید. در صورتی که مشتری خارج از این بازه زمانی اقدام به ثبت سفارش کند، سفارش ثبت شده و به مشتری اطلاع داده می‌شود که سفارش برای شیفت کاری بعدی آماده و ارسال خواهد شد.
        </p>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2 shadow-xs">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold space-y-1 shadow-xs">
            @foreach($errors->all() as $error)
                <div>• {{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('settings.cafe.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Shift & Operating Hours Card -->
        <div class="bg-white border border-[#E2E8F0] p-6 rounded-3xl space-y-5 shadow-xs">
            <div class="flex items-center justify-between border-b border-[#E2E8F0] pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-teal-50 border border-teal-200 flex items-center justify-center text-[#387780]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-[#19242B]">ساعات شیفت سفارش‌گیری</h3>
                        <p class="text-xs text-[#64748B]">ساعت شروع و پایان بازه کاری روزانه</p>
                    </div>
                </div>

                <label class="flex items-center gap-2 cursor-pointer bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200 hover:bg-slate-100 transition-colors">
                    <span class="text-xs font-bold text-[#19242B]">کلید سفارش‌گیری:</span>
                    <input type="checkbox" name="is_ordering_open" value="1" {{ old('is_ordering_open', $cafe->is_ordering_open) ? 'checked' : '' }} class="w-5 h-5 rounded text-[#387780] focus:ring-[#387780]">
                    <span class="text-xs font-semibold text-[#475569]">فعال و باز</span>
                </label>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <x-persian-time-picker name="order_start_time" :value="old('order_start_time', substr($cafe->order_start_time ?? '08:00', 0, 5))" label="ساعت شروع سفارش‌گیری *" placeholder="انتخاب ساعت شروع (مثال: 08:00)..." :required="true" />
                    <span class="text-[11px] text-[#64748B] block mt-1">فرمت ۲۴ ساعته ویژوال (HH:mm)</span>
                </div>

                <div>
                    <x-persian-time-picker name="order_end_time" :value="old('order_end_time', substr($cafe->order_end_time ?? '22:00', 0, 5))" label="ساعت پایان سفارش‌گیری *" placeholder="انتخاب ساعت پایان (مثال: 22:00)..." :required="true" />
                    <span class="text-[11px] text-[#64748B] block mt-1">سفارش‌های بعد از این ساعت به شیفت بعدی منتقل می‌شوند.</span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#19242B] mb-1.5">متن پیام اطلاع‌رسانی به مشتری خارج از شیفت (اختیاری)</label>
                <textarea name="closed_message" rows="2" placeholder="کافه در حال حاضر خارج از شیفت کاری است. سفارش شما ثبت شده و برای شروع شیفت کاری بعدی پردازش و آماده خواهد شد."
                          class="w-full px-4 py-3 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs leading-relaxed focus:outline-none focus:border-[#387780] focus:ring-1 focus:ring-[#387780]">{{ old('closed_message', $cafe->closed_message) }}</textarea>
                <span class="text-[11px] text-[#64748B] block mt-1">در صورت خالی بودن، متن استاندارد پیش‌فرض سیستم نمایش داده می‌شود.</span>
            </div>
        </div>

        <!-- General Cafe Info -->
        <div class="bg-white border border-[#E2E8F0] p-6 rounded-3xl space-y-4 shadow-xs">
            <h3 class="text-sm font-bold text-[#19242B] border-b border-[#E2E8F0] pb-3">اطلاعات تماس کافه</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-[#19242B] mb-1.5">شماره تماس کافه</label>
                    <input type="text" name="phone" value="{{ old('phone', $cafe->phone) }}" dir="ltr" placeholder="07136360000"
                           class="w-full px-4 py-3 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs font-mono focus:outline-none focus:border-[#387780]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-[#19242B] mb-1.5">آدرس کافه در شیراز</label>
                    <input type="text" name="address" value="{{ old('address', $cafe->address) }}" placeholder="شیراز، بلوار ستارخان..."
                           class="w-full px-4 py-3 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs focus:outline-none focus:border-[#387780]">
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex items-center justify-end">
            <button type="submit" class="px-6 py-3.5 bg-[#387780] hover:bg-[#2A5C63] text-white font-bold rounded-xl text-xs btn-tactile shadow-md flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>ذخیره تغییرات ساعات کاری و شیفت</span>
            </button>
        </div>
    </form>
</div>
@endsection
