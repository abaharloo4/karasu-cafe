<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StorefrontController extends Controller
{
    /**
     * Display the SnappFood-style online menu for B Cafe.
     */
    public function menu(Request $request)
    {
        $cafeId = 1;

        Cache::forget("storefront_categories_{$cafeId}");
        Cache::forget("storefront_plans_{$cafeId}");
        Cache::forget("storefront_cafe_{$cafeId}");

        $categories = Category::where('cafe_id', $cafeId)
            ->where('status', 'active')
            ->whereHas('products', function ($query) use ($cafeId) {
                $query->where('cafe_id', $cafeId)
                    ->where('status', 'active');
            })
            ->with(['products' => function ($query) use ($cafeId) {
                $query->where('cafe_id', $cafeId)
                    ->where('status', 'active')
                    ->orderBy('name');
            }])
            ->get();

        // Customer session or Sanctum/web
        $customer = auth('customer')->user() ?? auth()->user()?->customer ?? null;
        if (!$customer && auth()->check()) {
            $user = auth()->user();
            $customer = \App\Models\Customer::where('phone', $user->phone)->first();
        }
        $customerFavoriteIds = $customer ? $customer->favorites()->pluck('products.id')->toArray() : [];
        $activeSubscription = $customer?->activeSubscription;

        $subscriptionPlans = \App\Models\SubscriptionPlan::where('cafe_id', $cafeId)
            ->where('status', 'active')
            ->orderBy('duration_days')
            ->get();

        $cafe = \App\Models\Cafe::find($cafeId) ?? \App\Models\Cafe::firstOrCreate(['id' => $cafeId]);

        return view('storefront.menu', compact('categories', 'customer', 'activeSubscription', 'subscriptionPlans', 'cafe', 'customerFavoriteIds'));
    }

    /**
     * Get single product details with customization options.
     */
    public function showItem(Product $product)
    {
        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'price' => (int) $product->price,
            'category' => $product->category?->name ?? 'کافه',
            'type' => $product->type,
            'status' => $product->status,
        ]);
    }

    /**
     * Dedicated Subscription Plans Landing Page with Persuasive Copy & Conversion ROI.
     */
    public function subscriptions(Request $request)
    {
        $cafeId = 1;
        $customer = auth('customer')->user() ?? auth()->user()?->customer ?? null;
        $activeSubscription = $customer?->activeSubscription;

        $plans = \App\Models\SubscriptionPlan::where('cafe_id', $cafeId)
            ->where('status', 'active')
            ->with('products')
            ->orderBy('price')
            ->get();

        $loyaltySetting = \App\Models\LoyaltySetting::getForCafe($cafeId);

        return view('storefront.subscriptions', compact('plans', 'customer', 'activeSubscription', 'loyaltySetting'));
    }

    /**
     * Dedicated Individual Subscription Plan Showcase Page.
     */
    public function showSubscription(\App\Models\SubscriptionPlan $plan, Request $request)
    {
        $plan->load(['products.category']);
        $customer = auth('customer')->user() ?? auth()->user()?->customer ?? null;
        $activeSubscription = $customer?->activeSubscription;

        return view('storefront.subscription-detail', compact('plan', 'customer', 'activeSubscription'));
    }

    /**
     * Dedicated About Us Page for B Cafe Shiraz.
     */
    public function about(Request $request)
    {
        $cafe = \App\Models\Cafe::first() ?? new \App\Models\Cafe([
            'name' => 'بی کافه (B Cafe)',
            'phone' => '071-36280000',
            'address' => 'شیراز، میدان ارم، ابتدای بلوار دانشجو، کافه تخصصی بی کافه',
        ]);

        return view('storefront.about', compact('cafe'));
    }
}
