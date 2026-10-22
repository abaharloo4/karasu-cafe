<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'cafe_id',
        'category_id',
        'name',
        'type',
        'price',
        'stock',
        'description',
        'prep_time',
        'tasting_notes',
        'bean_type',
        'serving_type',
        'image',
        'status',
        'addons',
        'discount_percent',
        'discount_start_time',
        'discount_end_time',
        'discount_days',
        'is_hourly_discount_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'prep_time' => 'integer',
        'addons' => 'array',
        'discount_percent' => 'integer',
        'discount_days' => 'array',
        'is_hourly_discount_active' => 'boolean',
    ];

    /**
     * Check whether an hourly discount is currently active based on current day and time in Tehran/Shiraz.
     */
    public function isHourlyDiscountActive(): bool
    {
        if (!$this->is_hourly_discount_active || (int) $this->discount_percent <= 0) {
            return false;
        }

        $tehranNow = now()->setTimezone('Asia/Tehran');

        // Check configured days of week if specified
        if (!empty($this->discount_days) && is_array($this->discount_days)) {
            $allowedDays = array_map('strtolower', array_map('strval', $this->discount_days));
            $currentDayCode = strtolower($tehranNow->format('D')); // e.g. 'sat', 'sun'
            $currentDayFull = strtolower($tehranNow->format('l')); // e.g. 'saturday'
            $currentDayNum = (string) $tehranNow->dayOfWeek; // 0 (Sun) to 6 (Sat)

            if (!in_array($currentDayCode, $allowedDays) &&
                !in_array($currentDayFull, $allowedDays) &&
                !in_array($currentDayNum, $allowedDays)) {
                return false;
            }
        }

        if (empty($this->discount_start_time) || empty($this->discount_end_time) || $this->discount_start_time === $this->discount_end_time || ($this->discount_start_time === '00:00' && $this->discount_end_time === '23:59')) {
            return true;
        }

        $now = $tehranNow->format('H:i');
        $start = $this->discount_start_time;
        $end = $this->discount_end_time;

        if ($start <= $end) {
            return ($now >= $start && $now <= $end);
        } else {
            // Overnight window (e.g. 22:00 to 02:00)
            return ($now >= $start || $now <= $end);
        }
    }

    /**
     * Seconds remaining for the hourly discount until discount_end_time.
     */
    public function discountRemainingSeconds(): ?int
    {
        if (!$this->isHourlyDiscountActive()) {
            return null;
        }

        if (empty($this->discount_end_time)) {
            return null;
        }

        $tehranNow = now()->setTimezone('Asia/Tehran');
        $endParts = explode(':', $this->discount_end_time);
        $endHour = (int) ($endParts[0] ?? 0);
        $endMin = (int) ($endParts[1] ?? 0);

        $endDateTime = $tehranNow->copy()->setTime($endHour, $endMin, 0);

        if (!empty($this->discount_start_time) && $this->discount_start_time > $this->discount_end_time) {
            if ($tehranNow->format('H:i') >= $this->discount_start_time) {
                $endDateTime->addDay();
            }
        }

        $diff = $tehranNow->diffInSeconds($endDateTime, false);
        return $diff > 0 ? (int) $diff : 0;
    }

    /**
     * Human readable remaining time text for the discount window.
     */
    public function discountRemainingHuman(): ?string
    {
        $sec = $this->discountRemainingSeconds();
        if ($sec === null) {
            return !empty($this->discount_end_time) ? 'تا ساعت ' . $this->discount_end_time : 'تخفیف ویژه روز';
        }

        $hours = floor($sec / 3600);
        $minutes = floor(($sec % 3600) / 60);

        if ($hours > 0) {
            return "{$hours} ساعت و {$minutes} دقیقه مانده";
        }
        return "{$minutes} دقیقه مانده";
    }

    /**
     * Check whether product has available stock.
     */
    public function isInStock(): bool
    {
        return $this->stock === null || (int)$this->stock > 0;
    }

    /**
     * Check whether product is out of stock.
     */
    public function isOutOfStock(): bool
    {
        return $this->stock !== null && (int)$this->stock <= 0;
    }

    /**
     * Get effective discounted price if hourly discount is active.
     */
    public function getEffectivePriceAttribute(): float
    {
        $base = (float) $this->price;
        if ($this->isHourlyDiscountActive()) {
            $discount = round(($base * (int) $this->discount_percent) / 100);
            return max(0, $base - $discount);
        }
        return $base;
    }

    public function cafe(): BelongsTo
    {
        return $this->belongsTo(Cafe::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subscriptionPlans(): BelongsToMany
    {
        return $this->belongsToMany(SubscriptionPlan::class, 'subscription_plan_products');
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'customer_favorites')->withTimestamps();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Get image URL only if the file actually exists on disk or is an external URL.
     * Prevents calling deleted or non-existent image paths.
     */
    public function getImageAttribute($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        // External URLs (e.g. https://... or http://...)
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        // Direct static files in public directory (e.g. /images/... or images/...)
        $cleanRelative = ltrim($value, '/\\');
        $publicDirect = public_path($cleanRelative);
        if (file_exists($publicDirect) && is_file($publicDirect)) {
            return str_starts_with($value, '/') ? $value : '/' . $value;
        }

        // Local storage file path: strip leading /storage/ or storage/
        $cleanPath = ltrim(str_replace(['/storage/', 'storage/'], '', $value), '/\\');

        // Check if file exists in public/storage
        $publicStorage = public_path('storage/' . $cleanPath);
        if (file_exists($publicStorage) && is_file($publicStorage)) {
            return '/storage/' . $cleanPath;
        }

        // Check if file exists in storage/app/public
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($cleanPath)) {
            return '/storage/' . $cleanPath;
        }

        // Return path formatted with leading / so web route fallback can serve it
        return str_starts_with($value, '/') ? $value : '/' . $value;
    }

    /**
     * Get the raw database value for image column.
     */
    public function getRawImageAttribute(): ?string
    {
        return $this->attributes['image'] ?? null;
    }
}
