<?php

namespace App\Console\Commands;

use App\Models\BirthdayRewardLog;
use App\Models\Coupon;
use App\Models\Customer;
use App\Services\Sms\SmsGatewayInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class SendBirthdayRewardsCommand extends Command
{
    protected $signature = 'loyalty:birthday-rewards';
    protected $description = 'Send automated birthday discount coupons and greetings to customers on their birthday';

    public function handle(SmsGatewayInterface $smsGateway): int
    {
        $today = Carbon::today();
        $nowJalali = \Morilog\Jalali\Jalalian::now();
        $currentJalaliYear = $nowJalali->getYear();
        $currentJalaliMonth = $nowJalali->getMonth();
        $currentJalaliDay = $nowJalali->getDay();

        $this->info("Checking birthday rewards for Shamsi date {$nowJalali->format('Y/m/d')}...");

        // Query all customers with birthdate set
        $customersWithBirthdate = Customer::whereNotNull('birthdate')->get();
        $count = 0;

        foreach ($customersWithBirthdate as $customer) {
            $custCarbon = Carbon::parse($customer->birthdate);
            $custJalali = \Morilog\Jalali\Jalalian::fromCarbon($custCarbon);

            // Compare Jalali month and day
            if ($custJalali->getMonth() !== $currentJalaliMonth || $custJalali->getDay() !== $currentJalaliDay) {
                continue;
            }

            // Ensure they haven't already received a reward this Shamsi year
            $alreadySent = BirthdayRewardLog::where('customer_id', $customer->id)
                ->where('year', $currentJalaliYear)
                ->exists();

            if ($alreadySent) {
                continue;
            }

            // Create dedicated birthday coupon (e.g. 50% discount valid for 3 days)
            $couponCode = 'BDAY-' . strtoupper(Str::random(4)) . $currentJalaliYear;
            $coupon = Coupon::create([
                'cafe_id' => $customer->cafe_id ?? 1,
                'code' => $couponCode,
                'title' => 'هدیه تولد ویژه ۵۰٪ کافه بی',
                'discount_type' => 'percent',
                'discount_value' => 50,
                'points_required' => 0,
                'expires_at' => Carbon::now()->addDays(3),
                'status' => 'active',
            ]);

            // Link coupon to this customer specifically (prevents IDOR and public misuse)
            \App\Models\CustomerCoupon::create([
                'customer_id' => $customer->id,
                'coupon_id' => $coupon->id,
            ]);

            // Award Birthday Cashback Gift into wallet (from cafe admin loyalty settings)
            $loyaltySetting = \App\Models\LoyaltySetting::getForCafe($customer->cafe_id ?? 1);
            $birthdayCashback = (float) ($loyaltySetting->birthday_reward_amount ?? 50000);

            if ($birthdayCashback > 0) {
                $customer->increment('wallet_balance', $birthdayCashback);
                \App\Models\CustomerCashback::create([
                    'customer_id' => $customer->id,
                    'type' => 'credit',
                    'amount' => $birthdayCashback,
                    'description' => "هدیه نقدی زادروز شما در بی کافه ({$currentJalaliYear})",
                ]);
            }

            // Record log
            BirthdayRewardLog::create([
                'customer_id' => $customer->id,
                'coupon_id' => $coupon->id,
                'year' => $currentJalaliYear,
                'sent_at' => now(),
            ]);

            // Dispatch SMS greeting or log if SMS panel not set
            $message = "{$customer->name} عزیز، زادروزتان فرخنده باد! کافه بی به مناسبت تولدتان ۵۰،۰۰۰ تومان اعتبار هدیه کیف‌پول و ۵۰٪ تخفیف روی سفارش آنلاین تقدیم شما می‌کند. کد تخفیف: {$couponCode} - منو: " . url('/menu');
            
            try {
                $smsSent = $smsGateway->sendNotification($customer->phone, $message);
                if (!$smsSent) {
                    \Illuminate\Support\Facades\Log::info("SMS panel not active or failed. Recorded Birthday Greeting for {$customer->name} ({$customer->phone}): {$message}");
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Birthday SMS sending failed for {$customer->phone}, logged locally: " . $e->getMessage());
            }

            // Web Push notification for birthday
            try {
                app(\App\Services\Pwa\PushNotificationService::class)->send(
                    $customer,
                    '🎂 زادروزتان فرخنده باد از طرف بی کافه!',
                    "{$customer->name} عزیز، ۵۰،۰۰۰ تومان اعتبار هدیه و کوپن ۵۰٪ تخفیف به حساب شما افزوده شد.",
                    ['url' => '/panel/club', 'coupon_code' => $couponCode],
                    'loyalty'
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Birthday Push sending failed: " . $e->getMessage());
            }

            $this->info("Birthday reward sent to {$customer->name} ({$customer->phone}) with code: {$couponCode} + {$birthdayCashback} Toman cashback");
            $count++;
        }

        $this->info("Finished sending birthday rewards. Total dispatched: {$count}");

        return Command::SUCCESS;
    }
}
