<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'gateway',
        'transaction_id',
        'authority_or_ref',
        'card_pan_mask',
        'card_pan',
        'receipt_image',
        'tracking_number',
        'amount',
        'status',
        'admin_notes',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'verified_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function isSuccessful(): bool
    {
        return $this->status === 'successful';
    }

    public function getCardPanAttribute(): ?string
    {
        return $this->attributes['card_pan_mask'] ?? null;
    }

    public function setCardPanAttribute($value): void
    {
        $this->attributes['card_pan_mask'] = $value;
    }
}
