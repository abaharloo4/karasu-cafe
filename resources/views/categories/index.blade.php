@extends('layouts.app')

@section('title', 'مدیریت دسته‌بندی‌های منو')
@section('header_title', 'دسته‌بندی محصولات منو - بی کافه (B Cafe)')

@section('content')
<div class="space-y-6" x-data="{ editModal: false, editId: null, editName: '', editStatus: 'active', editSvg: '' }">

    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <h2 class="text-base font-bold text-[#353535] flex items-center gap-2">
            <svg class="w-5 h-5 text-[#3C6E71]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            <span>لیست دسته‌بندی‌های محصولات منو</span>
        </h2>

        <a href="{{ route('products.index') }}" class="px-4 py-2.5 bg-white border border-[#353535]/15 hover:bg-[#F7F9FA] text-[#353535] font-bold rounded-xl text-xs transition-all flex items-center gap-2 shadow-2xs">
            <svg class="w-4 h-4 text-[#284B63]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            <span>بازگشت به لیست محصولات</span>
        </a>
    </div>

    <!-- Create Category Card & List Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Add Category Form -->
        <div class="bg-white border border-[#353535]/12 p-6 rounded-3xl space-y-4 shadow-xs h-fit">
            <h3 class="text-sm font-black text-[#353535] border-b border-[#353535]/10 pb-3 flex items-center gap-2">
                <span class="w-2 h-4 rounded-full bg-[#284B63]"></span>
                <span>افزودن دسته‌بندی جدید</span>
            </h3>

            <form method="POST" action="{{ route('categories.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-[#353535] mb-1.5">عنوان دسته‌بندی *</label>
                    <input type="text" name="name" placeholder="مثلاً: قهوه تخصصی و دمی" required class="w-full px-4 py-3 rounded-xl bg-[#F7F9FA]/40 border border-[#353535]/15 text-[#353535] text-xs font-bold focus:outline-none focus:border-[#3C6E71] focus:bg-white transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#353535] mb-1.5">کد اختصاصی SVG آیکون (اختیاری)</label>
                    <textarea name="icon_svg" rows="3" placeholder="کد کامل تگ <svg>...</svg> جهت نمایش شکیل در منو" class="w-full px-3 py-2 text-[11px] font-mono rounded-xl bg-[#F7F9FA]/40 border border-[#353535]/15 text-[#353535] focus:outline-none focus:border-[#3C6E71] focus:bg-white transition-all dir-ltr text-left"></textarea>
                    <span class="text-[10px] text-[#353535]/50 block mt-1">می‌توانید مستقیماً کد SVG یا فایل SVG را آپلود کنید.</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#353535] mb-1.5">یا آپلود فایل SVG</label>
                    <input type="file" name="icon_file" accept=".svg" class="w-full text-xs text-[#353535]/70 file:ms-0 file:me-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#3C6E71]/15 file:text-[#353535] hover:file:bg-[#3C6E71] hover:file:text-white cursor-pointer">
                </div>

                <button type="submit" class="w-full py-3 bg-[#353535] hover:bg-[#1E2912] text-[#F7F9FA] font-black rounded-xl text-xs transition-all shadow-md flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>ایجاد دسته‌بندی</span>
                </button>
            </form>
        </div>

        <!-- Categories List Table -->
        <div class="md:col-span-2 bg-white border border-[#353535]/12 rounded-3xl overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-start text-xs">
                    <thead>
                        <tr class="bg-[#F7F9FA]/60 text-[#353535]/70 border-b border-[#353535]/10">
                            <th class="p-4 font-black">آیکون</th>
                            <th class="p-4 font-black">عنوان دسته‌بندی</th>
                            <th class="p-4 font-black">تعداد محصولات</th>
                            <th class="p-4 font-black">وضعیت</th>
                            <th class="p-4 font-black text-center">عملیات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#353535]/8 text-[#353535]">
                        @forelse($categories as $category)
                            <tr class="hover:bg-[#F7F9FA]/25 transition-colors">
                                <td class="p-4">
                                    <div class="w-9 h-9 rounded-xl bg-[#F7F9FA] border border-[#353535]/10 flex items-center justify-center text-[#284B63]">
                                        @if($category->icon_svg)
                                            <div class="w-5 h-5 flex items-center justify-center [&>svg]:w-5 [&>svg]:h-5">{!! $category->icon_svg !!}</div>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                        @endif
                                    </div>
                                </td>
                                <td class="p-4 font-black text-[#353535]">{{ $category->name }}</td>
                                <td class="p-4 font-bold text-[#3C6E71] tabular-nums">{{ number_format($category->products_count) }} محصول</td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $category->status === 'active' ? 'bg-[#3C6E71]/15 text-[#353535]' : 'bg-[#B44A4A]/10 text-[#B44A4A]' }}">
                                        {{ $category->status === 'active' ? 'فعال' : 'غیرفعال' }}
                                    </span>
                                </td>
                                <td class="p-4 text-center space-x-2 space-x-reverse">
                                    <button @click="editModal = true; editId = {{ $category->id }}; editName = '{{ addslashes($category->name) }}'; editStatus = '{{ $category->status }}'; editSvg = {{ json_encode($category->icon_svg ?? '') }}"
                                            class="px-3 py-1.5 bg-[#F7F9FA] hover:bg-[#3C6E71] hover:text-[#F7F9FA] text-[#353535] border border-[#353535]/10 rounded-xl text-[11px] font-bold transition-all">
                                        ویرایش
                                    </button>
                                    <form method="POST" action="{{ route('categories.destroy', $category) }}" class="inline-block" onsubmit="return confirm('آیا از حذف این دسته‌بندی اطمینان دارید؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 bg-white hover:bg-[#B44A4A] hover:text-white text-[#B44A4A] border border-[#B44A4A]/20 rounded-xl text-[11px] font-bold transition-all">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-[#353535]/60">هنوز دسته‌بندی ثبت نشده است.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Edit Category Modal -->
    <div x-show="editModal" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white border border-[#353535]/15 rounded-3xl p-6 w-full max-w-md space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-[#353535]/10 pb-3">
                <h3 class="text-sm font-black text-[#353535]">ویرایش دسته‌بندی</h3>
                <button type="button" @click="editModal = false" class="w-8 h-8 rounded-lg bg-[#F7F9FA] hover:bg-[#B44A4A]/10 text-[#353535] hover:text-[#B44A4A] flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="'{{ url('/categories') }}/' + editId" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-[#353535] mb-1">عنوان دسته‌بندی *</label>
                    <input type="text" name="name" x-model="editName" required class="w-full px-4 py-3 rounded-xl bg-[#F7F9FA]/40 border border-[#353535]/15 text-[#353535] text-xs font-bold focus:outline-none focus:border-[#3C6E71]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#353535] mb-1">کد اختصاصی SVG آیکون</label>
                    <textarea name="icon_svg" x-model="editSvg" rows="3" placeholder="<svg>...</svg>" class="w-full px-3 py-2 text-[11px] font-mono rounded-xl bg-[#F7F9FA]/40 border border-[#353535]/15 text-[#353535] focus:outline-none focus:border-[#3C6E71] dir-ltr text-left"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#353535] mb-1">یا آپلود فایل SVG جدید</label>
                    <input type="file" name="icon_file" accept=".svg" class="w-full text-xs text-[#353535]/70 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#3C6E71]/15 file:text-[#353535] cursor-pointer">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#353535] mb-1">وضعیت *</label>
                    <select name="status" x-model="editStatus" class="w-full px-4 py-3 rounded-xl bg-[#F7F9FA]/40 border border-[#353535]/15 text-[#353535] text-xs font-bold focus:outline-none focus:border-[#3C6E71]">
                        <option value="active">فعال</option>
                        <option value="inactive">غیرفعال</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-[#353535]/10">
                    <button type="button" @click="editModal = false" class="px-4 py-2.5 bg-[#F7F9FA] text-[#353535] font-bold rounded-xl text-xs">انصراف</button>
                    <button type="submit" class="px-5 py-2.5 bg-[#353535] hover:bg-[#1E2912] text-[#F7F9FA] font-black rounded-xl text-xs shadow-md">به‌روزرسانی دسته‌بندی</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

