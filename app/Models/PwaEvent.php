<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PwaEvent extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'panel',
        'event_type',
        'subscribable_type',
        'subscribable_id',
        'path',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->created_at = $model->created_at ?: now();
        });
    }

    public function subscribable(): MorphTo
    {
        return $this->morphTo();
    }
}
