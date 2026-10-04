<?php

namespace App\Services;

use App\Models\CustomerCashback;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class CashbackService
{
    /**
     * Award percentage cashback on a completed or paid order.
     */
    public function awardOrderCashback(Order $order, ?float $rate = null): float
    {
        if (!$order->customer_id || $order->final_payable <= 0) {
            return 0;
        }

        $cafeId = $order->cafe_id ?? 1;
        $setting = \App\Models\LoyaltySetting::getForCafe($cafeId);

        // If cashback is disabled by admin in settings
        if ($rate === null && !$setting->is_active) {
            return 0;
        }

        // Check minimum order amount if set
        if ($rate === null && $setting->min_order_amount > 0 && $order->final_payable < $setting->min_order_amount) {
            return 0;
        }

        // Prevent duplicate cashback for the same order
        $alreadyAwarded = CustomerCashback::where('order_id', $order->id)
            ->where('type', 'credit')
            ->exists();

        if ($alreadyAwarded) {
            return 0;
        }

        $percent = $rate !== null ? ($rate * 100) : (float) ($setting->cashback_percent ?? 5.0);
        $effectiveRate = $percent / 100.0;
        $cashbackAmount = (float) round($order->final_payable * $effectiveRate);

        if ($cashbackAmount <= 0) {
            return 0;
        }

        $customer = $order->customer;
        if (!$customer) {
            return 0;
        }

        $customer->increment('wallet_balance', $cashbackAmount);

        CustomerCashback::create([
            'customer_id' => $customer->id,
            'order_id' => $order->id,
            'type' => 'credit',
            'amount' => $cashbackAmount,
            'description' => 'کش‌بک ' . rtrim(rtrim(number_format($percent, 1), '0'), '.') . '٪ خرید سفارش ' . ($order->order_number ?? '#' . $order->id),
        ]);

        Log::info('Cashback of ' . $cashbackAmount . ' Toman awarded to customer #' . $customer->id . ' for order #' . $order->id);

        return $cashbackAmount;
    }
}