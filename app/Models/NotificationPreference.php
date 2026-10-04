<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class NotificationPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscribable_type',
        'subscribable_id',
        'category',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function subscribable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Check if a category is enabled for a subscriber.
     * Note: Marketing is OFF by default; other categories are ON by default.
     */
    public static function isEnabled(Model $subscribable, string $category): bool
    {
        $pref = static::where('subscribable_type', $subscribable->getMorphClass())
            ->where('subscribable_id', $subscribable->getKey())
            ->where('category', $category)
            ->first();

        if ($pref !== null) {
            return (bool) $pref->enabled;
        }

        // Default: marketing is opt-in (false by default), transactional is true by default
        return $category !== 'marketing';
    }
}
