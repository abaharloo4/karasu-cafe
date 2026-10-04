<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct(protected SubscriptionService $subscriptionService) {}

    public function index(Request $request)
    {
        $cafeId = auth()->user()->cafe_id ?? 1;

        $plans = SubscriptionPlan::where('cafe_id', $cafeId)
            ->with('products')
            ->latest()
            ->get();

        $query = Subscription::where('cafe_id', $cafeId)
            ->with(['customer', 'plan'])
            ->latest();

        if ($search = $request->input('search')) {
            $normalizedSearch = \App\Services\CustomerService::normalizePersianText($search);
            $cleanPhone = preg_replace('/[^0-9]/', '', $search);

            $query->whereHas('customer', function ($q) use ($search, $normalizedSearch, $cleanPhone) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$normalizedSearch}%");

                if (!empty($cleanPhone)) {
                    $q->orWhere('phone', 'like', "%{$cleanPhone}%");
                } else {
                    $q->orWhere('phone', 'like', "%{$search}%");
                }
            });
        }

        $activeSubscriptions = $query->paginate(15)->withQueryString();

        return view('subscriptions.index', compact('plans', 'activeSubscriptions', 'search'));
    }

    public function createPlan()
    {
        $this->authorize('create', SubscriptionPlan::class);

        $cafeId = auth()->user()->cafe_id ?? 1;
        $products = Product::where('cafe_id', $cafeId)->where('status', 'active')->get();

        return view('subscriptions.create-plan', compact('products'));
    }

    public function storePlan(Request $request)
    {
        $this->authorize('create', SubscriptionPlan::class);

        $cafeId = auth()->user()->cafe_id ?? 1;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'total_items' => 'required|integer|min:1',
            'duration_days' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
        ]);

        $plan = SubscriptionPlan::create([
            'cafe_id' => $cafeId,
            'name' => $validated['name'],
            'total_items' => $validated['total_items'],
            'duration_days' => $validated['duration_days'],
            'price' => $validated['price'],
            'status' => 'active',
        ]);

        $plan->products()->sync($validated['product_ids']);

        return redirect()->route('subscriptions.index')
            ->with('success', 'پلن اشتراک جدید با موفقیت ایجاد شد.');
    }

    public function editPlan(SubscriptionPlan $plan)
    {
        $this->authorize('update', $plan);

        $cafeId = auth()->user()->cafe_id ?? 1;
        $products = Product::where('cafe_id', $cafeId)->get();

        return view('subscriptions.edit-plan', compact('plan', 'products'));
    }

    public function updatePlan(Request $request, SubscriptionPlan $plan)
    {
        $this->authorize('update', $plan);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'total_items' => 'required|integer|min:1',
            'duration_days' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
        ]);

        $plan->update([
            'name' => $validated['name'],
            'total_items' => $validated['total_items'],
            'duration_days' => $validated['duration_days'],
            'price' => $validated['price'],
            'status' => $validated['status'],
        ]);

        $plan->products()->sync($validated['product_ids']);

        return redirect()->route('subscriptions.index')
            ->with('success', 'اطلاعات پلن اشتراک با موفقیت به‌روزرسانی شد.');
    }

    public function assignForm(Request $request)
    {
        $this->authorize('create', Subscription::class);

        $cafeId = auth()->user()->cafe_id ?? 1;
        $customerId = $request->query('customer_id');

        $customers = Customer::where('cafe_id', $cafeId)->orderBy('name')->get();
        $plans = SubscriptionPlan::where('cafe_id', $cafeId)->where('status', 'active')->with('products')->get();

        return view('subscriptions.assign', compact('customers', 'plans', 'customerId'));
    }

    public function assignStore(Request $request)
    {
        $this->authorize('create', Subscription::class);

        $cafeId = auth()->user()->cafe_id ?? 1;

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $subscription = $this->subscriptionService->createSubscription(
            $cafeId,
            $validated['customer_id'],
            $validated['subscription_plan_id']
        );

        return redirect()->route('customers.show', $validated['customer_id'])
            ->with('success', 'اشتراک با موفقیت به مشتری تخصیص داده شد.');
    }

    public function edit(Subscription $subscription)
    {
        $this->authorize('update', $subscription);

        return view('subscriptions.edit', compact('subscription'));
    }

    public function update(Request $request, Subscription $subscription)
    {
        $this->authorize('update', $subscription);

        $validated = $request->validate([
            'remaining_items' => 'required|integer|min:0',
            'expire_date' => 'required|string',
            'status' => 'required|in:active,inactive,expired,cancelled',
        ], [
            'remaining_items.required' => 'تعداد اعتبار الزامی است.',
            'remaining_items.integer' => 'تعداد اعتبار باید عدد صحیح باشد.',
            'expire_date.required' => 'تاریخ انقضا الزامی است.',
            'status.required' => 'انتخاب وضعیت اشتراک الزامی است.',
        ]);

        $rawDate = trim($validated['expire_date']);
        $gregorianDate = null;

        // Check if date is Jalali format (e.g. 1403/07/15 or 1403-07-15)
        if (preg_match('/^(13|14)\d{2}[\/\-]\d{1,2}[\/\-]\d{1,2}$/', $rawDate)) {
            $parts = preg_split('/[\/\-]/', $rawDate);
            $g = \App\Helpers\JalaliHelper::jalaliToGregorian((int)$parts[0], (int)$parts[1], (int)$parts[2]);
            $gregorianDate = sprintf('%04d-%02d-%02d', $g[0], $g[1], $g[2]);
        } else {
            try {
                $gregorianDate = \Illuminate\Support\Carbon::parse($rawDate)->toDateString();
            } catch (\Throwable $e) {
                return back()->withErrors(['expire_date' => 'فرمت تاریخ نامعتبر است.'])->withInput();
            }
        }

        $subscription->update([
            'remaining_items' => (int) $validated['remaining_items'],
            'expire_date' => $gregorianDate,
            'status' => $validated['status'],
        ]);

        return redirect()->route('subscriptions.index')
            ->with('success', 'اشتراک مشتری با موفقیت به‌روزرسانی گردید.');
    }

    public function cancel(Subscription $subscription)
    {
        $this->authorize('update', $subscription);

        $this->subscriptionService->cancelSubscription($subscription);

        return back()->with('info', 'اشتراک مشتری با موفقیت لغو شد.');
    }

    public function destroy(Subscription $subscription)
    {
        if ($subscription->cafe_id !== (auth()->user()->cafe_id ?? 1)) {
            abort(403);
        }

        $subscription->delete();

        return redirect()->route('subscriptions.index')
            ->with('success', 'اشتراک مشتری با موفقیت حذف گردید.');
    }

    /**
     * Record a usage (consume one item) for a subscription.
     */
    public function recordUsage(Request $request, Subscription $subscription)
    {
        $cafeId = auth()->user()->cafe_id ?? 1;

        if ($subscription->cafe_id !== $cafeId) {
            abort(403);
        }

        if ($subscription->status !== 'active' || $subscription->remaining_items <= 0) {
            return back()->with('error', 'این اشتراک فعال نیست یا سهمیه‌اش تموم شده.');
        }

        // Get first product from the plan
        $plan = $subscription->plan;
        $productId = $plan->products->first()?->id ?? \App\Models\Product::first()?->id;
        $staffUserId = auth()->id();

        $result = $this->subscriptionService->consumeDailyItem($cafeId, $subscription->id, $productId, $staffUserId);

        if ($result['success'] ?? false) {
            return back()->with('success', "یک مصرف از اشتراک {$subscription->customer->name} ثبت شد. باقی‌مانده: {$result['remaining']}");
        }

        return back()->with('error', $result['message'] ?? 'خطا در ثبت مصرف.');
    }
}
