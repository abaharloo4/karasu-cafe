<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Exception;
use Illuminate\Http\Request;

class OrderApiController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $user = auth()->user();
        if ($user instanceof \App\Models\Customer) {
            $cafeId = $user->cafe_id ?? 1;
            $customerId = $user->id;
            $staffUserId = 1;
        } elseif ($user instanceof \App\Models\User) {
            $cafeId = $user->cafe_id ?? 1;
            $customerId = $request->customer_id;
            $staffUserId = $user->id;
        } else {
            return response()->json(['success' => false, 'message' => 'دسترسی غیرمجاز.'], 403);
        }

        try {
            $order = $this->orderService->createNormalOrder(
                $cafeId,
                $customerId,
                $request->items,
                $staffUserId
            );

            return response()->json([
                'success' => true,
                'data' => new OrderResource($order),
                'message' => 'سفارش با موفقیت ثبت شد.',
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
