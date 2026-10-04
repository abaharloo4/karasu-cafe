<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KavenegarSmsGateway implements SmsGatewayInterface
{
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.kavenegar.api_key', '');
    }

    public function sendOtp(string $phone, string $code): bool
    {
        if (empty($this->apiKey)) {
            Log::warning('Kavenegar API key is empty, falling back to log.');
            Log::info("SMS OTP (Kavenegar Mock) -> Phone: {$phone} | Code: {$code}");
            return true;
        }

        try {
            $response = Http::get("https://api.kavenegar.com/v1/{$this->apiKey}/verify/lookup.json", [
                'receptor' => $phone,
                'token' => $code,
                'template' => config('services.kavenegar.otp_template', 'verify'),
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Kavenegar SMS Error: ' . $e->getMessage());
            return false;
        }
    }

    public function sendNotification(string $phone, string $message): bool
    {
        Log::info("SMS Notification (Kavenegar) -> Phone: {$phone} | Msg: {$message}");
        return true;
    }
}
