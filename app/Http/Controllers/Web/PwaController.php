<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PushSubscription;
use App\Models\PwaSetting;
use App\Services\Pwa\PwaAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PwaController extends Controller
{
    public function __construct(
        protected PwaAnalyticsService $analyticsService
    ) {}

    /**
     * Generate dynamic Web App Manifest per panel.
     */
    public function manifest(Request $request): Response
    {
        $panel = $request->route('panel', 'customer');
        if (!in_array($panel, ['customer', 'admin'])) {
            $panel = 'customer';
        }

        // Fetch settings for requested panel with customer default
        $setting = PwaSetting::forPanel($panel);

        $icon192 = $setting->icon_192 ?: asset('icons/icon-192x192.png');
        $icon512 = $setting->icon_512 ?: asset('icons/icon-512x512.png');
        $iconMaskable = $setting->icon_maskable ?: asset('icons/icon-512x512.png');

        $manifestData = [
            'name' => $setting->app_name ?: 'بی کافه (B Cafe) - شیراز',
            'short_name' => $setting->short_name ?: 'بی کافه',
            'description' => 'سامانه کافه، سفارش آنلاین و قهوه تخصصی بی کافه',
            'start_url' => $setting->start_url ?: '/',
            'scope' => $setting->scope ?: '/',
            'display' => 'standalone',
            'orientation' => 'portrait',
            'dir' => 'rtl',
            'lang' => 'fa',
            'theme_color' => $setting->theme_color ?: '#387780',
            'background_color' => $setting->background_color ?: '#0F172A',
            'icons' => [
                [
                    'src' => $icon192,
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'any',
                ],
                [
                    'src' => $icon512,
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'any',
                ],
                [
                    'src' => $iconMaskable,
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'maskable',
                ],
            ],
            'categories' => ['food', 'shopping'],
        ];

        return response(json_encode($manifestData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))
            ->header('Content-Type', 'application/manifest+json; charset=utf-8')
            ->header('Cache-Control', 'public, max-age=60');
    }

    /**
     * Provide VAPID public key to client.
     */
    public function vapidPublicKey(): JsonResponse
    {
        return response()->json([
            'publicKey' => config('webpush.vapid.public_key'),
        ]);
    }

    /**
     * Register or update push subscription for authenticated subscriber.
     */
    public function subscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => 'required|url|max:1000',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
            'panel' => 'nullable|string|in:customer,admin',
        ]);

        $subscriber = null;
        $panel = $validated['panel'] ?? 'customer';

        if (auth('customer')->check()) {
            $subscriber = auth('customer')->user();
            $panel = 'customer';
        } elseif (auth('web')->check()) {
            $subscriber = auth('web')->user();
            $panel = 'admin';
        }

        if (!$subscriber) {
            return response()->json([
                'error' => 'برای فعال‌سازی اعلان‌ها، ابتدا وارد حساب کاربری خود شوید.',
            ], 401);
        }

        $endpointHash = PushSubscription::hashEndpoint($validated['endpoint']);

        $subscription = PushSubscription::updateOrCreate(
            ['endpoint_hash' => $endpointHash],
            [
                'subscribable_type' => $subscriber->getMorphClass(),
                'subscribable_id' => $subscriber->getKey(),
                'endpoint' => $validated['endpoint'],
                'p256dh' => $validated['keys']['p256dh'],
                'auth' => $validated['keys']['auth'],
                'panel' => $panel,
                'user_agent' => $request->userAgent(),
            ]
        );

        $this->analyticsService->logEvent(
            $panel,
            'subscription_created',
            $request->input('path'),
            $subscriber->getMorphClass(),
            $subscriber->getKey(),
            $request->userAgent()
        );

        return response()->json([
            'success' => true,
            'id' => $subscription->id,
            'message' => 'اشتراک اعلان‌ها با موفقیت فعال شد.',
        ]);
    }

    /**
     * Unsubscribe and prune push endpoint.
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        $endpoint = $request->input('endpoint');
        if (!$endpoint) {
            return response()->json(['error' => 'Endpoint missing'], 422);
        }

        $endpointHash = PushSubscription::hashEndpoint($endpoint);
        $sub = PushSubscription::where('endpoint_hash', $endpointHash)->first();

        if ($sub) {
            $this->analyticsService->logEvent(
                $sub->panel,
                'subscription_revoked',
                $request->input('path'),
                $sub->subscribable_type,
                $sub->subscribable_id,
                $request->userAgent()
            );
            $sub->delete();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Record PWA lifecycle events (install, page_view, notification_opened).
     */
    public function logEvent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'event' => 'required|string|in:install,page_view,notification_opened',
            'panel' => 'nullable|string|in:customer,admin',
            'path' => 'nullable|string|max:255',
        ]);

        $panel = $validated['panel'] ?? 'customer';
        $subType = null;
        $subId = null;

        if (auth('customer')->check()) {
            $subType = auth('customer')->user()->getMorphClass();
            $subId = auth('customer')->id();
        } elseif (auth('web')->check()) {
            $subType = auth('web')->user()->getMorphClass();
            $subId = auth('web')->id();
        }

        $this->analyticsService->logEvent(
            $panel,
            $validated['event'],
            $validated['path'] ?? null,
            $subType,
            $subId,
            $request->userAgent()
        );

        return response()->json(['success' => true]);
    }
}
