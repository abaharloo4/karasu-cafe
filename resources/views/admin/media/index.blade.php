@extends('layouts.app')

@section('title', 'کتابخانه رسانه‌ها و تصاویر')
@section('header_title', 'کتابخانه رسانه‌ها - بی کافه (B Cafe)')

@section('content')
<div class="space-y-6" x-data="{
    copiedUrl: null,
    editingMedia: null,
    fileNames: [],
    handleFiles(event) {
        const files = event.target.files;
        this.fileNames = [];
        for (let i = 0; i < files.length; i++) {
            this.fileNames.push(files[i].name);
        }
    },
    copyText(text, label) {
        navigator.clipboard.writeText(text);
        this.copiedUrl = label;
        setTimeout(() => this.copiedUrl = null, 2500);
    }
}">

    <!-- Header Actions -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-black text-[#19242B] flex items-center gap-2">
                <span class="w-2.5 h-5 rounded-full bg-[#387780]"></span>
                <span>کتابخانه تصاویر و فایل‌های برنامه</span>
            </h2>
            <p class="text-xs text-[#19242B]/65 mt-1 font-medium">مدیریت متمرکز تصاویر، تعیین متن جایگزین (Alt) سئو، و کپی مستقیم آدرس جهت استفاده در محصولات و بنرها.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('products.index') }}" class="px-4 py-2.5 bg-white border border-[#19242B]/15 hover:bg-[#F8FAFC] text-[#19242B] font-bold rounded-xl text-xs transition-all flex items-center gap-2 shadow-2xs">
                <svg class="w-4 h-4 text-[#387780]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                <span>بازگشت به محصولات</span>
            </a>
        </div>
    </div>

    <!-- Custom Persian Upload Card -->
    <div class="bg-white border border-[#19242B]/12 p-6 rounded-3xl shadow-xs space-y-4">
        <div class="flex items-center justify-between border-b border-[#19242B]/8 pb-3">
            <h3 class="text-sm font-black text-[#19242B] flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-[#387780]/10 text-[#387780] flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                </span>
                <span>آپلود تصویر جدید به کتابخانه</span>
            </h3>
            <span class="text-[11px] text-[#19242B]/50 font-medium">فرمت‌های مجاز: JPG, PNG, WEBP, SVG (حداکثر ۵ مگابایت)</span>
        </div>

        <form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <!-- Custom Drag and Drop Persian Dropzone -->
            <div class="relative border-2 border-dashed border-[#387780]/30 hover:border-[#387780] bg-[#F8FAFC] hover:bg-[#387780]/5 rounded-2xl p-6 transition-all text-center group cursor-pointer">
                <input type="file" name="files[]" multiple accept="image/*,.svg" required
                       @change="handleFiles($event)"
                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                <div class="flex flex-col items-center justify-center gap-2 pointer-events-none">
                    <div class="w-14 h-14 rounded-2xl bg-white border border-[#387780]/20 text-[#387780] flex items-center justify-center shadow-xs group-hover:scale-105 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    </div>
                    <div class="text-center">
                        <p class="text-xs font-bold text-[#19242B]">
                            <span class="text-[#387780] underline underline-offset-4 font-black">انتخاب فایل از سیستم</span>
                            یا کشیدن و رها کردن فایل‌ها به این قسمت
                        </p>
                        <p class="text-[11px] text-[#19242B]/50 mt-1">امکان انتخاب همزمان چندین تصویر وجود دارد</p>
                    </div>
                </div>

                <!-- Selected Files Badge Preview -->
                <template x-if="fileNames.length > 0">
                    <div class="mt-4 pt-4 border-t border-[#387780]/20 flex flex-wrap gap-2 justify-center pointer-events-none">
                        <template x-for="(name, index) in fileNames" :key="index">
                            <span class="px-2.5 py-1 bg-white border border-[#387780]/30 text-[#19242B] text-[11px] font-mono rounded-lg shadow-2xs flex items-center gap-1.5" dir="ltr">
                                <svg class="w-3.5 h-3.5 text-[#387780]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span x-text="name"></span>
                            </span>
                        </template>
                    </div>
                </template>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-black text-[#19242B] mb-1">متن جایگزین پیش‌فرض (Alt Text سئو) <span class="text-[#19242B]/40 font-normal">(اختیاری)</span></label>
                    <input type="text" name="alt_text" placeholder="مثال: لاته کارامل کاراسو با فوم شیر تازه" class="w-full text-xs rounded-xl border border-[#19242B]/15 px-3 py-2.5 bg-white focus:border-[#387780] focus:ring-1 focus:ring-[#387780] transition-colors">
                </div>
                <div>
                    <label class="block text-xs font-black text-[#19242B] mb-1">عنوان تصویر (Title) <span class="text-[#19242B]/40 font-normal">(اختیاری)</span></label>
                    <input type="text" name="title" placeholder="مثال: لاته مخصوص بی کافه" class="w-full text-xs rounded-xl border border-[#19242B]/15 px-3 py-2.5 bg-white focus:border-[#387780] focus:ring-1 focus:ring-[#387780] transition-colors">
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-6 py-2.5 bg-[#19242B] hover:bg-[#387780] text-white font-black rounded-xl text-xs transition-all shadow-md flex items-center gap-2 min-h-[44px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <span>شروع آپلود در کتابخانه</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Media Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
        @forelse($mediaFiles as $media)
            <div class="bg-white border border-[#19242B]/12 rounded-2xl overflow-hidden shadow-xs hover:shadow-md transition-all flex flex-col justify-between group">
                <div class="relative aspect-square bg-[#F8FAFC] overflow-hidden flex items-center justify-center p-2">
                    <img src="{{ $media['url'] }}" alt="{{ $media['alt_text'] ?? $media['name'] }}" loading="lazy" class="w-full h-full object-cover rounded-xl group-hover:scale-105 transition-transform duration-200">
                    
                    <!-- Size Badge -->
                    <span class="absolute top-2 start-2 px-2 py-0.5 rounded-md bg-[#19242B]/80 text-white text-[9px] font-mono backdrop-blur-xs" dir="ltr">
                        {{ $media['formatted_size'] }}
                    </span>

                    <!-- Category / Location Badge -->
                    @if(!empty($media['is_product']))
                        <span class="absolute top-2 end-2 px-2 py-0.5 rounded-md bg-[#387780]/90 text-white text-[9px] font-bold backdrop-blur-xs">
                            محصول
                        </span>
                    @endif
                </div>

                <div class="p-3 space-y-2.5 border-t border-[#19242B]/8">
                    <div>
                        <p class="text-[11px] font-mono text-[#19242B] truncate text-start font-bold" dir="ltr" title="{{ $media['name'] }}">
                            {{ $media['name'] }}
                        </p>
                        <div class="mt-1 flex items-center justify-between">
                            @if(!empty($media['alt_text']))
                                <span class="text-[10px] text-[#387780] bg-[#387780]/10 px-2 py-0.5 rounded-md font-medium truncate max-w-[120px]" title="متن جایگزین: {{ $media['alt_text'] }}">
                                    Alt: {{ $media['alt_text'] }}
                                </span>
                            @else
                                <span class="text-[10px] text-[#E0A96D] bg-[#E0A96D]/10 px-1.5 py-0.5 rounded-md font-medium">
                                    فاقد Alt
                                </span>
                            @endif
                            
                            <!-- Edit Alt Button -->
                            <button type="button"
                                    @click="editingMedia = {{ json_encode($media) }}"
                                    class="text-[10px] text-[#387780] hover:underline font-bold">
                                ویرایش Alt
                            </button>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-1.5 pt-1 border-t border-[#19242B]/6">
                        <div class="flex items-center gap-1.5">
                            <!-- Copy Full URL -->
                            <button type="button"
                                    @click="copyText('{{ $media['url'] }}', 'full-{{ $media['id'] }}')"
                                    class="flex-1 py-1.5 bg-[#F8FAFC] hover:bg-[#387780] hover:text-white text-[#19242B] font-bold rounded-lg text-[10px] transition-colors flex items-center justify-center gap-1 border border-[#19242B]/10">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                <span x-text="copiedUrl === 'full-{{ $media['id'] }}' ? 'کپی شد!' : 'لینک کامل'"></span>
                            </button>

                            <!-- Copy Relative Path -->
                            <button type="button"
                                    @click="copyText('{{ $media['relative_url'] }}', 'rel-{{ $media['id'] }}')"
                                    title="کپی مسیر نسبی فایل برای استفاده در قالب"
                                    class="flex-1 py-1.5 bg-[#F8FAFC] hover:bg-[#19242B] hover:text-white text-[#19242B] font-bold rounded-lg text-[10px] transition-colors flex items-center justify-center gap-1 border border-[#19242B]/10">
                                <span x-text="copiedUrl === 'rel-{{ $media['id'] }}' ? 'کپی شد!' : 'کپی مسیر'"></span>
                            </button>

                            <!-- Delete Button -->
                            <form method="POST" action="{{ route('admin.media.destroy') }}" onsubmit="return confirm('آیا از حذف این تصویر از فضای ذخیره‌سازی اطمینان دارید؟');">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="id" value="{{ $media['id'] }}">
                                <input type="hidden" name="path" value="{{ $media['path'] }}">
                                <button type="submit" title="حذف تصویر" class="w-7 h-7 rounded-lg bg-[#B44A4A]/10 hover:bg-[#B44A4A] text-[#B44A4A] hover:text-white flex items-center justify-center transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white border border-[#19242B]/10 p-12 rounded-3xl text-center space-y-2">
                <svg class="w-12 h-12 text-[#387780]/40 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <p class="text-xs text-[#19242B]/60 font-bold">هنوز تصویری در کتابخانه رسانه آپلود نشده است.</p>
            </div>
        @endforelse
    </div>

    <!-- Edit Alt Text Modal (Alpine.js) -->
    <div x-show="editingMedia !== null"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs"
         @keydown.escape.window="editingMedia = null">
        
        <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4 border border-[#19242B]/10"
             @click.away="editingMedia = null">
            
            <div class="flex items-center justify-between border-b border-[#19242B]/8 pb-3">
                <h3 class="text-sm font-black text-[#19242B] flex items-center gap-2">
                    <span class="w-2.5 h-4 rounded-full bg-[#387780]"></span>
                    <span>ویرایش اطلاعات سئو تصویر</span>
                </h3>
                <button type="button" @click="editingMedia = null" class="text-[#19242B]/40 hover:text-[#19242B] text-lg font-bold">&times;</button>
            </div>

            <template x-if="editingMedia">
                <form :action="'{{ url('/admin/media') }}/' + editingMedia.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="flex items-center gap-3 p-3 bg-[#F8FAFC] rounded-2xl border border-[#19242B]/8">
                        <img :src="editingMedia.url" class="w-14 h-14 object-cover rounded-xl border border-[#19242B]/10">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-mono font-bold text-[#19242B] truncate" dir="ltr" x-text="editingMedia.name"></p>
                            <p class="text-[10px] text-[#19242B]/50 mt-0.5" dir="ltr" x-text="editingMedia.formatted_size"></p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-[#19242B] mb-1">متن جایگزین (Alt Text سئو)</label>
                        <input type="text" name="alt_text" :value="editingMedia.alt_text"
                               placeholder="توضیح تصویر برای موتورهای جستجو و معلولین..."
                               class="w-full text-xs rounded-xl border border-[#19242B]/15 px-3 py-2.5 bg-white focus:border-[#387780] focus:ring-1 focus:ring-[#387780]">
                    </div>

                    <div>
                        <label class="block text-xs font-black text-[#19242B] mb-1">عنوان تصویر (Title)</label>
                        <input type="text" name="title" :value="editingMedia.title"
                               placeholder="عنوان توضیحی تصویر..."
                               class="w-full text-xs rounded-xl border border-[#19242B]/15 px-3 py-2.5 bg-white focus:border-[#387780] focus:ring-1 focus:ring-[#387780]">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="editingMedia = null" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-[#19242B] rounded-xl text-xs font-bold transition-colors">
                            انصراف
                        </button>
                        <button type="submit" class="px-5 py-2 bg-[#387780] hover:bg-[#19242B] text-white rounded-xl text-xs font-black transition-colors shadow-xs">
                            ذخیره تغییرات
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

</div>
@endsection
