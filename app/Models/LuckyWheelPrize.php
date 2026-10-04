<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LuckyWheelPrize extends Model
{
    use HasFactory;

    protected $fillable = [
        'cafe_id',
        'title',
        'prize_type',
        'prize_value',
        'win_probability',
        'status',
    ];
}
