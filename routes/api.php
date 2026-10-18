<?php

use App\Http\Controllers\Api\CustomerApiController;
use App\Http\Controllers\Api\CustomerAuthApiController;
use App\Http\Controllers\Api\LoyaltyApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\SubscriptionApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| REST API Routes (All-Laravel Architecture: Core + Online + Loyalty)
|--------------------------------------------------------------------------
*/

// Public OTP Auth Endpoints (Protected by Rate Limiting against SMS bombing and Brute-force)
Route::post('/auth/send-otp', [CustomerAuthApiController::class, 'sendOtp'])->middleware('throttle:3,1');
Route::post('/auth/verify-otp', [CustomerAuthApiController::class, 'verifyOtp'])->middleware('throttle:5,1');

// Public Loyalty Catalog Endpoints
Route::get('/loyalty/coupons', [LoyaltyApiController::class, 'getCoupons']);

// Authenticated Routes (Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // Authenticated Loyalty Endpoints
    Route::get('/loyalty/points', [LoyaltyApiController::class, 'getPoints']);
    Route::post('/loyalty/redeem-coupon', [LoyaltyApiController::class, 'redeemCoupon']);

    // Customers API
    Route::get('/customers', [CustomerApiController::class, 'index']);
    Route::get('/customers/{id}', [CustomerApiController::class, 'show']);

    // Subscriptions API
    Route::post('/subscriptions/check', [SubscriptionApiController::class, 'check']);
    Route::post('/subscription/use', [SubscriptionApiController::class, 'use']);

    // Orders API
    Route::post('/orders', [OrderApiController::class, 'store']);
});
