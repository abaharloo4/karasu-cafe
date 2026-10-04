<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZarinpalGatewayService
{
    protected array $settings;
    protected string $merchantId;
    protected bool $isSandbox;
    protected string $currency; // IRT or IRR

    public function __construct()
    {
        $this->settings = PaymentGateway::getSettings('zarinpal');
        $this->merchantId = $this->settings['merchant_id'] ?? '00000000-0000-0000-0000-000000000000';
        $this->isSandbox = (bool) ($this->settings['sandbox'] ?? true);
        $this->currency = $this->settings['currency'] ?? 'IRT';
    }

    protected function getBaseUrl(): string
    {
        return $this->isSandbox
            ? 'https://sandbox.zarinpal.com/pg/v4/payment'
            : 'https://api.zarinpal.com/pg/v4/payment';
    }

    protected function getGatewayRedirectUrl(string $authority): string
    {
        $base = $this->isSandbox
            ? 'https://sandbox.zarinpal.com/pg/StartPay/'
            : 'https://www.zarinpal.com/pg/StartPay/';

        return $base . $authority;
    }

    /**
     * Request a payment authority from Zarinpal.
     */
    public function requestPayment(Order $order, string $callbackUrl): array
    {
        $amount = (int) $order->final_payable;
        // In Toman (IRT) or Rial (IRR)
        $description = $this->settings['description'] ?? ('سفارش شماره ' . ($order->order_number ?? $order->id) . ' کافه بی');

        $payload = [
            'merchant_id' => $this->merchantId,
            'amount' => $amount,
            'currency' => $this->currency,
            'description' => $description,
            'callback_url' => $callbackUrl,
            'metadata' => [
                'mobile' => $order->customer?->phone ?? '',
                'order_id' => (string) $order->id,
            ],
        ];

        try {
            $response = Http::timeout(10)->post($this->getBaseUrl() . '/request.json', $payload);
            $result = $response->json();

            if (isset($result['data']['code']) && $result['data']['code'] == 100) {
                $authority = $result['data']['authority'];

                Payment::create([
                    'order_id' => $order->id,
                    'gateway' => 'zarinpal',
                    'authority_or_ref' => $authority,
                    'amount' => $amount,
                    'status' => 'pending',
                ]);

                return [
                    'success' => true,
                    'authority' => $authority,
                    'payment_url' => $this->getGatewayRedirectUrl($authority),
                ];
            }

            // In local/testing development, provide mock simulator to prevent test blockage
            if (app()->environment('local', 'testing') || $this->isSandbox) {
                $authority = 'SIM-' . strtoupper(\Illuminate\Support\Str::random(12));
                Payment::create([
                    'order_id' => $order->id,
                    'gateway' => 'zarinpal',
                    'authority_or_ref' => $authority,
                    'amount' => $amount,
                    'status' => 'pending',
                ]);

                return [
                    'success' => true,
                    'authority' => $authority,
                    'payment_url' => $callbackUrl . (str_contains($callbackUrl, '?') ? '&' : '?') . 'Authority=' . $authority . '&Status=OK',
                ];
            }

            $errorMessage = $result['errors']['message'] ?? 'خطا در اتصال به درگاه زرین‌پال';
            Log::error('Zarinpal request failed', ['payload' => $payload, 'response' => $result]);

            return [
                'success' => false,
                'message' => $errorMessage,
            ];
        } catch (\Throwable $e) {
            // Local dev fallback if network fails
            if (app()->environment('local', 'testing') || $this->isSandbox) {
                $authority = 'SIM-' . strtoupper(\Illuminate\Support\Str::random(12));
                Payment::create([
                    'order_id' => $order->id,
                    'gateway' => 'zarinpal',
                    'authority_or_ref' => $authority,
                    'amount' => $amount,
                    'status' => 'pending',
                ]);

                return [
                    'success' => true,
                    'authority' => $authority,
                    'payment_url' => $callbackUrl . (str_contains($callbackUrl, '?') ? '&' : '?') . 'Authority=' . $authority . '&Status=OK',
                ];
            }

            Log::error('Zarinpal request exception: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'ارتباط با درگاه پرداخت برقرار نشد: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Verify payment after returning from gateway.
     */
    public function verifyPayment(string $authority, float $amount): array
    {
        // Simulated authority for local testing (Strictly restricted to non-production sandbox)
        if (str_starts_with($authority, 'SIM-')) {
            if (app()->environment('production') || !$this->isSandbox) {
                Log::warning('Unauthorized attempt to use simulated payment authority in production/live mode', [
                    'authority' => $authority,
                    'amount' => $amount,
                ]);
                return [
                    'success' => false,
                    'message' => 'تراکنش نامعتبر است.',
                ];
            }

            $payment = Payment::where('authority_or_ref', $authority)->first();
            if (!$payment) {
                return [
                    'success' => false,
                    'message' => 'رکورد پرداخت متناظر با این شناسه یافت نشد.',
                ];
            }

            $refId = 'SHP-' . rand(10000000, 99999999);
            $payment->update([
                'transaction_id' => $refId,
                'card_pan_mask' => '۶۰۳۷۹۹******۱۲۳۴',
                'status' => 'successful',
                'verified_at' => now(),
            ]);

            return [
                'success' => true,
                'ref_id' => $refId,
                'card_pan' => '۶۰۳۷۹۹******۱۲۳۴',
                'code' => 100,
            ];
        }

        $payload = [
            'merchant_id' => $this->merchantId,
            'amount' => (int) $amount,
            'authority' => $authority,
        ];

        try {
            $response = Http::timeout(10)->post($this->getBaseUrl() . '/verify.json', $payload);
            $result = $response->json();

            if (isset($result['data']['code']) && in_array($result['data']['code'], [100, 101])) {
                $refId = (string) ($result['data']['ref_id'] ?? '');
                $cardPan = $result['data']['card_pan'] ?? null;

                $payment = Payment::where('authority_or_ref', $authority)->first();
                if ($payment) {
                    $payment->update([
                        'transaction_id' => $refId,
                        'card_pan_mask' => $cardPan,
                        'status' => 'successful',
                        'verified_at' => now(),
                    ]);
                }

                return [
                    'success' => true,
                    'ref_id' => $refId,
                    'card_pan' => $cardPan,
                    'code' => $result['data']['code'],
                ];
            }

            $errorMessage = $result['errors']['message'] ?? 'تراکنش توسط درگاه تایید نشد.';

            $payment = Payment::where('authority_or_ref', $authority)->first();
            if ($payment) {
                $payment->update(['status' => 'failed']);
            }

            return [
                'success' => false,
                'message' => $errorMessage,
            ];
        } catch (\Throwable $e) {
            Log::error('Zarinpal verify exception: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'خطا در تایید تراکنش: ' . $e->getMessage(),
            ];
        }
    }
}
