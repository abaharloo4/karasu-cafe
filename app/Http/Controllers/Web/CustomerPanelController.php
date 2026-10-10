<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\DeliveryZone;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CustomerPanelController extends Controller
{
    /**
     * Customer OTP Login View.
     */
    public function showLogin()
    {
        return redirect()->route('login');
    }

    /**
     * Send OTP SMS to customer.
     */
    public function sendOtp(Request $request, \App\Services\CustomerAuthService $authService)
    {
        $request->validate([
            'phone' => ['required', 'regex:/^09[0-9]{9}$/'],
        ], [
            'phone.required' => 'شماره موبایل الزامی است.',
            'phone.regex' => 'فرمت شماره موبایل باید با ۰۹ شروع شده و ۱۱ رقم باشد.',
        ]);

        $cafeId = 1;
        $authService->sendOtp($cafeId, $request->input('phone'));

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'کد تایید پیامک شد.',
                'phone' => $request->input('phone'),
            ]);
        }

        return back()->with([
            'otp_sent' => true,
            'phone' => $request->input('phone'),
            'success' => 'کد تایید پیامک شد. لطفاً کد دریافتی را وارد فرمایید.',
        ]);
    }

    /**
     * Customer Login via OTP Verification.
     */
    public function verifyOtp(Request $request, \App\Services\CustomerAuthService $authService)
    {
        $request->validate([
            'phone' => ['required', 'regex:/^09[0-9]{9}$/'],
            'code' => ['required', 'string', 'min:4', 'max:8'],
        ], [
            'phone.required' => 'شماره موبایل الزامی است.',
            'code.required' => 'کد تایید پیامک‌شده الزامی است.',
        ]);

        $cafeId = 1;

        try {
            $result = $authService->verifyOtp($cafeId, $request->input('phone'), $request->input('code'));
            $customer = $result['customer'];
            auth('customer')->login($customer, true);
            $request->session()->regenerate();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('customer.dashboard'),
                ]);
            }

            $intended = session()->pull('url.intended');
            $redirectUrl = ($intended && $intended !== url('/')) ? $intended : route('customer.dashboard');
            return redirect($redirectUrl)->with('success', 'خوش اومدی به بی کافه!');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->with([
                'otp_sent' => true,
                'phone' => $request->input('phone'),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Customer Logout.
     */
    public function logout(Request $request)
    {
        auth('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('storefront.menu');
    }

    /**
     * Customer Portal Dashboard.
     */
    public function dashboard()
    {
        $customer = auth('customer')->user();
        if (!$customer && auth('web')->check()) {
            $user = auth('web')->user();
            $customer = \App\Models\Customer::firstOrCreate(
                ['phone' => $user->phone ?? '09170000000'],
                ['name' => $user->name, 'cafe_id' => $user->cafe_id ?? 1]
            );
        }

        if (!$customer) {
            return redirect()->route('login');
        }

        $customer->load(['tier', 'activeSubscription.plan']);

        $recentOrders = $customer->orders()
            ->with(['items.product'])
            ->latest()
            ->take(4)
            ->get();

        $totalOrdersCount = $customer->orders()->count();

        $activeOrder = $customer->orders()
            ->with(['items.product'])
            ->whereIn('status', ['pending', 'processing', 'ready', 'shipping'])
            ->latest()
            ->first();

        // Loyalty & Gamification Logic
        $loyaltyService = app(\App\Services\LoyaltyService::class);
        $loyaltyPoints = $loyaltyService->getTotalPoints($customer->id);

        $allTiers = \App\Models\CustomerTier::orderBy('min_spend')->get();
        $currentTier = $customer->tier ?? $allTiers->first();
        $nextTier = $allTiers->where('min_spend', '>', (float)$customer->total_spent)->first();

        $tierProgress = 100;
        $remainingToNextTier = 0;
        if ($nextTier) {
            $prevMin = $currentTier ? (float)$currentTier->min_spend : 0;
            $range = max(1, (float)$nextTier->min_spend - $prevMin);
            $progressAmount = max(0, (float)$customer->total_spent - $prevMin);
            $tierProgress = min(100, (int) round(($progressAmount / $range) * 100));
            $remainingToNextTier = max(0, (float)$nextTier->min_spend - (float)$customer->total_spent);
        }

        $loyaltySetting = \App\Models\LoyaltySetting::getForCafe($customer->cafe_id ?? 1);

        // Check and grant birthday gift if applicable
        $birthdayGiftAwarded = null;
        if ($customer->birthdate && $loyaltySetting->is_birthday_reward_enabled && (float)$loyaltySetting->birthday_reward_amount > 0) {
            $todayMonthDay = now()->format('m-d');
            $birthMonthDay = $customer->birthdate->format('m-d');

            if ($todayMonthDay === $birthMonthDay) {
                $currentYear = (int) now()->year;
                $alreadyReceived = \App\Models\BirthdayRewardLog::where('customer_id', $customer->id)
                    ->where('year', $currentYear)
                    ->exists();

                if (!$alreadyReceived) {
                    try {
                        \App\Models\BirthdayRewardLog::create([
                            'customer_id' => $customer->id,
                            'year' => $currentYear,
                            'sent_at' => now(),
                        ]);

                        $amount = (float) $loyaltySetting->birthday_reward_amount;
                        if ($loyaltySetting->birthday_reward_type === 'points') {
                            $customer->increment('loyalty_points', (int) $amount);
                            \App\Models\LoyaltyPoint::create([
                                'customer_id' => $customer->id,
                                'points' => (int) $amount,
                                'description' => "هدیه تولد سال {$currentYear} بی کافه",
                            ]);
                            $birthdayGiftAwarded = ['type' => 'points', 'amount' => (int) $amount];
                        } else {
                            $customer->increment('wallet_balance', $amount);
                            \App\Models\CustomerCashback::create([
                                'customer_id' => $customer->id,
                                'amount' => $amount,
                                'type' => 'credit',
                                'description' => "هدیه تولد سال {$currentYear} - واریز نقدی به کیف پول",
                            ]);
                            $birthdayGiftAwarded = ['type' => 'wallet', 'amount' => $amount];
                        }
                        $customer->refresh();
                    } catch (\Throwable $e) {
                        // Avoid crashing if duplicate hit simultaneously
                    }
                }
            }
        }

        $favoriteProducts = $customer->favorites()->with('category')->latest()->take(6)->get();

        return view('customer.dashboard', compact(
            'customer',
            'recentOrders',
            'totalOrdersCount',
            'activeOrder',
            'loyaltyPoints',
            'currentTier',
            'nextTier',
            'tierProgress',
            'remainingToNextTier',
            'loyaltySetting',
            'favoriteProducts',
            'birthdayGiftAwarded'
        ));
    }

    /**
     * Dedicated Customer Coffee Subscriptions Page.
     */
    public function subscriptions()
    {
        $customer = auth('customer')->user();
        $customer->load(['activeSubscription.plan']);

        $allSubscriptions = $customer->subscriptions()
            ->with('plan')
            ->latest()
            ->get();

        $usages = $customer->subscriptionUsages()
            ->with(['product', 'subscription.plan'])
            ->latest()
            ->take(20)
            ->get();

        $availablePlans = \App\Models\SubscriptionPlan::where('status', 'active')
            ->orderBy('price')
            ->get();

        return view('customer.subscriptions', compact(
            'customer',
            'allSubscriptions',
            'usages',
            'availablePlans'
        ));
    }

    /**
     * Dedicated Customer Loyalty Club & Cashback Page.
     */
    public function club()
    {
        $customer = auth('customer')->user();
        $customer->load(['tier']);

        $loyaltyService = app(\App\Services\LoyaltyService::class);
        $loyaltyPoints = $loyaltyService->getTotalPoints($customer->id);

        $allTiers = \App\Models\CustomerTier::orderBy('min_spend')->get();
        $currentTier = $customer->tier ?? $allTiers->first();
        $nextTier = $allTiers->where('min_spend', '>', (float)$customer->total_spent)->first();

        $tierProgress = 100;
        $remainingToNextTier = 0;
        if ($nextTier) {
            $prevMin = $currentTier ? (float)$currentTier->min_spend : 0;
            $range = max(1, (float)$nextTier->min_spend - $prevMin);
            $progressAmount = max(0, (float)$customer->total_spent - $prevMin);
            $tierProgress = min(100, (int) round(($progressAmount / $range) * 100));
            $remainingToNextTier = max(0, (float)$nextTier->min_spend - (float)$customer->total_spent);
        }

        $loyaltySetting = \App\Models\LoyaltySetting::getForCafe($customer->cafe_id ?? 1);

        $cashbacks = $customer->cashbacks()
            ->latest()
            ->paginate(15);

        $activeCoupons = \App\Models\Coupon::where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->latest()
            ->get();
        return view('customer.club', compact(
            'customer',
            'loyaltyPoints',
            'allTiers',
            'currentTier',
            'nextTier',
            'tierProgress',
            'remainingToNextTier',
            'loyaltySetting',
            'cashbacks',
            'activeCoupons'
        ));
    }

    /**
     * Spin the daily lucky wheel in Customer Portal (Disabled).
     */
    public function spinWheel(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'گردونه شانس در حال حاضر غیرفعال است.',
        ], 403);
    }

    /**
     * Customer Profile & Birthdate.
     */
    public function profile()
    {
        $customer = auth('customer')->user()->load('tier');
        return view('customer.profile', compact('customer'));
    }

    /**
     * Update customer profile including birthdate.
     */
    public function updateProfile(Request $request)
    {
        $customer = auth('customer')->user();

        $rules = [
            'name' => 'required|string|max:100',
            'email' => 'nullable|email|max:150',
        ];

        // Birthdate can only be set once to prevent birthday discount fraud
        if (empty($customer->birthdate)) {
            $rules['birthdate'] = 'nullable|date';
        }

        $validated = $request->validate($rules);

        $customer->name = $validated['name'];
        $customer->email = $validated['email'] ?? null;

        if (empty($customer->birthdate) && !empty($validated['birthdate'])) {
            $customer->birthdate = $validated['birthdate'];
        }

        $customer->save();

        return redirect()->route('customer.profile')->with('success', 'مشخصاتت با موفقیت ذخیره شد.');
    }

    /**
     * Customer Saved Addresses.
     */
    public function addresses()
    {
        $customer = auth('customer')->user();
        $addresses = $customer->addresses()->with('deliveryZone')->latest()->get();
        $deliveryZones = DeliveryZone::where('status', 'active')->get();

        return view('customer.addresses', compact('customer', 'addresses', 'deliveryZones'));
    }

    /**
     * Store new customer address.
     */
    public function storeAddress(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'address_line' => 'required|string|max:500',
            'delivery_zone_id' => 'nullable|exists:delivery_zones,id',
            'building_unit' => 'nullable|string|max:50',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_default' => 'nullable|boolean',
        ]);

        $customer = auth('customer')->user();

        if ($request->boolean('is_default')) {
            $customer->addresses()->update(['is_default' => false]);
        }

        $customer->addresses()->create([
            'title' => $validated['title'],
            'address_line' => $validated['address_line'],
            'delivery_zone_id' => $validated['delivery_zone_id'] ?? null,
            'building_unit' => $validated['building_unit'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'is_default' => $request->boolean('is_default'),
        ]);

        return redirect()->route('customer.addresses')->with('success', 'آدرس جدید اضافه شد.');
    }

    /**
     * Delete customer address.
     */
    public function destroyAddress(CustomerAddress $address)
    {
        $customer = auth('customer')->user();
        if ($address->customer_id === $customer->id) {
            $address->delete();
        }

        return redirect()->route('customer.addresses')->with('success', 'آدرس حذف شد.');
    }

    /**
     * Customer Orders History.
     */
    public function orders()
    {
        $customer = auth('customer')->user();
        $orders = $customer->orders()->with(['items.product', 'deliveryAddress'])->latest()->paginate(10);

        return view('customer.orders', compact('customer', 'orders'));
    }

    /**
     * Show single order detail & invoice.
     */
    public function showOrder(Order $order)
    {
        $customer = auth('customer')->user();
        abort_if($order->customer_id !== $customer->id, 403);

        $order->load(['items.product', 'deliveryAddress', 'payments']);

        return view('customer.order-show', compact('order'));
    }

    /**
     * Customer Notification Preferences View.
     */
    public function notifications()
    {
        $customer = auth('customer')->user();

        $preferences = [
            'order_status' => \App\Models\NotificationPreference::isEnabled($customer, 'order_status'),
            'rewards' => \App\Models\NotificationPreference::isEnabled($customer, 'rewards'),
            'marketing' => \App\Models\NotificationPreference::isEnabled($customer, 'marketing'),
        ];

        $hasPushSubscription = \App\Models\PushSubscription::where('subscribable_type', $customer->getMorphClass())
            ->where('subscribable_id', $customer->getKey())
            ->exists();

        return view('customer.notifications', compact('customer', 'preferences', 'hasPushSubscription'));
    }

    /**
     * Update Customer Notification Preferences.
     */
    public function updateNotifications(Request $request)
    {
        $customer = auth('customer')->user();

        $categories = ['order_status', 'rewards', 'marketing'];

        foreach ($categories as $cat) {
            $enabled = $request->boolean($cat);

            \App\Models\NotificationPreference::updateOrCreate(
                [
                    'subscribable_type' => $customer->getMorphClass(),
                    'subscribable_id' => $customer->getKey(),
                    'category' => $cat,
                ],
                [
                    'enabled' => $enabled,
                ]
            );
        }

        return back()->with('success', 'تنظیمات دریافت پیام‌ها با موفقیت به‌روزرسانی شد.');
    }

    /**
     * Dedicated Customer Favorite Products Page.
     */
    public function favorites()
    {
        $customer = auth('customer')->user();
        if (!$customer && auth('web')->check()) {
            $user = auth('web')->user();
            $customer = \App\Models\Customer::firstOrCreate(
                ['phone' => $user->phone ?? '09170000000'],
                ['name' => $user->name, 'cafe_id' => $user->cafe_id ?? 1]
            );
        }

        if (!$customer) {
            return redirect()->route('login');
        }

        $favorites = $customer->favorites()->with('category')->latest()->paginate(12);

        return view('customer.favorites', compact('customer', 'favorites'));
    }

    /**
     * Toggle Product in Customer Favorites (AJAX & Web).
     */
    public function toggleFavorite(Request $request)
    {
        $productId = (int) $request->input('product_id');
        $customer = auth('customer')->user();

        if (!$customer && auth('web')->check()) {
            $user = auth('web')->user();
            $customer = \App\Models\Customer::firstOrCreate(
                ['phone' => $user->phone ?? '09170000000'],
                ['name' => $user->name, 'cafe_id' => $user->cafe_id ?? 1]
            );
        }

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'لطفاً ابتدا وارد حساب کاربری خود شوید.',
            ], 401);
        }

        $exists = $customer->favorites()->where('product_id', $productId)->exists();

        if ($exists) {
            $customer->favorites()->detach($productId);
            $isFavorite = false;
            $message = 'محصول از علاقه‌مندی‌ها حذف شد.';
        } else {
            $customer->favorites()->attach($productId);
            $isFavorite = true;
            $message = 'محصول به علاقه‌مندی‌های شما اضافه شد.';
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'is_favorite' => $isFavorite,
                'product_id' => $productId,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Sync LocalStorage Favorites to Database upon login or page view.
     */
    public function syncFavorites(Request $request)
    {
        $productIds = $request->input('product_ids', []);
        if (!is_array($productIds) || empty($productIds)) {
            return response()->json(['success' => true, 'synced' => false, 'count' => 0]);
        }

        $customer = auth('customer')->user();
        if (!$customer && auth('web')->check()) {
            $user = auth('web')->user();
            $customer = \App\Models\Customer::firstOrCreate(
                ['phone' => $user->phone ?? '09170000000'],
                ['name' => $user->name, 'cafe_id' => $user->cafe_id ?? 1]
            );
        }

        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'کاربر وارد نشده است.'], 401);
        }

        $validProductIds = \App\Models\Product::whereIn('id', $productIds)->pluck('id')->toArray();
        $customer->favorites()->syncWithoutDetaching($validProductIds);

        return response()->json([
            'success' => true,
            'synced' => true,
            'count' => count($validProductIds),
            'favorites' => $customer->favorites()->pluck('products.id')->toArray(),
        ]);
    }
}


