<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Log;

class LogSmsGateway implements SmsGatewayInterface
{
    public function sendOtp(string $phone, string $code): bool
    {
        Log::info("SMS OTP SENT -> Phone: {$phone} | Code: {$code}");
        return true;
    }

    public function sendNotification(string $phone, string $message): bool
    {
        Log::info("SMS NOTIFICATION SENT -> Phone: {$phone} | Message: {$message}");
        return true;
    }
}
