@extends('layouts.app')

@section('title', 'مدیریت مشتریان')
@section('header_title', 'لیست مشتریان بی کافه (B Cafe)')

@section('content')
<div class="space-y-6">

    <!-- Actions Header -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <form method="GET" action="{{ route('customers.index') }}" class="flex-1 max-w-md">
            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="جستجو با نام، شماره موبایل..." class="w-full px-4 py-2.5 pr-10 rounded-xl bg-[#FFFFFF] border border-[#CCD5C8] text-[#1F2520] placeholder-[#8A918B] text-xs focus:outline-none focus:border-[#6A8C60]">
                <svg class="w-4 h-4 text-[#8A918B] absolute right-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </form>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('reports.export.customers') }}" class="px-4 py-2.5 bg-white hover:bg-slate-50 text-[#19242B] border border-slate-200 font-bold rounded-xl text-xs flex items-center gap-1.5 shadow-xs">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>خروجی اکسل مشتریان</span>
            </a>

            <a href="{{ route('customers.create') }}" class="px-5 py-2.5 bg-[#387780] hover:bg-[#2E626A] text-white font-bold rounded-xl text-xs transition-colors shadow-xs flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>ثبت مشتری جدید</span>
            </a>
        </div>
    </div>

    <!-- Customers Table -->
    <div class="bg-[#FCFCFB] border border-[#D9E0D6] rounded-2xl overflow-hidden shadow-sm">
        <!-- Mobile Card View (sm:hidden) -->
        <div class="sm:hidden divide-y divide-[#E7ECE4]">
            @forelse($customers as $customer)
                <div class="p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-sm text-[#1F2520]">{{ $customer->name }}</span>
                        @if($customer->subscriptions->count() > 0)
                            <span class="px-2.5 py-0.5 rounded-full bg-[#EAF2E7] text-[#4F8A4B] text-[10px] font-bold">اشتراک فعال</span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full bg-[#EEF2EC] text-[#8A918B] text-[10px]">عادی</span>
                        @endif
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <span class="font-mono text-[#48504B]" dir="ltr">{{ $customer->phone }}</span>
                        <span class="text-[11px] text-[#6E756F] tabular-nums">{{ \App\Helpers\JalaliHelper::format($customer->created_at, 'Y/m/d') }}</span>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-1 border-t border-[#E7ECE4]/60">
                        <a href="{{ route('customers.show', $customer) }}" class="px-3 py-1.5 bg-[#EEF2EC] hover:bg-[#D9E0D6] text-[#1F2520] rounded-lg text-xs font-semibold">پروفایل</a>
                        <a href="{{ route('daily-usage.index', ['phone' => $customer->phone]) }}" class="px-3 py-1.5 bg-[#F7F0DE] hover:bg-[#C8A35A] hover:text-white text-[#B78F44] rounded-lg text-xs font-semibold">صندوق</a>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-xs text-[#8A918B]">هیچ مشتری پیدا نشد.</div>
            @endforelse
        </div>

        <!-- Desktop Table View (hidden sm:block) -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-start text-xs">
                <thead>
                    <tr class="bg-[#EEF2EC] text-[#6E756F] border-b border-[#D9E0D6]">
                        <th class="p-4 font-semibold">#</th>
                        <th class="p-4 font-semibold">نام مشتری</th>
                        <th class="p-4 font-semibold">شماره موبایل</th>
                        <th class="p-4 font-semibold">وضعیت اشتراک</th>
                        <th class="p-4 font-semibold">تاریخ ثبت</th>
                        <th class="p-4 font-semibold text-center">عملیات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E7ECE4] text-[#48504B]">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-[#F7F8F5] transition-all">
                            <td class="p-4 text-[#8A918B] text-xs">{{ $customer->id }}</td>
                            <td class="p-4 font-bold text-[#1F2520]">{{ $customer->name }}</td>
                            <td class="p-4 font-mono text-[#48504B]">{{ $customer->phone }}</td>
                            <td class="p-4">
                                @if($customer->subscriptions->count() > 0)
                                    <span class="px-2.5 py-0.5 rounded-full bg-[#EAF2E7] text-[#4F8A4B] text-[11px] font-bold">دارای اشتراک فعال</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full bg-[#EEF2EC] text-[#8A918B] text-[11px]">بدون اشتراک</span>
                                @endif
                            </td>
                            <td class="p-4 text-xs text-[#6E756F]">{{ \App\Helpers\JalaliHelper::format($customer->created_at, 'Y/m/d') }}</td>
                            <td class="p-4 text-center space-x-2 space-x-reverse">
                                <a href="{{ route('customers.show', $customer) }}" class="px-3 py-1.5 bg-[#EEF2EC] hover:bg-[#D9E0D6] text-[#1F2520] rounded-lg text-xs font-semibold">مشاهده پروفایل</a>
                                <a href="{{ route('daily-usage.index', ['phone' => $customer->phone]) }}" class="px-3 py-1.5 bg-[#F7F0DE] hover:bg-[#C8A35A] hover:text-white text-[#B78F44] rounded-lg text-xs font-semibold">صندوق</a>
                                <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="inline-block" onsubmit="return confirm('آیا از حذف این مشتری و تمام سوابق مربوطه اطمینان دارید؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 bg-[#FDF2F2] hover:bg-[#F8B4B4] text-[#C94A4A] rounded-lg text-xs font-semibold">حذف</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-[#8A918B]">هیچ مشتری پیدا نشد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="p-4 border-t border-[#E7ECE4]">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
