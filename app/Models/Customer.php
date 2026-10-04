<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'cafe_id',
        'name',
        'phone',
        'email',
        'address',
        'birthdate',
        'tier_id',
        'total_spent',
        'wallet_balance',
    ];

    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
            'total_spent' => 'decimal:2',
            'wallet_balance' => 'decimal:2',
        ];
    }

    public function cafe(): BelongsTo
    {
        return $this->belongsTo(Cafe::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->where('expire_date', '>=', now()->toDateString())
            ->where('remaining_items', '>', 0)
            ->latest();
    }

    public function subscriptionUsages(): HasMany
    {
        return $this->hasMany(SubscriptionUsage::class);
    }

    public function favorites(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'customer_favorites')->withTimestamps();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function loyaltyPoints(): HasMany
    {
        return $this->hasMany(LoyaltyPoint::class);
    }

    public function tier(): BelongsTo
    {
        return $this->belongsTo(CustomerTier::class, 'tier_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function defaultAddress(): HasOne
    {
        return $this->hasOne(CustomerAddress::class)->where('is_default', true);
    }

    public function birthdayLogs(): HasMany
    {
        return $this->hasMany(BirthdayRewardLog::class);
    }

    public function cashbacks(): HasMany
    {
        return $this->hasMany(CustomerCashback::class);
    }

    public function getTotalLoyaltyPointsAttribute(): int
    {
        return (int) $this->loyaltyPoints()->sum('points');
    }
}
