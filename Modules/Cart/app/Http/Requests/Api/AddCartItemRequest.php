<?php

namespace Modules\Cart\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class AddCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'guest_uuid' => ['nullable', 'uuid'],
            'options' => ['nullable', 'array'],
        ];
    }
}
