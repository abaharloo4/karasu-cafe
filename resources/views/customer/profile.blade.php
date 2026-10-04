@extends('layouts.customer')

@section('title', 'مشخصات و تاریخ تولد')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-[#353535] flex items-center gap-2">
                <svg class="w-6 h-6 text-[#3C6E71]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>مشخصات من و هدیه تولد</span>
            </h1>
            <p class="text-xs text-[#353535]/65 mt-1">مشخصاتت رو تکمیل کن تا روز تولدت شارژ هدیه کافه برات واریز بشه</p>
        </div>
    </div>

    <div class="bg-white border border-[#D9D9D9] p-6 sm:p-8 rounded-3xl space-y-6 shadow-xs">
        <div class="border-b border-[#D9D9D9]/70 pb-5">
            <h2 class="text-base font-black text-[#353535] flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-[#284B63]/10 text-[#284B63] flex items-center justify-center">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V4a2 2 0 114 0v4m-4 0a2 2 0 10-4 0v4m-5 4h18M5 12a2 2 0 012-2h10a2 2 0 012 2v7a2 2 0 01-2 2H7a2 2 0 01-2-2v-7z"/></svg>
                </div>
                <span>هدیه ۵۰ هزار تومانی روز تولدت</span>
            </h2>
            <p class="text-xs text-[#353535]/65 mt-1.5 leading-relaxed">
                کافیه تاریخ تولدت رو وارد کنی؛ هر سال روز تولدت ۵۰ هزار تومان اعتبار نقدی میاد توی کیف‌پولت و با پیامک بهت تبریک می‌گیم.
            </p>
        </div>

        <form method="POST" action="{{ route('customer.profile.update') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-black text-[#353535] mb-1.5">شماره موبایل (تایید شده)</label>
                <div class="relative">
                    <input type="text" value="{{ $customer->phone }}" disabled class="w-full px-4 py-3 rounded-xl bg-[#F7F9FA] border border-[#D9D9D9] text-[#353535]/70 text-xs font-mono dir-ltr text-right cursor-not-allowed min-h-[46px]">
                    <span class="absolute end-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                        تایید شده
                        <svg class="w-3 h-3 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-black text-[#353535] mb-1.5">نام و نام خانوادگی *</label>
                <input type="text" name="name" value="{{ old('name', $customer->name) }}" required class="w-full px-4 py-3 rounded-xl bg-white border border-[#D9D9D9] text-[#353535] text-xs font-bold focus:outline-none focus:border-[#3C6E71] focus:ring-1 focus:ring-[#3C6E71] min-h-[46px]">
            </div>

            <div>
                <label class="block text-xs font-black text-[#353535] mb-1.5">ایمیل (اختیاری جهت دریافت فاکتور)</label>
                <input type="email" name="email" value="{{ old('email', $customer->email) }}" placeholder="example@email.com" class="w-full px-4 py-3 rounded-xl bg-white border border-[#D9D9D9] text-[#353535] text-xs font-medium dir-ltr text-right focus:outline-none focus:border-[#3C6E71] focus:ring-1 focus:ring-[#3C6E71] min-h-[46px]">
            </div>

            <!-- Birthdate Section - Jalali Picker -->
            <div class="p-5 rounded-2xl bg-[#F7F9FA] border border-[#D9D9D9] space-y-3"
                 x-data="jalaliDatePicker({{ $customer->birthdate ? 'true' : 'false' }}, '{{ $customer->birthdate ? \App\Helpers\JalaliHelper::format($customer->birthdate, 'Y/m/d') : '' }}')">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-black text-[#353535] flex items-center gap-2">
                        <svg class="w-4.5 h-4.5 text-[#284B63]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V4a2 2 0 114 0v4m-4 0a2 2 0 10-4 0v4m-5 4h18M5 12a2 2 0 012-2h10a2 2 0 012 2v7a2 2 0 01-2 2H7a2 2 0 01-2-2v-7z"/></svg>
                        <span>تاریخ تولد (برای هدیه سالانه)</span>
                    </label>
                    @if($customer->birthdate)
                        <span class="text-[11px] font-black text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-lg border border-emerald-200">
                            ثبت شده
                            <svg class="w-3 h-3 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </span>
                    @endif
                </div>

                @if($customer->birthdate)
                    <div class="flex items-center gap-2">
                        <input type="text" value="{{ \App\Helpers\JalaliHelper::format($customer->birthdate, 'Y/m/d') }}" disabled class="w-full px-4 py-3 rounded-xl bg-white border border-[#D9D9D9] text-[#353535] text-xs font-mono font-black text-right cursor-not-allowed min-h-[46px]">
                    </div>
                    <p class="text-[11px] text-[#353535]/65">
                        تاریخ تولد برای جلوگیری از ثبت تکراری قفل شده. اگه نیازه تغییرش بدی، با بچه‌های کافه هماهنگ کن.
                    </p>
                @else
                    <x-persian-date-picker name="birthdate" placeholder="انتخاب تاریخ تولد شمسی..." :minYear="1320" :maxYear="1405" />
                    <p class="text-[11px] text-[#353535]/65">
                        تاریخ تولد فقط یک‌بار ثبت میشه و در روز تولدت، هدیه ویژه کافه به صورت خودکار به کیف‌پولت واریز میشه.
                    </p>
                @endif
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-8 py-3 bg-[#284B63] hover:bg-[#1E374A] text-white font-black rounded-xl text-xs btn-tactile shadow-xs min-h-[46px] transition-colors">
                    ذخیره مشخصات
                </button>
            </div>
        </form>
    </div>

</div>
@endsection