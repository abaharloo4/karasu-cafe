<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CouponAdminController extends Controller
{
    /**
     * Display a listing of coupons.
     */
    public function index(Request $request)
    {
        $cafeId = auth()->user()->cafe_id ?? 1;

        $query = Coupon::where('cafe_id', $cafeId);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('discount_type', $request->type);
        }

        $coupons = $query->latest()->paginate(15)->withQueryString();

        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * Show form for creating a new coupon.
     */
    public function create()
    {
        return view('admin.coupons.create');
    }

    /**
     * Store a newly created coupon with full WooCommerce fields.
     */
    public function store(Request $request)
    {
        $cafeId = auth()->user()->cafe_id ?? 1;

        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('coupons', 'code')->where('cafe_id', $cafeId),
            ],
            'title' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:1',
            'min_spend' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'first_order_only' => 'nullable|boolean',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'status' => 'required|in:active,inactive',
        ], [
            'code.required' => 'کد تخفیف الزامی است.',
            'code.unique' => 'این کد تخفیف قبلاً تعریف شده است.',
            'title.required' => 'عنوان یا مناسبت تخفیف الزامی است.',
            'discount_value.required' => 'میزان تخفیف الزامی است.',
            'expires_at.after_or_equal' => 'تاریخ انقضا باید بعد از تاریخ شروع باشد.',
        ]);

        $validated['cafe_id'] = $cafeId;
        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['first_order_only'] = $request->boolean('first_order_only');
        $validated['times_used'] = 0;

        Coupon::create($validated);

        return redirect()->route('admin.coupons.index')
            ->with('success', "کد تخفیف «{$validated['code']}» با موفقیت ایجاد گردید.");
    }

    /**
     * Show form for editing the coupon.
     */
    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    /**
     * Update the coupon.
     */
    public function update(Request $request, Coupon $coupon)
    {
        $cafeId = auth()->user()->cafe_id ?? 1;

        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('coupons', 'code')->where('cafe_id', $cafeId)->ignore($coupon->id),
            ],
            'title' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:1',
            'min_spend' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'first_order_only' => 'nullable|boolean',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'status' => 'required|in:active,inactive',
        ], [
            'code.required' => 'کد تخفیف الزامی است.',
            'code.unique' => 'این کد تخفیف قبلاً تعریف شده است.',
            'title.required' => 'عنوان یا مناسبت تخفیف الزامی است.',
            'discount_value.required' => 'میزان تخفیف الزامی است.',
            'expires_at.after_or_equal' => 'تاریخ انقضا باید بعد از تاریخ شروع باشد.',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['first_order_only'] = $request->boolean('first_order_only');

        $coupon->update($validated);

        return redirect()->route('admin.coupons.index')
            ->with('success', "کد تخفیف «{$coupon->code}» با موفقیت به‌روزرسانی شد.");
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggle(Coupon $coupon)
    {
        $newStatus = $coupon->status === 'active' ? 'inactive' : 'active';
        $coupon->update(['status' => $newStatus]);

        $statusText = $newStatus === 'active' ? 'فعال' : 'غیرفعال';
        return redirect()->back()
            ->with('success', "وضعیت کد تخفیف {$coupon->code} به {$statusText} تغییر یافت.");
    }

    /**
     * Delete a coupon.
     */
    public function destroy(Coupon $coupon)
    {
        $code = $coupon->code;
        $coupon->delete();

        return redirect()->route('admin.coupons.index')
            ->with('success', "کد تخفیف «{$code}» حذف شد.");
    }
}
