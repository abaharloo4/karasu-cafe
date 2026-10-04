@extends('layouts.app')

@section('title', 'ویرایش پلن اشتراک')
@section('header_title', 'ویرایش پلن: ' . $plan->name)

@section('content')
<div class="max-w-xl mx-auto bg-[#FCFCFB] border border-[#D9E0D6] p-8 rounded-3xl space-y-6 shadow-sm">
    <h2 class="text-base font-bold text-[#1F2520] border-b border-[#E7ECE4] pb-4">ویرایش مشخصات پلن اشتراک</h2>

    <form method="POST" action="{{ route('subscriptions.update-plan', $plan) }}" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-xs font-semibold text-[#1F2520] mb-1.5">عنوان پلن *</label>
            <input type="text" name="name" value="{{ old('name', $plan->name) }}" required class="w-full px-4 py-3 rounded-xl bg-[#FFFFFF] border border-[#CCD5C8] text-[#1F2520] focus:outline-none focus:border-[#6A8C60] text-xs">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-[#1F2520] mb-1.5">تعداد کل فنجان (اعتبار) *</label>
                <input type="number" name="total_items" value="{{ old('total_items', $plan->total_items) }}" required class="w-full px-4 py-3 rounded-xl bg-[#FFFFFF] border border-[#CCD5C8] text-[#1F2520] focus:outline-none focus:border-[#6A8C60] text-xs font-bold">
            </div>

            <div>
                <label class="block text-xs font-semibold text-[#1F2520] mb-1.5">مدت اعتبار (روز) *</label>
                <input type="number" name="duration_days" value="{{ old('duration_days', $plan->duration_days) }}" required class="w-full px-4 py-3 rounded-xl bg-[#FFFFFF] border border-[#CCD5C8] text-[#1F2520] focus:outline-none focus:border-[#6A8C60] text-xs font-bold">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-[#1F2520] mb-1.5">قیمت کل پلن (تومان) *</label>
            <input type="number" name="price" value="{{ old('price', $plan->price) }}" required class="w-full px-4 py-3 rounded-xl bg-[#FFFFFF] border border-[#CCD5C8] text-[#1F2520] focus:outline-none focus:border-[#6A8C60] text-xs font-bold">
        </div>

        <div>
            <label class="block text-xs font-semibold text-[#1F2520] mb-1.5">وضعیت پلن *</label>
            <select name="status" class="w-full px-4 py-3 rounded-xl bg-[#FFFFFF] border border-[#CCD5C8] text-[#1F2520] focus:outline-none focus:border-[#6A8C60] text-xs font-medium">
                <option value="active" {{ $plan->status === 'active' ? 'selected' : '' }}>فعال</option>
                <option value="inactive" {{ $plan->status === 'inactive' ? 'selected' : '' }}>غیرفعال</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-[#1F2520] mb-2">محصولات مجاز برای این پلن *</label>
            <div class="space-y-2 max-h-40 overflow-y-auto p-3 bg-[#FFFFFF] border border-[#CCD5C8] rounded-xl">
                @php $selectedProducts = $plan->products->pluck('id')->toArray(); @endphp
                @foreach($products as $prod)
                    <label class="flex items-center gap-2.5 text-xs text-[#48504B] cursor-pointer">
                        <input type="checkbox" name="product_ids[]" value="{{ $prod->id }}" {{ in_array($prod->id, $selectedProducts) ? 'checked' : '' }} class="rounded bg-[#FCFCFB] border-[#CCD5C8] text-[#3A5232] focus:ring-0">
                        <span>{{ $prod->name }} ({{ number_format($prod->price) }} تومان)</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E7ECE4]">
            <a href="{{ route('subscriptions.index') }}" class="px-5 py-2.5 bg-[#EEF2EC] hover:bg-[#D9E0D6] text-[#48504B] font-semibold rounded-xl text-xs transition-all">انصراف</a>
            <button type="submit" class="px-6 py-2.5 bg-[#3A5232] hover:bg-[#2F4428] text-white font-bold rounded-xl text-xs transition-all shadow-md">به‌روزرسانی پلن</button>
        </div>
    </form>
</div>
@endsection
