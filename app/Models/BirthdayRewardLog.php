<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BirthdayRewardLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'coupon_id',
        'year',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'sent_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }
}
