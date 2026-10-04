<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use App\Services\SubscriptionService;
use Exception;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
        protected SubscriptionService $subscriptionService
    ) {}

    public function index()
    {
        $cafeId = auth()->user()->cafe_id;

        $orders = Order::where('cafe_id', $cafeId)
            ->select(['id', 'order_number', 'cafe_id', 'customer_id', 'order_type', 'payment_method', 'status', 'final_payable', 'total_price', 'table_number', 'created_at'])
            ->with(['customer' => fn($q) => $q->select(['id', 'name', 'phone'])])
            ->latest()
            ->paginate(15);

        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $cafeId = auth()->user()->cafe_id ?? 1;
        $todayStr = \Illuminate\Support\Carbon::today()->toDateString();
        $rawCustomers = Customer::where('cafe_id', $cafeId)
            ->with(['activeSubscription.plan.products'])
            ->select(['id', 'name', 'phone'])
            ->orderBy('name')
            ->get();

        $activeSubIds = $rawCustomers->pluck('activeSubscription.id')->filter()->values();
        $todayUsedSubIds = \App\Models\SubscriptionUsage::whereIn('subscription_id', $activeSubIds)
            ->where('used_date', $todayStr)
            ->pluck('subscription_id')
            ->flip()
            ->all();

        $customers = $rawCustomers->map(function ($c) use ($todayUsedSubIds) {
            $sub = $c->activeSubscription;
            $hasActiveSub = $sub && $sub->isValid();
            $hasUsedToday = $hasActiveSub && isset($todayUsedSubIds[$sub->id]);
            $planName = $hasActiveSub ? ($sub->plan?->name ?? 'اشتراک کافه') : null;
            $remainingItems = $hasActiveSub ? (int) $sub->remaining_items : 0;
            $allowedProductIds = $hasActiveSub ? ($sub->plan?->products->pluck('id')->toArray() ?? []) : [];

                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'phone' => $c->phone,
                    'has_active_sub' => (bool) $hasActiveSub,
                    'has_used_today' => (bool) $hasUsedToday,
                    'can_redeem' => $hasActiveSub && !$hasUsedToday && $remainingItems > 0,
                    'plan_name' => $planName,
                    'remaining_items' => $remainingItems,
                    'allowed_product_ids' => $allowedProductIds,
                ];
            });

        $products = Product::where('cafe_id', $cafeId)->where('status', 'active')->select(['id', 'name', 'price', 'type'])->get();

        return view('orders.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $cafeId = auth()->user()->cafe_id;
        $staffUserId = auth()->id();

        // Normalize items array if submitted as key-value pairs [product_id => quantity] from simple forms
        $rawItems = $request->input('items', []);
        $formattedItems = [];
        foreach ($rawItems as $key => $val) {
            if (is_array($val) && isset($val['product_id'])) {
                if ((int) ($val['quantity'] ?? 0) > 0) {
                    $formattedItems[] = $val;
                }
            } elseif (is_numeric($key) && (int) $val > 0) {
                $formattedItems[] = [
                    'product_id' => (int) $key,
                    'quantity' => (int) $val,
                ];
            }
        }
        // If user checked redeem_subscription but didn't select quantities in the manual form, auto-pick first eligible product
        if ($request->boolean('redeem_subscription') && empty($formattedItems) && $request->filled('customer_id')) {
            $tempCustomer = Customer::find($request->input('customer_id'));
            if ($tempCustomer) {
                $tempSub = $this->subscriptionService->checkActiveSubscription($tempCustomer);
                if ($tempSub && $tempSub->plan && $tempSub->plan->products->isNotEmpty()) {
                    $eligibleProductId = $tempSub->plan->products->first()->id;
                    $formattedItems[] = [
                        'product_id' => $eligibleProductId,
                        'quantity' => 1,
                    ];
                }
            }
        }

        $request->merge(['items' => $formattedItems]);

        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'redeem_subscription' => 'nullable|boolean',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ], [
            'items.required' => 'حداقل یک محصول برای سفارش باید انتخاب شود.',
            'items.min' => 'حداقل یک محصول برای سفارش باید انتخاب شود.',
        ]);

        try {
            $discountAmount = 0.00;
            $redeemedProductId = null;
            $sub = null;

            if ($request->boolean('redeem_subscription') && !empty($validated['customer_id'])) {
                $customer = Customer::findOrFail($validated['customer_id']);
                $sub = $this->subscriptionService->checkActiveSubscription($customer);

                if (!$sub) {
                    throw new Exception('مشتری انتخابی اشتراک معتبر و فعال ندارد.');
                }
                if ($this->subscriptionService->hasUsedToday($sub)) {
                    throw new Exception('سهمیه روزانه این اشتراک قبلاً امروز استفاده شده است.');
                }

                $allowedProductIds = $sub->plan?->products->pluck('id')->toArray() ?? [];

                // Find first product in order items that is covered by the plan
                foreach ($validated['items'] as $item) {
                    if (in_array((int) $item['product_id'], $allowedProductIds)) {
                        $redeemedProductId = (int) $item['product_id'];
                        break;
                    }
                }

                if (!$redeemedProductId) {
                    throw new Exception('هیچ‌یک از محصولات سفارش داده شده مشمول پلن اشتراک این مشتری نیست.');
                }

                $discountProduct = Product::findOrFail($redeemedProductId);
                $discountAmount = (float) $discountProduct->price;
            }

            $order = $this->orderService->createNormalOrder(
                $cafeId,
                $validated['customer_id'] ?? null,
                $validated['items'],
                $staffUserId,
                $discountAmount,
                'preparing'
            );

            // Record subscription item usage
            if ($redeemedProductId && $sub) {
                $this->subscriptionService->consumeDailyItem($cafeId, $sub->id, $redeemedProductId, $staffUserId);
            }

            return redirect()->route('orders.show', $order)
                ->with('success', "سفارش شماره {$order->order_number} با موفقیت ثبت شد و به مانیتور زنده بار ارسال گردید." . ($discountAmount > 0 ? " (۱ سهمیه اشتراک کسر شد)" : ''));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'خطا در ثبت سفارش: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Order $order)
    {
        $cafeId = auth()->user()->cafe_id ?? 1;
        abort_if($order->cafe_id !== $cafeId, 403, 'دسترسی غیرمجاز.');

        $order->load(['customer', 'items.product', 'deliveryAddress.deliveryZone', 'payments', 'latestPayment']);

        return view('orders.show', compact('order'));
    }

    public function printReceipt(\Illuminate\Http\Request $request, Order $order)
    {
        $cafeId = $order->cafe_id ?? 1;

        $isStaff = auth('web')->check() && (int)(auth('web')->user()->cafe_id ?? 1) === (int)$cafeId;
        $isOwnerCustomer = auth('customer')->check() && (int)$order->customer_id === (int)auth('customer')->id();

        // Secure guest token verification
        $hasValidGuestSignature = false;
        if ($request->has('token')) {
            $expectedToken = hash_hmac('sha256', "order_receipt_{$order->id}_{$order->created_at}", config('app.key'));
            $hasValidGuestSignature = hash_equals($expectedToken, (string) $request->query('token'));
        }

        if (!$isStaff && !$isOwnerCustomer && !$hasValidGuestSignature) {
            abort(403, 'دسترسی به این فاکتور نیازمند احراز هویت است.');
        }

        $order->load(['customer.tier', 'items.product', 'deliveryAddress.deliveryZone', 'creator', 'cafe']);
        $setting = \App\Models\ReceiptSetting::getForCafe($cafeId);

        return view('orders.receipt-80mm', compact('order', 'setting'));
    }
}
