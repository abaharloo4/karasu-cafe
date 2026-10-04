<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerOtp extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'code',
        'attempts',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'attempts' => 'integer',
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function isValid(): bool
    {
        return is_null($this->used_at) && $this->expires_at->isFuture();
    }
}
