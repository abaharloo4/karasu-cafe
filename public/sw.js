// ==============================================================================
// B Cafe (بی کافه) - Scoped Service Worker
// Cache-First for static assets, Stale-While-Revalidate for visited pages,
// Strict Offline Warning on checkout/live writes, and VAPID Push Notification Handler
// ==============================================================================

const STATIC_CACHE = 'bcafe-static-v7';
const RUNTIME_CACHE = 'bcafe-runtime-v7';

const OFFLINE_WRITE_HTML = `<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>بی کافه | اتصال اینترنت برقرار نیست</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #0F172A; color: #F8FAFC; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1.5rem; text-align: center; }
        .card { background: rgba(30, 41, 59, 0.85); border: 1px solid rgba(255,255,255,0.1); border-radius: 24px; max-width: 440px; padding: 2.5rem 2rem; box-shadow: 0 20px 40px rgba(0,0,0,0.5); }
        .icon { width: 64px; height: 64px; margin: 0 auto 1.25rem; background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 20px; display: flex; align-items: center; justify-content: center; }
        h1 { font-size: 1.3rem; font-weight: 800; color: #FFFFFF; margin-bottom: 0.75rem; }
        p { font-size: 0.9rem; color: #94A3B8; line-height: 1.7; margin-bottom: 1.5rem; }
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: 0.75rem 1.5rem; background: #387780; color: #FFF; font-weight: 700; font-size: 0.85rem; border-radius: 12px; text-decoration: none; cursor: pointer; border: none; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="1" y1="1" x2="23" y2="23"></line>
                <path d="M16.72 11.06A10.94 10.94 0 0 1 19 12.55"></path>
                <path d="M5 12.55a10.94 10.94 0 0 1 5.17-2.39"></path>
                <path d="M10.71 5.05A16 16 0 0 1 22.58 9"></path>
                <path d="M1.42 9a15.91 15.91 0 0 1 4.7-2.88"></path>
                <path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path>
                <line x1="12" y1="20" x2="12.01" y2="20"></line>
            </svg>
        </div>
        <h1>برای ثبت سفارش به اینترنت متصل شوید</h1>
        <p>
            مشاهده منو و صفحات قبلی به صورت آفلاین فعال است، اما جهت محاسبه دقیق موجودی و ثبت سفارش، اتصال به اینترنت الزامی می‌باشد.
        </p>
        <button class="btn" onclick="window.location.reload()">تلاش مجدد و بارگذاری</button>
    </div>
</body>
</html>`;

// Install event: skip waiting immediately
self.addEventListener('install', (event) => {
    self.skipWaiting();
});

