<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'title',
        'address_line',
        'latitude',
        'longitude',
        'delivery_zone_id',
        'building_unit',
        'postal_code',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_default' => 'boolean',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function deliveryZone(): BelongsTo
    {
        return $this->belongsTo(DeliveryZone::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'delivery_address_id');
    }
}
