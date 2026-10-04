<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'cafe_id',
        'code',
        'title',
        'discount_type',
        'discount_value',
        'points_required',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
