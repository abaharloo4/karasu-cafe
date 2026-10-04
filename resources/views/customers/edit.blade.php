@extends('layouts.app')

@section('title', 'ویرایش اطلاعات مشتری')
@section('header_title', 'ویرایش مشتری: ' . $customer->name)

@section('content')
<div class="max-w-xl mx-auto bg-white border border-[#353535]/15 p-8 rounded-3xl space-y-6 shadow-sm">
    <div class="border-b border-slate-100 pb-4">
        <h2 class="text-base font-black text-[#353535]">ویرایش مشخصات مشتری</h2>
        <p class="text-xs text-slate-400 mt-1">تغییر اطلاعات هویتی و تاریخ تولد</p>
    </div>

    <form method="POST" action="{{ route('customers.update', $customer) }}" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-xs font-bold text-[#353535] mb-1.5">نام و نام خانوادگی *</label>
            <input type="text" name="name" value="{{ old('name', $customer->name) }}" required class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#353535]/20 text-[#353535] focus:outline-none focus:border-[#3C6E71] text-xs min-h-[44px]">
        </div>

        <div>
            <label class="block text-xs font-bold text-[#353535] mb-1.5">شماره موبایل *</label>
            <input type="tel" name="phone" value="{{ old('phone', $customer->phone) }}" required dir="ltr" class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#353535]/20 text-[#353535] focus:outline-none focus:border-[#3C6E71] text-xs min-h-[44px] font-mono text-left">
        </div>

        <div>
            <x-persian-date-picker name="birthdate" :value="$customer->birthdate?->format('Y-m-d')" label="تاریخ تولد شمسی (جهت پاداش باشگاه)" :minYear="1320" :maxYear="1405" />
        </div>

        <div>
            <label class="block text-xs font-bold text-[#353535] mb-1.5">پست الکترونیک</label>
            <input type="email" name="email" value="{{ old('email', $customer->email) }}" dir="ltr" class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#353535]/20 text-[#353535] focus:outline-none focus:border-[#3C6E71] text-xs min-h-[44px] font-mono text-left">
        </div>

        <div>
            <label class="block text-xs font-bold text-[#353535] mb-1.5">آدرس در شیراز</label>
            <textarea name="address" rows="3" class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#353535]/20 text-[#353535] focus:outline-none focus:border-[#3C6E71] text-xs">{{ old('address', $customer->address) }}</textarea>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('customers.show', $customer) }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-colors min-h-[44px] flex items-center">انصراف</a>
            <button type="submit" class="px-6 py-2.5 bg-[#284B63] hover:bg-[#1E374A] text-white font-black rounded-xl text-xs transition-colors shadow-xs min-h-[44px] btn-tactile">به‌روزرسانی مشتری</button>
        </div>
    </form>
</div>
@endsection
