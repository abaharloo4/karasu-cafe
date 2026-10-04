<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'cafe_id',
        'is_active',
        'cashback_percent',
        'min_order_amount',
        'points_per_order_unit',
        'welcome_bonus_points',
        'birthday_reward_amount',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'cashback_percent' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'points_per_order_unit' => 'integer',
        'welcome_bonus_points' => 'integer',
        'birthday_reward_amount' => 'decimal:2',
    ];

    public function cafe(): BelongsTo
    {
        return $this->belongsTo(Cafe::class);
    }

    protected static function booted(): void
    {
        static::saved(fn ($setting) => \Illuminate\Support\Facades\Cache::forget("settings_loyalty_{$setting->cafe_id}"));
        static::deleted(fn ($setting) => \Illuminate\Support\Facades\Cache::forget("settings_loyalty_{$setting->cafe_id}"));
    }

    public static function getForCafe(int $cafeId = 1): self
    {
        return \Illuminate\Support\Facades\Cache::remember("settings_loyalty_{$cafeId}", 3600, function () use ($cafeId) {
            return static::firstOrCreate(
                ['cafe_id' => $cafeId],
                [
                    'is_active' => true,
                    'cashback_percent' => 5.00,
                    'min_order_amount' => 0.00,
                    'points_per_order_unit' => 10000,
                    'welcome_bonus_points' => 20,
                    'birthday_reward_amount' => 50000.00,
                ]
            );
        });
    }
}
