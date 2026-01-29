<?php

namespace Modules\Shipping\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->can('shipping_countries.update') ?? false;
    }

    public function rules(): array
    {
        return [
            'calculation_type' => ['required', 'in:flat,weight,order_total'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'price_per_kg' => ['nullable', 'numeric', 'min:0'],
            'free_shipping_over' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'delivery_estimate_en' => ['nullable', 'string', 'max:255'],
            'delivery_estimate_ar' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'shipping_state_id' => ['nullable', 'integer', 'exists:shipping_states,id'],
            'shipping_city_id' => ['nullable', 'integer', 'exists:shipping_cities,id'],
        ];
    }
}
