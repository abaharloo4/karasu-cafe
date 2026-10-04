@extends('layouts.app')

@section('title', 'تنظیمات باشگاه مشتریان')
@section('header_title', 'تنظیمات باشگاه مشتریان بی کافه')

@section('content')
<div class="max-w-xl mx-auto space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.loyalty.index') }}" class="w-10 h-10 rounded-xl bg-[#F7F9FA] border border-[#D9D9D9] flex items-center justify-center hover:bg-[#D9D9D9] transition-colors">
            <svg class="w-5 h-5 text-[#353535]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
        </a>
        <div>
            <h1 class="text-lg font-black text-[#353535]">تنظیمات باشگاه مشتریان</h1>
            <p class="text-xs text-[#353535]/60 mt-0.5">نرخ کش‌بک، هدیه تولد، امتیاز خوش‌آمدگویی و...</p>
        </div>
    </div>

    <div class="bg-white border border-[#D9D9D9] p-6 sm:p-8 rounded-2xl space-y-5">
        <form method="POST" action="{{ route('admin.loyalty.settings.update') }}" class="space-y-5">
            @csrf

            <!-- Active Toggle -->
            <div class="flex items-center justify-between p-4 rounded-xl bg-[#F7F9FA] border border-[#D9D9D9]">
                <div>
                    <label class="text-xs font-black text-[#353535]">فعال بودن باشگاه مشتریان</label>
                    <p class="text-[11px] text-[#353535]/50 mt-0.5">اگه غیرفعالش کنی، کش‌بک و امتیاز دادن متوقف میشه</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $settings->is_active) ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="w-11 h-6 bg-[#D9D9D9] peer-focus:ring-2 peer-focus:ring-[#3C6E71]/20 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#3C6E71]"></div>
                </label>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-black text-[#353535] mb-1.5">نرخ کش‌بک (درصد) *</label>
                    <input type="number" name="cashback_percent" value="{{ old('cashback_percent', $settings->cashback_percent) }}" required min="0" max="50" step="0.5"
                           class="w-full px-4 py-3 rounded-xl bg-white border border-[#D9D9D9] text-[#353535] text-xs font-bold focus:outline-none focus:border-[#3C6E71] focus:ring-1 focus:ring-[#3C6E71] min-h-[46px] dir-ltr text-right tabular-nums">
                    <p class="text-[11px] text-[#353535]/50 mt-1">از هر سفارش چند درصد به کیف‌پول برگرده</p>
                    @error('cashback_percent') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-black text-[#353535] mb-1.5">حداقل مبلغ سفارش (تومان)</label>
                    <input type="number" name="min_order_amount" value="{{ old('min_order_amount', $settings->min_order_amount) }}" required min="0" step="1000"
                           class="w-full px-4 py-3 rounded-xl bg-white border border-[#D9D9D9] text-[#353535] text-xs font-bold focus:outline-none focus:border-[#3C6E71] focus:ring-1 focus:ring-[#3C6E71] min-h-[46px] dir-ltr text-right tabular-nums">
                    <p class="text-[11px] text-[#353535]/50 mt-1">سفارش زیر این مبلغ کش‌بک نداره. ۰ = بدون حداقل</p>
                    @error('min_order_amount') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-black text-[#353535] mb-1.5">واحد امتیاز (به ازای هر x تومان)</label>
                    <input type="number" name="points_per_order_unit" value="{{ old('points_per_order_unit', $settings->points_per_order_unit) }}" required min="1"
                           class="w-full px-4 py-3 rounded-xl bg-white border border-[#D9D9D9] text-[#353535] text-xs font-bold focus:outline-none focus:border-[#3C6E71] focus:ring-1 focus:ring-[#3C6E71] min-h-[46px] dir-ltr text-right tabular-nums">
                    <p class="text-[11px] text-[#353535]/50 mt-1">مثلاً ۱۰۰۰۰ = هر ۱۰ هزار تومان = ۱ امتیاز</p>
                    @error('points_per_order_unit') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-black text-[#353535] mb-1.5">امتیاز خوش‌آمدگویی</label>
                    <input type="number" name="welcome_bonus_points" value="{{ old('welcome_bonus_points', $settings->welcome_bonus_points) }}" required min="0"
                           class="w-full px-4 py-3 rounded-xl bg-white border border-[#D9D9D9] text-[#353535] text-xs font-bold focus:outline-none focus:border-[#3C6E71] focus:ring-1 focus:ring-[#3C6E71] min-h-[46px] dir-ltr text-right tabular-nums">
                    <p class="text-[11px] text-[#353535]/50 mt-1">هر مشتری جدید چند امتیاز بگیره. ۰ = بدون هدیه</p>
                    @error('welcome_bonus_points') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-black text-[#353535] mb-1.5">هدیه تولد (تومان)</label>
                <input type="number" name="birthday_reward_amount" value="{{ old('birthday_reward_amount', $settings->birthday_reward_amount) }}" required min="0" step="1000"
                       class="w-full px-4 py-3 rounded-xl bg-white border border-[#D9D9D9] text-[#353535] text-xs font-bold focus:outline-none focus:border-[#3C6E71] focus:ring-1 focus:ring-[#3C6E71] min-h-[46px] dir-ltr text-right tabular-nums">
                <p class="text-[11px] text-[#353535]/50 mt-1">مبلغی که روز تولد مشتری به کیف‌پولش واریز میشه</p>
                @error('birthday_reward_amount') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-end pt-3 border-t border-[#D9D9D9]/50">
                <button type="submit" class="px-8 py-3 bg-[#284B63] hover:bg-[#1E374A] text-white font-black rounded-xl text-xs shadow-xs min-h-[46px] transition-colors">
                    ذخیره تنظیمات
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
