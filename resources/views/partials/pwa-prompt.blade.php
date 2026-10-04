@php
    $panelTitle = 'اپلیکیشن بی کافه (B Cafe)';
@endphp

<div x-data="{
    canInstall: false,
    installed: false,
    dismissed: localStorage.getItem('bcafe_pwa_dismissed') === 'true',
    pushSupported: ('serviceWorker' in navigator) && ('PushManager' in window),
    init() {
        if (window.BCafePwa) {
            window.BCafePwa.init('unified');
        }
        window.addEventListener('pwa-can-install', () => {
            if (!this.dismissed && !window.BCafePwa.isStandalone()) {
                this.canInstall = true;
            }
        });
        window.addEventListener('pwa-installed', () => {
            this.canInstall = false;
            this.installed = true;
        });
    },
    async installApp() {
        const ok = await window.BCafePwa.promptInstall();
        if (ok) {
            this.canInstall = false;
        }
    },
    dismiss() {
        this.canInstall = false;
        this.dismissed = true;
        localStorage.setItem('bcafe_pwa_dismissed', 'true');
    }
}" x-init="init()" class="select-none">

    <!-- Bottom Install Banner -->
    <template x-if="canInstall && !installed">
        <aside class="fixed bottom-20 md:bottom-4 inset-x-4 max-w-md mx-auto z-40 bg-[#141D22]/95 backdrop-blur-md text-white border border-[#3C6E71]/40 rounded-2xl p-4 shadow-2xl flex items-center justify-between gap-3 animate-fade-in"
               role="dialog"
               aria-label="نصب اپلیکیشن">
            <div class="flex items-center gap-3">
                <img src="{{ asset('icons/icon-192x192.png') }}"
                     alt="بی کافه"
                     class="w-12 h-12 rounded-xl object-cover border border-white/10 shadow-sm flex-shrink-0">
                <div>
                    <h2 class="text-sm font-black text-white leading-tight">نصب {{ $panelTitle }}</h2>
                    <p class="text-[11px] text-gray-300 mt-0.5 leading-normal">دسترسی سریع‌تر، مرور آفلاین و دریافت آنی اعلان‌ها</p>
                </div>
            </div>

            <div class="flex items-center gap-2 flex-shrink-0">
                <button type="button"
                        @click="installApp()"
                        class="px-3.5 py-2 bg-[#3C6E71] hover:bg-[#387780] active:scale-95 text-white text-xs font-black rounded-xl transition-all shadow-md flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>نصب</span>
                </button>
                <button type="button"
                        @click="dismiss()"
                        class="p-2 text-gray-400 hover:text-white rounded-lg transition-colors cursor-pointer"
                        title="بستن">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </aside>
    </template>
</div>
