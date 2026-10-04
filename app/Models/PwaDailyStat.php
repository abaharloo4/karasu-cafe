<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PwaDailyStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'panel',
        'date',
        'installs_count',
        'active_subscribers_count',
        'page_views_count',
        'notifications_sent_count',
        'notifications_opened_count',
        'page_breakdown',
    ];

    protected $casts = [
        'page_breakdown' => 'array',
        'installs_count' => 'integer',
        'active_subscribers_count' => 'integer',
        'page_views_count' => 'integer',
        'notifications_sent_count' => 'integer',
        'notifications_opened_count' => 'integer',
    ];

    /**
     * Compute notification open rate percentage.
     */
    public function getOpenRatePercentAttribute(): float
    {
        if ($this->notifications_sent_count === 0) {
            return 0.0;
        }
        return round(($this->notifications_opened_count / $this->notifications_sent_count) * 100, 1);
    }
}
