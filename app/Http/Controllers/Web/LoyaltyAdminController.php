<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerCashback;
use App\Models\CustomerTier;
use App\Models\LoyaltyPoint;
use App\Models\LoyaltySetting;
use Illuminate\Http\Request;

class LoyaltyAdminController extends Controller
{
    /**
     * Loyalty dashboard overview.
     */
    public function index()
    {
        $cafeId = auth()->user()->cafe_id ?? 1;
        $settings = LoyaltySetting::getForCafe($cafeId);
        $tiers = CustomerTier::orderBy('min_spend')->get();

        // Stats
        $totalCustomers = Customer::where('cafe_id', $cafeId)->count();
        $countsByTier = Customer::where('cafe_id', $cafeId)
            ->whereNotNull('tier_id')
            ->groupBy('tier_id')
            ->selectRaw('tier_id, count(*) as count')
            ->pluck('count', 'tier_id');

        $tierDistribution = [];
        foreach ($tiers as $tier) {
            $tierDistribution[] = [
                'tier' => $tier,
                'count' => (int) ($countsByTier[$tier->id] ?? 0),
            ];
        }
        $noTierCount = Customer::where('cafe_id', $cafeId)->whereNull('tier_id')->count();
        $totalCashbackIssued = CustomerCashback::whereHas('customer', function ($q) use ($cafeId) {
            $q->where('cafe_id', $cafeId);
        })->where('type', 'credit')->sum('amount') ?? 0;

        return view('admin.loyalty.index', compact(
            'settings', 'tiers', 'totalCustomers', 'tierDistribution', 'noTierCount', 'totalCashbackIssued'
        ));
    }

    /**
     * List and manage tiers.
     */
    public function tiers()
    {
        $tiers = CustomerTier::orderBy('min_spend')->get();
        return view('admin.loyalty.tiers', compact('tiers'));
    }

    /**
     * Show create tier form.
     */
    public function createTier()
    {
        return view('admin.loyalty.tier-form', ['tier' => null]);
    }

    /**
     * Store new tier.
     */
    public function storeTier(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'min_spend' => 'required|numeric|min:0',
            'discount_percentage' => 'required|integer|min:0|max:100',
            'free_delivery' => 'boolean',
            'badge_color' => 'required|string|max:20',
        ]);

        $validated['free_delivery'] = $request->boolean('free_delivery');

        CustomerTier::create($validated);

        return redirect()->route('admin.loyalty.tiers')
            ->with('success', 'لول جدید با موفقیت ساخته شد.');
    }

    /**
     * Show edit tier form.
     */
    public function editTier(CustomerTier $tier)
    {
        return view('admin.loyalty.tier-form', compact('tier'));
    }

    /**
     * Update tier.
     */
    public function updateTier(Request $request, CustomerTier $tier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'min_spend' => 'required|numeric|min:0',
            'discount_percentage' => 'required|integer|min:0|max:100',
            'free_delivery' => 'boolean',
            'badge_color' => 'required|string|max:20',
        ]);

        $validated['free_delivery'] = $request->boolean('free_delivery');

        $tier->update($validated);

        return redirect()->route('admin.loyalty.tiers')
            ->with('success', "لول «{$tier->name}» ویرایش شد.");
    }

    /**
     * Delete tier.
     */
    public function deleteTier(CustomerTier $tier)
    {
        // Move customers to no tier
        Customer::where('tier_id', $tier->id)->update(['tier_id' => null]);
        $tier->delete();

        return redirect()->route('admin.loyalty.tiers')
            ->with('success', 'لول حذف شد و مشتریانش بدون لول شدن.');
    }

    /**
     * Show loyalty settings form.
     */
    public function settings()
    {
        $cafeId = auth()->user()->cafe_id ?? 1;
        $settings = LoyaltySetting::getForCafe($cafeId);
        return view('admin.loyalty.settings', compact('settings'));
    }

    /**
     * Update loyalty settings.
     */
    public function updateSettings(Request $request)
    {
        $cafeId = auth()->user()->cafe_id ?? 1;
        $settings = LoyaltySetting::getForCafe($cafeId);

        $validated = $request->validate([
            'is_active' => 'boolean',
            'cashback_percent' => 'required|numeric|min:0|max:50',
            'min_order_amount' => 'required|numeric|min:0',
            'points_per_order_unit' => 'required|integer|min:1',
            'welcome_bonus_points' => 'required|integer|min:0',
            'is_birthday_reward_enabled' => 'nullable|boolean',
            'birthday_reward_type' => 'nullable|in:wallet,points',
            'birthday_reward_amount' => 'nullable|numeric|min:0',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_birthday_reward_enabled'] = $request->boolean('is_birthday_reward_enabled');
        $validated['birthday_reward_type'] = $request->input('birthday_reward_type', $settings->birthday_reward_type ?? 'wallet');
        $validated['birthday_reward_amount'] = (float) $request->input('birthday_reward_amount', $settings->birthday_reward_amount ?? 50000);

        $settings->update($validated);

        return redirect()->route('admin.loyalty.settings')
            ->with('success', 'تنظیمات باشگاه مشتریان و هدیه تولد با موفقیت ذخیره شد.');
    }
}

