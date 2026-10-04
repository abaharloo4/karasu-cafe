<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cafe extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'address',
        'order_start_time',
        'order_end_time',
        'is_ordering_open',
        'closed_message',
    ];

    protected $casts = [
        'is_ordering_open' => 'boolean',
    ];

    /**
     * Check if ordering is currently open based on shift hours and admin toggle.
     */
    public function isOrderingOpen(): bool
    {
        if ($this->is_ordering_open === false) {
            return false;
        }

        if (empty($this->order_start_time) || empty($this->order_end_time)) {
            return true;
        }

        $now = now()->setTimezone('Asia/Tehran')->format('H:i');
        $start = substr($this->order_start_time, 0, 5);
        $end = substr($this->order_end_time, 0, 5);

        if ($start <= $end) {
            return $now >= $start && $now <= $end;
        } else {
            // Overnight shift (e.g. 18:00 to 02:00)
            return $now >= $start || $now <= $end;
        }
    }

    public function getFormattedShiftHours(): string
    {
        $start = substr($this->order_start_time ?? '08:00', 0, 5);
        $end = substr($this->order_end_time ?? '22:00', 0, 5);
        return "{$start} الی {$end}";
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function subscriptionPlans(): HasMany
    {
        return $this->hasMany(SubscriptionPlan::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
