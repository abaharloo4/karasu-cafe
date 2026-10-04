@extends('layouts.customer')

@section('title', 'دفترچه آدرس‌های من')

@section('content')
<div class="space-y-6" x-data="{ newAddressOpen: false }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-[#353535] flex items-center gap-2">
                <svg class="w-6 h-6 text-[#3C6E71]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>آدرس‌های من در شیراز</span>
            </h1>
            <p class="text-xs text-[#353535]/65 mt-1">آدرس‌های خانه یا محل کار برای تحویل سریع سفارش با پیک در شیراز</p>
        </div>

        <button type="button" @click="newAddressOpen = !newAddressOpen" class="px-4 py-2.5 bg-[#284B63] hover:bg-[#1E374A] text-white text-xs font-black rounded-xl btn-tactile shadow-xs flex items-center gap-1.5 min-h-[44px] transition-colors self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>افزودن آدرس جدید</span>
        </button>
    </div>

    <!-- New Address Form (Collapsible) -->
    <div x-show="newAddressOpen" x-transition class="bg-white border-2 border-[#3C6E71] p-6 sm:p-7 rounded-3xl space-y-4 shadow-sm">
        <h3 class="text-sm font-black text-[#353535] border-b border-[#D9D9D9]/70 pb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-[#284B63]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>اضافه کردن آدرس جدید در شیراز</span>
        </h3>

        <form method="POST" action="{{ route('customer.addresses.store') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-[#353535] mb-1.5">نام آدرس (مثل: خونه، شرکت، مطب) *</label>
                    <input type="text" name="title" required placeholder="مثلاً: خونه (ارم)" class="w-full px-4 py-3 rounded-xl bg-white border border-[#D9D9D9] text-[#353535] text-xs font-bold focus:outline-none focus:border-[#3C6E71] min-h-[44px]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#353535] mb-1.5">منطقه یا محدوده در شیراز *</label>
                    <select name="delivery_zone_id" class="w-full px-4 py-3 rounded-xl bg-white border border-[#D9D9D9] text-[#353535] text-xs font-bold focus:outline-none focus:border-[#3C6E71] min-h-[44px]">
                        @foreach($deliveryZones as $zone)
                            <option value="{{ $zone->id }}">{{ $zone->name }} (هزینه پیک: {{ number_format($zone->delivery_price) }} تومان)</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#353535] mb-1.5">آدرس دقیق در شیراز *</label>
                <textarea name="address_line" rows="2" required placeholder="خیابان اصلی، کوچه، فرعی، پلاک..." class="w-full px-4 py-3 rounded-xl bg-white border border-[#D9D9D9] text-[#353535] text-xs font-medium focus:outline-none focus:border-[#3C6E71] leading-relaxed"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-[#353535] mb-1.5">پلاک / واحد / زنگ (اختیاری)</label>
                    <input type="text" name="building_unit" placeholder="مثلاً: پلاک ۱۰ واحد ۳" class="w-full px-4 py-3 rounded-xl bg-white border border-[#D9D9D9] text-[#353535] text-xs font-medium focus:outline-none focus:border-[#3C6E71] min-h-[44px]">
                </div>

                <div class="flex items-center pt-5">
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="is_default" value="1" class="w-4 h-4 rounded text-[#3C6E71] focus:ring-[#3C6E71]">
                        <span class="text-xs font-bold text-[#353535]">این آدرس پیش‌فرض من باشه</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-2.5 pt-2">
                <button type="button" @click="newAddressOpen = false" class="px-5 py-2.5 bg-[#F7F9FA] hover:bg-[#E2E8F0] text-[#353535] text-xs font-bold rounded-xl btn-tactile min-h-[44px]">
                    انصراف
                </button>
                <button type="submit" class="px-7 py-2.5 bg-[#284B63] hover:bg-[#1E374A] text-white text-xs font-black rounded-xl btn-tactile shadow-xs min-h-[44px] transition-colors">
                    ذخیره آدرس
                </button>
            </div>
        </form>
    </div>

    <!-- Address Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($addresses as $addr)
            <div class="bg-white border {{ $addr->is_default ? 'border-2 border-[#3C6E71] ring-2 ring-[#3C6E71]/20' : 'border-[#D9D9D9]' }} p-5 rounded-3xl space-y-3.5 shadow-xs flex flex-col justify-between hover:border-[#3C6E71] transition-all">
                <div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="font-black text-sm text-[#353535]">{{ $addr->title }}</span>
                            @if($addr->is_default)
                                <span class="px-2.5 py-0.5 rounded-lg bg-teal-50 text-[#3C6E71] text-[10px] font-black border border-teal-200">پیش‌فرض</span>
                            @endif
                        </div>
                        <span class="text-[11px] text-[#284B63] font-black bg-[#F7F9FA] px-2 py-0.5 rounded-md border border-[#D9D9D9]">
                            {{ $addr->deliveryZone->name ?? 'شیراز' }}
                        </span>
                    </div>

                    <p class="text-xs text-[#353535]/70 mt-2 leading-relaxed font-medium">
                        {{ $addr->address_line }}
                        @if($addr->building_unit)
                            <span class="text-[#353535] font-bold block mt-1">واحد / پلاک: {{ $addr->building_unit }}</span>
                        @endif
                    </p>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-[#D9D9D9]/70 text-xs">
                    <span class="text-[11px] text-[#353535]/50">
                        هزینه پیک: {{ number_format($addr->deliveryZone->delivery_price ?? 25000) }} تومان
                    </span>
                    <form method="POST" action="{{ route('customer.addresses.destroy', $addr) }}" onsubmit="return confirm('مطمئنی می‌خوای این آدرس رو حذف کنی؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-[#B44A4A] font-bold hover:underline flex items-center gap-1 min-h-[36px]">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>حذف</span>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full p-12 rounded-3xl bg-white border border-[#D9D9D9] text-center text-xs text-[#353535]/65 space-y-3 shadow-xs">
                <div class="w-16 h-16 rounded-3xl bg-[#F7F9FA] text-[#353535]/40 flex items-center justify-center mx-auto">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div class="space-y-1">
                    <p class="font-bold text-[#353535] text-sm">هنوز آدرسی ثبت نکردی!</p>
                    <p>با دکمه «افزودن آدرس جدید» می‌تونی آدرس خونه یا محل کارت رو ذخیره کنی تا هربار نیاز به تایپ دوباره نباشه.</p>
                </div>
            </div>
        @endforelse
    </div>

</div>
@endsection