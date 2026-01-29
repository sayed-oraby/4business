<?php

namespace Modules\Shipping\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Shipping\Models\ShippingCountry;

class StoreCountryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->can('shipping_countries.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'iso2' => ['required', 'string', 'size:2', 'unique:shipping_countries,iso2'],
            'iso3' => ['nullable', 'string', 'size:3'],
            'phone_code' => ['nullable', 'string', 'max:10'],
            'name_en' => ['required', 'string', 'max:191'],
            'name_ar' => ['required', 'string', 'max:191'],
            'flag_svg' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'is_shipping_enabled' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ];
    }
}
