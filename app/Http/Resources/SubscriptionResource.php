<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'plan_name' => $this->plan?->name,
            'total_items' => $this->total_items,
            'remaining_items' => $this->remaining_items,
            'start_date' => $this->start_date?->toDateString(),
            'expire_date' => $this->expire_date?->toDateString(),
            'status' => $this->status,
            'has_used_today' => $this->hasUsedToday(),
        ];
    }
}
