<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PwaDailyStat;
use App\Models\PwaSetting;
use App\Services\Pwa\PushNotificationService;
use App\Services\Pwa\PwaAnalyticsService;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PwaAdminController extends Controller
{
    /**
     * Show PWA & Push Settings for all panels.
     */
    public function settings()
    {
        $customerSetting = PwaSetting::forPanel('customer');
        $adminSetting = PwaSetting::forPanel('admin');

        return view('admin.pwa.settings', compact('customerSetting', 'adminSetting'));
    }

    /**
     * Update PWA & Push Settings for a specific panel.
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'panel' => 'required|in:customer,admin',
            'app_name' => 'required|string|max:100',
            'short_name' => 'required|string|max:50',
            'theme_color' => 'required|string|max:20',
            'background_color' => 'required|string|max:20',
            'icon_192' => 'nullable|string|max:255',
            'icon_512' => 'nullable|string|max:255',
            'icon_maskable' => 'nullable|string|max:255',
            'ga4_measurement_id' => 'nullable|string|max:50',
            'push_enabled' => 'nullable|boolean',
            'allowed_categories' => 'nullable|array',
            'allowed_categories.*' => 'string|max:50',
        ]);

        $setting = PwaSetting::forPanel($validated['panel']);

        $setting->update([
            'app_name' => $validated['app_name'],
            'short_name' => $validated['short_name'],
            'theme_color' => $validated['theme_color'],
            'background_color' => $validated['background_color'],
            'icon_192' => $request->input('icon_192') ?: null,
            'icon_512' => $request->input('icon_512') ?: null,
            'icon_maskable' => $request->input('icon_maskable') ?: null,
            'ga4_measurement_id' => $request->input('ga4_measurement_id') ?: null,
            'notification_killswitches' => [
                'push_enabled' => $request->boolean('push_enabled'),
                'allowed_categories' => $validated['allowed_categories'] ?? [],
            ],
        ]);

        return back()->with('success', "تنظیمات وب‌اپلیکیشن پنل [{$validated['panel']}] با موفقیت ذخیره شد.");
    }

    /**
     * Send a test push notification to verify Web Push pipeline.
     */
    public function testPush(Request $request, PushNotificationService $pushService)
    {
        $user = auth()->user();
        if (!$user) {
            return back()->with('error', 'کاربر احراز هویت نشده است.');
        }

        $sentCount = $pushService->send(
            $user,
            'تست پوش نوتیفیکیشن بی کافه',
            'سیستم اعلان‌های وب‌اپلیکیشن بی کافه به درستی کار می‌کند.',
            ['url' => route('admin.pwa-settings')],
            'new_order'
        );

        if ($sentCount === 0) {
            // Check if there are active push subscriptions for admin panel
            $adminSub = \App\Models\PushSubscription::where('panel', 'admin')->first()
                ?? \App\Models\PushSubscription::latest()->first();

            if ($adminSub) {
                // Associate this subscription with current user if orphaned
                if (!$adminSub->subscribable_id || $adminSub->subscribable_type !== $user->getMorphClass()) {
                    $adminSub->update([
                        'subscribable_type' => $user->getMorphClass(),
                        'subscribable_id' => $user->getKey(),
                        'panel' => 'admin',
                    ]);
                }
                $sentCount = $pushService->send(
                    $user,
                    'تست پوش نوتیفیکیشن بی کافه',
                    'سیستم اعلان‌های وب‌اپلیکیشن بی کافه به درستی کار می‌کند.',
                    ['url' => route('admin.pwa-settings')],
                    'new_order'
                );
            }
        }

        if ($sentCount > 0) {
            return back()->with('success', "اعلان آزمایشی به {$sentCount} دستگاه فعال ارسال شد.");
        }

        return back()->with('error', 'هیچ اشتراک اعلانی برای این مرورگر/کاربر ثبت نشده است. ابتدا روی دکمه «فعال‌سازی اعلان در این مرورگر» کلیک کنید.');
    }

    /**
     * Show PWA Analytics Dashboard.
     */
    public function analytics(Request $request, PwaAnalyticsService $analyticsService)
    {
        $panel = $request->query('panel', 'all');
        if (!in_array($panel, ['all', 'customer', 'admin'])) {
            $panel = 'all';
        }

        $range = $request->query('range', '30d');
        $days = match ($range) {
            '7d' => 7,
            '90d' => 90,
            default => 30,
        };

        $startDate = Carbon::today()->subDays($days - 1);
        $endDate = Carbon::today();

        // Ensure current day stats are rolled up so dashboard displays live data
        $analyticsService->aggregateDaily(Carbon::today());

        $query = PwaDailyStat::where('date', '>=', $startDate->toDateString())
            ->where('date', '<=', $endDate->toDateString());

        if ($panel !== 'all') {
            $query->where('panel', $panel);
        }

        $stats = $query->orderBy('date', 'desc')->get();

        // Rollup summary metrics
        $totalInstalls = $stats->sum('installs_count');
        $totalPageViews = $stats->sum('page_views_count');
        $totalNotificationsSent = $stats->sum('notifications_sent_count');
        $totalNotificationsOpened = $stats->sum('notifications_opened_count');
        $activeUsers = $stats->sum('active_subscribers_count');

        $openRate = $totalNotificationsSent > 0
            ? round(($totalNotificationsOpened / $totalNotificationsSent) * 100, 1)
            : 0;

        $installConversionRate = $activeUsers > 0
            ? round(($totalInstalls / $activeUsers) * 100, 1)
            : 0;

        // Group by date for charts/visualization
        $dailySummary = $stats->groupBy(function ($item) {
            return $item->date ?? '';
        })->map(function ($dayStats, $date) {
            $sent = $dayStats->sum('notifications_sent_count');
            $opened = $dayStats->sum('notifications_opened_count');
            $rate = $sent > 0 ? round(($opened / $sent) * 100, 1) : 0;

            return [
                'date' => $date,
                'installs' => $dayStats->sum('installs_count'),
                'active_users' => $dayStats->sum('active_subscribers_count'),
                'page_views' => $dayStats->sum('page_views_count'),
                'notifications_sent' => $sent,
                'notifications_opened' => $opened,
                'open_rate' => $rate,
            ];
        })->values();

        return view('admin.pwa.analytics', compact(
            'panel',
            'range',
            'totalInstalls',
            'totalPageViews',
            'totalNotificationsSent',
            'totalNotificationsOpened',
            'activeUsers',
            'openRate',
            'installConversionRate',
            'dailySummary',
            'stats'
        ));
    }

    /**
     * Export PWA Analytics as CSV.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $panel = $request->query('panel', 'all');
        $days = (int) $request->query('days', 30);
        $startDate = Carbon::today()->subDays($days - 1);

        $query = PwaDailyStat::where('date', '>=', $startDate->toDateString());
        if ($panel !== 'all' && in_array($panel, ['customer', 'admin'])) {
            $query->where('panel', $panel);
        }

        $records = $query->orderBy('date', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="bcafe-pwa-analytics-' . Carbon::today()->format('Y-m-d') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($records) {
            $handle = fopen('php://output', 'w');
            // Add UTF-8 BOM for Persian display in Excel
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header row
            fputcsv($handle, [
                'تاریخ',
                'پنل',
                'تعداد نصب',
                'کاربران فعال',
                'بازدید صفحات',
                'اعلان‌های ارسالی',
                'اعلان‌های باز شده',
                'نرخ باز شدن (%)',
            ]);

            foreach ($records as $row) {
                fputcsv($handle, [
                    str_replace('-', '/', $row->date),
                    match ($row->panel) {
                        'customer' => 'مشتریان',
                        'admin' => 'مدیریت',
                        default => $row->panel,
                    },
                    $row->installs_count,
                    $row->active_subscribers_count,
                    $row->page_views_count,
                    $row->notifications_sent_count,
                    $row->notifications_opened_count,
                    $row->open_rate_percent . '%',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
