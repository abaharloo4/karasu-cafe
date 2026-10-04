@extends('layouts.app')

@section('title', 'مدیریت لول‌ها')
@section('header_title', 'لول‌بندی مشتریان باشگاه بی کافه')

@section('content')
<div class="space-y-6">

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-black text-[#353535] flex items-center gap-2">
                <svg class="w-6 h-6 text-[#C8A35A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span>لول‌های باشگاه مشتریان</span>
            </h1>
            <p class="text-xs text-[#353535]/60 mt-1">مثلاً برنزی، نقره‌ای، طلایی — هر لول با حداقل خرید و کش‌بک مشخص</p>
        </div>
        <a href="{{ route('admin.loyalty.tiers.create') }}" class="px-4 py-2.5 bg-[#284B63] hover:bg-[#1E374A] text-white font-bold rounded-xl text-xs transition-colors flex items-center gap-2 min-h-[44px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>ساختن لول جدید</span>
        </a>
    </div>

    <div class="bg-white border border-[#D9D9D9] rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-start text-xs">
                <thead>
                    <tr class="bg-[#F7F9FA] border-b border-[#D9D9D9] text-[#353535]/60">
                        <th class="p-4 font-bold">رنگ</th>
                        <th class="p-4 font-bold">اسم لول</th>
                        <th class="p-4 font-bold">حداقل خرید</th>
                        <th class="p-4 font-bold">کش‌بک</th>
                        <th class="p-4 font-bold">ارسال رایگان</th>
                        <th class="p-4 font-bold text-center">عملیات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#D9D9D9]/50 text-[#353535]">
                    @forelse($tiers as $tier)
                        <tr>
                            <td class="p-4">
                                <div class="w-8 h-8 rounded-xl" style="background-color: {{ $tier->badge_color }}"></div>
                            </td>
                            <td class="p-4 font-black">{{ $tier->name }}</td>
                            <td class="p-4 font-bold tabular-nums">{{ number_format($tier->min_spend) }} تومان</td>
                            <td class="p-4 font-bold text-emerald-700">{{ $tier->discount_percentage }}٪</td>
                            <td class="p-4">
                                @if($tier->free_delivery)
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[11px]">بله</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 font-bold text-[11px]">خیر</span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.loyalty.tiers.edit', $tier) }}" class="px-3 py-1.5 bg-[#F7F9FA] hover:bg-[#D9D9D9] text-[#353535] rounded-lg text-[11px] font-bold transition-colors">ویرایش</a>
                                    <form method="POST" action="{{ route('admin.loyalty.tiers.delete', $tier) }}" class="inline" onsubmit="return confirm('مطمئنی از حذف لول «{{ $tier->name }}»؟ مشتریان این لول بدون لول میشن.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-[11px] font-bold transition-colors">حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-[#353535]/50">هنوز لولی ساخته نشده. اولین لول (مثلاً «برنزی») رو بساز.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.loyalty.index') }}" class="text-xs font-bold text-[#284B63] hover:underline flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
            <span>برگشت به داشبورد باشگاه</span>
        </a>
    </div>

</div>
@endsection
