@extends('layouts.app')

@section('title', 'ویرایش اشتراک مشتری')
@section('header_title', 'ویرایش اشتراک مشتری: ' . ($subscription->customer->name ?? ''))

@section('content')
<div class="max-w-xl mx-auto bg-[#FCFCFB] border border-[#D9E0D6] p-8 rounded-3xl space-y-6 shadow-sm">
    <h2 class="text-base font-bold text-[#1F2520] border-b border-[#E7ECE4] pb-4">ویرایش جزئیات اشتراک صادرشده</h2>

    @if($errors->any())
        <div class="p-4 rounded-xl bg-[#FDF2F2] border border-[#F8B4B4] text-[#C94A4A] text-xs space-y-1">
            @foreach($errors->all() as $error)
                <div>• {{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('subscriptions.update', $subscription) }}" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-xs font-semibold text-[#1F2520] mb-1.5">مشتری</label>
            <input type="text" value="{{ $subscription->customer->name ?? '-' }} ({{ $subscription->customer->phone ?? '-' }})" disabled class="w-full px-4 py-3 rounded-xl bg-[#EEF2EC] border border-[#CCD5C8] text-[#6E756F] text-xs font-bold">
        </div>

        <div>
            <label class="block text-xs font-semibold text-[#1F2520] mb-1.5">عنوان پلن</label>
            <input type="text" value="{{ $subscription->plan?->name ?? 'پلن کافه' }}" disabled class="w-full px-4 py-3 rounded-xl bg-[#EEF2EC] border border-[#CCD5C8] text-[#6E756F] text-xs font-bold">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-[#1F2520] mb-1.5">اعتبار باقی‌مانده (تعداد فنجان) *</label>
                <input type="number" name="remaining_items" value="{{ old('remaining_items', $subscription->remaining_items) }}" required min="0" max="{{ $subscription->total_items }}" class="w-full px-4 py-3 rounded-xl bg-[#FFFFFF] border border-[#CCD5C8] text-[#3A5232] focus:outline-none focus:border-[#6A8C60] text-xs font-bold min-h-[44px]">
            </div>

            <div>
                <x-persian-date-picker name="expire_date" label="تاریخ انقضا *" :value="old('expire_date', $subscription->expire_date?->format('Y-m-d'))" :required="true" />
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-[#1F2520] mb-1.5">وضعیت اشتراک *</label>
            <select name="status" class="w-full px-4 py-3 rounded-xl bg-[#FFFFFF] border border-[#CCD5C8] text-[#1F2520] focus:outline-none focus:border-[#6A8C60] text-xs font-medium min-h-[44px]">
                <option value="active" {{ $subscription->status === 'active' ? 'selected' : '' }}>فعال</option>
                <option value="inactive" {{ $subscription->status === 'inactive' ? 'selected' : '' }}>غیرفعال</option>
                <option value="expired" {{ $subscription->status === 'expired' ? 'selected' : '' }}>منقضی شده</option>
                <option value="cancelled" {{ $subscription->status === 'cancelled' ? 'selected' : '' }}>لغوشده</option>
            </select>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-[#E7ECE4]">
            <button type="button" onclick="if(confirm('آیا از حذف کامل این اشتراک از سیستم اطمینان دارید؟')) document.getElementById('delete-sub-form').submit();" class="px-4 py-2.5 bg-[#FDF2F2] hover:bg-[#F8B4B4] text-[#C94A4A] font-bold rounded-xl text-xs transition-all shadow-sm min-h-[42px]">
                حذف این اشتراک
            </button>

            <div class="flex items-center gap-3">
                <x-button variant="ghost" :href="route('subscriptions.index')">انصراف</x-button>
                <x-button variant="primary" type="submit" class="shadow-md">به‌روزرسانی اشتراک</x-button>
            </div>
        </div>
    </form>

    <form id="delete-sub-form" method="POST" action="{{ route('subscriptions.destroy', $subscription) }}" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection
