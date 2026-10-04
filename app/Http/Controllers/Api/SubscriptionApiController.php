<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionResource;
use App\Models\Customer;
use App\Services\CustomerService;
use App\Services\SubscriptionService;
use Exception;
use Illuminate\Http\Request;

class SubscriptionApiController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptionService,
        protected CustomerService $customerService
    ) {}

    public function check(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $user = auth()->user();

        // If authenticated as a Customer, they can only query their own phone
        if ($user instanceof Customer && $user->phone !== $request->phone) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'شما تنها مجاز به استعلام وضعیت اشتراک خود می‌باشید.',
            ], 403);
        }

        $cafeId = $user->cafe_id ?? 1;
        $customer = $this->customerService->searchCustomerByPhone($cafeId, $request->phone);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'مشتری پیدا نشد.',
            ], 404);
        }

        $activeSubscription = $this->subscriptionService->checkActiveSubscription($customer);

        return response()->json([
            'success' => true,
            'data' => $activeSubscription ? new SubscriptionResource($activeSubscription) : null,
            'message' => null,
        ]);
    }

    public function use(Request $request)
    {
        $user = auth()->user();

        // Only Staff / Admin can record in-person subscription quota consumption
        if ($user instanceof Customer || !($user instanceof \App\Models\User) || (!$user->isAdmin() && !$user->isStaff())) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'دسترسی غیرمجاز. تنها پرسنل و مدیران کافه مجاز به ثبت مصرف سهمیه اشتراک می‌باشند.',
            ], 403);
        }

        $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
            'product_id' => 'required|exists:products,id',
        ]);

        $cafeId = $user->cafe_id ?? 1;
        $staffUserId = $user->id;

        try {
            $result = $this->subscriptionService->consumeDailyItem(
                $cafeId,
                $request->subscription_id,
                $request->product_id,
                $staffUserId
            );

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => $result['message'],
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
