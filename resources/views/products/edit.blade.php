@extends('layouts.app')

@section('title', 'ویرایش محصول')
@section('header_title', 'ویرایش محصول: ' . $product->name)

@section('content')
<div class="max-w-2xl mx-auto bg-white border border-[#353535]/12 p-8 rounded-3xl space-y-6 shadow-xs">
    <div class="flex items-center justify-between border-b border-[#353535]/10 pb-4">
        <h2 class="text-base font-black text-[#353535] flex items-center gap-2">
            <span class="w-2.5 h-5 rounded-full bg-[#284B63]"></span>
            <span>ویرایش مشخصات محصول: {{ $product->name }}</span>
        </h2>
        <a href="{{ route('products.index') }}" class="text-xs font-bold text-[#3C6E71] hover:underline">بازگشت به لیست</a>
    </div>

    <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-[#353535] mb-1.5">عنوان محصول *</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full px-4 py-3 rounded-xl bg-[#F7F9FA]/40 border border-[#353535]/15 text-[#353535] focus:outline-none focus:border-[#3C6E71] focus:bg-white text-xs font-bold transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#353535] mb-1.5">دسته‌بندی محصول *</label>
                <select name="category_id" required class="w-full px-4 py-3 rounded-xl bg-[#F7F9FA]/40 border border-[#353535]/15 text-[#353535] focus:outline-none focus:border-[#3C6E71] focus:bg-white text-xs font-bold transition-all">
                    <option value="">-- انتخاب دسته‌بندی --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (string)old('category_id', $product->category_id) === (string)$cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-bold text-[#353535] mb-1.5">قیمت تک‌فروشی (تومان) *</label>
                <input type="number" name="price" value="{{ old('price', (int)$product->price) }}" required class="w-full px-4 py-3 rounded-xl bg-[#F7F9FA]/40 border border-[#353535]/15 text-[#284B63] focus:outline-none focus:border-[#3C6E71] focus:bg-white text-xs font-black tabular-nums transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#353535] mb-1.5">موجودی انبار (تعداد)</label>
                <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" placeholder="خالی = نامحدود" min="0" class="w-full px-4 py-3 rounded-xl bg-[#F7F9FA]/40 border border-[#353535]/15 text-[#353535] focus:outline-none focus:border-[#3C6E71] focus:bg-white text-xs font-bold tabular-nums transition-all">
                <span class="text-[10px] text-[#353535]/50 block mt-1">خالی بگذارید تا نامحدود باشد</span>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#353535] mb-1.5">زمان آماده‌سازی (دقیقه)</label>
                <input type="number" name="prep_time" value="{{ old('prep_time', $product->prep_time ?? 15) }}" min="1" max="120" class="w-full px-4 py-3 rounded-xl bg-[#F7F9FA]/40 border border-[#353535]/15 text-[#353535] focus:outline-none focus:border-[#3C6E71] focus:bg-white text-xs font-bold tabular-nums transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-[#353535] mb-1.5">وضعیت محصول *</label>
                <select name="status" class="w-full px-4 py-3 rounded-xl bg-[#F7F9FA]/40 border border-[#353535]/15 text-[#353535] focus:outline-none focus:border-[#3C6E71] focus:bg-white text-xs font-bold transition-all">
                    <option value="active" {{ $product->status === 'active' ? 'selected' : '' }}>فعال</option>
                    <option value="inactive" {{ $product->status === 'inactive' ? 'selected' : '' }}>غیرفعال</option>
                </select>
            </div>
        </div>

        <!-- Image Upload & Media Library Component -->
        <div x-data="{
            imageUrl: '{{ old('image', $product->image ?? '') }}',
            previewUrl: '{{ old('image', $product->image ?? '') }}',
            removeImage: false,
            mediaModalOpen: false,
            mediaItems: [],
            loadingMedia: false,
            handleFileSelect(event) {
                const file = event.target.files[0];
                if (file) {
                    this.previewUrl = URL.createObjectURL(file);
                    this.removeImage = false;
                }
            },
            clearImage() {
                this.imageUrl = '';
                this.previewUrl = '';
                this.removeImage = true;
                const fileInput = document.getElementById('product-file-input');
                if (fileInput) fileInput.value = '';
            },
            openMediaModal() {
                this.mediaModalOpen = true;
                if (this.mediaItems.length === 0) {
                    this.loadingMedia = true;
                    fetch('{{ route('admin.media.list-json') }}')
                        .then(r => r.json())
                        .then(data => {
                            this.mediaItems = data.items || [];
                            this.loadingMedia = false;
                        })
                        .catch(() => { this.loadingMedia = false; });
                }
            },
            selectMedia(url) {
                this.imageUrl = url;
                this.previewUrl = url;
                this.removeImage = false;
                this.mediaModalOpen = false;
            }
        }" class="p-5 rounded-2xl bg-white border border-[#19242B]/10 space-y-4 shadow-xs">
            <input type="hidden" name="remove_image" :value="removeImage ? 1 : 0">

            <div class="flex items-center justify-between border-b border-[#19242B]/8 pb-2">
                <div class="space-y-0.5">
                    <label class="block text-xs font-black text-[#19242B]">تصویر اختصاصی محصول</label>
                    <p class="text-[11px] text-[#19242B]/50">آپلود عکس با دکمه اختصاصی یا انتخاب از کتابخانه رسانه‌ها</p>
                </div>
                <button type="button" @click="openMediaModal()"
                        class="px-3 py-1.5 bg-[#387780]/10 hover:bg-[#387780]/20 text-[#387780] rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>انتخاب از کتابخانه تصاویر</span>
                </button>
            </div>

            <!-- Preview & Upload Dropzone -->
            <div class="flex flex-col sm:flex-row items-center gap-4">
                <!-- Live Preview Card -->
                <div class="relative w-28 h-28 rounded-2xl bg-[#F8FAFC] border-2 border-dashed border-[#19242B]/15 overflow-hidden flex items-center justify-center shrink-0">
                    <template x-if="previewUrl && !removeImage">
                        <div class="w-full h-full relative group">
                            <img :src="previewUrl" alt="پیش‌نمایش عکس" class="w-full h-full object-cover">
                            <button type="button" @click="clearImage()"
                                    title="حذف تصویر"
                                    class="absolute top-1 end-1 w-6 h-6 rounded-lg bg-rose-600 text-white flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity shadow-xs">
                                &times;
                            </button>
                        </div>
                    </template>
                    <template x-if="!previewUrl || removeImage">
                        <div class="text-center p-2 text-[#19242B]/40">
                            <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span class="text-[10px] font-bold block mt-1">بدون تصویر</span>
                        </div>
                    </template>
                </div>

                <!-- Custom Persian Upload Trigger -->
                <div class="flex-1 w-full space-y-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <label for="product-file-input"
                               class="cursor-pointer px-4 py-2.5 bg-[#F8FAFC] hover:bg-[#387780]/10 text-[#19242B] hover:text-[#387780] border border-[#19242B]/15 hover:border-[#387780]/40 rounded-xl text-xs font-black transition-all flex items-center gap-2 shadow-2xs">
                            <svg class="w-4 h-4 text-[#387780]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span>انتخاب و آپلود عکس جدید</span>
                        </label>
                        <input type="file" id="product-file-input" name="image_file" accept="image/*" class="hidden" @change="handleFileSelect($event)">

                        <template x-if="previewUrl && !removeImage">
                            <button type="button" @click="clearImage()"
                                    class="px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold transition-colors flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>حذف تصویر فعلی</span>
                            </button>
                        </template>
                    </div>

                    <!-- Direct URL input -->
                    <div>
                        <label class="block text-[11px] font-bold text-[#19242B]/70 mb-1">یا مسیر فایل / لینک اینترنتی تصویر:</label>
                        <input type="text" name="image" x-model="imageUrl" @input="previewUrl = imageUrl; removeImage = false"
                               placeholder="/storage/products/... یا https://..."
                               class="w-full px-3 py-2 rounded-xl bg-white border border-[#19242B]/15 text-[#19242B] text-xs font-mono dir-ltr text-left focus:border-[#387780] focus:ring-1 focus:ring-[#387780]">
                    </div>
                </div>
            </div>

            <!-- Media Library Picker Modal -->
            <div x-show="mediaModalOpen" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;" @keydown.escape.window="mediaModalOpen = false">
                <div class="bg-white rounded-3xl p-6 max-w-2xl w-full space-y-4 shadow-2xl border border-slate-200 max-h-[85vh] flex flex-col" @click.away="mediaModalOpen = false">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h4 class="text-xs font-black text-slate-800 flex items-center gap-2">
                            <span class="w-2.5 h-4 rounded-full bg-[#387780]"></span>
                            <span>انتخاب تصویر از کتابخانه رسانه‌ها</span>
                        </h4>
                        <button type="button" @click="mediaModalOpen = false" class="text-slate-400 hover:text-slate-700 font-bold text-lg leading-none">&times;</button>
                    </div>

                    <!-- Loading State -->
                    <div x-show="loadingMedia" class="py-12 text-center text-slate-400 text-xs font-bold">
                        در حال بارگذاری تصاویر...
                    </div>

                    <!-- Media Grid in Modal -->
                    <div x-show="!loadingMedia" class="grid grid-cols-3 sm:grid-cols-4 gap-3 overflow-y-auto max-h-[50vh] p-1">
                        <template x-for="item in mediaItems" :key="item.path">
                            <div @click="selectMedia(item.relative_url)"
                                 class="relative aspect-square rounded-xl overflow-hidden border-2 border-slate-200 hover:border-[#387780] cursor-pointer group bg-[#F8FAFC]">
                                <img :src="item.url" :alt="item.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                                <span class="absolute bottom-0 inset-x-0 bg-black/60 text-white text-[9px] font-mono px-1 py-0.5 truncate text-center" dir="ltr" x-text="item.name"></span>
                            </div>
                        </template>
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                        <a href="{{ route('admin.media.index') }}" target="_blank" class="text-xs font-bold text-[#387780] hover:underline flex items-center gap-1">
                            <span>مدیریت کامل کتابخانه رسانه‌ها</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                        <button type="button" @click="mediaModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-colors">
                            انصراف
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dynamic Product Popup Specifications -->
        <div class="p-4 rounded-2xl bg-[#F7F9FA]/35 border border-[#353535]/10 space-y-3">
            <h3 class="text-xs font-black text-[#353535]">مشخصات پاپ‌آپ محصول (نمایش به مشتری در منو)</h3>

            <div class="space-y-3">
                <div>
                    <label class="block text-[11px] font-bold text-[#353535] mb-1">طعم‌یادها (Tasting Notes):</label>
                    <input type="text" name="tasting_notes" value="{{ old('tasting_notes', $product->tasting_notes) }}" placeholder="مثلاً: شکلات تلخ، کارامل نمکی و فندق تفت‌داده" class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#353535]/15 text-[#353535] text-xs font-bold">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-[#353535] mb-1">نوع دانه (Bean Type):</label>
                        <input type="text" name="bean_type" value="{{ old('bean_type', $product->bean_type) }}" placeholder="مثلاً: ۱۰۰٪ عربیکا تخصصی" class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#353535]/15 text-[#353535] text-xs font-bold">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-[#353535] mb-1">نحوه سرو و بسته‌بندی:</label>
                        <input type="text" name="serving_type" value="{{ old('serving_type', $product->serving_type) }}" placeholder="مثلاً: فنجان سرامیکی / لیوان دوجداره بیرون‌بر" class="w-full px-4 py-2.5 rounded-xl bg-white border border-[#353535]/15 text-[#353535] text-xs font-bold">
                    </div>
                </div>
            </div>
        </div>

        <!-- Dynamic Product Add-ons & Customizations (Admin Configurable) -->
        <div x-data="{
            addons: {{ json_encode(old('addons', $product->addons ?? [])) }},
            addAddon(name = '', price = 0) {
                this.addons.push({ name: name, price: price });
            },
            removeAddon(index) {
                this.addons.splice(index, 1);
            }
        }" class="p-4 sm:p-5 rounded-2xl bg-white border border-[#353535]/10 space-y-4 shadow-xs">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-[#353535]/10 pb-3">
                <div>
                    <h3 class="text-xs font-black text-[#353535] flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#284B63]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        <span>افزودنی‌ها و شخصی‌سازی‌های محصول (Add-ons)</span>
                    </h3>
                    <p class="text-[11px] text-[#353535]/60 mt-0.5">افزودنی‌هایی که مشتری در پاپ‌آپ منو می‌تواند انتخاب کند (سیروپ، شات اضافه، شیر بادام و ...)</p>
                </div>

                <button type="button" @click="addAddon('', 20000)"
                        class="px-3 py-1.5 bg-[#3C6E71]/15 hover:bg-[#3C6E71]/25 text-[#353535] text-xs font-bold rounded-xl flex items-center gap-1.5 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>افزودن سطر افزودنی</span>
                </button>
            </div>

            <!-- Quick Preset Badges -->
            <div class="flex items-center gap-1.5 flex-wrap text-[11px]">
                <span class="text-[#353535]/60 font-medium">پیشنهادهای پرکاربرد:</span>
                <button type="button" @click="addAddon('شات اسپرسو اضافه', 30000)" class="px-2.5 py-1 rounded-lg bg-[#F7F9FA] hover:bg-[#3C6E71]/30 text-[#353535] border border-[#353535]/10 font-bold transition-colors">+ شات اضافه (۳۰,۰۰۰)</button>
                <button type="button" @click="addAddon('سیروپ کارامل نمکی', 25000)" class="px-2.5 py-1 rounded-lg bg-[#F7F9FA] hover:bg-[#3C6E71]/30 text-[#353535] border border-[#353535]/10 font-bold transition-colors">+ سیروپ کارامل (۲۵,۰۰۰)</button>
                <button type="button" @click="addAddon('سیروپ وانیل ماداگاسکار', 25000)" class="px-2.5 py-1 rounded-lg bg-[#F7F9FA] hover:bg-[#3C6E71]/30 text-[#353535] border border-[#353535]/10 font-bold transition-colors">+ سیروپ وانیل (۲۵,۰۰۰)</button>
                <button type="button" @click="addAddon('شیر جو دوسر گیاهی (Oat Milk)', 25000)" class="px-2.5 py-1 rounded-lg bg-[#F7F9FA] hover:bg-[#3C6E71]/30 text-[#353535] border border-[#353535]/10 font-bold transition-colors">+ شیر جو دوسر (۲۵,۰۰۰)</button>
                <button type="button" @click="addAddon('خامه فرم‌گرفته قنادی', 15000)" class="px-2.5 py-1 rounded-lg bg-[#F7F9FA] hover:bg-[#3C6E71]/30 text-[#353535] border border-[#353535]/10 font-bold transition-colors">+ خامه (۱۵,۰۰۰)</button>
            </div>

            <!-- Addons Repeater List -->
            <div class="space-y-2.5">
                <template x-for="(addon, index) in addons" :key="index">
                    <div class="flex items-center gap-2 bg-[#F7F9FA]/40 p-2.5 rounded-xl border border-[#353535]/10">
                        <div class="flex-1">
                            <input type="text" :name="'addons[' + index + '][name]'" x-model="addon.name"
                                   placeholder="عنوان افزودنی (مثلاً: سیروپ کارامل)"
                                   required
                                   class="w-full px-3 py-2 bg-white border border-[#353535]/15 rounded-lg text-xs font-bold text-[#353535] focus:border-[#3C6E71]">
                        </div>
                        <div class="w-36 sm:w-44 flex items-center gap-1.5">
                            <input type="number" :name="'addons[' + index + '][price]'" x-model="addon.price"
                                   placeholder="هزینه (تومان)"
                                   min="0" step="1000"
                                   required
                                   class="w-full px-3 py-2 bg-white border border-[#353535]/15 rounded-lg text-xs font-bold text-[#353535] tabular-nums focus:border-[#3C6E71]">
                            <span class="text-[11px] text-[#353535]/60 font-medium">تومان</span>
                        </div>
                        <button type="button" @click="removeAddon(index)"
                                title="حذف افزودنی"
                                class="w-8 h-8 rounded-lg text-[#B44A4A] hover:bg-[#B44A4A]/10 flex items-center justify-center transition-colors flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </template>

                <template x-if="addons.length === 0">
                    <div class="p-4 rounded-xl bg-[#F7F9FA]/20 border border-dashed border-[#353535]/15 text-center text-xs text-[#353535]/50">
                        هنوز هیچ افزودنی برای این محصول تعریف نشده است. با کلیک بر روی دکمه یا پیش‌فرض‌های بالا افزودنی اضافه کنید.
                    </div>
                </template>
        <!-- Hourly / All-Day Discount Configuration -->
        <div x-data="{ discountActive: {{ old('is_hourly_discount_active', $product->is_hourly_discount_active) ? 'true' : 'false' }} }"
             class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-amber-500/5 via-rose-500/5 to-transparent border border-amber-500/20 space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xs font-black text-[#19242B]">تخفیف روی محصول (تمام‌وقت یا بازه ساعتی)</h3>
                        <p class="text-[11px] text-slate-500">اعمال درصد تخفیف به صورت ساعتی (Happy Hour) یا ۲۴ ساعته با نمایش به کاربر</p>
                    </div>
                </div>

                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_hourly_discount_active" value="1" x-model="discountActive" class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-600"></div>
                </label>
            </div>

            <div x-show="discountActive" x-transition class="space-y-3 pt-2 border-t border-amber-500/15">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">درصد تخفیف (٪):</label>
                        <input type="number" name="discount_percent" value="{{ old('discount_percent', $product->discount_percent ?: 15) }}" min="1" max="90" placeholder="مثلاً: 20"
                               class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-xs font-black text-rose-600 tabular-nums focus:border-amber-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">ساعت شروع تخفیف:</label>
                        <input type="time" name="discount_start_time" value="{{ old('discount_start_time', $product->discount_start_time) }}"
                               class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-800 tabular-nums focus:border-amber-500 focus:outline-none">
                        <span class="text-[10px] text-slate-400">خالی = تمام روز</span>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">ساعت پایان تخفیف:</label>
                        <input type="time" name="discount_end_time" value="{{ old('discount_end_time', $product->discount_end_time) }}"
                               class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-800 tabular-nums focus:border-amber-500 focus:outline-none">
                        <span class="text-[10px] text-slate-400">خالی = تمام روز</span>
                    </div>
                </div>
                <p class="text-[11px] text-amber-800 bg-amber-50/80 p-2.5 rounded-xl border border-amber-200/60 leading-relaxed">
                    ✨ <strong>نکته:</strong> اگر ساعت شروع و پایان را خالی بگذارید، تخفیف به صورت دائمی (۲۴ ساعته) اعمال خواهد شد. اگر ساعت تعیین کنید، تخفیف در همان بازه فعال شده و ثانیه‌شمار مهلت باقی‌مانده در منو به مشتری نمایش داده می‌شود.
                </p>
            </div>
        </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-[#353535] mb-1.5">توضیحات کلی محصول</label>
            <textarea name="description" rows="3" placeholder="توضیحات کلی یا مشخصات جهت نمایش در منو..." class="w-full px-4 py-3 rounded-xl bg-[#F7F9FA]/40 border border-[#353535]/15 text-[#353535] focus:outline-none focus:border-[#3C6E71] focus:bg-white text-xs leading-relaxed font-medium">{{ old('description', $product->description) }}</textarea>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#353535]/10">
            <a href="{{ route('products.index') }}" class="px-5 py-2.5 bg-[#F7F9FA] hover:bg-[#F7F9FA]/80 text-[#353535] font-bold rounded-xl text-xs transition-all">انصراف</a>
            <button type="submit" class="px-6 py-2.5 bg-[#353535] hover:bg-[#1E2912] text-[#F7F9FA] font-black rounded-xl text-xs transition-all shadow-md flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>به‌روزرسانی محصول</span>
            </button>
        </div>
    </form>
</div>
@endsection

