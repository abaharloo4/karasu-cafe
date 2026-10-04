@extends('layouts.app')

@section('title', $tier ? 'ویرایش لول ' . $tier->name : 'ساختن لول جدید')
@section('header_title', $tier ? 'ویرایش لول باشگاه مشتریان' : 'ساختن لول جدید باشگاه مشتریان')

@section('content')
<div class="max-w-xl mx-auto space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.loyalty.tiers') }}" class="w-10 h-10 rounded-xl bg-[#F7F9FA] border border-[#D9D9D9] flex items-center justify-center hover:bg-[#D9D9D9] transition-colors">
            <svg class="w-5 h-5 text-[#353535]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
        </a>
        <h1 class="text-lg font-black text-[#353535]">{{ $tier ? 'ویرایش «' . $tier->name . '»' : 'لول جدید بساز' }}</h1>
    </div>

    <div class="bg-white border border-[#D9D9D9] p-6 sm:p-8 rounded-2xl space-y-5">
        <form method="POST"
              action="{{ $tier ? route('admin.loyalty.tiers.update', $tier) : route('admin.loyalty.tiers.store') }}"
              class="space-y-5">
            @csrf
            @if($tier) @method('PUT') @endif

            <div>
                <label class="block text-xs font-black text-[#353535] mb-1.5">اسم لول *</label>
                <input type="text" name="name" value="{{ old('name', $tier->name ?? '') }}" required placeholder="مثلاً: برنزی، نقره‌ای، طلایی..."
                       class="w-full px-4 py-3 rounded-xl bg-white border border-[#D9D9D9] text-[#353535] text-xs font-bold focus:outline-none focus:border-[#3C6E71] focus:ring-1 focus:ring-[#3C6E71] min-h-[46px]">
                @error('name') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-black text-[#353535] mb-1.5">حداقل مبلغ خرید (تومان) *</label>
                <input type="number" name="min_spend" value="{{ old('min_spend', $tier->min_spend ?? 0) }}" required min="0" step="1000"
                       class="w-full px-4 py-3 rounded-xl bg-white border border-[#D9D9D9] text-[#353535] text-xs font-bold focus:outline-none focus:border-[#3C6E71] focus:ring-1 focus:ring-[#3C6E71] min-h-[46px] dir-ltr text-right tabular-nums">
                <p class="text-[11px] text-[#353535]/50 mt-1">مشتری بعد از رسیدن به این مبلغ خرید کل، به این لول ارتقا پیدا می‌کنه</p>
                @error('min_spend') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-black text-[#353535] mb-1.5">درصد کش‌بک سفارش *</label>
                <div class="relative">
                    <input type="number" name="discount_percentage" value="{{ old('discount_percentage', $tier->discount_percentage ?? 5) }}" required min="0" max="100"
                           class="w-full px-4 py-3 rounded-xl bg-white border border-[#D9D9D9] text-[#353535] text-xs font-bold focus:outline-none focus:border-[#3C6E71] focus:ring-1 focus:ring-[#3C6E71] min-h-[46px] dir-ltr text-right tabular-nums">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-[#353535]/50">٪</span>
                </div>
                @error('discount_percentage') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-between p-4 rounded-xl bg-[#F7F9FA] border border-[#D9D9D9]">
                <div>
                    <label class="text-xs font-black text-[#353535]">ارسال رایگان</label>
                    <p class="text-[11px] text-[#353535]/50 mt-0.5">مشتریان این لول ارسال رایگان دارن</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="free_delivery" value="1" {{ old('free_delivery', $tier->free_delivery ?? false) ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="w-11 h-6 bg-[#D9D9D9] peer-focus:ring-2 peer-focus:ring-[#3C6E71]/20 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#3C6E71]"></div>
                </label>
            </div>

            <div>
                <label class="block text-xs font-black text-[#353535] mb-1.5">رنگ بج لول *</label>
                <div class="flex items-center gap-3">
                    <input type="color" name="badge_color" value="{{ old('badge_color', $tier->badge_color ?? '#C8A35A') }}"
                           class="w-12 h-12 rounded-xl border border-[#D9D9D9] cursor-pointer p-1">
                    <input type="text" value="{{ old('badge_color', $tier->badge_color ?? '#C8A35A') }}"
                           class="w-28 px-3 py-2.5 rounded-xl bg-[#F7F9FA] border border-[#D9D9D9] text-[#353535] text-xs font-mono font-bold dir-ltr min-h-[46px]"
                           oninput="this.previousElementSibling.value = this.value"
                           onchange="this.previousElementSibling.value = this.value">
                    <div class="w-10 h-10 rounded-xl" id="colorPreview" style="background-color: {{ old('badge_color', $tier->badge_color ?? '#C8A35A') }}"></div>
                </div>
                @error('badge_color') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-[#D9D9D9]/50">
                <a href="{{ route('admin.loyalty.tiers') }}" class="px-5 py-2.5 text-xs font-bold text-[#353535]/70 hover:bg-[#F7F9FA] rounded-xl transition-colors min-h-[44px] flex items-center">
                    انصراف
                </a>
                <button type="submit" class="px-6 py-2.5 bg-[#284B63] hover:bg-[#1E374A] text-white font-black rounded-xl text-xs shadow-xs min-h-[44px] transition-colors">
                    {{ $tier ? 'ذخیره تغییرات' : 'ساختن لول' }}
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
