<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Payment\ZarinpalGatewayService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function zarinpalCallback(Request $request, ZarinpalGatewayService $zarinpalService)
    {
        $authority = $request->query('Authority');
        $status = $request->query('Status');

        if (!$authority) {
            return redirect()->route('storefront.menu')
                ->with('error', 'اطلاعات درگاه پرداخت ناقص است.');
        }

        // Secure binding: Lookup the payment record strictly by Authority
        $payment = \App\Models\Payment::where('authority_or_ref', $authority)->first();
        if (!$payment || !$payment->order) {
            return redirect()->route('storefront.menu')
                ->with('error', 'شناسه تراکنش نامعتبر است یا سفارش مربوطه یافت نشد.');
        }

        $order = $payment->order;

        // Idempotency defense: If order is already processed, do not repeat awarding points or incrementing spend
        if ($order->status !== 'pending') {
            return redirect()->route('storefront.order-status', ['order_number' => $order->order_number])
                ->with('info', 'این سفارش قبلاً تایید و وارد چرخه آماده‌سازی شده است.');
        }

        if ($status !== 'OK') {
            return redirect()->route('storefront.order-status', ['order_number' => $order->order_number])
                ->with('error', 'پرداخت از طرف کاربر لغو شد یا ناموفق بود.');
        }

        // Verify with Zarinpal API
        $verifyRes = $zarinpalService->verifyPayment($authority, $order->final_payable);

        if ($verifyRes['success']) {
            $order->update([
                'status' => 'preparing', // Preparing in kitchen / fulfillment
            ]);

            // If this order contains a subscription plan, activate it immediately
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

            // Award customer total spent, loyalty points & check tier upgrade
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
                        'امتیاز خرید سفارش آنلاین ' . $order->order_number
                    );
                }

                // Award 5% wallet cashback
                app(\App\Services\CashbackService::class)->awardOrderCashback($order);

                // Web Push notifications
                $pushService = app(\App\Services\Pwa\PushNotificationService::class);
                $pushService->send(
                    $order->customer,
                    'پرداخت موفق',
                    "پرداخت سفارش (#{$order->order_number}) با موفقیت تایید شد و سفارش به مرحله آماده‌سازی رفت.",
                    ['order_id' => $order->id, 'url' => route('storefront.order-status', $order->order_number)],
                    'order_status'
                );

                $pushService->sendToAdmins(
                    'پرداخت آنلاین جدید',
                    "سفارش (#{$order->order_number}) به مبلغ " . number_format($order->final_payable) . " تومان پرداخت شد.",
                    ['order_id' => $order->id, 'url' => route('admin.live-orders.index')],
                    'new_order'
                );
            }

            return redirect()->route('storefront.order-status', ['order_number' => $order->order_number])
                ->with('success', 'پرداخت با موفقیت انجام شد. کد پیگیری شاپرک: ' . $verifyRes['ref_id']);
        }

        return redirect()->route('storefront.order-status', ['order_number' => $order->order_number])
            ->with('error', 'تایید پرداخت ناموفق بود: ' . ($verifyRes['message'] ?? 'خطای ناشناخته'));
    }

    /**
     * Securely serve uploaded card-to-card receipt image to authorized staff.
     */
    public function showReceipt(\App\Models\Payment $payment)
    {
        abort_if(!auth('web')->check(), 403, 'دسترسی غیرمجاز.');

        $imagePath = $payment->receipt_image;
        if (!$imagePath) {
            abort(404, 'تصویر رسید یافت نشد.');
        }

        // Automatic 7-day retention safety check
        if ($payment->created_at && $payment->created_at->lt(now()->subDays(7))) {
            if (\Illuminate\Support\Facades\Storage::disk('local')->exists($imagePath)) {
                \Illuminate\Support\Facades\Storage::disk('local')->delete($imagePath);
            }
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($imagePath)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($imagePath);
            }
            $payment->update([
                'receipt_image' => null,
                'admin_notes' => trim(($payment->admin_notes ?? '') . ' [فیش واریزی طبق قانون انقضای ۷ روزه حذف شد]'),
            ]);
            abort(404, 'این رسید منقضی شده و طبق قوانین امنیتی پس از ۷ روز حذف گردیده است.');
        }

        if (\Illuminate\Support\Facades\Storage::disk('local')->exists($imagePath)) {
            return \Illuminate\Support\Facades\Storage::disk('local')->response($imagePath);
        }

        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($imagePath)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->response($imagePath);
        }

        abort(404, 'فایل رسید یافت نشد.');
    }
}
