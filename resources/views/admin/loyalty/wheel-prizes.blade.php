@extends('layouts.app')

@section('title', 'مدیریت جوایز گردونه شانس')
@section('header_title', 'جوایز گردونه شانس روزانه کافه')

@section('content')
<div class="space-y-6" x-data="{ addModalOpen: false, editModalOpen: false, editPrize: {} }">

    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.loyalty.index') }}" class="w-10 h-10 rounded-xl bg-[#F7F9FA] border border-[#D9D9D9] flex items-center justify-center hover:bg-[#D9D9D9] transition-colors">
                <svg class="w-5 h-5 text-[#353535]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
            </a>
            <div>
                <h1 class="text-lg font-black text-[#353535] flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#E29578]"></span>
                    <span>جوایز گردونه شانس روزانه</span>
                </h1>
                <p class="text-xs text-[#353535]/60 mt-0.5">تعریف جوایز، درصد شانس برنده شدن و وضعیت فعال بودن هر بخش</p>
            </div>
        </div>

        <button type="button" @click="addModalOpen = true" class="px-4 py-2.5 bg-[#284B63] hover:bg-[#1E374A] text-white font-bold rounded-xl text-xs transition-colors flex items-center gap-2 min-h-[44px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>افزودن جایزه جدید</span>
        </button>
    </div>

    <!-- Table of Prizes -->
    <div class="bg-white border border-[#D9D9D9] rounded-2xl overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-start text-xs">
                <thead>
                    <tr class="bg-[#F7F9FA] border-b border-[#D9D9D9] text-[#353535]/60">
                        <th class="p-4 font-bold text-start">عنوان جایزه</th>
                        <th class="p-4 font-bold text-start">نوع جایزه</th>
                        <th class="p-4 font-bold text-start">مقدار جایزه</th>
                        <th class="p-4 font-bold text-start">وزن شانس (Probability)</th>
                        <th class="p-4 font-bold text-start">وضعیت</th>
                        <th class="p-4 font-bold text-center">عملیات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#D9D9D9]/50 text-[#353535]">
                    @forelse($prizes as $prize)
                        <tr class="hover:bg-[#F7F9FA]/60 transition-colors">
                            <td class="p-4 font-black flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#3C6E71]"></span>
                                <span>{{ $prize->title }}</span>
                            </td>
                            <td class="p-4 font-medium">
                                @if($prize->prize_type === 'points')
                                    <span class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 font-bold text-[11px]">امتیاز وفاداری</span>
                                @elseif($prize->prize_type === 'discount')
                                    <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-800 font-bold text-[11px]">کد تخفیف</span>
                                @elseif($prize->prize_type === 'free_product')
                                    <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 font-bold text-[11px]">آیتم رایگان</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 font-bold text-[11px]">پوچ</span>
                                @endif
                            </td>
                            <td class="p-4 font-bold tabular-nums">
                                @if($prize->prize_type === 'points')
                                    {{ number_format($prize->prize_value) }} امتیاز
                                @elseif($prize->prize_type === 'discount')
                                    {{ number_format($prize->prize_value) }} تومان
                                @else
                                    {{ $prize->prize_value }}
                                @endif
                            </td>
                            <td class="p-4 font-bold tabular-nums">
                                <span class="px-2 py-0.5 rounded-md bg-[#284B63]/10 text-[#284B63]">{{ $prize->win_probability }}</span>
                            </td>
                            <td class="p-4">
                                @if($prize->status === 'active')
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[11px]">فعال</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 font-bold text-[11px]">غیرفعال</span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button"
                                            @click="editPrize = {{ json_encode($prize) }}; editModalOpen = true"
                                            class="px-3 py-1.5 bg-[#F7F9FA] hover:bg-[#D9D9D9] text-[#353535] rounded-lg text-[11px] font-bold transition-colors">
                                        ویرایش
                                    </button>
                                    <form method="POST" action="{{ route('admin.loyalty.wheel-prizes.delete', $prize) }}" class="inline" onsubmit="return confirm('آیا از حذف این جایزه اطمینان دارید؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-[11px] font-bold transition-colors">
                                            حذف
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-[#353535]/50">
                                هیچ جایزه‌ای برای گردونه شانس ثبت نشده است.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Modal -->
    <div x-show="addModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div @click.away="addModalOpen = false" class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-xl">
            <h3 class="text-sm font-black text-[#353535]">افزودن جایزه جدید به گردونه</h3>
            <form method="POST" action="{{ route('admin.loyalty.wheel-prizes.store') }}" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-[#353535] mb-1">عنوان جایزه *</label>
                    <input type="text" name="title" required placeholder="مثلاً: ۲۰ امتیاز باشگاه" class="w-full px-3 py-2 text-xs border border-[#D9D9D9] rounded-xl focus:border-[#3C6E71] focus:outline-none">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-[#353535] mb-1">نوع جایزه *</label>
                        <select name="prize_type" class="w-full px-3 py-2 text-xs border border-[#D9D9D9] rounded-xl focus:border-[#3C6E71] focus:outline-none">
                            <option value="points">امتیاز (Points)</option>
                            <option value="discount">کد تخفیف</option>
                            <option value="free_product">محصول هدیه</option>
                            <option value="none">پوچ (بدون جایزه)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#353535] mb-1">مقدار عددی جایزه *</label>
                        <input type="number" name="prize_value" value="10" min="0" step="1" required class="w-full px-3 py-2 text-xs border border-[#D9D9D9] rounded-xl focus:border-[#3C6E71] focus:outline-none tabular-nums dir-ltr text-right">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-[#353535] mb-1">وزن شانس (Probability) *</label>
                        <input type="number" name="win_probability" value="10" min="0" max="100" required class="w-full px-3 py-2 text-xs border border-[#D9D9D9] rounded-xl focus:border-[#3C6E71] focus:outline-none tabular-nums dir-ltr text-right">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#353535] mb-1">وضعیت *</label>
                        <select name="status" class="w-full px-3 py-2 text-xs border border-[#D9D9D9] rounded-xl focus:border-[#3C6E71] focus:outline-none">
                            <option value="active">فعال</option>
                            <option value="inactive">غیرفعال</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-[#D9D9D9]">
                    <button type="button" @click="addModalOpen = false" class="px-4 py-2 bg-[#F7F9FA] hover:bg-[#D9D9D9] text-[#353535] rounded-xl text-xs font-bold">انصراف</button>
                    <button type="submit" class="px-5 py-2 bg-[#284B63] hover:bg-[#1E374A] text-white rounded-xl text-xs font-bold">ثبت جایزه</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div @click.away="editModalOpen = false" class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-xl">
            <h3 class="text-sm font-black text-[#353535]">ویرایش جایزه گردونه</h3>
            <form :action="`{{ url('/admin/loyalty/wheel-prizes') }}/${editPrize.id}`" method="POST" class="space-y-3.5">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-[#353535] mb-1">عنوان جایزه *</label>
                    <input type="text" name="title" x-model="editPrize.title" required class="w-full px-3 py-2 text-xs border border-[#D9D9D9] rounded-xl focus:border-[#3C6E71] focus:outline-none">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-[#353535] mb-1">نوع جایزه *</label>
                        <select name="prize_type" x-model="editPrize.prize_type" class="w-full px-3 py-2 text-xs border border-[#D9D9D9] rounded-xl focus:border-[#3C6E71] focus:outline-none">
                            <option value="points">امتیاز (Points)</option>
                            <option value="discount">کد تخفیف</option>
                            <option value="free_product">محصول هدیه</option>
                            <option value="none">پوچ (بدون جایزه)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#353535] mb-1">مقدار عددی جایزه *</label>
                        <input type="number" name="prize_value" x-model="editPrize.prize_value" min="0" step="1" required class="w-full px-3 py-2 text-xs border border-[#D9D9D9] rounded-xl focus:border-[#3C6E71] focus:outline-none tabular-nums dir-ltr text-right">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-[#353535] mb-1">وزن شانس *</label>
                        <input type="number" name="win_probability" x-model="editPrize.win_probability" min="0" max="100" required class="w-full px-3 py-2 text-xs border border-[#D9D9D9] rounded-xl focus:border-[#3C6E71] focus:outline-none tabular-nums dir-ltr text-right">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#353535] mb-1">وضعیت *</label>
                        <select name="status" x-model="editPrize.status" class="w-full px-3 py-2 text-xs border border-[#D9D9D9] rounded-xl focus:border-[#3C6E71] focus:outline-none">
                            <option value="active">فعال</option>
                            <option value="inactive">غیرفعال</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-[#D9D9D9]">
                    <button type="button" @click="editModalOpen = false" class="px-4 py-2 bg-[#F7F9FA] hover:bg-[#D9D9D9] text-[#353535] rounded-xl text-xs font-bold">انصراف</button>
                    <button type="submit" class="px-5 py-2 bg-[#284B63] hover:bg-[#1E374A] text-white rounded-xl text-xs font-bold">ذخیره تغییرات</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
