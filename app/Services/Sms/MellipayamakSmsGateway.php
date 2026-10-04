<?php

namespace App\Services\Sms;

use App\Models\SmsSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MellipayamakSmsGateway implements SmsGatewayInterface
{
    protected ?string $username = null;
    protected ?string $password = null;
    protected ?string $fromNumber = null;

    public function __construct(?int $cafeId = null)
    {
        $cafeId = $cafeId ?? (auth()->check() ? auth()->user()->cafe_id : 1);
        $setting = SmsSetting::where('cafe_id', $cafeId)->first();

        if ($setting) {
            $this->username = $setting->username;
            $this->password = $setting->password;
            $this->fromNumber = $setting->from_number;
        } else {
            $this->username = env('MELLIPAYAMAK_USERNAME');
            $this->password = env('MELLIPAYAMAK_PASSWORD');
            $this->fromNumber = env('MELLIPAYAMAK_FROM');
        }
    }

    /**
     * Send OTP code via pattern or standard SMS.
     */
    public function sendOtp(string $phone, string $code): bool
    {
        $cafeId = auth()->check() ? auth()->user()->cafe_id : 1;
        $setting = SmsSetting::where('cafe_id', $cafeId)->first();

        if ($setting && $setting->pattern_otp) {
            return $this->sendPattern($phone, $setting->pattern_otp, [$code]);
        }

        return $this->sendNotification($phone, "کد تایید ورود شما به بی کافه: {$code}");
    }

    /**
     * Send a pattern-based SMS via Melli Payamak BaseService API.
     * https://rest.payamak-panel.com/api/SendSMS/BaseServiceNumber
     */
    public function sendPattern(string $phone, string $bodyId, array $args = []): bool
    {
        if (empty($this->username) || empty($this->password)) {
            Log::info("MelliPayamak [Pattern Sim]: Sending BodyID {$bodyId} to {$phone} with args: " . implode(';', $args));
            return true;
        }

        try {
            $textValue = implode(';', $args);

            $response = Http::asForm()
                ->timeout(5)
                ->connectTimeout(3)
                ->post('https://rest.payamak-panel.com/api/SendSMS/BaseServiceNumber', [
                'username' => $this->username,
                'password' => $this->password,
                'text' => $textValue,
                'to' => $phone,
                'bodyId' => (int) $bodyId,
            ]);

            if ($response->successful()) {
                Log::info("MelliPayamak Pattern Success to {$phone}, BodyID: {$bodyId}, Response: " . $response->body());
                return true;
            }

            Log::error("MelliPayamak Pattern Failed to {$phone}, Status: " . $response->status() . ", Body: " . $response->body());
            return false;
        } catch (\Throwable $e) {
            Log::error("MelliPayamak Pattern Exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send general text SMS via Melli Payamak REST API.
     */
    public function sendNotification(string $phone, string $message): bool
    {
        if (empty($this->username) || empty($this->password)) {
            Log::info("MelliPayamak [Simulated SMS]: To {$phone}: {$message}");
            return true;
        }

        try {
            $response = Http::asForm()
                ->timeout(5)
                ->connectTimeout(3)
                ->post('https://rest.payamak-panel.com/api/SendSMS/SendSMS', [
                'username' => $this->username,
                'password' => $this->password,
                'to' => $phone,
                'from' => $this->fromNumber ?? '',
                'text' => $message,
                'isflash' => false,
            ]);

            if ($response->successful()) {
                Log::info("MelliPayamak SMS Sent to {$phone}: " . $response->body());
                return true;
            }

            Log::error("MelliPayamak SMS Failed to {$phone}: " . $response->body());
            return false;
        } catch (\Throwable $e) {
            Log::error("MelliPayamak SMS Exception: " . $e->getMessage());
            return false;
        }
    }
}
