<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FarazSmsGateway implements SmsGatewayInterface
{
    protected string $apiKey;
    protected string $sender;

    public function __construct()
    {
        $this->apiKey = config('services.farazsms.api_key', '');
        $this->sender = config('services.farazsms.sender', '3000505');
    }

    public function sendOtp(string $phone, string $code): bool
    {
        if (empty($this->apiKey)) {
            Log::warning('FarazSMS API key is empty, falling back to log.');
            Log::info("SMS OTP (FarazSMS Mock) -> Phone: {$phone} | Code: {$code}");
            return true;
        }

        try {
            $response = Http::post('https://ippanel.com/api/select', [
                'op' => 'pattern',
                'user' => config('services.farazsms.username'),
                'pass' => config('services.farazsms.password'),
                'fromNum' => $this->sender,
                'toNum' => $phone,
                'patternCode' => config('services.farazsms.pattern_code'),
                'inputData' => ['code' => $code],
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('FarazSMS Error: ' . $e->getMessage());
            return false;
        }
    }

    public function sendNotification(string $phone, string $message): bool
    {
        Log::info("SMS Notification (FarazSMS) -> Phone: {$phone} | Msg: {$message}");
        return true;
    }
}
