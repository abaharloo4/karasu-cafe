@extends('layouts.app')

@section('title', 'مرکز جامع کاربران سیستم')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-black text-[#353535]">مرکز جامع پرسنل و مشتریان</h1>
            <p class="text-xs text-[#353535]/60 mt-1">مشاهده، بررسی و تغییرات سطوح دسترسی تمامی افراد فعال در سامانه بی کافه</p>
        </div>
        <div class="flex items-center gap-2">
            @if($tab === 'staff')
                <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-[#284B63] hover:bg-[#1f3a4d] text-white font-bold text-xs rounded-xl transition-colors flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>تعریف پرسنل جدید</span>
                </a>
            @else
                <a href="{{ route('customers.create') }}" class="px-4 py-2 bg-[#C8A35A] hover:bg-[#b5924b] text-white font-bold text-xs rounded-xl transition-colors flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>تعریف مشتری جدید</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Category Tabs -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <a href="{{ route('admin.users.index', ['tab' => 'staff']) }}" class="p-4 rounded-2xl border transition-all flex items-center justify-between {{ $tab === 'staff' ? 'bg-[#284B63] text-white border-[#284B63] shadow-md' : 'bg-white border-gray-100 text-[#353535] hover:border-gray-200' }}">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $tab === 'staff' ? 'bg-white/10 text-white' : 'bg-[#284B63]/10 text-[#284B63]' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <div class="text-xs font-black">مدیران و پرسنل (Staff)</div>
                    <div class="text-[10px] opacity-75">دسترسی‌های پنل مدیریت و صندوق</div>
                </div>
            </div>
            <div class="text-lg font-black font-mono">{{ number_format($counts['staff']) }}</div>
        </a>

        <a href="{{ route('admin.users.index', ['tab' => 'customers']) }}" class="p-4 rounded-2xl border transition-all flex items-center justify-between {{ $tab === 'customers' ? 'bg-[#C8A35A] text-white border-[#C8A35A] shadow-md' : 'bg-white border-gray-100 text-[#353535] hover:border-gray-200' }}">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $tab === 'customers' ? 'bg-white/10 text-white' : 'bg-[#C8A35A]/10 text-[#C8A35A]' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <div class="text-xs font-black">مشتریان کافه (Customers)</div>
                    <div class="text-[10px] opacity-75">مشترکین، اعضای باشگاه و خریداران</div>
                </div>
            </div>
            <div class="text-lg font-black font-mono">{{ number_format($counts['customers']) }}</div>
        </a>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-3">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ $search }}" placeholder="جستجو بر اساس نام یا شماره موبایل..." class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs text-[#353535] focus:outline-none focus:border-[#3C6E71]">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            @if($tab === 'staff')
                <select name="role" class="px-3 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-medium text-[#353535] focus:outline-none focus:border-[#3C6E71]">
                    <option value="">همه نقش‌ها</option>
                    <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>مدیر کل (Admin)</option>
                    <option value="staff" {{ $role === 'staff' ? 'selected' : '' }}>پرسنل / باریستا (Staff)</option>
                    <option value="cashier" {{ $role === 'cashier' ? 'selected' : '' }}>صندوق‌دار (Cashier)</option>
                </select>
            @endif

            <x-button variant="secondary" size="md" type="submit">فیلتر</x-button>
            @if($search || $role)
                <a href="{{ route('admin.users.index', ['tab' => $tab]) }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-[#353535] text-xs font-bold rounded-xl transition-colors flex items-center justify-center">پاک کردن</a>
            @endif
        </form>
    </div>

    <!-- Content Table Based On Tab -->
    @if($tab === 'staff')
        <!-- STAFF TABLE -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-[#353535]/60 font-black">
                            <th class="p-4">پرسنل</th>
                            <th class="p-4">شماره تماس</th>
                            <th class="p-4">ایمیل</th>
                            <th class="p-4">نقش و سطح دسترسی</th>
                            <th class="p-4">تاریخ تعریف</th>
                            <th class="p-4 text-center">عملیات مدیریت</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="p-4">
                                    <div class="font-black text-sm text-[#353535]">{{ $user->name }}</div>
                                    @if($user->id === auth()->id())
                                        <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[10px] bg-blue-50 text-blue-600 font-bold">حساب شما</span>
                                    @endif
                                </td>
                                <td class="p-4 font-mono font-bold text-[#284B63]" dir="ltr">{{ $user->phone ?? '—' }}</td>
                                <td class="p-4 font-mono text-gray-500" dir="ltr">{{ $user->email ?? '—' }}</td>
                                <td class="p-4">
                                    @if($user->role === 'admin')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-purple-50 text-purple-700 border border-purple-200">مدیر کل سیستم</span>
                                    @elseif($user->role === 'staff')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-teal-50 text-teal-700 border border-teal-200">پرسنل / باریستا</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-50 text-amber-700 border border-amber-200">صندوق‌دار</span>
                                    @endif
                                </td>
                                <td class="p-4 text-gray-500">{{ \Morilog\Jalali\Jalalian::fromCarbon($user->created_at)->format('Y/m/d') }}</td>
                                <td class="p-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="p-1.5 text-gray-500 hover:text-[#3C6E71] rounded-lg hover:bg-gray-100 transition-colors" title="ویرایش دسترسی">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </a>
                                        @if($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('آیا از حذف این کاربر اطمینان دارید؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500 rounded-lg hover:bg-red-50 transition-colors" title="حذف کاربر">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-8 text-center text-gray-400">هیچ کاربری یافت نشد.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
                <div class="p-4 border-t border-gray-100">{{ $users->links() }}</div>
            @endif
        </div>

    @else
        <!-- CUSTOMERS TABLE -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-[#353535]/60 font-black">
                            <th class="p-4">مشتری</th>
                            <th class="p-4">شماره موبایل</th>
                            <th class="p-4">سطح وفاداری</th>
                            <th class="p-4">کیف پول / امتیاز</th>
                            <th class="p-4">وضعیت اشتراک</th>
                            <th class="p-4 text-center">پرونده مشتری (۳۶۰°)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($customers as $customer)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="p-4 font-black text-sm text-[#353535]">
                                    <a href="{{ route('customers.show', $customer) }}" class="hover:text-[#3C6E71] hover:underline">
                                        {{ $customer->name }}
                                    </a>
                                </td>
                                <td class="p-4 font-mono font-bold text-[#284B63]" dir="ltr">{{ $customer->phone }}</td>
                                <td class="p-4">
                                    @if($customer->tier)
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#C8A35A]/15 text-[#C8A35A] border border-[#C8A35A]/30">
                                            {{ $customer->tier->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">عادی</span>
                                    @endif
                                </td>
                                <td class="p-4 space-y-0.5">
                                    <div class="font-bold text-emerald-600 font-mono">{{ number_format($customer->wallet_balance ?? 0) }} ت</div>
                                    <div class="text-[10px] text-gray-500 font-mono">{{ number_format($customer->loyalty_points ?? 0) }} امتیاز</div>
                                </td>
                                <td class="p-4">
                                    @if($customer->activeSubscription && $customer->activeSubscription->isValid())
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            {{ $customer->activeSubscription->plan?->name }} ({{ $customer->activeSubscription->remaining_items }} فنجان)
                                        </span>
                                    @else
                                        <span class="text-gray-400">فاقد اشتراک فعال</span>
                                    @endif
                                </td>
                                <td class="p-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('customers.show', $customer) }}" class="px-3 py-1.5 bg-[#284B63] hover:bg-[#1f3a4d] text-white rounded-lg text-[10px] font-bold transition-colors">
                                            مشاهده کامل و ویرایش
                                        </a>
                                        <a href="{{ route('daily-usage.index', ['phone' => $customer->phone]) }}" class="px-2.5 py-1.5 bg-[#C8A35A] hover:bg-[#b5924b] text-white rounded-lg text-[10px] font-bold transition-colors" title="ورود به صندوق">
                                            صندوق
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-8 text-center text-gray-400">هیچ مشتری‌ای یافت نشد.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($customers->hasPages())
                <div class="p-4 border-t border-gray-100">{{ $customers->links() }}</div>
            @endif
        </div>
    @endif

</div>
@endsection
