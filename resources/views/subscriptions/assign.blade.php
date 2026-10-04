@extends('layouts.app')

@section('title', 'تخصیص اشتراک به مشتری')
@section('header_title', 'تخصیص اشتراک جدید به مشتری')

@section('content')
<div class="max-w-xl mx-auto bg-[#FCFCFB] border border-[#D9E0D6] p-8 rounded-3xl space-y-6 shadow-sm">
    <h2 class="text-base font-bold text-[#1F2520] border-b border-[#E7ECE4] pb-4">انتخاب مشتری و پلن اشتراک</h2>

    <form method="POST" action="{{ route('subscriptions.assign.store') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-xs font-semibold text-[#1F2520] mb-1.5">انتخاب مشتری *</label>
            <select name="customer_id" required class="w-full px-4 py-3 rounded-xl bg-[#FFFFFF] border border-[#CCD5C8] text-[#1F2520] focus:outline-none focus:border-[#6A8C60] text-xs font-medium">
                <option value="">-- یک مشتری را انتخاب کنید --</option>
                @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ (request('customer_id') == $c->id || old('customer_id') == $c->id) ? 'selected' : '' }}>
                        {{ $c->name }} ({{ $c->phone }})
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-[#1F2520] mb-1.5">انتخاب پلن اشتراک *</label>
            <select name="subscription_plan_id" required class="w-full px-4 py-3 rounded-xl bg-[#FFFFFF] border border-[#CCD5C8] text-[#1F2520] focus:outline-none focus:border-[#6A8C60] text-xs font-medium">
                <option value="">-- یک پلن اشتراک را انتخاب کنید --</option>
                @foreach($plans as $p)
                    <option value="{{ $p->id }}" {{ old('subscription_plan_id') == $p->id ? 'selected' : '' }}>
                        {{ $p->name }} ({{ $p->total_items }} فنجان - {{ number_format($p->price) }} تومان)
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E7ECE4]">
            <a href="{{ route('subscriptions.index') }}" class="px-5 py-2.5 bg-[#EEF2EC] hover:bg-[#D9E0D6] text-[#48504B] font-semibold rounded-xl text-xs transition-all">انصراف</a>
            <button type="submit" class="px-6 py-2.5 bg-[#C8A35A] hover:bg-[#B78F44] text-white font-bold rounded-xl text-xs transition-all shadow-md">تخصیص و فعال‌سازی اشتراک</button>
        </div>
    </form>
</div>
@endsection
