<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class FulfillmentSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'cafe_id',
        'key',
        'title',
        'subtitle',
        'description',
        'status',
        'coming_soon_note',
        'base_fee',
        'min_order_amount',
        'icon',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'base_fee' => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'sort_order' => 'integer',
        ];
    }

    public function cafe(): BelongsTo
    {
        return $this->belongsTo(Cafe::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isComingSoon(): bool
    {
        return $this->status === 'coming_soon';
    }

    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    /**
     * Get all fulfillment settings for a cafe, ordered by sort_order.
     * Seeds default methods if table is empty for the cafe.
     */
    public static function getForCafe(int $cafeId = 1): Collection
    {
        $settings = static::where('cafe_id', $cafeId)->orderBy('sort_order')->get();

        if ($settings->isEmpty()) {
            static::seedDefaultsForCafe($cafeId);
            $settings = static::where('cafe_id', $cafeId)->orderBy('sort_order')->get();
        }

        return $settings->keyBy('key');
    }

    /**
     * Seed default 3 methods if missing.
     */
    public static function seedDefaultsForCafe(int $cafeId = 1): void
    {
        $defaults = [
            [
                'cafe_id' => $cafeId,
                'key' => 'courier',
                'title' => 'ارسال با پیک شیراز',
                'subtitle' => 'تحویل درب آدرس توسط ناوگان پیک کافه',
                'description' => 'سفارش شما پس از آماده‌سازی، توسط پیک ارسال خواهد شد.',
                'status' => 'active',
                'coming_soon_note' => null,
                'base_fee' => 0,
                'min_order_amount' => 0,
                'icon' => 'truck',
                'sort_order' => 1,
            ],
            [
                'cafe_id' => $cafeId,
                'key' => 'takeaway',
                'title' => 'بیرون‌بر (تحویل حضوری)',
                'subtitle' => 'دریافت روی کانتر کافه بدون معطلی',
                'description' => 'سفارش در کافه بسته‌بندی و آماده تحویل حضوری می‌شود.',
                'status' => 'active',
                'coming_soon_note' => null,
                'base_fee' => 0,
                'min_order_amount' => 0,
                'icon' => 'shopping-bag',
                'sort_order' => 2,
            ],
            [
                'cafe_id' => $cafeId,
                'key' => 'dine_in',
                'title' => 'سرو در سالن کافه',
                'subtitle' => 'پذیرایی در فضای اختصاصی بی کافه',
                'description' => 'انتخاب شماره میز یا سرو پای بار کافه.',
                'status' => 'active',
                'coming_soon_note' => null,
                'base_fee' => 0,
                'min_order_amount' => 0,
                'icon' => 'coffee',
                'sort_order' => 3,
            ],
        ];

        foreach ($defaults as $item) {
            static::firstOrCreate(['cafe_id' => $cafeId, 'key' => $item['key']], $item);
        }
    }
}
