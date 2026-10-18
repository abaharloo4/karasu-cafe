<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CardToCardService
{
    /**
     * Submit a card-to-card receipt for an order.
     */
    public function submitReceipt(
        Order $order,
        ?string $trackingNumber,
        ?UploadedFile $receiptFile,
        ?string $cardPan = null
    ): Payment {
        $receiptPath = null;
        if ($receiptFile && $receiptFile->isValid()) {
            $receiptPath = $receiptFile->store('receipts', 'local');
        }

        $payment = Payment::create([
            'order_id' => $order->id,
            'gateway' => 'card_to_card',
            'transaction_id' => $trackingNumber,
            'tracking_number' => $trackingNumber,
            'card_pan_mask' => $cardPan,
            'receipt_image' => $receiptPath,
            'amount' => $order->final_payable,
            'status' => 'pending',
        ]);

        $order->update([
            'status' => 'awaiting_payment_verification', // Awaiting manual admin/barista verification
            'payment_method' => 'card_to_card',
        ]);

        return $payment;
    }

    /**
     * Approve card-to-card receipt by admin/staff.
     */
    public function approveReceipt(Payment $payment, ?string $adminNotes = null): bool
    {
        $payment->update([
            'status' => 'successful',
            'admin_notes' => $adminNotes,
            'verified_at' => now(),
        ]);

        $order = $payment->order;
        $order->update([
            'status' => 'preparing', // Preparing order in kitchen
        ]);

        // Activate any subscription plans in the order
        foreach ($order->items as $orderItem) {
            $planId = $orderItem->subscription_plan_id ?? ($order->type === 'subscription' ? $orderItem->product_id : null);
            if ($planId && $order->customer_id) {
                $plan = \App\Models\SubscriptionPlan::find($planId);
                if ($plan) {
                    app(\App\Services\SubscriptionService::class)->assignPlanToCustomer(
                        $order->cafe_id,
                        $order->customer_id,
                        $plan->id
                    );
                }
            }
        }

        if ($order->customer) {
            $order->customer->increment('total_spent', $order->final_payable);
            app(\App\Services\LoyaltyTierService::class)->evaluateTier($order->customer);

            $points = (int) floor($order->final_payable / 10000);
            if ($points > 0) {
                app(\App\Services\LoyaltyService::class)->addPoints(
                    $order->cafe_id,
                    $order->customer_id,
                    $points,
                    'earned',
                    'امتیاز خرید سفارش کارت به کارت ' . ($order->order_number ?? '#' . $order->id)
                );
            }

            // Award 5% wallet cashback
            app(\App\Services\CashbackService::class)->awardOrderCashback($order);
        }

        return true;
    }

    /**
     * Reject card-to-card receipt.
     */
    public function rejectReceipt(Payment $payment, string $reason, string $action = 'cancel_order'): bool
    {
        $payment->update([
            'status' => 'failed',
            'admin_notes' => $reason,
        ]);

        if ($action === 'request_reupload') {
            $payment->order->update([
                'status' => 'awaiting_payment_verification',
            ]);
        } else {
            $payment->order->update([
                'status' => 'cancelled',
            ]);
        }

        return true;
    }
}
