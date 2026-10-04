<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CustomerService;
use App\Services\OrderService;
use App\Services\SubscriptionService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailyUsageController extends Controller
{
    public function __construct(
        protected CustomerService $customerService,
        protected SubscriptionService $subscriptionService,
        protected OrderService $orderService
    ) {}

    public function index(Request $request)
    {
        $phone = $request->query('phone');
        $customer = null;
        $activeSubscription = null;
        $hasUsedToday = false;
        $allowedProducts = collect();
        $allProducts = collect();

        $cafeId = auth()->user()->cafe_id;

        // Load all active products for paid ordering
        $allProducts = Product::where('cafe_id', $cafeId)
            ->where('status', 'active')
            ->with('category')
            ->get();

        if ($phone) {
            $customer = $this->customerService->searchCustomerByPhone($cafeId, $phone);

            if ($customer) {
                $activeSubscription = $this->subscriptionService->checkActiveSubscription($customer);

                if ($activeSubscription) {
                    $hasUsedToday = $this->subscriptionService->hasUsedToday($activeSubscription);
                    if (!$hasUsedToday) {
                        $allowedProducts = $activeSubscription->plan?->products()->where('status', 'active')->get() ?? collect();
                        if ($allowedProducts->isEmpty()) {
                            $allowedProducts = $allProducts;
                        }
                    }
                }
            }
        }

        return view('daily-usage.index', compact(
            'phone', 'customer', 'activeSubscription',
            'hasUsedToday', 'allowedProducts', 'allProducts'
        ));
    }

    /**
     * Store free subscription daily usage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
            'product_id' => 'required|exists:products,id',
        ], [
            'subscription_id.required' => 'شناسه اشتراک معتبر نیست.',
            'product_id.required' => 'لطفاً یک محصول را انتخاب کنید.',
        ]);

        $cafeId = auth()->user()->cafe_id;
        $staffUserId = auth()->id();

        try {
            $result = $this->subscriptionService->consumeDailyItem(
                $cafeId,
                $validated['subscription_id'],
                $validated['product_id'],
                $staffUserId
            );

            return redirect()->route('daily-usage.index', ['phone' => $request->input('phone')])
                ->with('success', $result['message']);
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Store a paid order (with or without subscription).
     * If customer has an active subscription and orders allowed products,
     * the free item is applied automatically.
     */
    public function storePaidOrder(Request $request)
    {
        // If customer is just redeeming free item without any extra paid items
        if ($request->boolean('use_free_item') && empty($request->input('items', []))) {
            $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'free_product_id' => 'required|exists:products,id',
                'subscription_id' => 'required|exists:subscriptions,id',
            ]);

            $cafeId = auth()->user()->cafe_id;
            $staffUserId = auth()->id();

            try {
                $result = $this->subscriptionService->consumeDailyItem(
                    $cafeId,
                    (int) $request->input('subscription_id'),
                    (int) $request->input('free_product_id'),
                    $staffUserId
                );

                return redirect()->route('daily-usage.index', ['phone' => $request->input('phone')])
                    ->with('success', $result['message']);
            } catch (Exception $e) {
                return redirect()->back()
                    ->with('error', 'خطا در ثبت سهمیه: ' . $e->getMessage())
                    ->withInput();
            }
        }

        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'use_free_item' => 'nullable|boolean',
            'free_product_id' => 'nullable|exists:products,id',
            'subscription_id' => 'nullable|exists:subscriptions,id',
        ]);

        $cafeId = auth()->user()->cafe_id;
        $staffUserId = auth()->id();

        try {
            // If customer wants to use their free item alongside paid items
            $useFreeItem = $request->boolean('use_free_item');
            $freeProductId = $validated['free_product_id'] ?? null;
            $subscriptionId = $validated['subscription_id'] ?? null;

            $order = DB::transaction(function () use ($cafeId, $staffUserId, $useFreeItem, $freeProductId, $subscriptionId, $validated) {
                // First: consume the free subscription item if requested
                if ($useFreeItem && $freeProductId && $subscriptionId) {
                    $this->subscriptionService->consumeDailyItem(
                        $cafeId,
                        $subscriptionId,
                        $freeProductId,
                        $staffUserId
                    );
                }

                // Second: create the paid order for remaining items
                return $this->orderService->createNormalOrder(
                    $cafeId,
                    $validated['customer_id'] ?? null,
                    $validated['items'],
                    $staffUserId
                );
            });

            $msg = "سفارش شماره {$order->id} با مبلغ " . number_format($order->total_price) . " تومان ثبت شد.";
            if ($useFreeItem && $freeProductId) {
                $msg .= ' (ایتم رایگان اشتراک نیز ثبت شد)';
            }

            return redirect()->route('daily-usage.index', ['phone' => $request->input('phone')])
                ->with('success', $msg);
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'خطا: ' . $e->getMessage())
                ->withInput();
        }
    }
}
