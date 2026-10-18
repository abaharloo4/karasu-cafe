<?php

namespace App\Services\Sms;

use App\Models\Order;
use App\Models\SmsSetting;
use Illuminate\Support\Facades\Log;

class OrderSmsNotifier
{
    public function __construct(
        protected SmsGatewayInterface $smsGateway
    ) {}

    /**
     * Send order status notification SMS to customer.
     */
    public function notify(Order $order, string $status): void
    {
        try {
            $cafeId = $order->cafe_id ?? 1;
            $setting = SmsSetting::where('cafe_id', $cafeId)->first();

            if (!$setting || !$setting->is_order_sms_enabled) {
                return;
            }

            // Determine recipient phone
            $phone = $order->customer?->phone ?? null;
            if (!$phone) {
                return;
            }

            $customerName = $order->customer?->name ?? 'مشتری گرامی';
            $orderNumber = (string) ($order->order_number ?? $order->id);

            // Map status to setting keys
            $statusMap = [
                'pending' => 'order_submitted',
                'submitted' => 'order_submitted',
                'preparing' => 'order_preparing',
                'processing' => 'order_preparing',
                'ready' => 'order_ready',
                'on_the_way' => 'order_on_the_way',
                'delivering' => 'order_on_the_way',
                'delivered' => 'order_delivered',
                'completed' => 'order_delivered',
                'cancelled' => 'order_cancelled',
            ];

            $mappedKey = $statusMap[$status] ?? null;
            if (!$mappedKey) {
                return;
            }

            $patternField = "pattern_{$mappedKey}";
            $textField = "template_text_{$mappedKey}";
            $defaultConstant = 'DEFAULT_' . strtoupper($mappedKey) . '_TEXT';

            $patternCode = $setting->$patternField;
            $templateText = $setting->$textField ?: (defined(SmsSetting::class . '::' . $defaultConstant) ? constant(SmsSetting::class . '::' . $defaultConstant) : null);

            if (!empty($patternCode)) {
                \App\Jobs\SendSmsNotificationJob::dispatch('pattern', $phone, $patternCode, [
                    'name' => $customerName,
                    'order_number' => $orderNumber,
                ]);
            } elseif (!empty($templateText)) {
                $message = str_replace(
                    ['{name}', '{order_number}'],
                    [$customerName, $orderNumber],
                    $templateText
                );
                \App\Jobs\SendSmsNotificationJob::dispatch('notification', $phone, $message);
            }
        } catch (\Throwable $e) {
            Log::warning("Order SMS notification failed for order {$order->id}: " . $e->getMessage());
        }
    }

    /**
     * Send a custom text notification to the customer.
     */
    public function notifyCustom(Order $order, string $message): void
    {
        try {
            $phone = $order->customer?->phone ?? null;
            if (!$phone) {
                return;
            }
            \App\Jobs\SendSmsNotificationJob::dispatch('notification', $phone, $message);
        } catch (\Throwable $e) {
            Log::warning("Custom order SMS notification failed for order {$order->id}: " . $e->getMessage());
        }
    }
}