// Activate event: clean up stale caches immediately and take control
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.map((key) => {
                    if (key !== STATIC_CACHE && key !== RUNTIME_CACHE) {
                        console.log('[SW] Purging outdated cache:', key);
                        return caches.delete(key);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch event: caching strategy dispatcher
self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);

    // Only intercept http: and https: schemes (strictly ignore chrome-extension://, moz-extension://, etc.)
    if (!url.protocol.startsWith('http')) {
        return;
    }

    // Skip non-GET requests (e.g. POST, PUT, DELETE)
    if (request.method !== 'GET') {
        return;
    }

    // HTML Page Navigations: ALWAYS Network-First
    // Critical: Never serve stale HTML for session-dependent, auth, admin, or form-submitted pages!
    const isHtmlNavigation = request.mode === 'navigate' || request.headers.get('accept')?.includes('text/html');

    if (isHtmlNavigation) {
        event.respondWith(
            fetch(request)
                .then((networkResponse) => {
                    // Only cache public browsing pages like menu if needed, NEVER cache admin, auth, customer, or order routes
                    if (networkResponse && networkResponse.status === 200) {
                        const isDynamicRoute = url.pathname.startsWith('/admin') ||
                                               url.pathname.startsWith('/customer') ||
                                               url.pathname.startsWith('/login') ||
                                               url.pathname.startsWith('/orders') ||
                                               url.pathname.startsWith('/settings') ||
                                               url.pathname.startsWith('/subscriptions');
                        if (!isDynamicRoute) {
                            const clone = networkResponse.clone();
                            caches.open(RUNTIME_CACHE).then((cache) => cache.put(request, clone));
                        }
                    }
                    return networkResponse;
                })
                .catch(() => {
                    // Network failed (offline): Fallback to cached version or friendly offline fallback
                    return caches.match(request).then((cached) => {
                        return cached || new Response(OFFLINE_WRITE_HTML, {
                            headers: { 'Content-Type': 'text/html; charset=utf-8' },
                            status: 200
                        });
                    });
                })
        );
        return;
    }

    // Live Write Pages: checkout, cart order, payment callbacks
    const isLiveWriteRoute = url.pathname.includes('/cart/order') ||
                             url.pathname.includes('/cart/checkout') ||
                             url.pathname.includes('/payment/') ||
                             url.pathname.includes('/subscriptions/purchase');

    if (isLiveWriteRoute) {
        event.respondWith(
            fetch(request).catch(() => {
                return new Response(OFFLINE_WRITE_HTML, {
                    headers: { 'Content-Type': 'text/html; charset=utf-8' },
                    status: 503,
                    statusText: 'Offline Write Prohibited'
                });
            })
        );
        return;
    }

    // Static Assets: Cache-First (Exclude dynamic uploads in /storage/ to ensure fresh deletion/updates)
    const isStaticAsset = !url.pathname.startsWith('/storage/') && (
                          url.pathname.startsWith('/build/') ||
                          url.pathname.startsWith('/icons/') ||
                          url.hostname.includes('fonts.googleapis.com') ||
                          url.hostname.includes('fonts.gstatic.com') ||
                          url.pathname.endsWith('.js') ||
                          url.pathname.endsWith('.css') ||
                          url.pathname.endsWith('.woff2') ||
                          url.pathname.endsWith('.png') ||
                          url.pathname.endsWith('.svg'));

    if (isStaticAsset) {
        event.respondWith(
            caches.match(request).then((cached) => {
                if (cached) return cached;
                return fetch(request).then((response) => {
                    if (!response || response.status !== 200 || response.type === 'opaque') {
                        return response;
                    }
                    if (request.url.startsWith('http')) {
                        const clone = response.clone();
                        caches.open(STATIC_CACHE).then((cache) => cache.put(request, clone)).catch(() => {});
                    }
                    return response;
                });
            })
        );
        return;
    }
});

// Web Push Notification Received
self.addEventListener('push', (event) => {
    let payload = {};
    try {
        payload = event.data ? event.data.json() : {};
    } catch (e) {
        payload = {
            title: 'بی کافه (B Cafe)',
            body: event.data ? event.data.text() : 'اعلان جدید دریافت شد.',
        };
    }

    const title = payload.title || 'بی کافه (B Cafe)';
    const options = {
        body: payload.body || 'به‌روزرسانی جدیدی در سفارش شما ثبت شد.',
        icon: payload.icon || '/icons/icon-192x192.png',
        badge: payload.badge || '/icons/badge-72x72.png',
        vibrate: payload.vibrate || [100, 50, 100],
        dir: 'rtl',
        lang: 'fa',
        tag: payload.tag || 'bcafe-notification',
        renotify: payload.renotify !== undefined ? payload.renotify : true,
        data: payload.data || { url: '/' },
        actions: payload.actions || [
            { action: 'open', title: 'مشاهده جزئیات' }
        ],
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

// Notification Click Event
self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    const targetUrl = event.notification.data?.url || '/';

    // Log notification_opened event to server via beacon / fetch
    const logPromise = fetch('/pwa/event', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            event: 'notification_opened',
            path: targetUrl,
            panel: targetUrl.startsWith('/admin') ? 'admin' : (targetUrl.startsWith('/driver') ? 'driver' : 'customer')
        })
    }).catch(() => {});

    // Focus or open target URL in client window
    const clientPromise = clients.matchAll({ type: 'window', includeUncontrolled: true }).then((windowClients) => {
        for (let client of windowClients) {
            if (client.url.includes(targetUrl) && 'focus' in client) {
                return client.focus();
            }
        }
        if (clients.openWindow) {
            return clients.openWindow(targetUrl);
        }
    });

    event.waitUntil(Promise.all([logPromise, clientPromise]));
});
