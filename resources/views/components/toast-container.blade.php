<!-- Luxury B Cafe Bespoke Toast / Notification System -->
<div x-data="cafeToastSystem()"
     @cafe-toast.window="addToast($event.detail)"
     class="fixed top-5 end-5 z-50 flex flex-col gap-2.5 max-w-sm w-full pointer-events-none px-4 sm:px-0"
     dir="rtl">

    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.visible"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-2 sm:translate-x-4 scale-95"
             class="pointer-events-auto bg-white/95 backdrop-blur-md border rounded-2xl shadow-xl p-3.5 flex items-start gap-3 relative overflow-hidden transition-all group"
             :class="{
                 'border-emerald-200 text-emerald-950 shadow-emerald-500/10': toast.type === 'success',
                 'border-rose-200 text-rose-950 shadow-rose-500/10': toast.type === 'error',
                 'border-amber-200 text-amber-950 shadow-amber-500/10': toast.type === 'warning',
                 'border-[#3C6E71]/20 text-[#284B63] shadow-teal-500/10': toast.type === 'info'
             }">

            <!-- Icon -->
            <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5"
                 :class="{
                     'bg-emerald-50 text-emerald-600': toast.type === 'success',
                     'bg-rose-50 text-rose-600': toast.type === 'error',
                     'bg-amber-50 text-amber-600': toast.type === 'warning',
                     'bg-teal-50 text-[#3C6E71]': toast.type === 'info'
                 }">
                <template x-if="toast.type === 'success'">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </template>
                <template x-if="toast.type === 'error'">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </template>
                <template x-if="toast.type === 'warning'">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </template>
                <template x-if="toast.type === 'info'">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </template>
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0 pr-0.5">
                <template x-if="toast.title">
                    <h4 class="text-xs font-black mb-0.5 leading-snug" x-text="toast.title"></h4>
                </template>
                <p class="text-xs font-medium leading-relaxed opacity-90" x-text="toast.message"></p>
            </div>

            <!-- Dismiss Button -->
            <button type="button"
                    @click="removeToast(toast.id)"
                    class="text-slate-400 hover:text-slate-700 transition-colors p-1 rounded-lg hover:bg-slate-100 flex-shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </template>
</div>

@once
<script>
function cafeToastSystem() {
    return {
        toasts: [],
        addToast(detail) {
            const id = Date.now() + Math.random();
            const toast = {
                id: id,
                type: detail.type || 'info',
                title: detail.title || '',
                message: detail.message || '',
                visible: true
            };
            this.toasts.push(toast);

            // Auto dismiss after 4.5 seconds
            setTimeout(() => {
                this.removeToast(id);
            }, 4500);
        },
        removeToast(id) {
            const index = this.toasts.findIndex(t => t.id === id);
            if (index !== -1) {
                this.toasts[index].visible = false;
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 300);
            }
        }
    };
}

// Global JavaScript Helpers
window.CafeToast = {
    show(type, message, title = '') {
        window.dispatchEvent(new CustomEvent('cafe-toast', {
            detail: { type, message, title }
        }));
    },
    success(message, title = 'موفقیت') {
        this.show('success', message, title);
    },
    error(message, title = 'خطا') {
        this.show('error', message, title);
    },
    warning(message, title = 'توجه') {
        this.show('warning', message, title);
    },
    info(message, title = 'اطلاعیه') {
        this.show('info', message, title);
    }
};

// Intercept browser window.alert to render our custom luxury toast instead
window.alert = function(message) {
    window.CafeToast.info(message, 'پیام سیستم');
};
</script>
@endonce

<!-- Auto render Laravel session alerts -->
@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.CafeToast.success(@json(session('success')));
        });
    </script>
@endif

@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.CafeToast.error(@json(session('error')));
        });
    </script>
@endif

@if(session('info'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.CafeToast.info(@json(session('info')));
        });
    </script>
@endif

@if(session('warning'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.CafeToast.warning(@json(session('warning')));
        });
    </script>
@endif
