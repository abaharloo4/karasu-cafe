<?php

namespace App\Console\Commands;

use App\Models\Cafe;
use App\Models\SmsSetting;
use App\Models\Subscription;
use App\Services\Sms\MellipayamakSmsGateway;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class SendDailySmsReminders extends Command
{
    protected $signature = 'sms:send-daily-reminders {--force : Force sending regardless of current hour check}';

    protected $description = 'Send daily SMS reminder to subscription customers who have not redeemed their free daily item yet.';

    public function handle(): int
    {
        $currentHour = (int) Carbon::now()->format('H');
        $this->info("Checking daily SMS reminders at hour {$currentHour}:00...");

        $cafes = Cafe::all();

        foreach ($cafes as $cafe) {
            $setting = SmsSetting::where('cafe_id', $cafe->id)->first();

            if (!$setting || !$setting->is_reminder_enabled) {
                $this->info("SMS reminder disabled or not configured for cafe ID {$cafe->id}. Skipping.");
                continue;
            }

            // Check if current hour matches configured reminder hour (unless --force is passed)
            if (!$this->option('force') && $currentHour !== $setting->reminder_hour) {
                $this->info("Current hour ({$currentHour}) does not match configured reminder hour ({$setting->reminder_hour}) for cafe ID {$cafe->id}. Skipping.");
                continue;
            }

            $gateway = new MellipayamakSmsGateway($cafe->id);

            // Find all customers with active non-expired subscriptions
            $activeSubscriptions = Subscription::where('cafe_id', $cafe->id)
                ->where('status', 'active')
                ->where('expire_date', '>=', Carbon::today()->toDateString())
                ->where('remaining_items', '>', 0)
                ->with(['customer', 'plan'])
                ->get();

            $sentCount = 0;

            foreach ($activeSubscriptions as $sub) {
                $sub->syncMissedDaysCredit();

                if ($sub->remaining_items <= 0 || $sub->status !== 'active') {
                    continue;
                }

                // Skip if customer ALREADY redeemed their free item today!
                if ($sub->hasUsedToday()) {
                    continue;
                }

                $customer = $sub->customer;
                if (!$customer || !$customer->phone) {
                    continue;
                }

                // Send Pattern SMS if pattern code is set, otherwise standard text template
                if ($setting->pattern_reminder) {
                    // Pattern Parameters: {0} = Customer Name, {1} = Plan Name
                    $gateway->sendPattern($customer->phone, $setting->pattern_reminder, [
                        $customer->name,
                        $sub->plan->name ?? 'اشتراک کافه',
                    ]);
                } else {
                    $rawText = $setting->template_text_reminder ?: 'سلام {name} عزیز، امروز هنوز سهمیه رایگان قهوه اشتراک «{plan_name}» خود را در بی کافه دریافت نکرده‌اید! منتظر دیدارتان هستیم.';
                    $msg = str_replace(
                        ['{name}', '{plan_name}'],
                        [$customer->name, $sub->plan->name ?? 'اشتراک کافه'],
                        $rawText
                    );
                    $gateway->sendNotification($customer->phone, $msg);
                }

                $sentCount++;
            }

            $this->info("Successfully sent {$sentCount} reminder SMS messages for cafe ID {$cafe->id}.");
            Log::info("SendDailySmsReminders: Sent {$sentCount} reminder SMS messages for cafe ID {$cafe->id}.");
        }

        return 0;
    }
}
