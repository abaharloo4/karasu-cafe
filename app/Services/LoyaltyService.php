<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CustomerCoupon;
use App\Models\LoyaltyPoint;
use App\Models\LuckyWheelPrize;
use App\Models\LuckyWheelSpin;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LoyaltyService
{
    /**
     * Add points to a customer's balance.
     */
    public function addPoints(int $cafeId, int $customerId, int $points, string $type = 'earned', ?string $description = null): LoyaltyPoint
    {
        return LoyaltyPoint::create([
            'cafe_id' => $cafeId,
            'customer_id' => $customerId,
            'points' => $points,
            'type' => $type,
            'description' => $description ?? 'اعطای امتیاز وفاداری',
        ]);
    }

    /**
     * Get total net points balance for a customer.
     */
    public function getTotalPoints(int $customerId): int
    {
        return (int) LoyaltyPoint::where('customer_id', $customerId)->sum('points');
    }

    /**
     * Redeem points for a coupon.
     */
    public function redeemCoupon(int $customerId, int $couponId): CustomerCoupon
    {
        $coupon = Coupon::findOrFail($couponId);
        $currentPoints = $this->getTotalPoints($customerId);

        if ($currentPoints < $coupon->points_required) {
            throw new Exception("موجودی امتیاز شما ({$currentPoints}) برای دریافت این کوپن کافی نیست. نیاز به {$coupon->points_required} امتیاز دارید.");
        }

        return DB::transaction(function () use ($customerId, $coupon) {
            // Deduct points
            LoyaltyPoint::create([
                'cafe_id' => $coupon->cafe_id,
                'customer_id' => $customerId,
                'points' => -$coupon->points_required,
                'type' => 'spent',
                'description' => "دریافت کوپن تخفیف: {$coupon->title}",
            ]);

            return CustomerCoupon::create([
                'customer_id' => $customerId,
                'coupon_id' => $coupon->id,
            ]);
        });
    }

    /**
     * Spin the lucky wheel (Max 1 spin per customer per day).
     */
    public function spinLuckyWheel(int $cafeId, int $customerId): array
    {
        $today = Carbon::today()->toDateString();

        // Check daily spin attempt
        $existingSpin = LuckyWheelSpin::where('customer_id', $customerId)
            ->where('spin_date', $today)
            ->first();

        if ($existingSpin) {
            throw new Exception('شما امروز سهمیه چرخاندن گردونه شانس را استفاده کرده‌اید. فردا دوباره شانس خود را امتحان کنید!');
        }

        $prizes = LuckyWheelPrize::where('cafe_id', $cafeId)
            ->where('status', 'active')
            ->get();

        if ($prizes->isEmpty()) {
            throw new Exception('در حال حاضر هیچ جایزه‌ای برای گردونه شانس فعال نیست.');
        }

        // Weighted random selection based on win_probability
        $prize = $this->selectWeightedPrize($prizes);

        try {
            return DB::transaction(function () use ($cafeId, $customerId, $prize, $today) {
                $spin = LuckyWheelSpin::create([
                    'customer_id' => $customerId,
                    'lucky_wheel_prize_id' => $prize ? $prize->id : null,
                    'spin_date' => $today,
                ]);

                // Grant reward if points
                if ($prize && $prize->prize_type === 'points') {
                    $earnedPoints = (int) $prize->prize_value;
                    if ($earnedPoints > 0) {
                        $this->addPoints($cafeId, $customerId, $earnedPoints, 'bonus', "برنده شدن در گردونه شانس: {$prize->title}");
                    }
                }

                return [
                    'success' => true,
                    'spin' => $spin,
                    'prize' => $prize,
                    'message' => $prize ? "تبریک! شما برنده «{$prize->title}» شدید." : 'متأسفانه این بار شانس با شما یار نبود.',
                ];
            });
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) {
                throw new Exception('شما امروز سهمیه گردونه شانس خود را استفاده کرده‌اید.');
            }
            throw $e;
        }
    }

    protected function selectWeightedPrize($prizes): ?LuckyWheelPrize
    {
        $totalWeight = $prizes->sum('win_probability');
        if ($totalWeight <= 0) {
            return $prizes->first();
        }

        $rand = random_int(1, $totalWeight);
        $currentWeight = 0;

        foreach ($prizes as $prize) {
            $currentWeight += $prize->win_probability;
            if ($rand <= $currentWeight) {
                return $prize;
            }
        }

        return $prizes->first();
    }
}
