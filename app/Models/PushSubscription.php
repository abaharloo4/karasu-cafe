<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PushSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscribable_type',
        'subscribable_id',
        'endpoint',
        'endpoint_hash',
        'p256dh',
        'auth',
        'panel',
        'user_agent',
    ];

    public function subscribable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Compute SHA256 of endpoint.
     */
    public static function hashEndpoint(string $endpoint): string
    {
        return hash('sha256', $endpoint);
    }
}
