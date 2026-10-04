<?php

namespace App\Services\Sms;

interface SmsGatewayInterface
{
    /**
     * Send OTP verification code to a phone number.
     */
    public function sendOtp(string $phone, string $code): bool;

    /**
     * Send a general notification SMS.
     */
    public function sendNotification(string $phone, string $message): bool;
}
