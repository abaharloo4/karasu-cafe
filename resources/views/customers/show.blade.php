@extends('layouts.app')

@section('title', 'پروفایل مشتری - ' . $customer->name)
@section('header_title', 'پروفایل مشتری: ' . $customer->name)

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">

    <!-- Header Card -->
    <div class="bg-white border border-gray-100 p-6 rounded-3xl flex flex-wrap items-center justify-between gap-4 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-[#284B63] flex items-center justify-center text-white font-black text-2xl shadow-md">
                {{ mb_substr($customer->name, 0, 1) }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-black text-[#353535]">{{ $customer->name }}</h2>
                    @if($customer->tier)
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#C8A35A]/15 text-[#C8A35A] border border-[#C8A35A]/30">
                            سطح {{ $customer->tier->name }}
                        </span>
                    @endif
                </div>
                <div class="flex items-center gap-4 text-xs font-semibold text-gray-500 mt-1">
                    <span class="font-mono text-[#284B63] font-bold" dir="ltr">{{ $customer->phone }}</span>
                    @if($customer->birthdate)
                        <span>• تولد: {{ \Morilog\Jalali\Jalalian::fromCarbon(\Illuminate\Support\Carbon::parse($customer->birthdate))->format('Y/m/d') }}</span>
                    @endif
                    <span>• تاریخ عضویت: {{ \Morilog\Jalali\Jalalian::fromCarbon($customer->created_at)->format('Y/m/d') }}</span>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('daily-usage.index', ['phone' => $customer->phone]) }}" class="px-4 py-2 bg-[#C8A35A] hover:bg-[#b5924b] text-white font-bold rounded-xl text-xs transition-colors shadow-sm flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>ورود به صندوق POS</span>
            </a>
            <a href="{{ route('subscriptions.assign', ['customer_id' => $customer->id]) }}" class="px-4 py-2 bg-[#3C6E71] hover:bg-[#2e5557] text-white font-bold rounded-xl text-xs transition-colors shadow-sm flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>تخصیص اشتراک</span>
            </a>
            <a href="{{ route('customers.edit', $customer) }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-[#353535] font-bold rounded-xl text-xs transition-colors">
                ویرایش
            </a>
            <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="inline-block" onsubmit="return confirm('آیا از حذف کامل این مشتری و سوابق اطمینان دارید؟');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-xl text-xs transition-colors">
                    حذف
                </button>
            </form>
        </div>
    </div>

    <!-- Quick Stats Overview (Customer 360) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <!-- Wallet Balance -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm space-y-1">
            <span class="text-xs text-gray-500 font-bold block">موجودی کیف پول</span>
            <div class="text-xl font-black text-emerald-600">
                {{ number_format($customer->wallet_balance ?? 0) }} <span class="text-xs font-normal text-gray-500">تومان</span>
            </div>
        </div>

        <!-- Loyalty Points -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm space-y-1">
            <span class="text-xs text-gray-500 font-bold block">امتیاز باشگاه مشتریان</span>
            <div class="text-xl font-black text-[#C8A35A]">
                {{ number_format($customer->loyalty_points ?? 0) }} <span class="text-xs font-normal text-gray-500">امتیاز</span>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm space-y-1">
            <span class="text-xs text-gray-500 font-bold block">تعداد سفارشات ثبت شده</span>
            <div class="text-xl font-black text-[#284B63]">
                {{ number_format($customer->orders->count()) }} <span class="text-xs font-normal text-gray-500">فاکتور</span>
            </div>
        </div>

        <!-- Active Subscription Quota -->
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm space-y-1">
            <span class="text-xs text-gray-500 font-bold block">سهمیه اشتراک باقی‌مانده</span>
            <div class="text-xl font-black text-[#3C6E71]">
                {{ $active_subscription ? number_format($active_subscription->remaining_items) : 0 }} <span class="text-xs font-normal text-gray-500">فنجان</span>
            </div>
        </div>
    </div>

    <!-- Active Subscription Status Card -->
    <div class="bg-white border border-gray-100 p-6 rounded-3xl space-y-4 shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
            <h3 class="text-sm font-black text-[#353535] flex items-center gap-2">
                <svg class="w-5 h-5 text-[#3C6E71]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2H5z"/></svg>
                <span>وضعیت اشتراک فعال</span>
            </h3>
            @if($active_subscription)
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">فعال و معتبر</span>
                    <a href="{{ route('subscriptions.edit', $active_subscription) }}" class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-bold transition-colors">ویرایش</a>
                    <form method="POST" action="{{ route('subscriptions.destroy', $active_subscription) }}" class="inline-block" onsubmit="return confirm('آیا از حذف کامل این اشتراک اطمینان دارید؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-bold transition-colors">حذف اشتراک</button>
                    </form>
                </div>
            @endif
        </div>

        @if($active_subscription)
            <div class="p-5 rounded-2xl bg-gray-50 border border-gray-100 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <span class="text-xs text-gray-500 block mb-0.5">پلن اشتراک:</span>
                        <span class="text-base font-black text-[#284B63]">{{ $active_subscription->plan?->name }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 block mb-0.5">سهمیه باقیمانده:</span>
                        <span class="text-base font-black text-[#C8A35A]">{{ $active_subscription->remaining_items }} از {{ $active_subscription->total_items }} فنجان</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 block mb-0.5">تاریخ انقضا:</span>
                        <span class="text-sm font-bold text-[#353535]">{{ \Morilog\Jalali\Jalalian::fromCarbon(\Illuminate\Support\Carbon::parse($active_subscription->expire_date))->format('Y/m/d') }}</span>
                    </div>
                </div>

                @if($active_subscription->plan && $active_subscription->plan->products->isNotEmpty())
                    <div class="border-t border-gray-200 pt-3">
                        <span class="text-xs font-bold text-gray-600 block mb-2">محصولات مجاز برای دریافت سهمیه روزانه:</span>
                        <div class="flex flex-wrap gap-2">
                            @foreach($active_subscription->plan->products as $prod)
                                <span class="px-2.5 py-1 bg-white border border-gray-200 text-[#353535] rounded-lg text-xs font-semibold shadow-2xs">
                                    {{ $prod->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @else
            <div class="p-6 rounded-2xl bg-gray-50 border border-gray-100 text-center text-gray-500 text-xs">
                این مشتری در حال حاضر هیچ اشتراک فعالی ندارد.
            </div>
        @endif
    </div>

    <!-- Customer Saved Addresses (Delivery) -->
    <div class="bg-white border border-gray-100 p-6 rounded-3xl space-y-4 shadow-sm">
        <h3 class="text-sm font-black text-[#353535] flex items-center gap-2 border-b border-gray-100 pb-3">
            <svg class="w-5 h-5 text-[#284B63]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
            <span>آدرس‌های ثبت‌شده برای پیک شیراز</span>
        </h3>

        @if($customer->addresses && $customer->addresses->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($customer->addresses as $addr)
                    <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black text-[#353535]">{{ $addr->title ?? 'آدرس' }}</span>
                            @if($addr->deliveryZone)
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-teal-50 text-teal-700 font-bold border border-teal-200">{{ $addr->deliveryZone->name }}</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-600 leading-relaxed">{{ $addr->address }}</p>
                        @if($addr->notes)
                            <p class="text-[11px] text-gray-400">یادداشت: {{ $addr->notes }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-4 text-center text-gray-400 text-xs">
                هیچ آدرس پیکی برای این کاربر ثبت نشده است.
            </div>
        @endif
    </div>

    <!-- Orders History Section -->
    <div class="bg-white border border-gray-100 p-6 rounded-3xl space-y-4 shadow-sm">
        <h3 class="text-sm font-black text-[#353535] flex items-center gap-2 border-b border-gray-100 pb-3">
            <svg class="w-5 h-5 text-[#284B63]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>تاریخچه سفارش‌های ثبت شده</span>
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-[#353535]/60 font-black">
                        <th class="p-3">شماره سفارش</th>
                        <th class="p-3">تاریخ ثبت</th>
                        <th class="p-3">نوع سفارش</th>
                        <th class="p-3">اقلام سفارش</th>
                        <th class="p-3">مبلغ پرداختی</th>
                        <th class="p-3">وضعیت</th>
                        <th class="p-3 text-center">مشاهده</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recent_orders as $order)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="p-3 font-mono font-bold text-[#284B63]">{{ $order->order_number ?? ('#' . $order->id) }}</td>
                            <td class="p-3 text-gray-500">{{ \Morilog\Jalali\Jalalian::fromCarbon($order->created_at)->format('Y/m/d H:i') }}</td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $order->isDelivery() ? 'bg-blue-50 text-blue-700' : 'bg-emerald-50 text-emerald-700' }}">
                                    {{ $order->isDelivery() ? 'پیک' : 'حضوری' }}
                                </span>
                            </td>
                            <td class="p-3">
                                <div class="max-w-xs truncate text-gray-700">
                                    {{ $order->items->map(fn($i) => ($i->product?->name ?? 'محصول') . ' (x' . $i->quantity . ')')->implode('، ') }}
                                </div>
                            </td>
                            <td class="p-3 font-black text-[#284B63]">{{ number_format($order->final_payable) }} تومان</td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-700">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="p-3 text-center">
                                <a href="{{ route('orders.show', $order) }}" class="p-1.5 text-gray-500 hover:text-[#3C6E71] rounded-lg hover:bg-gray-100 inline-block transition-colors" title="مشاهده سفارش">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-gray-400">
                                سفارشی برای این مشتری یافت نشد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
