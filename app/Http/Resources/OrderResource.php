<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'status' => $this->status,
            'total_price' => (float) $this->total_price,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
