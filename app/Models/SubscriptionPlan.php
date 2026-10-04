<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'cafe_id',
        'name',
        'total_items',
        'duration_days',
        'price',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'total_items' => 'integer',
        'duration_days' => 'integer',
    ];

    public function cafe(): BelongsTo
    {
        return $this->belongsTo(Cafe::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'subscription_plan_products');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }
}
