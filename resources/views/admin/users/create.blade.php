@extends('layouts.app')

@section('title', 'تعریف کاربر جدید')

@section('content')
<div class="max-w-xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-black text-[#353535]">تعریف کاربر جدید</h1>
            <p class="text-xs text-[#353535]/60 mt-1">افزودن دسترسی جدید به سیستم برای ادمین یا پرسنل</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="px-3.5 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-[#353535] text-xs font-bold transition-colors">
            بازگشت
        </a>
    </div>

    @if($errors->any())
        <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold space-y-1">
            @foreach($errors->all() as $err)
                <p>• {{ $err }}</p>
            @endforeach
        </div>
    @endif

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-gray-100 shadow-sm">
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-[#353535] mb-1.5">نام و نام خانوادگی <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="مثال: رضا محمدی" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-xs text-[#353535] focus:outline-none focus:border-[#3C6E71]">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#353535] mb-1.5">شماره تماس (موبایل) <span class="text-red-500">*</span></label>
                <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="09171234567" dir="ltr" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-xs font-mono font-bold text-[#353535] focus:outline-none focus:border-[#3C6E71] text-center">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#353535] mb-1.5">ایمیل (اختیاری)</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="user@karasu.cafe" dir="ltr" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-xs font-mono text-[#353535] focus:outline-none focus:border-[#3C6E71]">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-[#353535] mb-1.5">رمز عبور <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required placeholder="حداقل ۶ کاراکتر" dir="ltr" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-xs text-[#353535] focus:outline-none focus:border-[#3C6E71]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#353535] mb-1.5">نقش کاربری <span class="text-red-500">*</span></label>
                    <select name="role" required class="w-full px-3 py-3 rounded-xl bg-gray-50 border border-gray-200 text-xs font-medium text-[#353535] focus:outline-none focus:border-[#3C6E71]">
                        <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>پرسنل / باریستا (Staff)</option>
                        <option value="cashier" {{ old('role') === 'cashier' ? 'selected' : '' }}>صندوق‌دار (Cashier)</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>مدیر سیستم (Admin)</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-[#353535] text-xs font-bold transition-colors">
                    انصراف
                </a>
                <x-button variant="primary" size="md" type="submit">
                    ذخیره و ثبت کاربر
                </x-button>
            </div>
        </form>
    </div>

</div>
@endsection
