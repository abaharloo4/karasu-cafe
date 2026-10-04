/**
 * B Cafe Progressive Web App (PWA) & Web Push Client
 */

// Helper to convert base64 VAPID key to Uint8Array
function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding)
        .replace(/-/g, '+')
        .replace(/_/g, '/');

    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

// Global PWA State & Controller
window.BCafePwa = {
    deferredPrompt: null,
    swRegistration: null,
    panel: 'customer',

    init(panel = 'customer') {
        this.panel = panel;
        this.registerServiceWorker();
        this.setupInstallPrompt();
        this.setupOfflineGuards();
        this.trackPageView();
    },

    // 1. Service Worker Registration
    async registerServiceWorker() {
        if (!('serviceWorker' in navigator)) return;

        try {
            const reg = await navigator.serviceWorker.register('/sw.js', { scope: '/' });
            this.swRegistration = reg;
            console.log('[PWA] ServiceWorker registered with scope:', reg.scope);
        } catch (err) {
            console.warn('[PWA] ServiceWorker registration failed:', err);
        }
    },

    // 2. Install Prompt Handling
    setupInstallPrompt() {
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            this.deferredPrompt = e;
            window.dispatchEvent(new CustomEvent('pwa-can-install', { detail: { installable: true } }));
        });

        window.addEventListener('appinstalled', () => {
            this.deferredPrompt = null;
            this.logEvent('install');
            window.dispatchEvent(new CustomEvent('pwa-installed'));
        });
    },

    // Trigger install flow
    async promptInstall() {
        if (!this.deferredPrompt) {
            return false;
        }

        this.deferredPrompt.prompt();
        const choiceResult = await this.deferredPrompt.userChoice;
        if (choiceResult.outcome === 'accepted') {
            this.logEvent('install');
        }
        this.deferredPrompt = null;
        return choiceResult.outcome === 'accepted';
    },

    // Check if running in standalone PWA mode
    isStandalone() {
        return window.matchMedia('(display-mode: standalone)').matches ||
            window.navigator.standalone === true;
    },

    // 3. Web Push Subscription
    async subscribePush() {
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
            alert('مرورگر شما از دریافت اعلان‌های اینترنتی پشتیبانی نمی‌کند.');
            return { success: false, reason: 'unsupported' };
        }

        try {
            const permission = await Notification.requestPermission();
            if (permission !== 'granted') {
                return { success: false, reason: 'denied' };
            }

            const reg = this.swRegistration || await navigator.serviceWorker.ready;
            
            // Get VAPID public key
            const res = await fetch('/pwa/vapid-public-key');
            const data = await res.json();
            if (!data.publicKey) {
                throw new Error('کلید عمومی اعلان دریافت نشد.');
            }

            const convertedVapidKey = urlBase64ToUint8Array(data.publicKey);

            // Subscribe via browser push manager
            const subscription = await reg.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: convertedVapidKey,
            });

            // Send subscription to server
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const subRes = await fetch('/pwa/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                },
                body: JSON.stringify({
                    endpoint: subscription.endpoint,
                    keys: {
                        p256dh: btoa(String.fromCharCode.apply(null, new Uint8Array(subscription.getKey('p256dh')))),
                        auth: btoa(String.fromCharCode.apply(null, new Uint8Array(subscription.getKey('auth')))),
                    },
                    panel: this.panel,
                    path: window.location.pathname,
                }),
            });

            if (!subRes.ok) {
                const errJson = await subRes.json();
                throw new Error(errJson.error || 'خطا در ثبت اشتراک در سرور');
            }

            return { success: true };
        } catch (error) {
            console.error('[PWA Push Error]', error);
            alert(error.message || 'خطا در فعال‌سازی دریافت اعلان.');
            return { success: false, error };
        }
    },

    // 4. Strict Offline Order Guards
    setupOfflineGuards() {
        const updateOnlineStatus = () => {
            const isOnline = navigator.onLine;
            let banner = document.getElementById('bcafe-offline-banner');

            if (!isOnline) {
                if (!banner) {
                    banner = document.createElement('div');
                    banner.id = 'bcafe-offline-banner';
                    banner.className = 'fixed top-0 left-0 right-0 z-50 bg-[#B44A4A] text-white text-xs font-bold py-2 px-4 text-center shadow-md flex items-center justify-center gap-2 animate-bounce';
                    banner.innerHTML = `
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 0a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.166a1 1 0 111.414 1.414"/></svg>
                        <span>اتصال اینترنت قطع است - صفحات ذخیره‌شده را مشاهده می‌کنید</span>
                    `;
                    document.body.prepend(banner);
                }
            } else {
                if (banner) banner.remove();
            }
        };

        window.addEventListener('online', updateOnlineStatus);
        window.addEventListener('offline', updateOnlineStatus);

        // Strict Offline Order Interceptor: Do NOT allow offline checkout or order submissions
        document.addEventListener('submit', (e) => {
            if (!navigator.onLine) {
                const targetForm = e.target;
                const action = targetForm.getAttribute('action') || '';
                if (action.includes('/cart/order') || action.includes('/cart/checkout') || targetForm.classList.contains('checkout-form')) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    alert('برای ثبت سفارش به اینترنت متصل شوید.');
                }
            }
        }, true);

        // Also intercept clicks on checkout buttons when offline
        document.addEventListener('click', (e) => {
            if (!navigator.onLine) {
                const btn = e.target.closest('[data-checkout-btn], a[href*="/cart/checkout"], button[type="submit"]');
                if (btn && (btn.closest('form[action*="/cart/order"]') || btn.matches('a[href*="/cart/checkout"]'))) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    alert('برای ثبت سفارش به اینترنت متصل شوید.');
                }
            }
        }, true);
    },

    // 5. Lightweight Telemetry Event Logger
    logEvent(event) {
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const payload = JSON.stringify({
                event: event,
                panel: this.panel,
                path: window.location.pathname,
            });

            if (navigator.sendBeacon) {
                const blob = new Blob([payload], { type: 'application/json' });
                navigator.sendBeacon('/pwa/event', blob);
            } else {
                fetch('/pwa/event', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || '',
                    },
                    body: payload,
                    keepalive: true,
                }).catch(() => {});
            }
        } catch (_) {}
    },

    trackPageView() {
        this.logEvent('page_view');
    },
};
