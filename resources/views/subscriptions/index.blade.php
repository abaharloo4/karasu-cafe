@extends('layouts.app')

@section('title', 'پلن‌ها و اشتراک‌ها')
@section('header_title', 'مدیریت پلن‌ها و اشتراک‌های صادرشده')

@section('content')
<div class="space-y-8">

    <!-- Plans Section -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-bold text-[#1F2520] flex items-center gap-2">
                <svg class="w-5 h-5 text-[#3A5232]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span>پلن‌های اشتراک تعریف‌شده</span>
            </h2>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('subscriptions.create-plan') }}" class="px-4 py-2.5 bg-[#3A5232] hover:bg-[#2F4428] text-white font-bold rounded-xl text-xs transition-all shadow-md flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>تعریف پلن جدید</span>
                </a>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($plans as $plan)
                <div class="bg-[#FCFCFB] border border-[#D9E0D6] p-6 rounded-3xl space-y-4 flex flex-col justify-between shadow-sm">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-black text-[#1F2520]">{{ $plan->name }}</h3>
                            <span class="px-2.5 py-1 bg-[#EAF2E7] text-[#3A5232] font-extrabold rounded-lg text-xs">{{ number_format($plan->price) }} تومان</span>
                        </div>
                        <div class="text-xs text-[#6E756F] space-y-1">
                            <div>• تعداد اعتبار کل: <span class="font-bold text-[#1F2520]">{{ $plan->total_items }} فنجان</span></div>
                            <div>• مدت اعتبار: <span class="font-bold text-[#1F2520]">{{ $plan->duration_days }} روز</span></div>
                            <div>• وضعیت: <span class="font-bold {{ $plan->status === 'active' ? 'text-[#4F8A4B]' : 'text-[#C94A4A]' }}">{{ $plan->status === 'active' ? 'فعال' : 'غیرفعال' }}</span></div>
                        </div>
                    </div>

                    <div class="border-t border-[#E7ECE4] pt-3 flex items-center justify-between">
                        <div class="flex flex-wrap gap-1.5 max-w-[70%]">
                            @foreach($plan->products as $p)
                                <span class="px-2 py-0.5 bg-[#EEF2EC] border border-[#D9E0D6] text-[#48504B] rounded text-[10px]">{{ $p->name }}</span>
                            @endforeach
                        </div>
                        @if(auth()->user()->isAdmin())
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('subscriptions.edit-plan', $plan) }}" class="px-3 py-1.5 bg-[#EEF2EC] hover:bg-[#D9E0D6] text-[#1F2520] font-semibold rounded-lg text-xs transition-colors">ویرایش</a>
                                <form method="POST" action="{{ route('subscriptions.destroy-plan', $plan) }}" class="inline-block" onsubmit="return confirm('آیا از حذف کامل پلن «{{ $plan->name }}» اطمینان دارید؟ تمام اشتراک‌های صادرشده این پلن نیز حذف خواهند شد.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 bg-[#FDF2F2] hover:bg-[#F8B4B4] text-[#C94A4A] font-semibold rounded-lg text-xs transition-colors" title="حذف پلن اشتراک">
                                        حذف
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-[#FCFCFB] border border-[#D9E0D6] p-8 rounded-3xl text-center text-[#8A918B]">
                    هنوز هیچ پلن اشتراکی تعریف نشده است.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Active Subscriptions Table -->
    <div class="space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-base font-bold text-[#1F2520] flex items-center gap-2">
                <svg class="w-5 h-5 text-[#C8A35A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2H5z"/></svg>
                <span>لیست اشتراک‌های صادرشده برای مشتریان</span>
            </h2>
            <div class="flex items-center gap-2 flex-wrap">
                <!-- Search -->
                <form method="GET" action="{{ route('subscriptions.index') }}" class="relative">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-4 h-4 text-[#6E756F]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="جستجو با اسم یا شماره..."
                           class="block w-48 sm:w-60 min-h-[44px] pl-3 pr-10 text-xs text-[#1F2520] bg-white border border-[#D9E0D6] rounded-xl focus:ring-[#3A5232] focus:border-[#3A5232] placeholder-[#8A918B] font-bold transition-all">
                </form>
                <a href="{{ route('daily-usage.index') }}" class="px-4 py-2.5 bg-[#3A5232] hover:bg-[#2F4428] text-white font-bold rounded-xl text-xs transition-all shadow-md flex items-center gap-2 min-h-[44px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>ثبت سفارش سهمیه روزانه</span>
                </a>
                <a href="{{ route('subscriptions.assign') }}" class="px-4 py-2.5 bg-[#C8A35A] hover:bg-[#B78F44] text-white font-bold rounded-xl text-xs transition-all shadow-md flex items-center gap-2 min-h-[44px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>تخصیص اشتراک جدید</span>
                </a>
            </div>
        </div>

        @if($search)
            <div class="flex items-center gap-2 text-xs text-[#6E756F]">
                <span>نتایج جستجوی:</span>
                <span class="font-bold text-[#1F2520]">{{ $search }}</span>
                <a href="{{ route('subscriptions.index') }}" class="text-[#C94A4A] hover:underline font-bold">(پاک‌کردن)</a>
            </div>
        @endif

        <div class="bg-[#FCFCFB] border border-[#D9E0D6] rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-start text-xs">
                    <thead>
                        <tr class="bg-[#EEF2EC] text-[#6E756F] border-b border-[#D9E0D6]">
                            <th class="p-4 font-semibold">مشتری</th>
                            <th class="p-4 font-semibold">شماره</th>
                            <th class="p-4 font-semibold">نام پلن</th>
                            <th class="p-4 font-semibold">اعتبار باقی‌مانده</th>
                            <th class="p-4 font-semibold">تاریخ شروع</th>
                            <th class="p-4 font-semibold">تاریخ انقضا</th>
                            <th class="p-4 font-semibold">وضعیت</th>
                            <th class="p-4 font-semibold text-center">عملیات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E7ECE4] text-[#48504B]">
                        @forelse($activeSubscriptions as $sub)
                            <tr>
                                <td class="p-4 font-bold text-[#1F2520]">
                                    <a href="{{ route('customers.show', $sub->customer_id) }}" class="hover:underline hover:text-[#3A5232]">
                                        {{ $sub->customer->name ?? '-' }}
                                    </a>
                                </td>
                                <td class="p-4 text-[#6E756F] font-mono dir-ltr text-right">{{ $sub->customer->phone ?? '-' }}</td>
                                <td class="p-4">{{ $sub->plan?->name ?? 'پلن کافه' }}</td>
                                <td class="p-4 font-bold text-[#3A5232]">{{ $sub->remaining_items }} از {{ $sub->total_items }}</td>
                                <td class="p-4 text-[#6E756F]">{{ \App\Helpers\JalaliHelper::format($sub->start_date, 'Y/m/d') }}</td>
                                <td class="p-4 text-[#6E756F]">{{ \App\Helpers\JalaliHelper::format($sub->expire_date, 'Y/m/d') }}</td>
                                <td class="p-4">
                                    @if($sub->status === 'active' && $sub->remaining_items > 0)
                                        <span class="px-2 py-0.5 rounded-full bg-[#EAF2E7] text-[#4F8A4B] font-bold">فعال</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-[#FDF2F2] text-[#C94A4A] font-bold">لغوشده / منقضی</span>
                                    @endif
                                </td>
                                <td class="p-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                        @if($sub->status === 'active' && $sub->remaining_items > 0)
                                            <a href="{{ route('daily-usage.index', ['phone' => $sub->customer?->phone]) }}" class="px-3 py-1 bg-[#3A5232] hover:bg-[#2F4428] text-white rounded text-[11px] font-bold" title="تحویل سهمیه در صندوق">صندوق سهمیه</a>
                                        @endif
                                        <a href="{{ route('subscriptions.edit', $sub) }}" class="px-3 py-1 bg-[#EEF2EC] hover:bg-[#D9E0D6] text-[#1F2520] rounded text-[11px] font-semibold">ویرایش</a>
                                        @if($sub->status === 'active')
                                            <form method="POST" action="{{ route('subscriptions.cancel', $sub) }}" class="inline-block" onsubmit="return confirm('آیا از لغو این اشتراک اطمینان دارید؟');">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-[#F7F0DE] hover:bg-[#C8A35A] text-[#B78F44] hover:text-white rounded text-[11px] font-semibold">لغو</button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('subscriptions.destroy', $sub) }}" class="inline-block" onsubmit="return confirm('آیا از حذف کامل این اشتراک از دیتابیس اطمینان دارید؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1 bg-[#FDF2F2] hover:bg-[#F8B4B4] text-[#C94A4A] rounded text-[11px] font-semibold">حذف</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-[#8A918B]">هیچ اشتراکی صادر نشده است.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($activeSubscriptions->hasPages())
                <div class="p-4 border-t border-[#E7ECE4]">
                    {{ $activeSubscriptions->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
