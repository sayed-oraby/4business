<?php

namespace Modules\Cart\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'guest_uuid' => ['nullable', 'uuid'],
            'user_address_id' => ['nullable', 'integer', 'exists:user_addresses,id'],
            'cart_id' => ['nullable', 'integer', 'exists:carts,id'],
        ];
    }
}
