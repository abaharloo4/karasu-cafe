@extends('layouts.app')

@section('title', 'به‌روزرسانی و استقرار امن سیستم')
@section('header_title', 'مدیریت نسخه‌ها و استقرار cPanel')

@section('content')
<div class="space-y-6" x-data="deploymentManager()">

    <!-- Header & Quick Diagnostics -->
    <div class="bg-white border border-slate-200/80 p-5 rounded-2xl flex flex-wrap items-center justify-between gap-4 shadow-xs">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-[#387780] border border-teal-200/80 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </div>
            <div>
                <h1 class="text-base sm:text-lg font-black text-slate-800 flex items-center gap-2">
                    <span>مدیریت و استقرار خودکار نسخه‌ها</span>
                    <span class="px-2.5 py-0.5 text-[10px] font-mono font-bold rounded-full bg-teal-100/80 text-teal-800">Git Zero-Downtime</span>
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">به‌روزرسانی اتمیک با جابجایی Symlink، پشتیبان‌گیری پیش از مایگریشن، و بازگشت خودکار در صورت خطا</p>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <button type="button" @click="checkForUpdates()" :disabled="checking"
                    class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-xl text-xs btn-tactile flex items-center gap-2 transition-all disabled:opacity-50">
                <svg class="w-4 h-4 text-slate-600" :class="checking ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span x-text="checking ? 'در حال بررسی گیت‌هاب...' : 'بررسی آپدیت'"></span>
            </button>

            <button type="button" @click="promptDeploy()" :disabled="deploying"
                    class="px-4 py-2.5 bg-[#387780] hover:bg-[#2E636B] text-white font-bold rounded-xl text-xs btn-tactile flex items-center gap-2 shadow-xs transition-all disabled:opacity-40">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/></svg>
                <span x-text="deploying ? 'در حال استقرار...' : 'اجرای آپدیت'">اجرای آپدیت</span>
            </button>
        </div>
    </div>

    <!-- Toast Notification Banner -->
    <template x-if="notification">
        <div class="p-4 rounded-2xl border text-xs sm:text-sm font-bold flex items-center justify-between gap-3 shadow-xs"
             :class="notification.type === 'error' ? 'bg-rose-50 border-rose-200 text-rose-800' : (notification.type === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-blue-50 border-blue-200 text-blue-800')">
            <div class="flex items-center gap-2.5">
                <template x-if="notification.type === 'error'">
                    <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </template>
                <template x-if="notification.type === 'success'">
                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </template>
                <template x-if="notification.type === 'info'">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </template>
                <span x-text="notification.message"></span>
            </div>
            <button type="button" @click="notification = null" class="text-slate-400 hover:text-slate-600 p-1">✕</button>
        </div>
    </template>

    <!-- Active Version & Update Status Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- Card 1: Currently Deployed Version -->
        <div class="bg-white border border-slate-200/80 p-5 rounded-2xl shadow-xs space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400">نسخه مستقر فعلی</span>
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 ring-4 ring-emerald-50"></span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-xl font-black font-mono text-slate-800">{{ $current['short_sha'] }}</span>
                <span class="text-xs text-slate-500">شاخه main</span>
            </div>
            <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed bg-slate-50 p-2.5 rounded-xl border border-slate-100 font-medium">
                {{ $current['message'] ?: 'بدون پیام کامیت' }}
            </p>
            <div class="flex items-center justify-between text-[11px] text-slate-400 pt-1">
                <span>توسعه‌دهنده: {{ $current['author'] }}</span>
                <span>{{ $current['deployed_at'] ? \Illuminate\Support\Carbon::parse($current['deployed_at'])->format('Y/m/d H:i') : 'نصب اولیه' }}</span>
            </div>
        </div>

        <!-- Card 2: GitHub Repository & Remote Status -->
        <div class="bg-white border border-slate-200/80 p-5 rounded-2xl shadow-xs space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400">وضعیت مخزن گیت‌هاب</span>
                <template x-if="checkCompleted && updateAvailable">
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-50 text-amber-700 border border-amber-200">نسخه جدید موجود است</span>
                </template>
                <template x-if="checkCompleted && !updateAvailable">
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">به‌روز است</span>
                </template>
                <template x-if="!checkCompleted">
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-100 text-slate-600">آماده بررسی</span>
                </template>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-sm font-bold font-mono text-slate-700 truncate" title="{{ config('deployment.github_repo') }}">{{ config('deployment.github_repo') }}</span>
            </div>
            <div class="text-xs text-slate-600 space-y-1.5 pt-1">
                <template x-if="updateAvailable">
                    <div>
                        <div class="font-bold text-amber-700 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            <span x-text="latestCommit.short_sha + ' : ' + latestCommit.message"></span>
                        </div>
                        <div class="text-[11px] text-slate-400 mt-1" x-text="commitsDiff.length + ' کامیت جدید آماده استقرار'"></div>
                    </div>
                </template>
                <template x-if="!updateAvailable && checkCompleted">
                    <p class="text-emerald-700 font-medium flex items-center gap-1.5">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>سرور دقیقاً منطبق با آخرین کامیت گیت‌هاب است.</span>
                    </p>
                </template>
                <template x-if="!checkCompleted">
                    <p class="text-slate-500">جهت مقایسه نسخه سرور با آخرین کامیت گیت‌هاب، دکمه بررسی آپدیت را کلیک کنید.</p>
                </template>
            </div>
        </div>

        <!-- Card 3: Security & cPanel UAPI Status -->
        <div class="bg-white border border-slate-200/80 p-5 rounded-2xl shadow-xs space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400">اتصال سرور و cPanel UAPI</span>
                @if($isConfigured)
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">متصل به cPanel</span>
                @else
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-rose-50 text-rose-700 border border-rose-200">نیاز به توکن API</span>
                @endif
            </div>
            <div class="text-xs text-slate-700 space-y-2">
                <div class="flex items-center justify-between text-[11px]">
                    <span class="text-slate-400">پشتیبان‌گیری پیش از مایگریشن:</span>
                    <span class="font-bold text-emerald-700">فعال (mysqldump .sql.gz)</span>
                </div>
                <div class="flex items-center justify-between text-[11px]">
                    <span class="text-slate-400">تغییر مسیر اتمیک (Symlink):</span>
                    <span class="font-bold text-slate-700">deploy/current -> releases</span>
                </div>
                <div class="flex items-center justify-between text-[11px]">
                    <span class="text-slate-400">آخرین فایل پشتیبان پایگاه:</span>
                    <span class="font-mono text-slate-600 truncate max-w-[150px]" title="{{ $backups[0]['name'] ?? 'یافت نشد' }}">{{ $backups[0]['name'] ?? 'ثبت نشده' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Terminal Deployment Progress Modal / Box -->
    <div x-show="activeDeployment || deploying" class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-2xl text-slate-100 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                    <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                </div>
                <span class="text-xs font-mono font-bold text-slate-300">cPanel Zero-Downtime Deployment Console</span>
            </div>
            <div>
                <span x-text="deployStatusLabel" class="px-2.5 py-1 text-[11px] font-bold rounded-lg"
                      :class="deployStatus === 'success' ? 'bg-emerald-950 text-emerald-400 border border-emerald-800' : (deployStatus === 'failed' ? 'bg-rose-950 text-rose-400 border border-rose-800' : 'bg-blue-950 text-blue-400 border border-blue-800 animate-pulse')">
                </span>
            </div>
        </div>

        <!-- Terminal Output -->
        <pre class="bg-slate-950 p-4 rounded-xl text-xs font-mono text-emerald-400 leading-relaxed overflow-x-auto max-h-80 custom-scrollbar whitespace-pre-wrap select-text" x-text="deploymentLog || 'در حال آماده‌سازی عملیات استقرار...'"></pre>

        <div class="flex items-center justify-between text-xs text-slate-400 pt-2">
            <span>فرآیندها: دانلود مخزن ← پیوند مشترک ← کامپوزر ← بک‌آپ دیتابیس ← مایگریشن ← بیلد فرانت ← سوئیچ سیم‌لینک ← ریست OPcache</span>
            <template x-if="deployStatus === 'success' || deployStatus === 'failed'">
                <button type="button" @click="activeDeployment = null; deploying = false;" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs">
                    بستن ترمینال
                </button>
            </template>
        </div>
    </div>

    <!-- Recent Commits Diff List (if updates found) -->
    <template x-if="commitsDiff.length > 0">
        <div class="bg-amber-50/70 border border-amber-200/80 rounded-2xl p-5 space-y-3">
            <h3 class="text-xs sm:text-sm font-extrabold text-amber-900 flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <span>تغییرات جدید آماده دریافت و استقرار روی سرور (<span x-text="commitsDiff.length"></span> کامیت)</span>
            </h3>
            <div class="divide-y divide-amber-200/50">
                <template x-for="c in commitsDiff" :key="c.sha">
                    <div class="py-2.5 flex items-center justify-between text-xs gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="font-mono font-bold text-amber-900 bg-amber-200/60 px-2 py-0.5 rounded text-[11px]" x-text="c.short_sha"></span>
                            <span class="text-slate-800 font-medium truncate" x-text="c.message"></span>
                        </div>
                        <div class="flex items-center gap-3 text-[11px] text-slate-500 flex-shrink-0">
                            <span x-text="c.author"></span>
                            <a :href="c.url" target="_blank" class="text-[#387780] hover:underline">مشاهده در GitHub ↗</a>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </template>

    <!-- Deployments Audit History Table -->
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
                <svg class="w-4 h-4 text-[#387780]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>تاریخچه استقرارها و لاگ تغییرات سیستم</span>
            </h2>
            <span class="text-xs text-slate-400">{{ $deployments->total() }} رکورد ثبت‌شده</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-500 font-bold border-b border-slate-100">
                        <th class="p-3.5 sm:px-5">#</th>
                        <th class="p-3.5 sm:px-5">کامیت</th>
                        <th class="p-3.5 sm:px-5">توضیحات و پیام</th>
                        <th class="p-3.5 sm:px-5">اجرا توسط</th>
                        <th class="p-3.5 sm:px-5">زمان آغاز</th>
                        <th class="p-3.5 sm:px-5">وضعیت</th>
                        <th class="p-3.5 sm:px-5 text-center">عملیات و لاگ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    @forelse($deployments as $d)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-3.5 sm:px-5 text-slate-400 font-mono">{{ $d->id }}</td>
                            <td class="p-3.5 sm:px-5 font-mono font-bold text-slate-800">
                                <span class="bg-slate-100 px-2 py-0.5 rounded text-[11px]">{{ $d->short_sha }}</span>
                            </td>
                            <td class="p-3.5 sm:px-5 max-w-xs truncate" title="{{ $d->commit_message }}">
                                {{ $d->commit_message ?: 'استقرار خودکار' }}
                            </td>
                            <td class="p-3.5 sm:px-5 text-slate-600">
                                {{ $d->triggerUser?->name ?? ($d->commit_author ?: 'سیستم') }}
                            </td>
                            <td class="p-3.5 sm:px-5 text-slate-500 text-[11px]">
                                {{ $d->started_at->format('Y/m/d H:i') }}
                            </td>
                            <td class="p-3.5 sm:px-5">
                                <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg border {{ $d->status_badge_class }}">
                                    {{ $d->status_label }}
                                </span>
                            </td>
                            <td class="p-3.5 sm:px-5 text-center">
                                <button type="button" @click="viewLog({{ json_encode($d->log) }}, '{{ $d->short_sha }}')"
                                        class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg text-[11px] btn-tactile">
                                    مشاهده لاگ کامل
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">
                                تاکنون هیچ استقراری در این جدول ثبت نشده است. با کلیک بر روی دکمه «اجرای آپدیت»، اولین فرآیند ثبت خواهد شد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($deployments->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $deployments->links() }}
            </div>
        @endif
    </div>

    <!-- Log Viewer Modal -->
    <div x-show="logModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
         x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         style="display: none;">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-3xl overflow-hidden shadow-2xl space-y-4 p-5" @click.outside="logModalOpen = false">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span>گزارش کامل اجرای استقرار کامیت</span>
                    <span class="font-mono text-emerald-400" x-text="selectedLogCommit"></span>
                </h3>
                <button type="button" @click="logModalOpen = false" class="text-slate-400 hover:text-white p-1 rounded-lg">✕</button>
            </div>
            <pre class="bg-slate-950 p-4 rounded-xl text-xs font-mono text-emerald-400 leading-relaxed overflow-x-auto max-h-96 custom-scrollbar whitespace-pre-wrap select-text" x-text="selectedLogContent || 'گزارشی ثبت نشده است.'"></pre>
            <div class="flex justify-end pt-2">
                <button type="button" @click="logModalOpen = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl">
                    بستن
                </button>
            </div>
        </div>
    </div>

    <!-- Deployment Confirmation Modal -->
    <div x-show="confirmDeployModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
         x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         style="display: none;">
        <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-md overflow-hidden shadow-2xl space-y-4 p-6 text-right" @click.outside="confirmDeployModalOpen = false">
            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-[#387780] border border-teal-200 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </div>
            
            <div class="space-y-1.5">
                <h3 class="text-base font-extrabold text-slate-800">تأیید اجرای استقرار و به‌روزرسانی</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    سیستم به صورت خودکار مراحل زیر را بدون قطعی انجام می‌دهد:
                </p>
                <ul class="text-[11px] text-slate-600 space-y-1 list-disc list-inside bg-slate-50 p-3 rounded-xl border border-slate-100 font-medium">
                    <li>دریافت آخرین کدها از مخزن گیت‌هاب</li>
                    <li>پشتیبان‌گیری کامل دیتابیس در فایل <span class="font-mono text-emerald-700">.sql.gz</span></li>
                    <li>اجرای خودکار مایگریشن‌های افزایشی (<span class="font-mono">migrate --force</span>)</li>
                    <li>سوئیچ اتمیک سیم‌لینک و ریست حافظه OPcache</li>
                </ul>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" @click="confirmDeployModalOpen = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-bold btn-tactile">
                    انصراف
                </button>
                <button type="button" @click="confirmAndStartDeploy()" class="px-5 py-2.5 rounded-xl bg-[#387780] hover:bg-[#2E636B] text-white text-xs font-bold btn-tactile shadow-xs">
                    شروع استقرار امن
                </button>
            </div>
        </div>
    </div>

</div>

<!-- Alpine.js Deployment Logic -->
<script>
function deploymentManager() {
    return {
        checking: false,
        checkCompleted: false,
        updateAvailable: false,
        forceDeployAllowed: false,
        latestCommit: {},
        commitsDiff: [],
        
        deploying: false,
        activeDeployment: null,
        deployStatus: 'pending',
        deployStatusLabel: 'آماده',
        deploymentLog: '',
        pollTimer: null,

        logModalOpen: false,
        selectedLogContent: '',
        selectedLogCommit: '',

        confirmDeployModalOpen: false,
        notification: null,

        notify(message, type = 'info') {
            this.notification = { message, type };
            if (type !== 'error') {
                setTimeout(() => {
                    if (this.notification?.message === message) {
                        this.notification = null;
                    }
                }, 8000);
            }
        },

        checkForUpdates() {
            this.checking = true;
            this.notification = null;

            fetch('{{ route('admin.deployments.check') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                this.checking = false;
                this.checkCompleted = true;
                if (data.success) {
                    this.updateAvailable = data.has_update;
                    this.latestCommit = data.latest || {};
                    this.commitsDiff = data.commits || [];
                    this.forceDeployAllowed = true;

                    if (data.has_update) {
                        this.notify(`نسخه جدید (${data.latest.short_sha}) در گیت‌هاب یافت شد. آماده استقرار است.`, 'info');
                    } else {
                        this.notify('سرور کاملاً به‌روز است و با آخرین کامیت گیت‌هاب مطابقت دارد.', 'success');
                    }
                } else {
                    this.notify(data.error || 'خطا در بررسی گیت‌هاب', 'error');
                }
            })
            .catch(err => {
                this.checking = false;
                this.notify('خطا در ارسال درخواست بررسی آپدیت به سرور', 'error');
                console.error(err);
            });
        },

        promptDeploy() {
            this.confirmDeployModalOpen = true;
        },

        confirmAndStartDeploy() {
            this.confirmDeployModalOpen = false;
            const commitSha = this.latestCommit.sha || '{{ $current['sha'] ?? 'HEAD' }}';
            this.startDeployment(commitSha);
        },

        startDeployment(commitSha) {
            this.deploying = true;
            this.deployStatus = 'running';
            this.deployStatusLabel = 'در حال اجرای UAPI و استقرار...';
            this.deploymentLog = '→ ارسال درخواست استقرار به سرور cPanel...\n';

            fetch('{{ route('admin.deployments.trigger') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    commit_sha: commitSha,
                    commit_message: this.latestCommit.message || 'استقرار دستی',
                    commit_author: this.latestCommit.author || 'مدیر'
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.activeDeployment = data.deployment_id;
                    this.deploymentLog += data.message + '\n';
                    this.pollStatus(data.deployment_id);
                } else {
                    this.deploying = false;
                    this.deployStatus = 'failed';
                    this.deployStatusLabel = 'خطا در آغاز';
                    this.deploymentLog += '✕ ' + (data.error || 'خطا در ارسال دستور استقرار') + '\n';
                    alert(data.error || 'خطا در استقرار');
                }
            })
            .catch(err => {
                this.deploying = false;
                this.deployStatus = 'failed';
                this.deployStatusLabel = 'خطای اتصال';
                this.deploymentLog += '✕ خطای شبکه در ارسال درخواست استقرار\n';
                console.error(err);
            });
        },

        pollStatus(deploymentId) {
            if (this.pollTimer) clearInterval(this.pollTimer);

            this.pollTimer = setInterval(() => {
                fetch(`/admin/deployments/${deploymentId}/status`)
                    .then(res => res.json())
                    .then(data => {
                        this.deployStatus = data.status;
                        if (data.log) {
                            this.deploymentLog = data.log;
                        }

                        if (data.status === 'success') {
                            this.deployStatusLabel = 'استقرار با موفقیت انجام شد';
                            this.deploying = false;
                            clearInterval(this.pollTimer);
                        } else if (data.status === 'failed') {
                            this.deployStatusLabel = 'عملیات استقرار ناموفق بود';
                            this.deploying = false;
                            clearInterval(this.pollTimer);
                        }
                    })
                    .catch(err => console.warn('Poll error:', err));
            }, 3000);
        },

        viewLog(log, sha) {
            this.selectedLogContent = log;
            this.selectedLogCommit = sha;
            this.logModalOpen = true;
        }
    };
}
</script>
@endsection
