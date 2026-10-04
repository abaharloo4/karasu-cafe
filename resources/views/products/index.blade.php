@extends('layouts.app')

@section('title', 'منو و محصولات')
@section('header_title', 'مدیریت محصولات بی کافه (B Cafe)')

@section('content')
<div class="space-y-6">

    <!-- Actions Header -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h2 class="text-base font-bold text-[#1F2520] flex items-center gap-2">
            <svg class="w-5 h-5 text-[#3A5232]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            <span>لیست محصولات منو</span>
        </h2>

        @if(auth()->user()->isAdmin())
            <div class="flex items-center gap-3">
                <a href="{{ route('categories.index') }}" class="px-4 py-2.5 bg-[#EEF2EC] hover:bg-[#D9E0D6] text-[#1F2520] font-bold rounded-xl text-xs transition-all shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#3A5232]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    <span>مدیریت دسته‌بندی‌ها</span>
                </a>
                <a href="{{ route('products.create') }}" class="px-5 py-2.5 bg-[#3A5232] hover:bg-[#2F4428] text-white font-bold rounded-xl text-xs transition-all shadow-md flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>افزودن محصول جدید</span>
                </a>
            </div>
        @endif
    </div>

    <!-- Category Filter Pills -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-[#E7ECE4]">
        <a href="{{ route('products.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex-shrink-0 {{ empty($categoryId) ? 'bg-[#3A5232] text-white shadow-md' : 'bg-[#FCFCFB] border border-[#D9E0D6] text-[#48504B] hover:bg-[#EEF2EC]' }}">
            همه محصولات ({{ $products->count() }})
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('products.index', ['category_id' => $cat->id]) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex-shrink-0 {{ (string)$categoryId === (string)$cat->id ? 'bg-[#3A5232] text-white shadow-md' : 'bg-[#FCFCFB] border border-[#D9E0D6] text-[#48504B] hover:bg-[#EEF2EC]' }}">
                {{ $cat->name }}
            </a>
        @endforeach
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
        @forelse($products as $product)
            <div class="bg-[#FCFCFB] border border-[#D9E0D6] p-5 rounded-3xl space-y-3 flex flex-col justify-between shadow-sm">
                <div class="space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-12 h-12 rounded-2xl bg-white border border-[#D9E0D6] flex items-center justify-center text-[#3A5232] flex-shrink-0 shadow-2xs overflow-hidden">
                                @if($product->image)
                                    <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.classList.remove('hidden')">
                                    <div class="hidden w-full h-full flex items-center justify-center">
                                        <x-product-icon :product="$product" class="w-6 h-6" />
                                    </div>
                                @else
                                    <x-product-icon :product="$product" class="w-6 h-6" />
                                @endif
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-sm font-extrabold text-[#1F2520] truncate">{{ $product->name }}</h3>
                                <div class="flex items-center gap-1.5 flex-wrap mt-1">
                                    <span class="text-[10px] text-[#C8A35A] font-bold bg-[#F7F0DE] px-2 py-0.5 rounded-md">
                                        {{ $product->category->name ?? 'بدون دسته‌بندی' }}
                                    </span>
                                    @if($product->stock === null)
                                        <span class="text-[10px] text-slate-600 bg-slate-100 font-bold px-1.5 py-0.5 rounded-md">
                                            موجودی: نامحدود
                                        </span>
                                    @elseif($product->stock > 0)
                                        <span class="text-[10px] text-emerald-700 bg-emerald-50 border border-emerald-200 font-bold px-1.5 py-0.5 rounded-md">
                                            موجودی: {{ $product->stock }} عدد
                                        </span>
                                    @else
                                        <span class="text-[10px] text-rose-700 bg-rose-50 border border-rose-200 font-black px-1.5 py-0.5 rounded-md">
                                            ناموجود
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold flex-shrink-0 {{ $product->status === 'active' ? 'bg-[#EAF2E7] text-[#4F8A4B]' : 'bg-[#FDF2F2] text-[#C94A4A]' }}">
                            {{ $product->status === 'active' ? 'فعال' : 'غیرفعال' }}
                        </span>
                    </div>

                    @if($product->isHourlyDiscountActive())
                        <div class="px-2.5 py-1.5 rounded-xl bg-amber-50 border border-amber-200/70 flex items-center justify-between text-[11px]">
                            <span class="font-bold text-amber-800 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                <span>{{ $product->discount_percent }}٪ تخفیف فعال</span>
                            </span>
                            <span class="text-amber-700 font-medium text-[10px]">
                                {{ $product->discountRemainingHuman() }}
                            </span>
                        </div>
                    @endif
                </div>

                <div class="border-t border-[#E7ECE4] pt-3 flex items-center justify-between">
                    <div>
                        <span class="text-[11px] text-[#6E756F] block">قیمت:</span>
                        @if($product->isHourlyDiscountActive())
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-xs text-slate-400 line-through">{{ number_format($product->price) }}</span>
                                <span class="text-sm font-black text-rose-600">{{ number_format($product->effective_price) }} تومان</span>
                            </div>
                        @else
                            <span class="text-sm font-black text-[#3A5232]">{{ number_format($product->price) }} تومان</span>
                        @endif
                    </div>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('products.edit', $product) }}" class="px-3 py-1.5 bg-[#EEF2EC] hover:bg-[#D9E0D6] text-[#1F2520] font-semibold rounded-lg text-xs transition-colors">ویرایش</a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-[#FCFCFB] border border-[#D9E0D6] p-8 rounded-3xl text-center text-[#8A918B]">
                هیچ محصولی در این دسته‌بندی ثبت نشده است.
            </div>
        @endforelse
    </div>

</div>
@endsection
