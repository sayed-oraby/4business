<?php

namespace Modules\Order\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Ensure status is loaded and title uses accessor (localized)
        // Accessing ->title will trigger getTitleAttribute() accessor
        // which uses app()->getLocale() to return the correct translation
        $statusTitle = $this->status?->title;

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'guest_uuid' => $this->guest_uuid,
            'status' => $statusTitle,
            'status_code' => $this->status?->code,
            'order_status_id' => $this->order_status_id,
            'status_color' => $this->status?->color,
            'payment_status' => $this->payment_status,
            'payment_status_label' => $this->payment_status ? __("order::messages.order.payment_status.{$this->payment_status}") : null,
            'currency' => $this->currency,
            'subtotal' => (float) $this->subtotal,
            'discount_total' => (float) $this->discount_total,
            'shipping_total' => (float) $this->shipping_total,
            'tax_total' => (float) $this->tax_total,
            'grand_total' => (float) $this->grand_total,
            'items_count' => (int) $this->items_count,
            'items_qty' => (int) $this->items_qty,
            'shipping_address' => $this->whenLoaded('shippingAddress', function () {
                $address = $this->shippingAddress;

                return $address ? [
                    'id' => $address->id,
                    'user_address_id' => $address->user_address_id,
                    'full_name' => $address->full_name,
                    'phone' => $address->phone,
                    'address' => $address->address,
                    'city' => $address->city,
                    'state' => $address->state,
                    'country' => $address->country,
                    'postal_code' => $address->postal_code,
                ] : null;
            }),
            'placed_at' => $this->placed_at,
            'paid_at' => $this->paid_at,
            'created_at' => $this->created_at?->toDateTimeString(),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
        ];
    }
}
