// B Cafe Custom Alert & Toast Notification System
// Replaces standard browser alert() with beautiful, branded Persian toast modal notifications

class BCafeToastManager {
    constructor() {
        this.container = null;
        this.initContainer();
        this.overrideNativeAlert();
    }

    initContainer() {
        if (document.getElementById('bcafe-toast-container')) {
            this.container = document.getElementById('bcafe-toast-container');
            return;
        }

        const div = document.createElement('div');
        div.id = 'bcafe-toast-container';
        div.className = 'fixed top-4 left-1/2 -translate-x-1/2 z-[99999] flex flex-col items-center gap-2.5 w-[92%] max-w-md pointer-events-none transition-all';
        div.setAttribute('dir', 'rtl');
        document.body ? document.body.appendChild(div) : document.addEventListener('DOMContentLoaded', () => document.body.appendChild(div));
        this.container = div;
    }

    show(message, type = 'info', duration = 4000) {
        if (!this.container) this.initContainer();

        const toast = document.createElement('div');
        toast.className = 'pointer-events-auto w-full p-4 rounded-2xl shadow-xl border flex items-center justify-between gap-3 transform -translate-y-4 opacity-0 transition-all duration-300 backdrop-blur-md';

        // Styles & Icons based on type
        let iconSvg = '';
        let colorClasses = '';

        if (type === 'error' || type === 'danger') {
            colorClasses = 'bg-rose-950/90 border-rose-500/40 text-rose-100 shadow-rose-950/30';
            iconSvg = `<div class="w-8 h-8 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>`;
        } else if (type === 'success') {
            colorClasses = 'bg-emerald-950/90 border-emerald-500/40 text-emerald-100 shadow-emerald-950/30';
            iconSvg = `<div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>`;
        } else if (type === 'warning') {
            colorClasses = 'bg-amber-950/90 border-amber-500/40 text-amber-100 shadow-amber-950/30';
            iconSvg = `<div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>`;
        } else {
            colorClasses = 'bg-[#19242B]/95 border-[#387780]/40 text-slate-100 shadow-black/40';
            iconSvg = `<div class="w-8 h-8 rounded-xl bg-[#387780]/20 text-[#387780] flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>`;
        }

        toast.className += ' ' + colorClasses;
        toast.innerHTML = `
            <div class="flex items-center gap-3 flex-1 min-w-0">
                ${iconSvg}
                <div class="text-xs font-bold leading-relaxed break-words">${message}</div>
            </div>
            <button type="button" class="p-1 rounded-lg text-white/50 hover:text-white hover:bg-white/10 transition-colors flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        `;

        const closeBtn = toast.querySelector('button');
        const removeToast = () => {
            toast.classList.add('-translate-y-4', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        };

        closeBtn.addEventListener('click', removeToast);

        this.container.appendChild(toast);

        // Animate in
        requestAnimationFrame(() => {
            toast.classList.remove('-translate-y-4', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
        });

        // Auto remove
        if (duration > 0) {
            setTimeout(removeToast, duration);
        }
    }

    overrideNativeAlert() {
        const self = this;
        // Keep original if ever needed
        window.__nativeAlert = window.alert;

        window.alert = function (message) {
            const msgStr = String(message || '');
            let type = 'info';

            if (msgStr.includes('خطا') || msgStr.includes('اشتباه') || msgStr.includes('ناموفق') || msgStr.includes('منقضی') || msgStr.includes('اینترنت')) {
                type = 'error';
            } else if (msgStr.includes('موفقیت') || msgStr.includes('ثبت شد') || msgStr.includes('انجام شد') || msgStr.includes('تایید')) {
                type = 'success';
            } else if (msgStr.includes('توجه') || msgStr.includes('هشدار') || msgStr.includes('اجباری')) {
                type = 'warning';
            }

            self.show(msgStr, type);
        };
    }
}

// Instantiate globally
if (typeof window !== 'undefined') {
    window.bcafeToast = new BCafeToastManager();
}

export default BCafeToastManager;
