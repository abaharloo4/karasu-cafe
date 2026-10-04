<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerCoupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'coupon_id',
        'used_at',
    ];

    protected $casts = [
        'used_at' => 'datetime',
    ];

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }
}
