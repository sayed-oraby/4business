<?php

namespace Modules\Shipping\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->can('shipping_countries.update') ?? false;
    }

    public function rules(): array
    {
        return [
            'shipping_country_id' => ['required', 'exists:shipping_countries,id'],
            'code' => ['required', 'string', 'max:10', Rule::unique('shipping_states', 'code')->where('shipping_country_id', $this->input('shipping_country_id'))],
            'name_en' => ['required', 'string', 'max:191'],
            'name_ar' => ['required', 'string', 'max:191'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }
}
