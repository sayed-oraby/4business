<?php

namespace Modules\Shipping\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'label' => ['nullable', 'string', 'max:100'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'shipping_country_id' => ['required', 'exists:shipping_countries,id'],
            'state_code' => ['nullable', 'string', 'max:50'],
            'state_name_en' => ['nullable', 'string', 'max:255'],
            'state_name_ar' => ['nullable', 'string', 'max:255'],
            'city_code' => ['nullable', 'string', 'max:50'],
            'city_name_en' => ['nullable', 'string', 'max:255'],
            'city_name_ar' => ['nullable', 'string', 'max:255'],
            'block' => ['nullable', 'string', 'max:255'],
            'street' => ['nullable', 'string', 'max:255'],
            'avenue' => ['nullable', 'string', 'max:255'],
            'building' => ['nullable', 'string', 'max:255'],
            'floor' => ['nullable', 'string', 'max:50'],
            'apartment' => ['nullable', 'string', 'max:50'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'additional_details' => ['nullable', 'string'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'is_default_shipping' => ['boolean'],
            'is_default_billing' => ['boolean'],
        ];
    }
}
