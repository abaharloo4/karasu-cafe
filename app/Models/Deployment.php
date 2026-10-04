<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deployment extends Model
{
    use HasFactory;

    protected $fillable = [
        'cafe_id',
        'commit_sha',
        'commit_message',
        'commit_author',
        'started_at',
        'finished_at',
        'status',
        'log',
        'backup_path',
        'triggered_by',
        'cpanel_task_id',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function cafe(): BelongsTo
    {
        return $this->belongsTo(Cafe::class);
    }

    public function triggerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }

    /**
     * Get short commit SHA (7 chars).
     */
    public function getShortShaAttribute(): string
    {
        return substr($this->commit_sha, 0, 7);
    }

    /**
     * Persian label for deployment status.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'در صف انتظار',
            'running' => 'در حال استقرار',
            'success' => 'موفقیت‌آمیز',
            'failed' => 'ناموفق',
            'rolled_back' => 'بازگشت به نسخه قبل (Rollback)',
            default => $this->status,
        };
    }

    /**
     * Badge CSS class for status.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
            'running' => 'bg-blue-50 text-blue-700 border-blue-200 animate-pulse',
            'success' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'failed' => 'bg-rose-50 text-rose-700 border-rose-200',
            'rolled_back' => 'bg-purple-50 text-purple-700 border-purple-200',
            default => 'bg-slate-50 text-slate-700 border-slate-200',
        };
    }
}
