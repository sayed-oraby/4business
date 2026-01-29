<?php

namespace Modules\Cart\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'currency' => $this->currency,
            'status' => $this->status,
            'subtotal' => (float) $this->subtotal,
            'discount_total' => (float) $this->discount_total,
            'grand_total' => (float) $this->grand_total,
            'last_activity_at' => optional($this->last_activity_at)->toIso8601String(),
            'expires_at' => optional($this->expires_at)->toIso8601String(),
            'items' => CartItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
