<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'title',
        'status',
        'coming_soon_note',
        'is_active',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'settings' => \App\Casts\SafeEncryptedArray::class,
        ];
    }

    public function isActive(): bool
    {
        return ($this->status === 'active' || (empty($this->status) && $this->is_active));
    }

    public function isComingSoon(): bool
    {
        return $this->status === 'coming_soon';
    }

    public function isInactive(): bool
    {
        return $this->status === 'inactive' || (!$this->is_active && $this->status !== 'coming_soon');
    }

    public static function getSettings(string $key): array
    {
        try {
            $gateway = static::where('key', $key)->first();
            return $gateway?->settings ?? [];
        } catch (Throwable $e) {
            // Fallback for legacy unencrypted data in database
            $raw = DB::table('payment_gateways')->where('key', $key)->value('settings');
            if ($raw) {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    // Re-save encrypted
                    $gateway = static::where('key', $key)->first();
                    if ($gateway) {
                        $gateway->settings = $decoded;
                        $gateway->saveQuietly();
                    }
                    return $decoded;
                }
            }
            return [];
        }
    }
}
