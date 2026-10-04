<?php

namespace App\Jobs;

use App\Models\PushSubscription;
use App\Models\PwaEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class SendPushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 30;

    public function __construct(
        protected array $subscriptionIds,
        protected string $title,
        protected string $body,
        protected array $data = [],
        protected string $category = 'general'
    ) {}

    public function handle(): void
    {
        $publicKey = config('webpush.vapid.public_key');
        $privateKey = config('webpush.vapid.private_key');
        $subject = config('webpush.vapid.subject', 'mailto:admin@bcafe.ir');

        if (empty($publicKey) || empty($privateKey)) {
            Log::warning('Web Push skipped: VAPID keys not configured in .env');
            return;
        }

        // On Windows local development, ensure OpenSSL conf is registered
        if (DIRECTORY_SEPARATOR === '\\' && !getenv('OPENSSL_CONF')) {
            $conf = 'C:/wamp64/bin/php/php8.3.14/extras/ssl/openssl.cnf';
            if (file_exists($conf)) {
                putenv("OPENSSL_CONF={$conf}");
            }
        }

        $auth = [
            'VAPID' => [
                'subject' => $subject,
                'publicKey' => $publicKey,
                'privateKey' => $privateKey,
            ],
        ];

        try {
            $webPush = new WebPush($auth, ['timeout' => config('webpush.timeout', 20)]);
            $subscriptions = PushSubscription::whereIn('id', $this->subscriptionIds)->get();

            if ($subscriptions->isEmpty()) {
                return;
            }

            // Build context-aware action buttons based on category
            $actions = match($this->category) {
                'order_status', 'card_verification' => [
                    ['action' => 'open', 'title' => 'مشاهده سفارش'],
                ],
                'new_order', 'card_verification_pending' => [
                    ['action' => 'open', 'title' => 'مشاهده پنل مدیریت'],
                ],
                'order_ready' => [
                    ['action' => 'open', 'title' => 'مشاهده سفارش‌ها'],
                ],
                'rewards', 'loyalty' => [
                    ['action' => 'open', 'title' => 'مشاهده باشگاه مشتریان'],
                ],
                default => [
                    ['action' => 'open', 'title' => 'مشاهده جزئیات'],
                ],
            };

            $payload = json_encode([
                'title' => $this->title,
                'body' => $this->body,
                'icon' => $this->data['icon'] ?? '/icons/icon-192x192.png',
                'badge' => '/icons/badge-72x72.png',
                'vibrate' => [100, 50, 100],
                'dir' => 'rtl',
                'lang' => 'fa',
                'tag' => $this->data['tag'] ?? $this->category . '-' . now()->timestamp,
                'renotify' => true,
                'actions' => $actions,
                'data' => array_merge($this->data, [
                    'category' => $this->category,
                    'timestamp' => now()->timestamp,
                    'url' => $this->data['url'] ?? '/',
                ]),
            ], JSON_UNESCAPED_UNICODE);

            $mappedSubscriptions = [];
            foreach ($subscriptions as $sub) {
                try {
                    $webPushSubscription = Subscription::create([
                        'endpoint' => $sub->endpoint,
                        'publicKey' => $sub->p256dh,
                        'authToken' => $sub->auth,
                    ]);

                    $webPush->queueNotification($webPushSubscription, $payload);
                    $mappedSubscriptions[$sub->endpoint] = $sub;
                } catch (\Throwable $e) {
                    Log::warning("Failed to queue push for subscription #{$sub->id}: " . $e->getMessage());
                }
            }

            // Flush all queued notifications
            foreach ($webPush->flush() as $report) {
                $endpoint = $report->getRequest()->getUri()->__toString();
                $subModel = $mappedSubscriptions[$endpoint] ?? null;

                if ($report->isSuccess()) {
                    if ($subModel) {
                        // Log event
                        PwaEvent::create([
                            'panel' => $subModel->panel ?? 'customer',
                            'event_type' => 'notification_sent',
                            'subscribable_type' => $subModel->subscribable_type,
                            'subscribable_id' => $subModel->subscribable_id,
                            'path' => $this->data['url'] ?? '/',
                            'user_agent' => $subModel->user_agent,
                            'created_at' => now(),
                        ]);
                    }
                } else {
                    $reason = $report->getReason();
                    Log::info("Web push failed for endpoint: {$reason}");

                    // If subscription is expired (410 Gone or 404), prune it
                    if ($report->isSubscriptionExpired() && $subModel) {
                        Log::info("Pruning dead push subscription #{$subModel->id}");
                        PwaEvent::create([
                            'panel' => $subModel->panel ?? 'customer',
                            'event_type' => 'subscription_revoked',
                            'subscribable_type' => $subModel->subscribable_type,
                            'subscribable_id' => $subModel->subscribable_id,
                            'path' => null,
                            'user_agent' => $subModel->user_agent,
                            'created_at' => now(),
                        ]);
                        $subModel->delete();
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('SendPushNotificationJob exception: ' . $e->getMessage());
        }
    }
}
