<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LuckyWheelSpin extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'lucky_wheel_prize_id',
        'spin_date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function prize(): BelongsTo
    {
        return $this->belongsTo(LuckyWheelPrize::class, 'lucky_wheel_prize_id');
    }

    /**
     * Scope spins by cafe_id via customer relationship.
     */
    public function scopeCafe($query, $cafeId)
    {
        return $query->whereHas('customer', fn($q) => $q->where('cafe_id', $cafeId));
    }
}
