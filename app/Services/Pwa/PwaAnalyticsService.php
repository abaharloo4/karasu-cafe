<?php

namespace App\Services\Pwa;

use App\Models\PushSubscription;
use App\Models\PwaDailyStat;
use App\Models\PwaEvent;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PwaAnalyticsService
{
    /**
     * Log a lightweight PWA event.
     */
    public function logEvent(
        string $panel,
        string $eventType,
        ?string $path = null,
        ?string $subscribableType = null,
        ?int $subscribableId = null,
        ?string $userAgent = null
    ): PwaEvent {
        return PwaEvent::create([
            'panel' => $panel,
            'event_type' => $eventType,
            'subscribable_type' => $subscribableType,
            'subscribable_id' => $subscribableId,
            'path' => $path ? substr($path, 0, 255) : null,
            'user_agent' => $userAgent ? substr($userAgent, 0, 255) : null,
            'created_at' => now(),
        ]);
    }

    /**
     * Aggregate daily stats for a specific date (default: yesterday).
     */
    public function aggregateDaily(Carbon $targetDate): void
    {
        $startDate = $targetDate->copy()->startOfDay();
        $endDate = $targetDate->copy()->endOfDay();
        $dateString = $targetDate->toDateString();

        $panels = ['customer', 'admin'];

        foreach ($panels as $panel) {
            $events = PwaEvent::where('panel', $panel)
                ->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate);

            $installsCount = (clone $events)->where('event_type', 'install')->count();
            $pageViewsCount = (clone $events)->where('event_type', 'page_view')->count();
            $sentCount = (clone $events)->where('event_type', 'notification_sent')->count();
            $openedCount = (clone $events)->where('event_type', 'notification_opened')->count();

            // Unique active subscribers who generated an event that day
            $activeSubscribersCount = (clone $events)
                ->whereNotNull('subscribable_id')
                ->distinct()
                ->count('subscribable_id');

            // Page by page breakdown (top 15 visited paths)
            $pageBreakdown = (clone $events)
                ->where('event_type', 'page_view')
                ->whereNotNull('path')
                ->select('path', DB::raw('count(*) as views'))
                ->groupBy('path')
                ->orderByDesc('views')
                ->limit(15)
                ->pluck('views', 'path')
                ->toArray();

            PwaDailyStat::updateOrCreate(
                [
                    'panel' => $panel,
                    'date' => $dateString,
                ],
                [
                    'installs_count' => $installsCount,
                    'active_subscribers_count' => $activeSubscribersCount,
                    'page_views_count' => $pageViewsCount,
                    'notifications_sent_count' => $sentCount,
                    'notifications_opened_count' => $openedCount,
                    'page_breakdown' => $pageBreakdown,
                ]
            );
        }
    }

    /**
     * Compute Install-to-Active Conversion Rate over a period.
     */
    public function computeConversionRate(string $panel, int $days = 30): float
    {
        $since = Carbon::now()->subDays($days);

        // Find users who installed in this period
        $installEvents = PwaEvent::where('panel', $panel)
            ->where('event_type', 'install')
            ->whereNotNull('subscribable_id')
            ->where('created_at', '>=', $since)
            ->get();

        $totalInstallers = $installEvents->pluck('subscribable_id')->unique()->count();
        if ($totalInstallers === 0) {
            return 0.0;
        }

        // Among those who installed, find those who had subsequent page views or actions >= 1 day later
        $activeCount = 0;
        foreach ($installEvents->unique('subscribable_id') as $install) {
            $subId = $install->subscribable_id;
            $subType = $install->subscribable_type;
            $installTime = $install->created_at;

            $hasLaterActivity = PwaEvent::where('panel', $panel)
                ->where('subscribable_type', $subType)
                ->where('subscribable_id', $subId)
                ->where('created_at', '>', $installTime->copy()->addDay())
                ->exists();

            if ($hasLaterActivity) {
                $activeCount++;
            }
        }

        return round(($activeCount / $totalInstallers) * 100, 1);
    }
}
