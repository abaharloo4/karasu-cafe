@extends('layouts.app')

@section('title', 'مدیریت روش‌های تحویل و ارسال')
@section('header_title', 'پیکربندی روش‌های ارسال پیک، تحویل حضوری و سرو در سالن')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header info card -->
    <div class="bg-white border border-[#E2E8F0] p-6 rounded-3xl space-y-2 shadow-xs">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-extrabold text-[#19242B] flex items-center gap-2">
                <svg class="w-5 h-5 text-[#387780]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                <span>مدیریت کامل روش‌های دریافت و تحویل سفارشات</span>
            </h2>
            <span class="px-3 py-1 bg-teal-50 text-[#387780] text-xs font-bold rounded-lg border border-[#387780]/20">ناوگان و کانتر کافه بی</span>
        </div>
        <p class="text-xs text-[#64748B]">
            وضعیت، هزینه‌ها و نحوه ارائه هر یک از روش‌های ارسال با پیک، بیرون‌بر یا سرو در سالن را مدیریت کنید. همچنین می‌توانید هر روش را در وضعیت «به‌زودی» قرار دهید تا با برچسب اختصاصی به مشتریان نمایش داده شود.
        </p>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('settings.fulfillment.update') }}" class="space-y-6">
        @csrf

        @php
            $methodConfigs = [
                'courier' => [
                    'label' => 'ارسال با پیک موتوری (ناوگان شیراز)',
                    'sub' => 'تحویل سفارش به آدرس مشتری با پیک اختصاصی کافه بی',
                    'icon_bg' => 'bg-teal-50 text-[#387780] border-teal-200',
                    'has_delivery_zones' => true,
                ],
                'takeaway' => [
                    'label' => 'بیرون‌بر / تحویل حضوری روی کانتر',
                    'sub' => 'مشتری شخصاً برای تحویل سفارش به کافه مراجعه می‌کند',
                    'icon_bg' => 'bg-amber-50 text-amber-600 border-amber-200',
                    'has_delivery_zones' => false,
                ],
                'dine_in' => [
                    'label' => 'سرو در سالن و پشت کانتر کافه',
                    'sub' => 'پذیرایی در فضای کافه با تعیین شماره میز یا صندلی بار',
                    'icon_bg' => 'bg-sky-50 text-sky-600 border-sky-200',
                    'has_delivery_zones' => false,
                ],
            ];
        @endphp

        @foreach(['courier', 'takeaway', 'dine_in'] as $key)
            @php
                $item = $methods[$key] ?? null;
                $cfg = $methodConfigs[$key];
                $currentStatus = old("methods.{$key}.status", $item?->status ?? 'active');
            @endphp
            <div class="bg-white border border-[#E2E8F0] p-6 rounded-3xl space-y-4 shadow-xs">
                <!-- Top Bar -->
                <div class="flex flex-wrap items-center justify-between border-b border-[#E2E8F0] pb-4 gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl border flex items-center justify-center {{ $cfg['icon_bg'] }}">
                            @if($key === 'courier')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                            @elseif($key === 'takeaway')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z M6 1v3 M10 1v3 M14 1v3"/></svg>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-[#19242B]">{{ $cfg['label'] }}</h3>
                            <p class="text-xs text-[#64748B]">{{ $cfg['sub'] }}</p>
                        </div>
                    </div>

                    <!-- 3-way Status Selector -->
                    <div class="flex items-center gap-1.5 bg-[#F8FAFC] p-1.5 rounded-2xl border border-[#CBD5E1]" x-data="{ status: '{{ $currentStatus }}' }">
                        <label :class="status === 'active' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                               class="flex items-center gap-1 px-3 py-1.5 rounded-xl cursor-pointer text-xs font-bold transition-all">
                            <input type="radio" name="methods[{{ $key }}][status]" value="active" x-model="status" class="sr-only">
                            <span>فعال</span>
                        </label>
                        <label :class="status === 'coming_soon' ? 'bg-amber-500 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                               class="flex items-center gap-1 px-3 py-1.5 rounded-xl cursor-pointer text-xs font-bold transition-all">
                            <input type="radio" name="methods[{{ $key }}][status]" value="coming_soon" x-model="status" class="sr-only">
                            <span>به‌زودی</span>
                        </label>
                        <label :class="status === 'inactive' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                               class="flex items-center gap-1 px-3 py-1.5 rounded-xl cursor-pointer text-xs font-bold transition-all">
                            <input type="radio" name="methods[{{ $key }}][status]" value="inactive" x-model="status" class="sr-only">
                            <span>غیرفعال</span>
                        </label>
                    </div>
                </div>

                <!-- Fields Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-[#19242B] mb-1.5">عنوان نمایشی برای مشتری *</label>
                        <input type="text" name="methods[{{ $key }}][title]" value="{{ old("methods.{$key}.title", $item?->title ?? '') }}" required class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs font-bold focus:outline-none focus:border-[#387780] focus:ring-1 focus:ring-[#387780]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#19242B] mb-1.5">زیرعنوان / برچسب کوتاه</label>
                        <input type="text" name="methods[{{ $key }}][subtitle]" value="{{ old("methods.{$key}.subtitle", $item?->subtitle ?? '') }}" placeholder="مثلاً: تحویل درب آدرس" class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs focus:outline-none focus:border-[#387780] focus:ring-1 focus:ring-[#387780]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#19242B] mb-1.5">پیام حالت «به‌زودی» (در صورت انتخاب وضعیت به‌زودی)</label>
                        <input type="text" name="methods[{{ $key }}][coming_soon_note]" value="{{ old("methods.{$key}.coming_soon_note", $item?->coming_soon_note ?? '') }}" placeholder="مثلاً: سرویس پیک در حال ارتقا ناوگان است و به زودی فعال می‌شود." class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs focus:outline-none focus:border-[#387780] focus:ring-1 focus:ring-[#387780]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#19242B] mb-1.5">حداقل مبلغ سفارش برای این روش (تومان)</label>
                        <input type="number" step="1000" min="0" name="methods[{{ $key }}][min_order_amount]" value="{{ old("methods.{$key}.min_order_amount", (int)($item?->min_order_amount ?? 0)) }}" class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs font-mono tabular-nums focus:outline-none focus:border-[#387780] focus:ring-1 focus:ring-[#387780]">
                    </div>

                    @if($key === 'courier')
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-xs font-bold text-[#19242B] mb-1.5">هزینه پایه ارسال (تومان - علاوه بر نرخ مناطق نقشه)</label>
                            <input type="number" step="1000" min="0" name="methods[{{ $key }}][base_fee]" value="{{ old("methods.{$key}.base_fee", (int)($item?->base_fee ?? 0)) }}" class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs font-mono tabular-nums focus:outline-none focus:border-[#387780] focus:ring-1 focus:ring-[#387780]">
                            <p class="text-[11px] text-[#64748B] mt-1">هزینه نهایی پیک از جمع هزینه پایه و نرخ اختصاصی منطقه تحویل (Delivery Zone) محاسبه خواهد شد.</p>
                        </div>
                    @endif

                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-xs font-bold text-[#19242B] mb-1.5">توضیحات و راهنمای تفصیلی برای مشتری</label>
                        <textarea name="methods[{{ $key }}][description]" rows="2" class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#CBD5E1] text-[#19242B] text-xs focus:outline-none focus:border-[#387780] focus:ring-1 focus:ring-[#387780]">{{ old("methods.{$key}.description", $item?->description ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Submit Button -->
        <div class="flex justify-end pt-2">
            <button type="submit" class="px-8 py-3.5 bg-[#387780] hover:bg-[#2E626A] text-white font-bold rounded-2xl text-xs transition-colors shadow-xs flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>ذخیره تنظیمات روش‌های تحویل و ارسال</span>
            </button>
        </div>
    </form>

</div>
@endsection
