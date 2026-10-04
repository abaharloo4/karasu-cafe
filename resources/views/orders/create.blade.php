@extends('layouts.app')

@section('title', 'ثبت سفارش جدید')
@section('header_title', 'ثبت سفارش تک‌فروشی / پولی')

@section('content')
<div class="max-w-2xl mx-auto bg-[#FCFCFB] border border-[#D9E0D6] p-8 rounded-3xl space-y-6 shadow-sm"
     x-data="{
         customers: {{ Js::from($customers) }},
         selectedCustomerId: '{{ old('customer_id', '') }}',
         redeemSubscription: {{ old('redeem_subscription') ? 'true' : 'false' }},
         get selectedCustomer() {
             return this.customers.find(c => String(c.id) === String(this.selectedCustomerId)) || null;
         }
     }">
    <h2 class="text-base font-bold text-[#1F2520] border-b border-[#E7ECE4] pb-4">مشخصات فاکتور فروش</h2>

    <form method="POST" action="{{ route('orders.store') }}" class="space-y-6">
        @csrf

        <div>
            <label class="block text-xs font-semibold text-[#1F2520] mb-1.5">انتخاب مشتری (اختیاری برای مشتری عبوری)</label>
            <select name="customer_id" x-model="selectedCustomerId" class="w-full px-4 py-3 rounded-xl bg-[#FFFFFF] border border-[#CCD5C8] text-[#1F2520] focus:outline-none focus:border-[#6A8C60] text-xs font-medium">
                <option value="">-- مشتری عبوری (بدون ثبت نام) --</option>
                @foreach($customers as $c)
                    <option value="{{ $c['id'] }}">{{ $c['name'] }} ({{ $c['phone'] }}) {{ $c['has_active_sub'] ? '★' : '' }}</option>
                @endforeach
            </select>
        </div>

        <!-- Subscription Active Banner -->
        <div x-show="selectedCustomer && selectedCustomer.can_redeem" x-cloak class="p-4 bg-[#EAF2E7] border border-[#3A5232]/30 rounded-2xl space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#3A5232] flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-[#C8A35A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    اشتراک فعال: <span x-text="selectedCustomer?.plan_name"></span> (<span x-text="selectedCustomer?.remaining_items"></span> سهمیه باقی‌مانده)
                </span>
                <span class="text-[11px] text-[#3A5232] font-semibold bg-[#FFFFFF] px-2 py-0.5 rounded-lg border border-[#3A5232]/20">سهمیه امروز آزاد</span>
            </div>
            <label class="flex items-center gap-2.5 pt-1 cursor-pointer">
                <input type="checkbox" name="redeem_subscription" value="1" x-model="redeemSubscription" class="w-4 h-4 rounded text-[#3A5232] focus:ring-[#3A5232] border-[#CCD5C8]">
                <span class="text-xs font-extrabold text-[#1F2520]">کسر ۱ سهمیه روزانه از اشتراک (۱ قلم واجد شرایط با تخفیف ۱۰۰٪ کسر می‌شود)</span>
            </label>
            <div x-show="redeemSubscription" class="pt-2 border-t border-[#3A5232]/20 flex items-center justify-between">
                <span class="text-[11px] text-[#3A5232]">قلم مجاز سهمیه به طور خودکار به سفارش اضافه و صفر ریال محاسبه خواهد شد.</span>
            </div>
        </div>

        <div x-show="selectedCustomer && selectedCustomer.has_active_sub && selectedCustomer.has_used_today" x-cloak class="p-3 bg-[#F4F6F4] border border-[#D9E0D6] rounded-xl text-xs text-[#6E756F] flex items-center gap-2">
            <svg class="w-4 h-4 text-[#8A9287]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>سهمیه رایگان امروز این مشتری قبلاً مصرف شده است (سفارش به عنوان پولی ثبت می‌شود).</span>
        </div>

        <div>
            <label class="block text-xs font-bold text-[#1F2520] mb-3">اقلام سفارش:</label>
            <div class="space-y-3">
                @foreach($products as $prod)
                    <div class="flex items-center justify-between p-3.5 bg-[#FFFFFF] border border-[#CCD5C8] rounded-xl text-xs">
                        <div>
                            <span class="font-bold text-[#1F2520] block">{{ $prod->name }}</span>
                            <span class="text-[11px] text-[#6E756F]">{{ number_format($prod->price) }} تومان</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] text-[#6E756F]">تعداد:</span>
                            <input type="number" name="items[{{ $prod->id }}]" value="0" min="0" max="20" class="w-16 px-2 py-1 bg-[#FCFCFB] border border-[#CCD5C8] rounded-lg text-center font-bold text-[#1F2520]">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E7ECE4]">
            <x-button variant="ghost" :href="route('orders.index')">انصراف</x-button>
            <x-button variant="primary" type="submit" class="shadow-md">ثبت و صدور فاکتور</x-button>
        </div>
    </form>
</div>
@endsection
