<?php

namespace Modules\Order\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'sku' => $this->sku,
            'title' => $this->title,
            'qty' => (int) $this->qty,
            'unit_price' => (float) $this->unit_price,
            'line_total' => (float) $this->line_total,
            'discount_total' => (float) $this->discount_total,
            'tax_total' => (float) $this->tax_total,
            'weight' => (float) $this->weight,
        ];
    }
}
