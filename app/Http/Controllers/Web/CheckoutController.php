<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\DeliveryZone;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentGateway;
use App\Models\Product;
use App\Services\Payment\CardToCardService;
use App\Services\Payment\ZarinpalGatewayService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function cart(Request $request)
    {
        $customer = auth('customer')->user() ?? auth()->user()?->customer ?? null;
        $deliveryZones = DeliveryZone::where('status', 'active')->get();
        $suggestedProducts = Product::where('status', 'active')
            ->whereIn('type', ['pastry', 'retail', 'addon'])
            ->inRandomOrder()
            ->take(4)
            ->get();

        $autoAddPlan = null;
        if ($request->filled('add_plan')) {
            $autoAddPlan = \App\Models\SubscriptionPlan::find($request->input('add_plan'));
        }

        return view('storefront.cart', compact('customer', 'deliveryZones', 'suggestedProducts', 'autoAddPlan'));
    }

    public function index(Request $request)
    {
        $customer = auth('customer')->user() ?? auth()->user()?->customer ?? null;
        $deliveryZones = DeliveryZone::where('status', 'active')->get();
        $gateways = PaymentGateway::where('is_active', true)->get()->keyBy('key');
        $savedAddresses = $customer ? $customer->addresses()->with('deliveryZone')->get() : collect();
        $activeSubscription = $customer?->activeSubscription;

        return view('storefront.checkout', compact(
            'customer',
            'deliveryZones',
            'gateways',
            'savedAddresses',
            'activeSubscription'
        ));
    }

    public function placeOrder(
        Request $request,
        ZarinpalGatewayService $zarinpalService,
        CardToCardService $cardToCardService,
        SubscriptionService $subscriptionService
    ) {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => ['required', 'regex:/^09[0-9]{9}$/'],
            'order_type' => 'required|in:dine_in,courier,takeaway',
            'fulfillment_type' => 'nullable|in:pickup,delivery',
            'table_number' => 'nullable|string|max:50',
            'address_id' => 'nullable|integer',
            'address_line' => 'nullable|required_if:order_type,courier|string|max:500',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'delivery_zone_id' => 'nullable|integer',
            'building_unit' => 'nullable|string|max:50',
            'payment_method' => 'required|in:zarinpal,card_to_card,subscription_credit,cash',
            'receipt_image' => 'nullable|file|mimes:jpeg,png,webp,jpg|max:4096',
            'tracking_number' => 'nullable|string|max:50',
            'coupon_code' => 'nullable|string|max:50',
            'use_wallet' => 'nullable|boolean',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.type' => 'nullable|in:product,subscription',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.subscription_plan_id' => 'nullable|exists:subscription_plans,id',
            'items.*.quantity' => 'required|integer|min:1|max:50',
            'items.*.notes' => 'nullable|string|max:500',
            'items.*.addons' => 'nullable|array',
            'items.*.addons.*' => 'string|max:100',
        ]);

        $cafeId = 1;

        // In-person only guard for subscription credit
        if ($validated['payment_method'] === 'subscription_credit' && $validated['order_type'] === 'courier') {
            return back()->with('error', 'سهمیه اشتراک قهوه روزانه فقط مختص مراجعه حضوری و بیرون‌بر است و برای ارسال با پیک قابل استفاده نمی‌باشد.');
        }

        // Check if selected payment gateway is active
        if (in_array($validated['payment_method'], ['zarinpal', 'card_to_card', 'cash'])) {
            $gw = PaymentGateway::where('key', $validated['payment_method'])->first();
            if ($gw && !$gw->is_active) {
                return back()->with('error', 'این روش پرداخت فعلاً فعال نیست، لطفاً روش دیگه‌ای رو انتخاب کن.');
            }
        }

        // Resolve or create customer
        if (auth('customer')->check()) {
            $customer = auth('customer')->user();
        } else {
            // Guests cannot drain someone's wallet or burn daily subscription credit without logging in
            if (!empty($validated['use_wallet']) || $validated['payment_method'] === 'subscription_credit') {
                return back()->with('error', 'برای استفاده از کیف‌پول یا سهمیه اشتراک، اول وارد حسابت شو.');
            }

            $customer = Customer::firstOrCreate(
                ['cafe_id' => $cafeId, 'phone' => $validated['customer_phone']],
                ['name' => $validated['customer_name']]
            );

            if ($customer->name !== $validated['customer_name']) {
                $customer->update(['name' => $validated['customer_name']]);
            }
        }

        // Handle address for courier delivery in Shiraz with server-side Point-in-Polygon check
        $deliveryAddressId = null;
        $deliveryFee = 0;
        $isDelivery = ($validated['order_type'] === 'courier' || ($validated['fulfillment_type'] ?? '') === 'delivery');

        if ($isDelivery) {
            $lat = !empty($validated['latitude']) ? (float)$validated['latitude'] : null;
            $lng = !empty($validated['longitude']) ? (float)$validated['longitude'] : null;

            if (!empty($validated['address_id'])) {
                $savedAddress = CustomerAddress::where('customer_id', $customer->id)->find($validated['address_id']);
                if ($savedAddress) {
                    $lat = (float)$savedAddress->latitude;
                    $lng = (float)$savedAddress->longitude;
                    $deliveryAddressId = $savedAddress->id;
                }
            }

            if (!$lat || !$lng) {
                return back()->with('error', 'لوکیشن دقیق تحویل رو روی نقشه شیراز مشخص کن.');
            }

            // Server-side Point-in-Polygon check against all active delivery zones
            $activeZones = DeliveryZone::where('cafe_id', $cafeId)->where('status', 'active')->get();
            $matchedZone = null;

            foreach ($activeZones as $activeZone) {
                if ($activeZone->containsPoint($lat, $lng)) {
                    $matchedZone = $activeZone;
                    break;
                }
            }

            if (!$matchedZone) {
                return back()->with('error', 'این لوکیشن متاسفانه خارج از محدوده ارسال فعلی پیک ما در شیرازه. لطفاً یه آدرس داخل محدوده انتخاب کن.');
            }

            if (!$deliveryAddressId) {
                // Duplicate prevention: check if customer already has this exact or near-identical address
                $cleanLine = trim($validated['address_line'] ?? '');
                $existing = CustomerAddress::where('customer_id', $customer->id)
                    ->where(function ($q) use ($lat, $lng, $cleanLine) {
                        $q->where(function ($sq) use ($lat, $lng) {
                            $sq->whereBetween('latitude', [$lat - 0.00025, $lat + 0.00025])
                               ->whereBetween('longitude', [$lng - 0.00025, $lng + 0.00025]);
                        });
                        if (!empty($cleanLine)) {
                            $q->orWhere('address_line', $cleanLine);
                        }
                    })
                    ->first();

                if ($existing) {
                    $deliveryAddressId = $existing->id;
                    if (!empty($validated['building_unit']) && empty($existing->building_unit)) {
                        $existing->update(['building_unit' => $validated['building_unit']]);
                    }
                } else {
                    $address = CustomerAddress::create([
                        'customer_id' => $customer->id,
                        'title' => 'آدرس تحویل سفارش',
                        'address_line' => $cleanLine ?: 'شیراز',
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'delivery_zone_id' => $matchedZone->id,
                        'building_unit' => $validated['building_unit'] ?? null,
                    ]);

                    $deliveryAddressId = $address->id;
                }
            }

            $deliveryFee = (float) $matchedZone->delivery_price;
        }

        // Separate product IDs and subscription plan IDs
        $productIds = collect($validated['items'])
            ->filter(fn($i) => ($i['type'] ?? 'product') === 'product' && !empty($i['product_id']))
            ->pluck('product_id')->unique()->all();
        $planIds = collect($validated['items'])
            ->filter(fn($i) => ($i['type'] ?? '') === 'subscription' || !empty($i['subscription_plan_id']))
            ->map(fn($i) => $i['subscription_plan_id'] ?? $i['product_id'])
            ->unique()->all();

        $products = Product::where('cafe_id', $cafeId)->whereIn('id', $productIds)->get()->keyBy('id');
        $plans = \App\Models\SubscriptionPlan::where('cafe_id', $cafeId)->whereIn('id', $planIds)->get()->keyBy('id');

        foreach ($validated['items'] as $itemInput) {
            $isSub = ($itemInput['type'] ?? '') === 'subscription' || !empty($itemInput['subscription_plan_id']);
            if ($isSub) {
                $pid = $itemInput['subscription_plan_id'] ?? $itemInput['product_id'];
                if (!$plans->has($pid)) {
                    return back()->with('error', 'یکی از پلن‌های اشتراک انتخاب‌شده در سامانه یافت نشد.');
                }
            } else {
                if (!$products->has($itemInput['product_id'])) {
                    return back()->with('error', 'یکی از محصولات انتخاب‌شده در منو یافت نشد.');
                }
                $prod = $products->get($itemInput['product_id']);
                $qty = (int) $itemInput['quantity'];
                if ($prod->stock !== null) {
                    if ($prod->stock <= 0) {
                        return back()->with('error', "محصول «{$prod->name}» متاسفانه در حال حاضر ناموجود است.");
                    }
                    if ($prod->stock < $qty) {
                        return back()->with('error', "موجودی محصول «{$prod->name}» تنها {$prod->stock} عدد است و امکان ثبت {$qty} عدد وجود ندارد.");
                    }
                }
            }
        }

        // Pre-validate subscription credit
        if ($validated['payment_method'] === 'subscription_credit') {
            $activeSub = $customer->activeSubscription;
            if (!$activeSub || !$activeSub->isValid() || $subscriptionService->hasUsedToday($activeSub)) {
                return back()->with('error', 'شما اشتراک فعال قهوه روزانه ندارید یا سهمیه امروز قبلاً مصرف شده است.');
            }
        }

        try {
            [$order, $subtotal, $finalPayable, $walletDeduction] = DB::transaction(function () use (
                $cafeId,
                $customer,
                $validated,
                $deliveryAddressId,
                $deliveryFee,
                $isDelivery,
                $products,
                $plans,
                $subscriptionService
            ) {
                $subtotal = 0;
                $orderItemsData = [];
                $purchasedPlanIds = [];

                foreach ($validated['items'] as $itemInput) {
                    $isSub = ($itemInput['type'] ?? '') === 'subscription' || !empty($itemInput['subscription_plan_id']);

                    if ($isSub) {
                        $planId = $itemInput['subscription_plan_id'] ?? $itemInput['product_id'];
                        $plan = $plans->get($planId);
                        $qty = (int) $itemInput['quantity'];
                        $unitPrice = (float) $plan->price;
                        $itemTotal = $unitPrice * $qty;
                        $subtotal += $itemTotal;

                        // Link fallback product from plan if needed to keep legacy views stable
                        $fallbackProdId = $plan->products()->first()?->id;

                        $orderItemsData[] = [
                            'product_id' => $fallbackProdId,
                            'subscription_plan_id' => $plan->id,
                            'item_type' => 'subscription',
                            'quantity' => $qty,
                            'price' => $unitPrice,
                            'total_price' => $itemTotal,
                            'notes' => 'خرید پلن اشتراک: ' . $plan->name,
                        ];

                        for ($i = 0; $i < $qty; $i++) {
                            $purchasedPlanIds[] = $plan->id;
                        }
                    } else {
                        $product = $products->get($itemInput['product_id']);
                        $qty = (int) $itemInput['quantity'];

                        // Enforce stock inventory control
                        if ($product->stock !== null) {
                            if ($product->stock <= 0) {
                                throw new \DomainException("محصول «{$product->name}» متاسفانه ناموجود شده است.");
                            }
                            if ($product->stock < $qty) {
                                throw new \DomainException("موجودی محصول «{$product->name}» تنها {$product->stock} عدد است و امکان ثبت {$qty} عدد وجود ندارد.");
                            }
                        }

                        // Use effective discounted price if happy hour discount active
                        $unitPrice = (float) $product->effective_price;

                        // Validate and compute add-on pricing from official product configuration
                        $productAddons = is_array($product->addons) ? collect($product->addons)->keyBy('name') : collect();
                        $selectedAddons = $itemInput['addons'] ?? [];
                        $addonNotes = [];

                        foreach ($selectedAddons as $addonName) {
                            if ($productAddons->has($addonName)) {
                                $addonData = $productAddons->get($addonName);
                                $addonFee = (float) ($addonData['price'] ?? 0);
                                $unitPrice += $addonFee;
                                $addonNotes[] = $addonName . ' (+' . number_format($addonFee) . ' تومان)';
                            }
                        }

                        $itemTotal = $unitPrice * $qty;
                        $subtotal += $itemTotal;

                        $finalNotes = $itemInput['notes'] ?? '';
                        if (!empty($addonNotes)) {
                            $addonSummary = 'افزودنی‌ها: ' . implode('، ', $addonNotes);
                            $finalNotes = $finalNotes ? ($finalNotes . ' | ' . $addonSummary) : $addonSummary;
                        }

                        $orderItemsData[] = [
                            'product_id' => $product->id,
                            'subscription_plan_id' => null,
                            'item_type' => 'product',
                            'quantity' => $qty,
                            'price' => $unitPrice,
                            'total_price' => $itemTotal,
                            'notes' => !empty($finalNotes) ? mb_substr($finalNotes, 0, 500) : null,
                        ];
                    }
                }

                // Customer tier discount
                $discountAmount = 0;
                if ($customer->tier && $customer->tier->discount_percentage > 0) {
                    $discountAmount = round(($subtotal * $customer->tier->discount_percentage) / 100);
                }

                // Coupon Code discount with ownership check (Anti-IDOR)
                if (!empty($validated['coupon_code'])) {
                    $coupon = \App\Models\Coupon::where('code', strtoupper(trim($validated['coupon_code'])))
                        ->where('status', 'active')
                        ->first();

                    if ($coupon && (!$coupon->expires_at || $coupon->expires_at->isFuture())) {
                        // Check if this coupon is restricted to specific customer(s)
                        $personalBinding = \App\Models\CustomerCoupon::where('coupon_id', $coupon->id)->first();
                        if (!$personalBinding || $personalBinding->customer_id === $customer->id) {
                            if ($coupon->discount_type === 'percent') {
                                $couponDiscount = round(($subtotal * $coupon->discount_value) / 100);
                            } else {
                                $couponDiscount = min($subtotal, (float)$coupon->discount_value);
                            }
                            $discountAmount += $couponDiscount;

                            // Mark personal coupon as used
                            if ($personalBinding && $personalBinding->customer_id === $customer->id) {
                                $personalBinding->update(['used_at' => now()]);
                            }
                        }
                    }
                }

                // Free delivery threshold check
                if ($validated['order_type'] === 'courier') {
                    if ($customer->tier && $customer->tier->free_delivery) {
                        $deliveryFee = 0;
                    }
                }

                $payableBeforeWallet = max(0, ($subtotal - $discountAmount) + $deliveryFee);
                $walletDeduction = 0;

                // Handle Customer Wallet Cashback Deduction with atomic row lock (SEC-06 defense)
                if (!empty($validated['use_wallet'])) {
                    $lockedCustomer = \App\Models\Customer::where('id', $customer->id)->lockForUpdate()->first();
                    if ($lockedCustomer && (float)$lockedCustomer->wallet_balance > 0) {
                        $walletDeduction = min((float)$lockedCustomer->wallet_balance, $payableBeforeWallet);
                        if ($walletDeduction > 0) {
                            $updated = \App\Models\Customer::where('id', $lockedCustomer->id)
                                ->where('wallet_balance', '>=', $walletDeduction)
                                ->decrement('wallet_balance', $walletDeduction);

                            if ($updated) {
                                \App\Models\CustomerCashback::create([
                                    'customer_id' => $lockedCustomer->id,
                                    'type' => 'debit',
                                    'amount' => $walletDeduction,
                                    'description' => 'کسر از موجودی کیف‌پول جهت پرداخت سفارش',
                                ]);
                            } else {
                                $walletDeduction = 0;
                            }
                        }
                    }
                }

                $finalPayable = max(0, $payableBeforeWallet - $walletDeduction);

                // Handle Subscription item credit (SEC-02 defense)
                if ($validated['payment_method'] === 'subscription_credit') {
                    $activeSub = $customer->activeSubscription;
                    $firstItemProductId = $orderItemsData[0]['product_id'] ?? null;
                    $firstProduct = $products->get($firstItemProductId);
                    $coffeeDiscount = (float) ($firstProduct?->price ?? 0);
                    $discountAmount += $coffeeDiscount;
                    $finalPayable = max(0, ($subtotal - $discountAmount) + $deliveryFee);

                    if ($finalPayable > 0) {
                        throw new \DomainException('سهمیه اشتراک تنها هزینه ۱ فنجان قهوه را پوشش می‌دهد. برای پرداخت الباقی سفارش (' . number_format($finalPayable) . ' تومان)، لطفاً درگاه زرین‌پال یا کارت‌به‌کارت را انتخاب کنید.');
                    }

                    $staffUserId = auth()->id() ?? 1;
                    $subscriptionService->consumeDailyItem($cafeId, $activeSub->id, $firstItemProductId, $staffUserId);
                }

                // Generate Order Number: 5 digits numeric
                do {
                    $orderNumber = (string) random_int(10000, 99999);
                    $exists = Order::where('cafe_id', $cafeId)->where('order_number', $orderNumber)->exists();
                } while ($exists);

                $cafe = \App\Models\Cafe::find($cafeId);
                $isQueuedForNextShift = $cafe ? !$cafe->isOrderingOpen() : false;

                $order = Order::create([
                    'cafe_id' => $cafeId,
                    'order_number' => $orderNumber,
                    'customer_id' => $customer->id,
                    'type' => 'normal',
                    'order_type' => $validated['order_type'],
                    'fulfillment_type' => $isDelivery ? 'delivery' : 'pickup',
                    'table_number' => $validated['table_number'] ?? null,
                    'delivery_address_id' => $deliveryAddressId,
                    'payment_method' => $validated['payment_method'],
                    'status' => ($validated['payment_method'] === 'card_to_card') ? 'awaiting_payment_verification' : 'pending',
                    'total_price' => $subtotal,
                    'discount_amount' => $discountAmount,
                    'delivery_fee' => $deliveryFee,
                    'final_payable' => $finalPayable,
                    'notes' => $validated['notes'] ?? null,
                    'estimated_prep_time' => 15,
                    'is_queued_for_next_shift' => $isQueuedForNextShift,
                ]);

                foreach ($orderItemsData as $item) {
                    $order->items()->create($item);
                    if ($item['item_type'] === 'product' && !empty($item['product_id'])) {
                        $prod = $products->get($item['product_id']);
                        if ($prod && $prod->stock !== null) {
                            $prod->decrement('stock', $item['quantity']);
                        }
                    }
                }

                return [$order, $subtotal, $finalPayable, $walletDeduction];
            });
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        // Web Push notification to admins/kitchen (Sent outside DB transaction)
        // Web Push notification to admins/kitchen (Sent outside DB transaction)
        $pushService = app(\App\Services\Pwa\PushNotificationService::class);
        $pushService->sendToAdmins(
            'سفارش جدید!',
            "سفارش جدید ({$order->order_number}) به مبلغ " . number_format($order->final_payable) . " تومان ثبت شد.",
            ['order_id' => $order->id, 'url' => route('admin.live-orders.index')],
            'new_order'
        );

        // SMS notification to customer for order submission
        try {
            app(\App\Services\Sms\OrderSmsNotifier::class)->notify($order, \App\Models\SmsSetting::ORDER_STATUS_SUBMITTED);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Failed sending order submitted SMS: ' . $e->getMessage());
        }

        // 0. If fully covered by wallet or coupon
        if ($finalPayable == 0 && $validated['payment_method'] !== 'subscription_credit') {
            $order->update([
                'status' => 'preparing',
                'payment_method' => $walletDeduction > 0 ? 'wallet' : 'coupon',
            ]);

            // Activate any purchased subscription plans in the order
            foreach ($order->items as $orderItem) {
                if ($orderItem->item_type === 'subscription' && $orderItem->subscription_plan_id) {
                    $subscriptionService->assignPlanToCustomer(
                        $order->cafe_id,
                        $customer->id,
                        $orderItem->subscription_plan_id
                    );
                }
            }

            $customer->increment('total_spent', $subtotal);
            app(\App\Services\LoyaltyTierService::class)->evaluateTier($customer);

            return redirect()->route('storefront.order-status', ['order_number' => $order->order_number])
                ->with('success', 'سفارشت با موجودی کیف‌پول ثبت شد و رفت برای آماده‌سازی!');
        }

        // 1. Zarinpal Payment Flow (External HTTP request executed safely outside DB transaction)
        if ($validated['payment_method'] === 'zarinpal' && $finalPayable > 0) {
            $callbackUrl = route('payment.zarinpal.callback', ['order_id' => $order->id]);
            $gatewayRes = $zarinpalService->requestPayment($order, $callbackUrl);

            if ($gatewayRes['success']) {
                return redirect()->away($gatewayRes['payment_url']);
            } else {
                return back()->with('error', 'ارتباط با درگاه زرین‌پال برقرار نشد: ' . ($gatewayRes['message'] ?? ''));
            }
        }

        // 2. Card to Card Payment Flow
        if ($validated['payment_method'] === 'card_to_card') {
            $cardToCardService->submitReceipt(
                $order,
                $validated['tracking_number'] ?? null,
                $request->file('receipt_image')
            );

            $pushService->sendToAdmins(
                'فیش کارت‌به‌کارت جدید',
                "فیش واریزی سفارش ({$order->order_number}) ثبت شد و در انتظار تایید است.",
                ['order_id' => $order->id, 'url' => route('admin.live-orders.index')],
                'payment_pending'
            );

            return redirect()->route('storefront.order-status', ['order_number' => $order->order_number])
                ->with('success', 'سفارشت ثبت شد! فیش واریزی رو باریستا چک می‌کنه و سریع آماده‌ش می‌کنه.');
        }

        // 3. Cash on Delivery / POS at Counter
        if ($validated['payment_method'] === 'cash') {
            $order->update(['status' => 'pending']);

            return redirect()->route('storefront.order-status', ['order_number' => $order->order_number])
                ->with('success', 'سفارشت ثبت شد. موقع تحویل می‌تونی نقدی یا با کارتخوان تسویه کنی.');
        }

        // 4. Free Subscription Credit / Fully Covered
        if ($validated['payment_method'] === 'subscription_credit' && $finalPayable == 0) {
            $order->update(['status' => 'pending']);

            return redirect()->route('storefront.order-status', ['order_number' => $order->order_number])
                ->with('success', 'سفارش شما با سهمیه اشتراک ثبت شد و پس از تأیید باریستا به مرحله آماده‌سازی می‌رود.');
        }

        return redirect()->route('storefront.order-status', ['order_number' => $order->order_number])
            ->with('info', 'سفارشت ثبت شد.');
    }

    /**
     * Public order status tracking page.
     */
    public function orderStatus($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->with(['items.product', 'deliveryAddress', 'customer', 'payments'])
            ->firstOrFail();

        return view('storefront.order-status', compact('order'));
    }

    /**
     * Lightweight JSON polling feed for order tracking page.
     */
    public function orderStatusFeed($orderNumber, Request $request)
    {
        $order = Order::where('order_number', $orderNumber)
            ->select(['id', 'order_number', 'status', 'order_type', 'updated_at'])
            ->firstOrFail();

        $etag = md5("order_{$order->id}_{$order->status}_{$order->updated_at}");
        if ($request->headers->get('If-None-Match') === $etag) {
            return response('', 304)->header('ETag', $etag);
        }

        return response()->json([
            'status' => $order->status,
            'status_label' => match($order->status) {
                'pending' => 'در انتظار تایید پرداخت / باریستا',
                'processing' => 'در حال آماده‌سازی در بار',
                'ready' => $order->isDineIn() ? 'آماده تحویل روی کانتر' : 'آماده ارسال با پیک',
                'shipping' => 'در مسیر ارسال با پیک شیراز',
                'completed' => 'تکمیل و تحویل داده شد',
                'cancelled' => 'سفارش لغو شد',
                default => 'در حال بررسی',
            },
            'status_step' => match($order->status) {
                'pending' => 1,
                'processing' => 2,
                'ready', 'shipping' => 3,
                'completed' => 4,
                'cancelled' => 0,
                default => 1,
            },
            'is_active' => in_array($order->status, ['pending', 'processing', 'ready', 'shipping']),
        ])->header('ETag', $etag);
    }

    /**
     * Validate Coupon Code via AJAX for Checkout
     */
    public function validateCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        $code = strtoupper(trim($request->code));

        $coupon = \App\Models\Coupon::where('code', $code)
            ->where('status', 'active')
            ->first();

        if (!$coupon) {
            return response()->json([
                'valid' => false,
                'message' => 'کد تخفیف وارد شده معتبر نیست یا غیرفعال شده است.',
            ], 404);
        }

        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            return response()->json([
                'valid' => false,
                'message' => 'مهلت استفاده از این کد تخفیف به پایان رسیده است.',
            ], 422);
        }

        // Check if coupon belongs to a specific customer (e.g. Birthday coupon)
        $personalBinding = \App\Models\CustomerCoupon::where('coupon_id', $coupon->id)->first();
        if ($personalBinding) {
            $customer = auth('customer')->user();
            if (!$customer) {
                return response()->json([
                    'valid' => false,
                    'message' => 'این کد تخفیف اختصاصی است. لطفاً ابتدا وارد حساب کاربری خود شوید.',
                ], 403);
            }

            if ($personalBinding->customer_id !== $customer->id) {
                return response()->json([
                    'valid' => false,
                    'message' => 'این کد تخفیف متعلق به شماره شما نمی‌باشد.',
                ], 403);
            }
        }

        return response()->json([
            'valid' => true,
            'code' => $coupon->code,
            'title' => $coupon->title,
            'discount_type' => $coupon->discount_type,
            'discount_value' => (float) $coupon->discount_value,
            'message' => 'کد تخفیف «' . $coupon->title . '» اعمال شد.',
        ]);
    }
}
