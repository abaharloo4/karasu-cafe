<?php

namespace App\Services\Pwa;

use App\Jobs\SendPushNotificationJob;
use App\Models\Customer;
use App\Models\NotificationPreference;
use App\Models\PushSubscription;
use App\Models\PwaSetting;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    /**
     * Send a Web Push notification to a specific subscribable (Customer, User, Driver).
     */
    public function send(
        Model $subscribable,
        string $title,
        string $body,
        array $data = [],
        string $category = 'order_status'
    ): int {
        $panel = $this->determinePanelForModel($subscribable);

        // 1. Check global panel killswitch from pwa_settings
        $setting = PwaSetting::forPanel($panel);
        if (!$setting->isNotificationCategoryAllowed($category)) {
            Log::info("Push skipped: Category [{$category}] disabled globally for panel [{$panel}].");
            return 0;
        }

        // 2. Check subscriber preference
        if (!NotificationPreference::isEnabled($subscribable, $category)) {
            Log::info("Push skipped: Subscriber [{$subscribable->getMorphClass()} #{$subscribable->getKey()}] disabled [{$category}].");
            return 0;
        }

        // 3. Find active subscriptions for this subscriber
        $subscriptions = PushSubscription::where('subscribable_type', $subscribable->getMorphClass())
            ->where('subscribable_id', $subscribable->getKey())
            ->get();

        if ($subscriptions->isEmpty()) {
            return 0;
        }

        // 4. Dispatch push job (dispatchSync in local/sync mode, or queued in production)
        $subIds = $subscriptions->pluck('id')->all();
        if (app()->environment('local') || config('queue.default') === 'sync') {
            SendPushNotificationJob::dispatchSync($subIds, $title, $body, $data, $category);
        } else {
            SendPushNotificationJob::dispatch($subIds, $title, $body, $data, $category);
        }

        return count($subIds);
    }

    /**
     * Broadcast a push notification to all staff/admin users.
     */
    public function sendToAdmins(
        string $title,
        string $body,
        array $data = [],
        string $category = 'new_order'
    ): int {
        // Global panel killswitch
        $setting = PwaSetting::forPanel('admin');
        if (!$setting->isNotificationCategoryAllowed($category)) {
            return 0;
        }

        $adminUsers = User::whereIn('role', ['admin', 'staff'])->get();
        $totalSent = 0;

        foreach ($adminUsers as $admin) {
            if (NotificationPreference::isEnabled($admin, $category)) {
                $subIds = PushSubscription::where('subscribable_type', $admin->getMorphClass())
                    ->where('subscribable_id', $admin->getKey())
                    ->pluck('id')
                    ->all();

                if (!empty($subIds)) {
                    if (app()->environment('local') || config('queue.default') === 'sync') {
                        SendPushNotificationJob::dispatchSync($subIds, $title, $body, $data, $category);
                    } else {
                        SendPushNotificationJob::dispatch($subIds, $title, $body, $data, $category);
                    }
                    $totalSent += count($subIds);
                }
            }
        }

        return $totalSent;
    }

    /**
     * Send marketing/promotional push to all opted-in customers.
     */
    public function sendMarketingBroadcast(string $title, string $body, array $data = []): int
    {
        // 1. Check global killswitch
        $setting = PwaSetting::forPanel('customer');
        if (!$setting->isNotificationCategoryAllowed('marketing')) {
            Log::info('Marketing push skipped: globally disabled in pwa_settings.');
            return 0;
        }

        // 2. Fetch all customers who explicitly opted in
        $optedInCustomerIds = NotificationPreference::where('subscribable_type', (new Customer())->getMorphClass())
            ->where('category', 'marketing')
            ->where('enabled', true)
            ->pluck('subscribable_id')
            ->all();

        if (empty($optedInCustomerIds)) {
            return 0;
        }

        $subIds = PushSubscription::where('subscribable_type', (new Customer())->getMorphClass())
            ->whereIn('subscribable_id', $optedInCustomerIds)
            ->pluck('id')
            ->all();

        if (empty($subIds)) {
            return 0;
        }

        // Chunk to avoid massive arrays in single jobs
        foreach (array_chunk($subIds, 50) as $chunk) {
            SendPushNotificationJob::dispatch($chunk, $title, $body, $data, 'marketing');
        }

        return count($subIds);
    }

    /**
     * Helper to map model to panel name.
     */
    protected function determinePanelForModel(Model $model): string
    {
        if ($model instanceof Customer) {
            return 'customer';
        }
        return 'admin';
    }
}
