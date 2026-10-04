<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'min_spend',
        'discount_percentage',
        'free_delivery',
        'badge_color',
    ];

    protected function casts(): array
    {
        return [
            'min_spend' => 'decimal:2',
            'discount_percentage' => 'integer',
            'free_delivery' => 'boolean',
        ];
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class, 'tier_id');
    }
}
