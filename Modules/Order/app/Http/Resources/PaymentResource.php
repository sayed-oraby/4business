<?php

namespace Modules\Order\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ref_number' => $this->ref_number,
            'provider' => $this->provider,
            'provider_label' => $this->provider ? __("order::messages.payment_provider.{$this->provider}") : null,
            'amount' => (float) $this->amount,
            'currency' => $this->currency,
            'status' => $this->status,
            'status_label' => $this->status ? __("order::messages.order.payment_status.{$this->status}") : null,
            'invoice_url' => $this->invoice_url,
            'callback_url' => $this->callback_url,
            'created_at' => $this->created_at,
        ];
    }
}
