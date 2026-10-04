<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\SmsSetting;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionUsage;
use App\Services\Sms\MellipayamakSmsGateway;
use App\Services\Sms\SmsGatewayInterface;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    /**
     * Create and activate a subscription for a customer based on a plan.
     */
    public function createSubscription(int $cafeId, int $customerId, int $planId): Subscription
    {
        $plan = SubscriptionPlan::where('cafe_id', $cafeId)->findOrFail($planId);

        $startDate = Carbon::today();
        // duration_days: e.g. 100 days means starting today up to today + duration_days - 1
        $expireDate = Carbon::today()->addDays(max(0, $plan->duration_days - 1));

        $subscription = DB::transaction(function () use ($cafeId, $customerId, $plan, $startDate, $expireDate) {
            return Subscription::create([
                'cafe_id' => $cafeId,
                'customer_id' => $customerId,
                'subscription_plan_id' => $plan->id,
                'total_items' => $plan->total_items,
                'remaining_items' => $plan->total_items,
                'start_date' => $startDate->toDateString(),
                'expire_date' => $expireDate->toDateString(),
                'status' => 'active',
            ]);
        });

        // Trigger SMS notification
        $this->sendSubscriptionWelcomeSms($subscription);

        return $subscription;
    }

    /**
     * Alias for createSubscription for semantic clarity in cart/checkout.
     */
    public function assignPlanToCustomer(int $cafeId, int $customerId, int $planId): Subscription
    {
        return $this->createSubscription($cafeId, $customerId, $planId);
    }


    /**
     * Check if a customer has an active and valid subscription.
     * Automatically syncs and burns credits for missed days!
     */
    public function checkActiveSubscription(Customer $customer): ?Subscription
    {
        $subscription = $customer->subscriptions()
            ->where('status', 'active')
            ->where('expire_date', '>=', Carbon::today())
            ->where('remaining_items', '>', 0)
            ->with(['plan.products'])
            ->latest()
            ->first();

        if ($subscription) {
            $subscription->syncMissedDaysCredit();
            if ($subscription->remaining_items <= 0 || $subscription->status !== 'active') {
                return null;
            }
        }

        return $subscription;
    }

    /**
     * Check if a subscription has already been used today.
     */
    public function hasUsedToday(Subscription $subscription): bool
    {
        return SubscriptionUsage::where('subscription_id', $subscription->id)
            ->where('used_date', Carbon::today()->toDateString())
            ->exists();
    }

    /**
     * Get remaining credits for a subscription (synced for missed days).
     */
    public function getRemainingCredits(Subscription $subscription): int
    {
        return $subscription->syncMissedDaysCredit();
    }

    /**
     * Cancel an active subscription.
     */
    public function cancelSubscription(Subscription $subscription): bool
    {
        return $subscription->update(['status' => 'cancelled']);
    }

    /**
     * Core Business Rule: Consume daily free item credit.
     * Capped to max 1 item per day + missed days forfeit automatically.
     */
    public function consumeDailyItem(int $cafeId, int $subscriptionId, int $productId, int $staffUserId): array
    {
        $todayStr = Carbon::today()->toDateString();

        $subscription = Subscription::where('cafe_id', $cafeId)
            ->with(['plan.products'])
            ->findOrFail($subscriptionId);

        // Sync missed days first
        $subscription->syncMissedDaysCredit();

        // Validation 1: Subscription status
        if ($subscription->status !== 'active') {
            throw new Exception('اشتراک مورد نظر فعال نیست.');
        }

        // Validation 2: Expiration check
        if (Carbon::parse($subscription->expire_date)->endOfDay()->isPast()) {
            $subscription->update(['status' => 'expired']);
            throw new Exception('اعتبار زمان اشتراک به پایان رسیده است.');
        }

        // Validation 3: Remaining credits check (synced)
        if ($subscription->remaining_items <= 0) {
            throw new Exception('اعتبار (تعداد سهمیه) این اشتراک تمام شده است.');
        }

        // Validation 4: Has used today check (strictly 1 item per day)
        if ($this->hasUsedToday($subscription)) {
            throw new Exception('مشتری امروز قبلاً از سهمیه رایگان روزانه خود استفاده کرده است.');
        }

        // Validation 5: Allowed product check
        $allowedProductIds = $subscription->plan->products->pluck('id')->toArray();
        if (!in_array($productId, $allowedProductIds)) {
            throw new Exception('محصول انتخاب شده جزو لیست محصولات مجاز این پلن اشتراک نیست.');
        }

        try {
            $result = DB::transaction(function () use ($cafeId, $subscription, $productId, $staffUserId, $todayStr) {
                // 1. Record daily usage
                $usage = SubscriptionUsage::create([
                    'cafe_id' => $cafeId,
                    'customer_id' => $subscription->customer_id,
                    'subscription_id' => $subscription->id,
                    'product_id' => $productId,
                    'used_date' => $todayStr,
                    'created_by' => $staffUserId,
                ]);

                // 2. Decrement remaining items
                $subscription->decrement('remaining_items');

                // Auto-expire if credits reached zero
                if ($subscription->remaining_items <= 0) {
                    $subscription->update(['status' => 'expired']);
                }

                // 3. Create zero-price subscription order for unified reporting and live tracking
                $order = Order::create([
                    'cafe_id' => $cafeId,
                    'customer_id' => $subscription->customer_id,
                    'type' => 'subscription',
                    'order_type' => 'dine_in',
                    'fulfillment_type' => 'pickup',
                    'payment_method' => 'subscription_credit',
                    'status' => 'preparing',
                    'total_price' => 0.00,
                    'discount_amount' => 0.00,
                    'final_payable' => 0.00,
                    'created_by' => $staffUserId,
                ]);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => 1,
                    'price' => 0.00,
                ]);

                return [
                    'success' => true,
                    'message' => 'سهمیه روزانه با موفقیت ثبت گردید.',
                    'usage' => $usage,
                    'order' => $order,
                    'remaining_items' => $subscription->remaining_items,
                ];
            });

            // Trigger SMS notification
            $product = Product::find($productId);
            if ($product) {
                $this->sendDailyUsageSms($subscription, $product);
            }

            return $result;
        } catch (QueryException $e) {
            // Handle DB unique composite index constraint violation (subscription_id + used_date)
            if (str_contains($e->getMessage(), 'UNIQUE constraint failed') || str_contains($e->getMessage(), 'Duplicate entry') || $e->getCode() == 23000) {
                throw new Exception('مشتری امروز قبلاً از سهمیه رایگان روزانه استفاده کرده است.');
            }
            throw $e;
        }
    }

    /**
     * Send welcome SMS when a subscription is activated.
     */
    protected function sendSubscriptionWelcomeSms(Subscription $subscription): void
    {
        try {
            $setting = SmsSetting::where('cafe_id', $subscription->cafe_id)->first();
            if (!$setting || !$setting->is_welcome_enabled) {
                return;
            }

            $customer = $subscription->customer;
            $plan = $subscription->plan;

            if (!$customer || !$customer->phone || !$plan) {
                return;
            }

            $gateway = app(SmsGatewayInterface::class);
            $expireDateStr = \App\Helpers\JalaliHelper::format($subscription->expire_date, 'Y/m/d');

            if ($setting->pattern_welcome) {
                // Pattern Parameters: {0} = Name, {1} = Plan Name, {2} = Expire Date
                $gateway->sendPattern($customer->phone, $setting->pattern_welcome, [
                    $customer->name,
                    $plan->name,
                    $expireDateStr,
                ]);
            } else {
                $rawText = !empty($setting->template_text_welcome) ? $setting->template_text_welcome : SmsSetting::DEFAULT_WELCOME_TEXT;
                $msg = str_replace(
                    ['{name}', '{plan_name}', '{expire_date}'],
                    [$customer->name, $plan->name, $expireDateStr],
                    $rawText
                );
                $gateway->sendNotification($customer->phone, $msg);
            }
        } catch (\Throwable $e) {
            Log::error("sendSubscriptionWelcomeSms Exception: " . $e->getMessage());
        }
    }

    /**
     * Send confirmation SMS when daily free coffee is redeemed.
     */
    protected function sendDailyUsageSms(Subscription $subscription, Product $product): void
    {
        try {
            $setting = SmsSetting::where('cafe_id', $subscription->cafe_id)->first();
            if (!$setting || !$setting->is_daily_usage_enabled) {
                return;
            }

            $customer = $subscription->customer;
            if (!$customer || !$customer->phone) {
                return;
            }

            $gateway = app(SmsGatewayInterface::class);

            if ($setting->pattern_daily_usage) {
                // Pattern Parameters: {0} = Name, {1} = Product Name, {2} = Remaining Items
                $gateway->sendPattern($customer->phone, $setting->pattern_daily_usage, [
                    $customer->name,
                    $product->name,
                    (string) $subscription->remaining_items,
                ]);
            } else {
                $rawText = !empty($setting->template_text_daily_usage) ? $setting->template_text_daily_usage : SmsSetting::DEFAULT_DAILY_USAGE_TEXT;
                $msg = str_replace(
                    ['{name}', '{product_name}', '{remaining_items}'],
                    [$customer->name, $product->name, (string) $subscription->remaining_items],
                    $rawText
                );
                $gateway->sendNotification($customer->phone, $msg);
            }
        } catch (\Throwable $e) {
            Log::error("sendDailyUsageSms Exception: " . $e->getMessage());
        }
    }
}
