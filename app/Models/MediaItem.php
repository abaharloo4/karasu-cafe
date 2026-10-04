<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MediaItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cafe_id',
        'filename',
        'path',
        'disk',
        'alt_text',
        'title',
        'folder',
        'size',
        'mime_type',
    ];

    public function cafe(): BelongsTo
    {
        return $this->belongsTo(Cafe::class);
    }

    public function getNameAttribute(): string
    {
        return $this->filename;
    }

    /**
     * Get full absolute public asset URL.
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . ltrim($this->path, '/\\'));
    }

    /**
     * Get relative public path starting with /storage/.
     */
    public function getRelativeUrlAttribute(): string
    {
        return '/storage/' . ltrim(str_replace('\\', '/', $this->path), '/');
    }

    /**
     * Human-readable size in KB / MB.
     */
    public function getFormattedSizeAttribute(): string
    {
        if ($this->size >= 1048576) {
            return round($this->size / 1048576, 1) . ' MB';
        }
        return round($this->size / 1024) . ' KB';
    }
}
