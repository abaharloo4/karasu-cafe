<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\LoyaltyPoint;
use App\Services\LoyaltyService;
use Exception;
use Illuminate\Http\Request;

class LoyaltyApiController extends Controller
{
    public function __construct(protected LoyaltyService $loyaltyService) {}

    public function getPoints(Request $request)
    {
        $user = $request->user();
        if (!($user instanceof \App\Models\Customer)) {
            return response()->json(['success' => false, 'message' => 'دسترسی غیرمجاز. احراز هویت مشتری الزامی است.'], 403);
        }
        $customerId = $user->id;

        $totalPoints = $this->loyaltyService->getTotalPoints($customerId);
        $history = LoyaltyPoint::where('customer_id', $customerId)->latest()->take(20)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total_points' => $totalPoints,
                'history' => $history,
            ],
        ]);
    }

    public function getCoupons()
    {
        $coupons = Coupon::where('status', 'active')->get();

        return response()->json([
            'success' => true,
            'data' => $coupons,
        ]);
    }

    public function redeemCoupon(Request $request)
    {
        $request->validate([
            'coupon_id' => 'required|integer',
        ]);

        $user = $request->user();
        if (!($user instanceof \App\Models\Customer)) {
            return response()->json(['success' => false, 'message' => 'دسترسی غیرمجاز. احراز هویت مشتری الزامی است.'], 403);
        }
        $customerId = $user->id;

        try {
            $customerCoupon = $this->loyaltyService->redeemCoupon($customerId, $request->coupon_id);

            return response()->json([
                'success' => true,
                'data' => $customerCoupon,
                'message' => 'کوپن تخفیف با موفقیت دریافت گردید.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
