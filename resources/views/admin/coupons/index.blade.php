@extends('layouts.app')

@section('title', 'مدیریت کدهای تخفیف')
@section('header_title', 'کدهای تخفیف و بن‌های خرید (ووکامرس)')

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="bg-white border border-[#D9D9D9] p-5 sm:p-6 rounded-3xl shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base sm:text-lg font-black text-[#353535] flex items-center gap-2">
                <svg class="w-6 h-6 text-[#3C6E71]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                <span>مدیریت کدهای تخفیف بی کافه</span>
            </h2>
            <p class="text-xs text-[#353535]/65 mt-1">ساخت و تنظیم کدهای تخفیف درصدی و ریالی با محدودیت‌های حداقل سفارش، سقف تخفیف و دفعات مصرف</p>
        </div>

        <a href="{{ route('admin.coupons.create') }}" class="px-5 py-3 bg-[#3C6E71] hover:bg-[#284B63] text-white font-black text-xs rounded-xl shadow-xs flex items-center justify-center gap-2 transition-colors min-h-[44px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>افزودن کد تخفیف جدید</span>
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white border border-[#D9D9D9] p-4 rounded-2xl shadow-xs">
        <form method="GET" action="{{ route('admin.coupons.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="جستجو در کد یا عنوان..."
                       class="w-full px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-[#353535] focus:outline-none focus:border-[#3C6E71]">
            </div>

            <div>
                <select name="type" class="w-full px-3 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-[#353535] focus:outline-none focus:border-[#3C6E71]">
                    <option value="">همه انواع تخفیف</option>
                    <option value="percent" {{ request('type') === 'percent' ? 'selected' : '' }}>درصدی (%)</option>
                    <option value="fixed" {{ request('type') === 'fixed' ? 'selected' : '' }}>مبلغ ثابت (تومان)</option>
                </select>
            </div>

            <div>
                <select name="status" class="w-full px-3 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-[#353535] focus:outline-none focus:border-[#3C6E71]">
                    <option value="">همه وضعیت‌ها</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>فعال</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>غیرفعال</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2.5 bg-[#284B63] hover:bg-[#1E374A] text-white text-xs font-black rounded-xl transition-colors">
                    اعمال فیلتر
                </button>
                @if(request()->hasAny(['search', 'type', 'status']))
                    <a href="{{ route('admin.coupons.index') }}" class="p-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold" title="پاکسازی">
                        ✕
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Coupons Table -->
    <div class="bg-white border border-[#D9D9D9] rounded-3xl overflow-hidden shadow-xs">
        @if($coupons->isEmpty())
            <div class="p-12 text-center space-y-3">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                </div>
                <h3 class="text-sm font-black text-[#353535]">هنوز هیچ کد تخفیفی ثبت نشده است</h3>
                <p class="text-xs text-gray-500">می‌توانید اولین کد تخفیف را با کلیک روی دکمه بالا ایجاد کنید.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-gray-50/75 border-b border-gray-200 text-gray-600 font-black">
                        <tr>
                            <th class="py-3.5 px-4">کد تخفیف</th>
                            <th class="py-3.5 px-4">عنوان / مناسبت</th>
                            <th class="py-3.5 px-4">نوع و میزان تخفیف</th>
                            <th class="py-3.5 px-4">حداقل خرید / سقف</th>
                            <th class="py-3.5 px-4">دفعات مصرف</th>
                            <th class="py-3.5 px-4">مهلت اعتبار</th>
                            <th class="py-3.5 px-4">وضعیت</th>
                            <th class="py-3.5 px-4 text-center">عملیات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-bold">
                        @foreach($coupons as $coupon)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <!-- Code -->
                                <td class="py-4 px-4 font-mono font-black text-sm text-[#284B63]">
                                    <span class="px-2.5 py-1 bg-slate-100 rounded-lg border border-slate-200 select-all">{{ $coupon->code }}</span>
                                    @if($coupon->first_order_only)
                                        <span class="block text-[10px] text-amber-600 font-sans font-bold mt-1">★ اولین خرید</span>
                                    @endif
                                </td>

                                <!-- Title -->
                                <td class="py-4 px-4">
                                    <span class="text-[#353535] font-black">{{ $coupon->title }}</span>
                                    @if($coupon->description)
                                        <p class="text-[11px] text-gray-400 font-normal line-clamp-1 mt-0.5">{{ $coupon->description }}</p>
                                    @endif
                                </td>

                                <!-- Type & Amount -->
                                <td class="py-4 px-4">
                                    @if($coupon->discount_type === 'percent')
                                        <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200 text-xs">
                                            {{ (int)$coupon->discount_value }}٪ تخفیف
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs tabular-nums">
                                            {{ number_format($coupon->discount_value) }} تومان
                                        </span>
                                    @endif
                                </td>

                                <!-- Min Spend & Cap -->
                                <td class="py-4 px-4 text-gray-600 text-[11px] space-y-0.5">
                                    <div>حداقل: {{ $coupon->min_spend ? number_format($coupon->min_spend) . ' ت' : 'بدون سقف' }}</div>
                                    @if($coupon->discount_type === 'percent' && $coupon->max_discount)
                                        <div class="text-gray-400">سقف: {{ number_format($coupon->max_discount) }} ت</div>
                                    @endif
                                </td>

                                <!-- Usage limit -->
                                <td class="py-4 px-4 tabular-nums">
                                    <span class="text-[#353535] font-black">{{ $coupon->times_used }}</span>
                                    <span class="text-gray-400 font-normal"> / {{ $coupon->usage_limit ? $coupon->usage_limit : 'نامحدود' }}</span>
                                    @if($coupon->usage_limit_per_user)
                                        <div class="text-[10px] text-gray-400">هر کاربر: {{ $coupon->usage_limit_per_user }} بار</div>
                                    @endif
                                </td>

                                <!-- Validity -->
                                <td class="py-4 px-4 text-[11px] text-gray-600">
                                    @if($coupon->expires_at)
                                        @if($coupon->expires_at->isPast())
                                            <span class="text-rose-600 font-black">منقضی شده</span>
                                        @else
                                            <span>تا {{ $coupon->expires_at->format('Y/m/d H:i') }}</span>
                                        @endif
                                    @else
                                        <span class="text-emerald-700">دائمی (بدون انقضا)</span>
                                    @endif
                                </td>

                                <!-- Status -->
                                <td class="py-4 px-4">
                                    <form method="POST" action="{{ route('admin.coupons.toggle', $coupon) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 rounded-full text-[11px] font-black border transition-colors {{ $coupon->status === 'active' ? 'bg-emerald-50 text-emerald-700 border-emerald-300 hover:bg-emerald-100' : 'bg-gray-100 text-gray-500 border-gray-300 hover:bg-gray-200' }}" title="تغییر وضعیت">
                                            {{ $coupon->status === 'active' ? '● فعال' : '○ غیرفعال' }}
                                        </button>
                                    </form>
                                </td>

                                <!-- Actions -->
                                <td class="py-4 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="p-2 text-[#3C6E71] hover:bg-teal-50 rounded-lg transition-colors" title="ویرایش">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>

                                        <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" onsubmit="return confirm('آیا از حذف این کد تخفیف اطمینان دارید؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="حذف">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($coupons->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $coupons->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
