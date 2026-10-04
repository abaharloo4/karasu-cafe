<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerOtp;
use App\Services\Sms\SmsGatewayInterface;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CustomerAuthService
{
    public function __construct(
        protected SmsGatewayInterface $smsGateway,
        protected CustomerService $customerService
    ) {}

    /**
     * Generate 6-digit OTP code and dispatch SMS.
     */
    public function sendOtp(int $cafeId, string $phone): bool
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

        // Invalidate previous unused OTPs for this phone
        CustomerOtp::where('phone', $cleanPhone)
            ->whereNull('used_at')
            ->update(['used_at' => Carbon::now()]);

        // Generate 6-digit random OTP
        $code = (string) random_int(100000, 999999);

        CustomerOtp::create([
            'phone' => $cleanPhone,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);

        return $this->smsGateway->sendOtp($cleanPhone, $code);
    }

    /**
     * Verify OTP code and return customer + Sanctum token.
     */
    public function verifyOtp(int $cafeId, string $phone, string $code): array
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

        $otp = CustomerOtp::where('phone', $cleanPhone)
            ->whereNull('used_at')
            ->where('expires_at', '>', Carbon::now())
            ->latest()
            ->first();

        if (!$otp) {
            throw new Exception('کد تایید معتبری برای این شماره یافت نشد یا منقضی شده است.');
        }

        if ((int)$otp->attempts >= 3) {
            $otp->update(['used_at' => Carbon::now()]);
            throw new Exception('تعداد تلاش‌های ناموفق بیش از حد مجاز بود. لطفاً مجدداً درخواست کد کنید.');
        }

        if ($otp->code !== $code) {
            $otp->increment('attempts');
            throw new Exception('کد تایید وارد شده نادرست است.');
        }

        // Mark OTP as used
        $otp->update(['used_at' => Carbon::now()]);

        // Find or create customer
        $customer = Customer::firstOrCreate(
            ['cafe_id' => $cafeId, 'phone' => $cleanPhone],
            ['name' => 'مشتری آنلاین ' . substr($cleanPhone, -4)]
        );

        $token = $customer->createToken('customer-api')->plainTextToken;

        return [
            'success' => true,
            'customer' => $customer,
            'token' => $token,
        ];
    }
}
