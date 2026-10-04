<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'cafe_id',
        'name',
        'description',
        'delivery_price',
        'min_order_free_delivery',
        'center_lat',
        'center_lng',
        'radius_km',
        'polygon_coordinates',
        'boundary',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'delivery_price' => 'decimal:2',
            'min_order_free_delivery' => 'decimal:2',
            'center_lat' => 'decimal:7',
            'center_lng' => 'decimal:7',
            'radius_km' => 'decimal:2',
            'polygon_coordinates' => 'array',
            'boundary' => 'array',
        ];
    }

    public function cafe(): BelongsTo
    {
        return $this->belongsTo(Cafe::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Checks if this delivery zone contains the specified point.
     */
    public function containsPoint(float $lat, float $lng): bool
    {
        $geoService = app(\App\Services\Geo\PointInPolygonService::class);

        // 1. Check GeoJSON boundary polygon first
        $boundary = $this->boundary ?? $this->polygon_coordinates;
        if (!empty($boundary)) {
            return $geoService->contains($lat, $lng, $boundary);
        }

        // 2. Fallback to circle radius check if lat/lng/radius are configured
        if ($this->center_lat && $this->center_lng && $this->radius_km) {
            $dist = $geoService->haversineDistance($lat, $lng, (float)$this->center_lat, (float)$this->center_lng);
            return $dist <= (float)$this->radius_km;
        }

        return false;
    }
}
