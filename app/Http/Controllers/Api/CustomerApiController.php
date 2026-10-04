<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\Request;

class CustomerApiController extends Controller
{
    public function __construct(protected CustomerService $customerService) {}

    public function index()
    {
        $user = auth()->user();

        // Only Staff / Admin can list all cafe customers
        if ($user instanceof Customer || !($user instanceof \App\Models\User) || (!$user->isAdmin() && !$user->isStaff())) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'دسترسی غیرمجاز. تنها مدیران و پرسنل کافه به لیست مشتریان دسترسی دارند.',
            ], 403);
        }

        $cafeId = $user->cafe_id ?? 1;
        $customers = Customer::where('cafe_id', $cafeId)->paginate(15);

        return response()->json([
            'success' => true,
            'data' => CustomerResource::collection($customers),
            'message' => null,
        ]);
    }

    public function show($id)
    {
        $user = auth()->user();

        // If authenticated as a Customer, restrict strictly to their own ID
        if ($user instanceof Customer) {
            if ((int) $user->id !== (int) $id) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'شما مجاز به مشاهده اطلاعات سایر مشتریان نیستید.',
                ], 403);
            }
            $customer = $user;
        } else {
            // Staff / Admin check
            if (!($user instanceof \App\Models\User) || (!$user->isAdmin() && !$user->isStaff())) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'دسترسی غیرمجاز.',
                ], 403);
            }

            $cafeId = $user->cafe_id ?? 1;
            $customer = Customer::where('cafe_id', $cafeId)->findOrFail($id);
        }

        return response()->json([
            'success' => true,
            'data' => new CustomerResource($customer),
            'message' => null,
        ]);
    }
}
