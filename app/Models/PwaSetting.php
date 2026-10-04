<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PwaSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'panel',
        'app_name',
        'short_name',
        'theme_color',
        'background_color',
        'icon_192',
        'icon_512',
        'icon_maskable',
        'start_url',
        'scope',
        'ga4_measurement_id',
        'notification_killswitches',
    ];

    protected $casts = [
        'notification_killswitches' => 'array',
    ];

    /**
     * Get settings for a panel with defaults fallback.
     */
    public static function forPanel(string $panel): self
    {
        return static::firstOrCreate(
            ['panel' => $panel],
            [
                'app_name' => $panel === 'admin' ? 'مدیریت بی کافه' : 'بی کافه',
                'short_name' => $panel === 'admin' ? 'مدیریت' : 'بی کافه',
                'theme_color' => $panel === 'admin' ? '#141D22' : '#387780',
                'background_color' => '#0F172A',
                'start_url' => $panel === 'admin' ? '/admin' : '/',
                'scope' => $panel === 'admin' ? '/admin/' : '/',
                'notification_killswitches' => [
                    'push_enabled' => true,
                    'allowed_categories' => $panel === 'admin'
                        ? ['new_order', 'payment_pending']
                        : ['order_status', 'rewards', 'marketing'],
                ],
            ]
        );
    }

    /**
     * Seed default settings for panels.
     */
    public static function seedDefaults(): void
    {
        foreach (['customer', 'admin'] as $panel) {
            static::forPanel($panel);
        }
    }

    /**
     * Push enabled accessor.
     */
    public function getPushEnabledAttribute(): bool
    {
        $switches = $this->notification_killswitches ?? [];
        return $switches['push_enabled'] ?? true;
    }

    /**
     * Allowed categories accessor.
     */
    public function getAllowedCategoriesAttribute(): array
    {
        $switches = $this->notification_killswitches ?? [];
        return $switches['allowed_categories'] ?? ['order_status', 'rewards', 'marketing', 'new_order', 'payment_pending', 'order_assigned', 'order_ready'];
    }

    /**
     * Check if a notification category is globally allowed for this panel.
     */
    public function isNotificationCategoryAllowed(string $category): bool
    {
        if (!$this->push_enabled) {
            return false;
        }

        $allowed = $this->allowed_categories;
        return in_array($category, $allowed);
    }
}
