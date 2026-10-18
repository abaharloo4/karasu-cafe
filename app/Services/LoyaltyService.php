<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CustomerCoupon;
use App\Models\LoyaltyPoint;
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
}
