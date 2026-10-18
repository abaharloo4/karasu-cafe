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
    protected ?string $lastError = null;

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

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    /**
     * Send OTP code via pattern or standard SMS.
     */
    public function sendOtp(string $phone, string $code): bool
    {
        $this->lastError = null;
        $cafeId = auth()->check() ? auth()->user()->cafe_id : 1;
        $setting = SmsSetting::where('cafe_id', $cafeId)->first();

        if ($setting && !empty($setting->pattern_otp)) {
            $sent = $this->sendPattern($phone, $setting->pattern_otp, [$code]);
            if ($sent) {
                return true;
            }

            // Fallback to notification SMS if pattern fails and sender number is configured
            $patternError = $this->lastError;
            if (!empty($this->fromNumber)) {
                $template = !empty($setting->template_text_otp)
                    ? str_replace('{code}', $code, $setting->template_text_otp)
                    : "کد تایید ورود شما به بی کافه: {$code}";
                if ($this->sendNotification($phone, $template)) {
                    $this->lastError = null;
                    return true;
                }
            }

            $this->lastError = $patternError;
            return false;
        }

        $template = ($setting && !empty($setting->template_text_otp))
            ? str_replace('{code}', $code, $setting->template_text_otp)
            : "کد تایید ورود شما به بی کافه: {$code}";

        return $this->sendNotification($phone, $template);
    }

    /**
     * Send a pattern-based SMS via Melli Payamak BaseService API.
     * https://rest.payamak-panel.com/api/SendSMS/BaseServiceNumber
     */
    public function sendPattern(string $phone, string $bodyId, array $args = []): bool
    {
        $this->lastError = null;

        if (empty($this->username) || empty($this->password)) {
            if (app()->environment('testing')) {
                return true;
            }
            $this->lastError = 'نام کاربری یا کلمه عبور ملی‌پیامک در بخش تنظیمات وارد نشده است.';
            Log::warning("MelliPayamak: Credentials missing for sending pattern to {$phone}");
            return false;
        }

        if (empty($bodyId)) {
            $this->lastError = 'کد پترن (Body ID) برای این بخش در سامانه ملی‌پیامک مشخص نشده است.';
            return false;
        }

        try {
            $textValue = implode(';', $args);

            $response = Http::asForm()
                ->timeout(10)
                ->connectTimeout(5)
                ->withoutVerifying()
                ->post('https://rest.payamak-panel.com/api/SendSMS/BaseServiceNumber', [
                    'username' => $this->username,
                    'password' => $this->password,
                    'text' => $textValue,
                    'to' => $phone,
                    'bodyId' => (int) $bodyId,
                ]);

            if ($response->successful()) {
                $json = $response->json();

                if (is_array($json)) {
                    $retStatus = isset($json['RetStatus']) ? (int) $json['RetStatus'] : null;
                    $strRetStatus = (string) ($json['StrRetStatus'] ?? '');
                    $val = (string) ($json['Value'] ?? '');

                    if ($retStatus === 1) {
                        Log::info("MelliPayamak Pattern Success to {$phone}, BodyID: {$bodyId}, Response: " . $response->body());
                        return true;
                    }

                    $this->lastError = $this->translateMelliPayamakError($retStatus, $strRetStatus, $val);
                    Log::error("MelliPayamak Pattern Error: {$this->lastError} - Response: " . $response->body());
                    return false;
                }

                $body = trim($response->body());
                if (is_numeric($body) && (float) $body > 0) {
                    return true;
                }

                $this->lastError = "پاسخ نامشخص از ملی‌پیامک: {$body}";
                return false;
            }

            $this->lastError = "خطای سرور ملی‌پیامک (کد HTTP {$response->status()})";
            Log::error("MelliPayamak Pattern Failed to {$phone}, Status: " . $response->status() . ", Body: " . $response->body());
            return false;
        } catch (\Throwable $e) {
            $this->lastError = "خطای ارتباط با درگاه ملی‌پیامک: " . $e->getMessage();
            Log::error("MelliPayamak Pattern Exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send general text SMS via Melli Payamak REST API.
     */
    public function sendNotification(string $phone, string $message): bool
    {
        $this->lastError = null;

        if (empty($this->username) || empty($this->password)) {
            if (app()->environment('testing')) {
                return true;
            }
            $this->lastError = 'نام کاربری یا کلمه عبور ملی‌پیامک در تنظیمات وارد نشده است.';
            Log::warning("MelliPayamak: Credentials missing for notification to {$phone}");
            return false;
        }

        if (empty($this->fromNumber)) {
            $this->lastError = 'شماره خط اختصاصی فرستنده (from_number) در تنظیمات ملی‌پیامک ثبت نشده است.';
            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout(10)
                ->connectTimeout(5)
                ->withoutVerifying()
                ->post('https://rest.payamak-panel.com/api/SendSMS/SendSMS', [
                    'username' => $this->username,
                    'password' => $this->password,
                    'to' => $phone,
                    'from' => $this->fromNumber,
                    'text' => $message,
                    'isflash' => false,
                ]);

            if ($response->successful()) {
                $json = $response->json();

                if (is_array($json)) {
                    $retStatus = isset($json['RetStatus']) ? (int) $json['RetStatus'] : null;
                    $strRetStatus = (string) ($json['StrRetStatus'] ?? '');
                    $val = (string) ($json['Value'] ?? '');

                    if ($retStatus === 1) {
                        Log::info("MelliPayamak SMS Sent to {$phone}: " . $response->body());
                        return true;
                    }

                    $this->lastError = $this->translateMelliPayamakError($retStatus, $strRetStatus, $val);
                    Log::error("MelliPayamak SMS Error: {$this->lastError} - Response: " . $response->body());
                    return false;
                }

                $body = trim($response->body());
                if (is_numeric($body) && (float) $body > 0) {
                    return true;
                }

                $this->lastError = "پاسخ نامشخص از ملی‌پیامک: {$body}";
                return false;
            }

            $this->lastError = "خطای سرور ملی‌پیامک (کد HTTP {$response->status()})";
            Log::error("MelliPayamak SMS Failed to {$phone}: " . $response->body());
            return false;
        } catch (\Throwable $e) {
            $this->lastError = "خطای ارتباط با درگاه ملی‌پیامک: " . $e->getMessage();
            Log::error("MelliPayamak SMS Exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Map MelliPayamak status/error codes to user-friendly Persian explanations.
     */
    protected function translateMelliPayamakError(?int $retStatus, string $strRetStatus, string $val): string
    {
        $statusLower = strtolower($strRetStatus);

        if (str_contains($statusLower, 'user') || str_contains($statusLower, 'pass') || $val === '-1' || $val === '-11' || $val === '-110') {
            return "نام کاربری یا کلمه عبور / API Key در پنل ملی‌پیامک نامعتبر است (کد وضعیت: {$strRetStatus}).";
        }
        if ($val === '-4' || str_contains($statusLower, 'body') || str_contains($statusLower, 'pattern') || str_contains($statusLower, 'notfound')) {
            return "کد متن یا پترن (Body ID) در پنل ملی‌پیامک یافت نشد، رد شده یا هنوز به تایید نرسیده است (Value: {$val}).";
        }
        if (str_contains($statusLower, 'credit') || $val === '-2' || $val === '-12' || $retStatus === 2) {
            return "اعتبار ریالی یا پیامکی حساب ملی‌پیامک شما کافی نیست.";
        }
        if (str_contains($statusLower, 'line') || str_contains($statusLower, 'from') || str_contains($statusLower, 'invalidnumber') || $val === '-5' || $val === '5' || $retStatus === 9) {
            return "شماره خط فرستنده اختصاصی در پنل ملی‌پیامک فعال نیست یا به این حساب تعلق ندارد (شماره ۵۰۰۰۲۷۱۰۰۶۱۰۳ را در پنل بررسی کنید).";
        }
        if ($val === '-8' || ($retStatus === 35 && $val !== '-4')) {
            return "شماره گیرنده در لیست سیاه مخابرات است یا ارسال به این شماره مجاز نیست.";
        }
        if (str_contains($statusLower, 'filter') || $val === '-7') {
            return "متن پیامک حاوی عبارات فیلتر شده در سامانه مخابراتی است.";
        }
        if (str_contains($statusLower, 'invaliddata')) {
            return "اطلاعات ارسالی (کد پترن، متغیرها یا فرمت شماره) نامعتبر است (کد خطا: {$val}).";
        }

        return "خطای سامانه ملی‌پیامک: {$strRetStatus} (کد: {$retStatus}" . ($val ? ", نتیجه: {$val}" : "") . ")";
    }
}
