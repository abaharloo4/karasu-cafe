@extends('layouts.app')

@section('title', 'صندوق فروش و ثبت اشتراک')
@section('header_title', 'صندوق فروش و تحویل سفارشات - بی کافه (B Cafe)')

@section('content')
@php
    $categories = $allProducts->pluck('category')->filter()->unique('id')->values();
@endphp

<div class="space-y-6" x-data="posSystem()" @keydown.escape="closeProductModal()">

    <!-- POS Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-50 flex items-center justify-center btn-tactile shadow-2xs">
                <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-800">ثبت سفارش جدید</h1>
                <p class="text-xs text-slate-400">صندوق فروش و تحویل سفارشات کافه</p>
            </div>
        </div>

        <!-- Customer Phone Lookup Bar -->
        <div class="w-full sm:w-auto">
            <form method="GET" action="{{ route('daily-usage.index') }}" class="flex items-center gap-2">
                <div class="relative flex-1 sm:w-64">
                    <input type="text"
                           name="phone"
                           value="{{ $phone }}"
                           placeholder="نام یا شماره موبایل مشتری..."
                           class="w-full pe-3 ps-9 py-2 rounded-xl bg-white border border-slate-200 text-slate-800 placeholder-slate-400 text-xs font-bold focus:outline-none focus:border-[#387780] shadow-2xs">
                    <svg class="w-4 h-4 text-slate-400 absolute start-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <button type="submit" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl btn-tactile text-xs shadow-2xs">
                    استعلام
                </button>
                @if($phone)
                    <a href="{{ route('daily-usage.index') }}" class="px-2.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-600 btn-tactile">
                        ✕
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Customer Profile & Subscription Banner (If queried) -->
    @if($phone)
        @if($customer)
            <div class="bg-white border border-slate-200/80 p-4 sm:p-5 rounded-2xl space-y-3 shadow-xs">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#387780] text-white flex items-center justify-center font-bold text-sm shadow-xs flex-shrink-0">
                            {{ mb_substr($customer->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-bold text-slate-800">{{ $customer->name }}</h3>
                                @if($customer->tier ?? false)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold text-white" style="background-color: {{ $customer->tier->badge_color }}">
                                        {{ $customer->tier->name }} ({{ $customer->tier->discount_percentage }}٪ تخفیف)
                                    </span>
                                @endif
                            </div>
                            <span class="text-xs font-mono text-slate-400 dir-ltr text-end">{{ $customer->phone }}</span>
                        </div>
                    </div>

                    <a href="{{ route('customers.show', $customer) }}" target="_blank" class="text-xs text-[#387780] font-bold hover:underline flex items-center gap-1">
                        <span>پرونده مشتری</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                </div>

                @if($activeSubscription)
                    <div class="p-3.5 rounded-xl bg-teal-50/70 border border-teal-200/70 flex flex-wrap items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-4 flex-wrap">
                            <div>
                                <span class="text-slate-500 font-medium">پلن اشتراک فعال:</span>
                                <span class="font-bold text-slate-800 ms-1">{{ $activeSubscription->plan->name ?? 'اشتراک فعال' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500 font-medium">مانده سهمیه:</span>
                                <span class="font-black text-[#387780] tabular-nums ms-1">{{ $activeSubscription->remaining_items }} فنجان</span>
                            </div>
                        </div>

                        <div>
                            @if($hasUsedToday)
                                <span class="px-3 py-1.5 rounded-xl bg-slate-200 text-slate-600 font-bold text-xs">سهمیه امروز دریافت شده</span>
                            @elseif($activeSubscription->remaining_items > 0)
                                <form method="POST" action="{{ route('daily-usage.store') }}" class="flex items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="phone" value="{{ $phone }}">
                                    <input type="hidden" name="subscription_id" value="{{ $activeSubscription->id }}">
                                    <select name="product_id" required class="px-2.5 py-1.5 rounded-xl bg-white border border-teal-300 text-slate-800 text-xs font-bold focus:outline-none focus:border-[#387780]">
                                        @foreach($allowedProducts as $alp)
                                            <option value="{{ $alp->id }}">{{ $alp->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="px-4 py-1.5 bg-[#387780] hover:bg-[#2E636B] text-white font-bold rounded-xl text-xs transition-colors shadow-xs flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span>ثبت سریع سهمیه اشتراک (بدون هزینه)</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @else
            <div class="bg-white border border-slate-200/80 p-4 rounded-2xl text-center space-y-2 shadow-xs">
                <span class="text-xs font-bold text-slate-700">مشتری با شماره «{{ $phone }}» یافت نشد.</span>
                <a href="{{ route('customers.create') }}" class="ms-2 text-xs text-[#387780] font-bold hover:underline">
                    + ایجاد حساب مشتری
                </a>
            </div>
        @endif
    @endif

    <!-- Two-Column POS Layout (Matching Screen 2 of Mockup) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Column 1 (Right in RTL): Menu Catalog (7 Cols) -->
        <div class="lg:col-span-7 space-y-4">
            <!-- Search & Category Filters -->
            <div class="bg-white border border-slate-200/80 p-4 rounded-2xl shadow-xs space-y-3">
                <!-- Search input -->
                <div class="relative w-full">
                    <input type="text"
                           x-model="productSearch"
                           placeholder="جستجوی محصول..."
                           class="w-full ps-9 pe-4 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 focus:outline-none focus:border-[#387780] focus:bg-white transition-all">
                    <svg class="w-4 h-4 text-slate-400 absolute start-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <!-- Category Filter Pills -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 custom-scrollbar">
                    <button type="button"
                            @click="selectedCategory = 'all'"
                            :class="selectedCategory === 'all' ? 'bg-[#19242B] text-white' : 'bg-slate-100 hover:bg-slate-200/80 text-slate-600'"
                            class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap btn-tactile transition-colors">
                        همه
                    </button>
                    @foreach($categories as $cat)
                        <button type="button"
                                @click="selectedCategory = '{{ $cat->id }}'"
                                :class="selectedCategory === '{{ $cat->id }}' ? 'bg-[#387780] text-white' : 'bg-slate-100 hover:bg-slate-200/80 text-slate-600'"
                                class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap btn-tactile transition-colors">
                            {{ $cat->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Product Grid (3 cols, Photo Cards with circular + button) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3.5 max-h-[640px] overflow-y-auto custom-scrollbar p-0.5">
                @foreach($allProducts as $product)
                    @php
                        $posProductData = [
                            'id' => $product->id,
                            'name' => $product->name,
                            'category_id' => $product->category_id,
                            'category_name' => $product->category->name ?? 'عمومی',
                            'price' => (float)$product->price,
                            'description' => $product->description ?? 'تهیه شده با استاندارد باریستا در بی کافه.',
                        ];
                    @endphp
                    <div x-show="matchesFilter('{{ $product->category_id }}', '{{ addslashes($product->name) }}')"
                         @click="openProductModal({{ json_encode($posProductData) }})"
                         class="bg-white border border-slate-200/80 hover:border-[#387780] rounded-2xl overflow-hidden shadow-xs hover:shadow-md transition-all cursor-pointer group flex flex-col justify-between">
                        <!-- Product Image / Banner -->
                        <div class="h-28 bg-gradient-to-br from-slate-100 to-teal-50 flex items-center justify-center relative overflow-hidden">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-12 h-12 rounded-2xl bg-white/80 border border-slate-200/60 text-[#387780] flex items-center justify-center shadow-xs">
                                    <x-product-icon :product="$product" class="w-6 h-6 text-[#387780]" />
                                </div>
                            @endif
                        </div>

                        <!-- Card Footer Details -->
                        <div class="p-3 space-y-1.5">
                            <span class="text-xs font-bold text-slate-800 block truncate group-hover:text-[#387780] transition-colors">
                                {{ $product->name }}
                            </span>

                            <div class="flex items-center justify-between pt-1">
                                <span class="text-xs font-black text-slate-900 tabular-nums">
                                    {{ number_format($product->price) }} <span class="text-[10px] font-normal text-slate-400">تومان</span>
                                </span>
                                <!-- Circular Teal + Button -->
                                <button type="button"
                                        @click.stop="addItem({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }})"
                                        title="افزودن به سبد"
                                        class="w-7 h-7 rounded-full bg-[#387780] hover:bg-[#2E636B] text-white flex items-center justify-center font-bold text-base shadow-xs btn-tactile">
                                    +
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Column 2 (Left in RTL): Current Order Ticket (5 Cols) -->
        <div class="lg:col-span-5 bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs space-y-4 sticky top-24">
            <!-- Ticket Header -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h2 class="text-sm font-black text-slate-800">سفارش جاری</h2>
                    <span class="text-[11px] text-slate-400">
                        @if($customer ?? false)
                            مشتری: <span class="text-slate-700 font-bold">{{ $customer->name }}</span>
                        @else
                            مشتری حضوری
                        @endif
                    </span>
                </div>
                <button type="button" @click="clearCart()" x-show="cart.length > 0" class="text-xs text-rose-500 hover:underline font-bold">
                    حذف همه
                </button>
            </div>

            <!-- Free item toggle for subscription customers inside cart -->
            @if(($customer ?? false) && ($activeSubscription ?? false) && !($hasUsedToday ?? true) && ($allowedProducts ?? collect())->count() > 0)
                <div class="p-3 rounded-xl bg-teal-50 border border-teal-200/70 space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" x-model="useFreeItem" class="w-4 h-4 rounded text-[#387780] focus:ring-[#387780]">
                        <span class="text-xs font-bold text-slate-800">اعمال سهمیه رایگان امروز در این فاکتور</span>
                    </label>
                    <div x-show="useFreeItem" x-transition class="mt-1">
                        <select x-model="freeProductId" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-800 text-xs font-bold focus:outline-none focus:border-[#387780]">
                            <option value="">-- انتخاب قهوه سهمیه --</option>
                            @foreach($allowedProducts as $fp)
                                <option value="{{ $fp->id }}">{{ $fp->name }} (۰ تومان)</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif

            <!-- Empty Cart State -->
            <div x-show="cart.length === 0 && !useFreeItem" class="py-12 text-center space-y-2">
                <div class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-300 flex items-center justify-center mx-auto border border-slate-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
                <div class="text-xs font-bold text-slate-500">سبد سفارش خالی است</div>
                <div class="text-[11px] text-slate-400">آیتم‌های مورد نظر را از کاتالوگ انتخاب نمایید یا سهمیه اشتراک را فعال کنید.</div>
            </div>

            <!-- Cart Items List (Matching Screen 2 of Mockup) -->
            <div x-show="cart.length > 0" class="space-y-2.5 max-h-72 overflow-y-auto custom-scrollbar">
                <template x-for="(item, index) in cart" :key="index">
                    <div class="p-3 rounded-xl bg-slate-50/80 border border-slate-100 flex items-center justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <span class="text-xs font-bold text-slate-800 block truncate" x-text="item.name"></span>
                            <span class="text-xs font-black text-slate-700 tabular-nums" x-text="(item.price * item.quantity).toLocaleString('fa-IR') + ' ت'"></span>
                        </div>

                        <!-- Stepper Controls [ - ] [ 1 ] [ + ] -->
                        <div class="flex items-center gap-1 bg-white border border-slate-200 rounded-lg p-0.5">
                            <button @click="decrementItem(index)" type="button" class="w-6 h-6 rounded bg-slate-50 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center btn-tactile">−</button>
                            <span class="w-6 text-center font-bold text-xs text-slate-800 tabular-nums" x-text="item.quantity"></span>
                            <button @click="incrementItem(index)" type="button" class="w-6 h-6 rounded bg-slate-50 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center btn-tactile">+</button>
                        </div>

                        <!-- Delete Button -->
                        <button @click="removeItem(index)" type="button" class="w-7 h-7 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-rose-50 flex items-center justify-center btn-tactile">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </template>
            </div>

            <!-- Total Price & Final Submit -->
            <div x-show="cart.length > 0 || (useFreeItem && freeProductId)" class="pt-3 border-t border-slate-100 space-y-3">
                <div class="space-y-1.5 text-xs text-slate-500">
                    <div class="flex items-center justify-between">
                        <span>جمع اقلام پولی:</span>
                        <span class="font-bold text-slate-700 tabular-nums" x-text="totalPrice.toLocaleString('fa-IR') + ' تومان'"></span>
                    </div>
                    <div x-show="useFreeItem && freeProductId" class="flex items-center justify-between text-emerald-600 font-bold">
                        <span>سهمیه رایگان روزانه:</span>
                        <span>۱ فنجان رایگان</span>
                    </div>
                </div>

                <div class="flex items-center justify-between text-sm sm:text-base border-t border-slate-100 pt-2">
                    <span class="font-bold text-slate-800">مبلغ نهایی:</span>
                    <span class="font-black text-lg text-slate-900 tabular-nums" x-text="totalPrice.toLocaleString('fa-IR') + ' تومان'"></span>
                </div>

                <form method="POST" action="{{ route('daily-usage.store-paid') }}" @submit="populateForm($event)">
                    @csrf
                    @if($customer ?? false)
                        <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                    @endif
                    <input type="hidden" name="phone" value="{{ $phone }}">
                    <input type="hidden" name="use_free_item" x-bind:value="useFreeItem ? '1' : '0'">
                    <input type="hidden" name="free_product_id" x-bind:value="freeProductId">
                    @if($activeSubscription ?? false)
                        <input type="hidden" name="subscription_id" value="{{ $activeSubscription->id }}">
                    @endif
                    <div id="items-container"></div>

                    <button type="submit" class="w-full py-3.5 bg-[#387780] hover:bg-[#2E636B] text-white font-bold rounded-xl text-sm btn-tactile shadow-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>ثبت سفارش</span>
                    </button>
                </form>
            </div>
        </div>

    </div>

    <!-- Product Detail Popup Modal in POS -->
    <div x-show="productModalOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
         style="display: none;">

        <div @click.outside="closeProductModal()"
             x-show="productModalOpen"
             x-transition:enter="transition ease-out duration-250"
             x-transition:enter-start="scale-95 opacity-0"
             x-transition:enter-end="scale-100 opacity-100"
             class="w-full max-w-md bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col">

            <!-- Modal Header -->
            <div class="p-5 bg-[#19242B] text-white flex items-center justify-between">
                <div>
                    <span class="px-2.5 py-0.5 rounded-lg bg-white/10 text-teal-300 text-[10px] font-bold" x-text="modalProduct ? modalProduct.category_name : ''"></span>
                    <h3 class="text-base font-bold text-white mt-1" x-text="modalProduct ? modalProduct.name : ''"></h3>
                </div>
                <button type="button" @click="closeProductModal()" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center btn-tactile">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-5 space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-500 font-medium">قیمت واحد:</span>
                    <span class="text-base font-black text-slate-900 tabular-nums"
                          x-text="modalProduct ? (Number(modalProduct.price).toLocaleString('fa-IR') + ' تومان') : ''"></span>
                </div>

                <div class="space-y-1">
                    <span class="text-xs font-bold text-slate-700">توضیحات محصول:</span>
                    <p class="text-xs text-slate-500 leading-relaxed bg-slate-50 p-3 rounded-xl border border-slate-100"
                       x-text="modalProduct ? modalProduct.description : ''"></p>
                </div>

                <!-- Quantity Stepper -->
                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-xs font-bold text-slate-700">تعداد:</span>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="modalQuantity > 1 ? modalQuantity-- : null"
                                class="w-8 h-8 rounded-lg bg-white text-slate-700 font-bold text-base flex items-center justify-center btn-tactile shadow-2xs border border-slate-200">−</button>
                        <span class="w-8 text-center font-black text-xs text-slate-800 tabular-nums" x-text="modalQuantity"></span>
                        <button type="button" @click="modalQuantity++"
                                class="w-8 h-8 rounded-lg bg-[#387780] text-white font-bold text-base flex items-center justify-center btn-tactile shadow-2xs">+</button>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-4 border-t border-slate-100 bg-white flex items-center gap-2.5">
                <button type="button" @click="closeProductModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 text-xs font-bold text-slate-600 hover:bg-slate-200">
                    انصراف
                </button>
                <button type="button" @click="confirmModalToCart()"
                        class="flex-1 py-2.5 bg-[#387780] hover:bg-[#2E636B] text-white font-bold rounded-xl text-xs btn-tactile shadow-xs flex items-center justify-between px-4">
                    <span>افزودن به سبد</span>
                    <span class="tabular-nums" x-text="(modalProduct ? (modalProduct.price * modalQuantity).toLocaleString('fa-IR') : 0) + ' تومان'"></span>
                </button>
            </div>
        </div>
    </div>

</div>

<script>
function posSystem() {
    return {
        selectedCategory: 'all',
        productSearch: '',
        cart: [],
        useFreeItem: false,
        freeProductId: '',

        // Product Modal State
        productModalOpen: false,
        modalProduct: null,
        modalQuantity: 1,

        openProductModal(product) {
            this.modalProduct = product;
            this.modalQuantity = 1;
            this.productModalOpen = true;
        },

        closeProductModal() {
            this.productModalOpen = false;
        },

        confirmModalToCart() {
            if (!this.modalProduct) return;
            const existing = this.cart.find(i => i.product_id === this.modalProduct.id);
            if (existing) {
                existing.quantity += this.modalQuantity;
            } else {
                this.cart.push({
                    product_id: this.modalProduct.id,
                    name: this.modalProduct.name,
                    price: this.modalProduct.price,
                    quantity: this.modalQuantity
                });
            }
            this.closeProductModal();
        },

        matchesFilter(categoryId, productName) {
            const matchesCat = this.selectedCategory === 'all' || this.selectedCategory === String(categoryId);
            const matchesText = !this.productSearch || productName.toLowerCase().includes(this.productSearch.toLowerCase());
            return matchesCat && matchesText;
        },

        addItem(productId, name, price) {
            const existing = this.cart.find(i => i.product_id === productId);
            if (existing) {
                existing.quantity++;
            } else {
                this.cart.push({ product_id: productId, name: name, price: price, quantity: 1 });
            }
        },
        incrementItem(index) { this.cart[index].quantity++; },
        decrementItem(index) {
            if (this.cart[index].quantity > 1) this.cart[index].quantity--;
            else this.cart.splice(index, 1);
        },
        removeItem(index) { this.cart.splice(index, 1); },
        clearCart() { this.cart = []; },

        get totalCount() {
            return this.cart.reduce((sum, item) => sum + item.quantity, 0);
        },

        get totalPrice() {
            return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        },

        populateForm(event) {
            const container = document.getElementById('items-container');
            container.innerHTML = '';
            this.cart.forEach((item, i) => {
                const pidInput = document.createElement('input');
                pidInput.type = 'hidden'; pidInput.name = `items[${i}][product_id]`; pidInput.value = item.product_id;
                const qtyInput = document.createElement('input');
                qtyInput.type = 'hidden'; qtyInput.name = `items[${i}][quantity]`; qtyInput.value = item.quantity;
                container.appendChild(pidInput);
                container.appendChild(qtyInput);
            });
        }
    }
}
</script>
@endsection
