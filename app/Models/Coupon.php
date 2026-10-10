<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'cafe_id',
        'code',
        'title',
        'description',
        'discount_type',
        'discount_value',
        'min_spend',
        'max_discount',
        'points_required',
        'usage_limit',
        'usage_limit_per_user',
        'times_used',
        'first_order_only',
        'starts_at',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_spend' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'points_required' => 'integer',
        'usage_limit' => 'integer',
        'usage_limit_per_user' => 'integer',
        'times_used' => 'integer',
        'first_order_only' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function cafe(): BelongsTo
    {
        return $this->belongsTo(Cafe::class);
    }

    public function customerCoupons(): HasMany
    {
        return $this->hasMany(CustomerCoupon::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Check if the coupon is valid for given amount and customer.
     * Returns ['valid' => bool, 'message' => ?string, 'discount_amount' => float]
     */
    public function validateForCart(float $orderAmount, ?Customer $customer = null): array
    {
        if ($this->status !== 'active') {
            return [
                'valid' => false,
                'message' => 'این کد تخفیف در حال حاضر غیرفعال است.',
            ];
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return [
                'valid' => false,
                'message' => 'مهلت استفاده از این کد تخفیف هنوز آغاز نشده است.',
            ];
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return [
                'valid' => false,
                'message' => 'مهلت استفاده از این کد تخفیف به پایان رسیده است.',
            ];
        }

        if ($this->usage_limit !== null && $this->times_used >= $this->usage_limit) {
            return [
                'valid' => false,
                'message' => 'سقف تعداد استفاده از این کد تخفیف به پایان رسیده است.',
            ];
        }

        if ($this->min_spend !== null && $orderAmount < (float) $this->min_spend) {
            $formattedMin = number_format((float) $this->min_spend);
            return [
                'valid' => false,
                'message' => "حداقل مبلغ سفارش برای استفاده از این کد تخفیف {$formattedMin} تومان است.",
            ];
        }

        if ($this->first_order_only) {
            if (!$customer) {
                return [
                    'valid' => false,
                    'message' => 'این کد تخفیف مخصوص اولین خرید است. لطفاً ابتدا وارد حساب کاربری خود شوید.',
                ];
            }

            $hasPreviousOrders = Order::where('customer_id', $customer->id)
                ->whereIn('status', ['completed', 'delivered', 'preparing', 'on_the_way'])
                ->exists();

            if ($hasPreviousOrders) {
                return [
                    'valid' => false,
                    'message' => 'این کد تخفیف فقط برای اولین سفارش کاربران جدید قابل استفاده است.',
                ];
            }
        }

        if ($this->usage_limit_per_user !== null && $customer) {
            $userUsageCount = Order::where('customer_id', $customer->id)
                ->where('coupon_id', $this->id)
                ->where('status', '!=', 'cancelled')
                ->count();

            if ($userUsageCount >= $this->usage_limit_per_user) {
                return [
                    'valid' => false,
                    'message' => 'شما به حداکثر دفعات مجاز استفاده از این کد تخفیف رسیده‌اید.',
                ];
            }
        }

        // Check customer specific coupon bindings
        $personalBinding = CustomerCoupon::where('coupon_id', $this->id)->first();
        if ($personalBinding) {
            if (!$customer) {
                return [
                    'valid' => false,
                    'message' => 'این کد تخفیف اختصاصی است. لطفاً برای استفاده وارد حساب کاربری خود شوید.',
                ];
            }
            if ($personalBinding->customer_id !== $customer->id) {
                return [
                    'valid' => false,
                    'message' => 'این کد تخفیف متعلق به شماره همراه دیگری است.',
                ];
            }
        }

        $discountAmount = $this->calculateDiscount($orderAmount);

        return [
            'valid' => true,
            'message' => 'کد تخفیف با موفقیت اعمال شد.',
            'discount_amount' => $discountAmount,
        ];
    }

    /**
     * Calculate discount amount in Toman based on order total.
     */
    public function calculateDiscount(float $orderAmount): float
    {
        $discount = 0.0;

        if ($this->discount_type === 'percent') {
            $discount = ($orderAmount * (float) $this->discount_value) / 100.0;
            if ($this->max_discount !== null && $discount > (float) $this->max_discount) {
                $discount = (float) $this->max_discount;
            }
        } else {
            $discount = (float) $this->discount_value;
        }

        // Discount cannot exceed order amount
        return min($discount, $orderAmount);
    }
}
