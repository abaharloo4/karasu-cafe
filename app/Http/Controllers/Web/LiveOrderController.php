<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\Payment\CardToCardService;
use Illuminate\Http\Request;

class LiveOrderController extends Controller
{
    /**
     * Live kitchen & staff orders board.
     */
    public function index()
    {
        $cafeId = auth()->user()->cafe_id ?? 1;

        $orders = Order::where('cafe_id', $cafeId)
            ->whereIn('status', ['pending', 'awaiting_payment_verification', 'preparing', 'processing', 'ready', 'shipping', 'out_for_delivery', 'completed'])
            ->with(['items.product', 'customer.addresses.deliveryZone', 'deliveryAddress.deliveryZone', 'latestPayment'])
            ->latest()
            ->take(80)
            ->get();

        $initialOrders = $orders->map(fn($order) => $this->formatOrderForLiveBoard($order))->values();

        return view('orders.live', compact('initialOrders'));
    }

    /**
     * Lightweight JSON polling feed for live order updates.
     */
    public function pollFeed(Request $request)
    {
        $cafeId = auth()->user()->cafe_id ?? 1;

        $orders = Order::where('cafe_id', $cafeId)
            ->whereIn('status', ['pending', 'awaiting_payment_verification', 'preparing', 'processing', 'ready', 'shipping', 'out_for_delivery', 'completed'])
            ->with(['items.product', 'customer.addresses.deliveryZone', 'deliveryAddress.deliveryZone', 'latestPayment'])
            ->latest()
            ->take(60)
            ->get()
            ->map(fn($order) => $this->formatOrderForLiveBoard($order))
            ->values();

        $todayStart = \Illuminate\Support\Carbon::today()->startOfDay();
        $todayEnd = \Illuminate\Support\Carbon::today()->endOfDay();

        $stats = Order::where('cafe_id', $cafeId)
            ->selectRaw("
                SUM(CASE WHEN status IN ('pending', 'awaiting_payment_verification') THEN 1 ELSE 0 END) as pending_cnt,
                SUM(CASE WHEN status IN ('preparing', 'processing') THEN 1 ELSE 0 END) as preparing_cnt,
                SUM(CASE WHEN status IN ('ready', 'shipping', 'out_for_delivery') THEN 1 ELSE 0 END) as ready_cnt,
                SUM(CASE WHEN status = 'completed' AND created_at BETWEEN ? AND ? THEN 1 ELSE 0 END) as completed_cnt
            ", [$todayStart, $todayEnd])
            ->first();

        return response()->json([
            'orders' => $orders,
            'count' => $orders->count(),
            'pending_count' => (int) ($stats->pending_cnt ?? 0),
            'preparing_count' => (int) ($stats->preparing_cnt ?? 0),
            'ready_count' => (int) ($stats->ready_cnt ?? 0),
            'completed_count' => (int) ($stats->completed_cnt ?? 0),
        ]);
    }

    /**
     * Format an order for the live kitchen / bar display.
     */
    private function formatOrderForLiveBoard(Order $order): array
    {
        $paymentMethodTitle = match ($order->payment_method) {
            'card_to_card' => 'کارت به کارت',
            'zarinpal' => 'درگاه آنلاین',
            'cash', 'cod' => 'پرداخت نقدی/پوز',
            'subscription' => 'سهمیه اشتراک',
            'wallet' => 'کیف‌پول',
            'coupon' => 'کوپن تخفیف ۱۰۰٪',
            default => $order->payment_method ?? 'نامشخص',
        };

        $isDelivery = $order->isDelivery();
        $isTakeaway = $order->isTakeaway();
        $isDineIn = $order->isDineIn();

        // Address resolution: ONLY for delivery orders! For in-person/pickup, address MUST be null!
        $addr = $isDelivery ? ($order->deliveryAddress ?? $order->customer?->addresses?->first()) : null;

        $lat = $addr?->latitude ? (float) $addr->latitude : null;
        $lng = $addr?->longitude ? (float) $addr->longitude : null;
        $addressLine = $isDelivery ? ($addr?->address_line ?? ($order->shipping_address ?? null)) : null;
        $unit = $addr?->building_unit ?? ($addr?->unit ?? null);
        $zoneName = $addr?->deliveryZone?->name ?? null;

        return [
            'id' => $order->id,
            'order_number' => $order->order_number ?? ('#' . $order->id),
            'customer_name' => $order->customer?->name ?? ($order->customer_name ?? 'مشتری ناشناس'),
            'customer_phone' => $order->customer?->phone ?? ($order->customer_phone ?? ''),
            'order_type' => $order->order_type,
            'is_dine_in' => $isDineIn,
            'is_takeaway' => $isTakeaway,
            'is_delivery' => $isDelivery,
            'fulfillment_label' => $order->getFulfillmentLabel(),
            'table_number' => $order->table_number,
            'address' => $addressLine,
            'delivery_zone' => $zoneName,
            'unit' => $unit,
            'building_number' => $addr?->building_number ?? null,
            'latitude' => $lat,
            'longitude' => $lng,
            'neshan_url' => ($lat && $lng) ? "https://neshan.org/maps/@{$lat},{$lng},16z" : null,
            'google_maps_url' => ($lat && $lng) ? "https://www.google.com/maps?q={$lat},{$lng}" : null,
            'balad_url' => ($lat && $lng) ? "https://balad.ir/location?latitude={$lat}&longitude={$lng}" : null,
            'order_notes' => $order->notes ?? $addr?->notes ?? null,
            'status' => $order->status,
            'payment_method' => $order->payment_method,
            'payment_method_label' => $paymentMethodTitle,
            'payment_status' => $order->payment_status,
            'final_payable' => (float) $order->final_payable,
            'final_payable_formatted' => number_format($order->final_payable),
            'created_at_time' => $order->created_at ? $order->created_at->format('H:i') : '',
            'items' => $order->items->map(fn($i) => [
                'name' => $i->product->name ?? 'محصول',
                'quantity' => $i->quantity,
                'price' => number_format($i->unit_price),
                'notes' => $i->notes,
            ]),
            'receipt_image' => $order->latestPayment?->receipt_image ? (route('admin.payments.receipt', $order->latestPayment) . '/' . $order->latestPayment->receipt_image) : null,
            'payment_id' => $order->latestPayment?->id,
            'card_pan' => $order->latestPayment?->card_pan,
            'tracking_number' => $order->latestPayment?->tracking_number,
        ];
    }

    /**
     * Update order status from live board.
     */
    public function updateStatus(Order $order, Request $request)
    {
        $cafeId = auth()->user()->cafe_id ?? 1;
        abort_if($order->cafe_id !== $cafeId, 403, 'دسترسی غیرمجاز به سفارش این کافه.');

        $action = $request->input('action');

        $pushService = app(\App\Services\Pwa\PushNotificationService::class);
        $orderUrl = route('storefront.order-status', $order->order_number);

        switch ($action) {
            case 'start_prep':
                $order->update(['status' => 'preparing']);
                $msg = "سفارش {$order->order_number} در وضعیت «در حال آماده‌سازی» قرار گرفت.";

                try {
                    app(\App\Services\Sms\OrderSmsNotifier::class)->notify($order, \App\Models\SmsSetting::ORDER_STATUS_PREPARING);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Order preparing SMS failed: ' . $e->getMessage());
                }

                if ($order->customer) {
                    $pushService->send(
                        $order->customer,
                        'سفارش در حال آماده‌سازی است',
                        "باریستای بی کافه در حال آماده‌سازی سفارش شما (#{$order->order_number}) است.",
                        ['order_id' => $order->id, 'url' => $orderUrl],
                        'order_status'
                    );
                }
                break;

            case 'ready':
                $order->update(['status' => 'ready']);

                try {
                    app(\App\Services\Sms\OrderSmsNotifier::class)->notify($order, \App\Models\SmsSetting::ORDER_STATUS_READY);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Order ready SMS failed: ' . $e->getMessage());
                }

                if ($order->isDelivery()) {
                    if ($order->customer) {
                        $pushService->send(
                            $order->customer,
                            'سفارش آماده ارسال است',
                            "سفارش شما (#{$order->order_number}) آماده شد و جهت ارسال آماده‌سازی گردید.",
                            ['order_id' => $order->id, 'url' => $orderUrl],
                            'order_status'
                        );
                    }

                    $msg = "سفارش {$order->order_number} آماده ارسال شد.";
                } else {
                    if ($order->customer) {
                        $pushService->send(
                            $order->customer,
                            'سفارش شما آماده است!',
                            "سفارش شما (#{$order->order_number}) آماده تحویل است. نوش جان!",
                            ['order_id' => $order->id, 'url' => $orderUrl],
                            'order_status'
                        );
                    }
                    $msg = "سفارش {$order->order_number} آماده تحویل به مشتری است.";
                }
                break;

            case 'complete':
                $order->update(['status' => 'completed']);
                // Award cashback if customer exists and hasn't received it yet
                app(\App\Services\CashbackService::class)->awardOrderCashback($order);

                try {
                    app(\App\Services\Sms\OrderSmsNotifier::class)->notify($order, \App\Models\SmsSetting::ORDER_STATUS_DELIVERED);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Order delivered SMS failed: ' . $e->getMessage());
                }

                if ($order->customer) {
                    $pushService->send(
                        $order->customer,
                        'سفارش تحویل شد',
                        "از همراهی شما سپاسگزاریم! سفارش (#{$order->order_number}) با موفقیت تحویل داده شد.",
                        ['order_id' => $order->id, 'url' => $orderUrl],
                        'order_status'
                    );
                }
                $msg = "سفارش {$order->order_number} تکمیل و تحویل داده شد.";
                break;

            case 'cancel':
                $order->update(['status' => 'cancelled']);

                try {
                    app(\App\Services\Sms\OrderSmsNotifier::class)->notify($order, \App\Models\SmsSetting::ORDER_STATUS_CANCELLED);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Order cancelled SMS failed: ' . $e->getMessage());
                }

                if ($order->customer) {
                    $pushService->send(
                        $order->customer,
                        'سفارش لغو شد',
                        "متاسفانه سفارش شما (#{$order->order_number}) لغو گردید.",
                        ['order_id' => $order->id, 'url' => $orderUrl],
                        'order_status'
                    );
                }
                $msg = "سفارش {$order->order_number} لغو گردید.";
                break;

            default:
                return back()->with('error', 'عملیات نامعتبر است.');
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg, 'status' => $order->status]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Approve card-to-card receipt directly from kitchen board.
     */
    public function verifyCardPayment(Payment $payment, Request $request, CardToCardService $cardToCardService)
    {
        $cafeId = auth()->user()->cafe_id ?? 1;
        if ($payment->order && $payment->order->cafe_id !== $cafeId) {
            abort(403, 'دسترسی غیرمجاز به پرداخت این کافه.');
        }

        $pushService = app(\App\Services\Pwa\PushNotificationService::class);
        $action = $request->input('action');
        $rejectAction = $request->input('reject_action', 'cancel_order');
        $reason = $request->input('reason', 'فیش نامعتبر یا مبلغ واریز نشده است.');

        if ($action === 'approve') {
            $cardToCardService->approveReceipt($payment, 'تایید شده توسط باریستا');
            $msg = 'فیش واریزی با موفقیت تایید شد و سفارش به مرحله آماده‌سازی منتقل گردید.';

            if ($payment->order && $payment->order->customer) {
                $pushService->send(
                    $payment->order->customer,
                    'پرداخت تایید شد',
                    "فیش کارت‌به‌کارت سفارش (#{$payment->order->order_number}) تایید شد و سفارش به چرخه آماده‌سازی رفت.",
                    ['order_id' => $payment->order->id, 'url' => route('storefront.order-status', $payment->order->order_number)],
                    'order_status'
                );
            }
        } else {
            $cardToCardService->rejectReceipt($payment, $reason, $rejectAction);

            if ($rejectAction === 'request_reupload') {
                $msg = 'درخواست بارگذاری مجدد فیش برای مشتری ارسال شد و سفارش فعال باقی ماند.';

                if ($payment->order && $payment->order->customer) {
                    $pushService->send(
                        $payment->order->customer,
                        'درخواست ارسال مجدد فیش بانکی',
                        "فیش سفارش (#{$payment->order->order_number}) تایید نشد: {$reason}. لطفاً فیش صحیح را مجدداً بارگذاری فرمایید.",
                        ['order_id' => $payment->order->id, 'url' => route('storefront.order-status', $payment->order->order_number)],
                        'order_status'
                    );
                }

                if ($payment->order) {
                    try {
                        app(\App\Services\Sms\OrderSmsNotifier::class)->notifyCustom(
                            $payment->order,
                            "بی کافه: فیش واریزی سفارش {$payment->order->order_number} تایید نشد ({$reason}). لطفاً جهت بارگذاری مجدد وارد لینک شوید: " . route('storefront.order-status', $payment->order->order_number)
                        );
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::warning('Re-upload SMS failed: ' . $e->getMessage());
                    }
                }
            } else {
                $msg = 'فیش واریزی رد و سفارش به طور قطعی لغو گردید.';

                if ($payment->order && $payment->order->customer) {
                    $pushService->send(
                        $payment->order->customer,
                        'سفارش لغو شد',
                        "فیش کارت‌به‌کارت سفارش (#{$payment->order->order_number}) تایید نشد و سفارش لغو گردید: {$reason}",
                        ['order_id' => $payment->order->id, 'url' => route('storefront.order-status', $payment->order->order_number)],
                        'order_status'
                    );
                }

                if ($payment->order) {
                    try {
                        app(\App\Services\Sms\OrderSmsNotifier::class)->notify(
                            $payment->order,
                            \App\Models\SmsSetting::ORDER_STATUS_CANCELLED
                        );
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::warning('Cancel SMS failed: ' . $e->getMessage());
                    }
                }
            }
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return back()->with('success', $msg);
    }
}
