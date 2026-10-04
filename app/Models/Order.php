<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'cafe_id',
        'order_number',
        'customer_id',
        'type',
        'order_type',
        'fulfillment_type',
        'table_number',
        'delivery_address_id',
        'payment_method',
        'status',
        'total_price',
        'discount_amount',
        'delivery_fee',
        'final_payable',
        'notes',
        'estimated_prep_time',
        'is_queued_for_next_shift',
        'created_by',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'final_payable' => 'decimal:2',
        'estimated_prep_time' => 'integer',
        'is_queued_for_next_shift' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->order_number)) {
                $orderNumber = (string) random_int(10000, 99999);
                while (static::where('order_number', $orderNumber)->exists()) {
                    $orderNumber = (string) random_int(10000, 99999);
                }
                $order->order_number = $orderNumber;
            }
        });
    }

    public function cafe(): BelongsTo
    {
        return $this->belongsTo(Cafe::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function deliveryAddress(): BelongsTo
    {
        return $this->belongsTo(CustomerAddress::class, 'delivery_address_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isDineIn(): bool
    {
        return $this->order_type === 'dine_in';
    }

    public function isTakeaway(): bool
    {
        return $this->order_type === 'takeaway' || ($this->fulfillment_type === 'pickup' && $this->order_type !== 'dine_in');
    }

    public function isCourier(): bool
    {
        return $this->order_type === 'courier' || $this->order_type === 'delivery' || $this->fulfillment_type === 'delivery';
    }

    public function isPickup(): bool
    {
        return !$this->isCourier();
    }

    public function isDelivery(): bool
    {
        return $this->isCourier();
    }

    public function getFulfillmentLabel(): string
    {
        if ($this->isDineIn()) {
            return 'سرو در سالن (حضوری)' . ($this->table_number ? ' - میز ' . $this->table_number : '');
        }
        if ($this->isTakeaway()) {
            return 'بیرون‌بر (تحویل حضوری)';
        }
        if ($this->isCourier()) {
            return 'ارسال با پیک (شیراز)';
        }
        return 'تحویل حضوری';
    }
}
