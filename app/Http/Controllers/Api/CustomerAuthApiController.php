<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CustomerAuthService;
use Exception;
use Illuminate\Http\Request;

class CustomerAuthApiController extends Controller
{
    public function __construct(protected CustomerAuthService $customerAuthService) {}

    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:10|max:15',
            'cafe_id' => 'nullable|integer',
        ]);

        $cafeId = $request->input('cafe_id', 1);

        try {
            $this->customerAuthService->sendOtp($cafeId, $request->phone);

            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'کد تایید با موفقیت ارسال شد.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:10|max:15',
            'code' => 'required|string|size:6',
            'cafe_id' => 'nullable|integer',
        ]);

        $cafeId = $request->input('cafe_id', 1);

        try {
            $result = $this->customerAuthService->verifyOtp($cafeId, $request->phone, $request->code);

            return response()->json([
                'success' => true,
                'data' => [
                    'customer' => $result['customer'],
                    'token' => $result['token'],
                ],
                'message' => 'ورود با موفقیت انجام شد.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
