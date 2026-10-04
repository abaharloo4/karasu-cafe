<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'cafe_id',
        'customer_id',
        'subscription_plan_id',
        'total_items',
        'remaining_items',
        'start_date',
        'expire_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expire_date' => 'date',
        'total_items' => 'integer',
        'remaining_items' => 'integer',
    ];

    public function cafe(): BelongsTo
    {
        return $this->belongsTo(Cafe::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function usages(): HasMany
    {
        return $this->hasMany(SubscriptionUsage::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active')
            ->where('expire_date', '>=', now()->toDateString())
            ->where('remaining_items', '>', 0);
    }

    /**
     * Calculate days remaining including today until expiration date.
     */
    public function getDaysRemainingAttribute(): int
    {
        if (!$this->expire_date) {
            return 0;
        }

        $today = Carbon::today();
        $expire = Carbon::parse($this->expire_date)->startOfDay();

        if ($expire->isPast() && !$expire->isToday()) {
            return 0;
        }

        return max(0, (int) $today->diffInDays($expire, false) + 1);
    }

    /**
     * Get effective remaining items (capped by remaining days since missed days are forfeited).
     */
    public function getEffectiveRemainingItemsAttribute(): int
    {
        return min((int) $this->remaining_items, $this->days_remaining);
    }

    /**
     * Sync and update remaining_items column in DB if missed days have reduced available credits.
     */
    public function syncMissedDaysCredit(): int
    {
        $effective = $this->effective_remaining_items;

        if ($effective < $this->remaining_items) {
            $this->update([
                'remaining_items' => $effective,
                'status' => $effective <= 0 ? 'expired' : $this->status,
            ]);
        }

        return $effective;
    }

    public function isValid(): bool
    {
        return $this->status === 'active'
            && $this->remaining_items > 0
            && $this->expire_date >= now()->startOfDay();
    }

    public function hasUsedToday(): bool
    {
        return $this->usages()
            ->where('used_date', now()->toDateString())
            ->exists();
    }
}
