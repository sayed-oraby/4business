<?php

namespace Modules\Shipping\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->can('shipping_countries.update') ?? false;
    }

    public function rules(): array
    {
        $cityId = $this->route('city')?->id;

        return [
            'shipping_state_id' => ['required', 'exists:shipping_states,id'],
            'code' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('shipping_cities', 'code')
                    ->where('shipping_state_id', $this->input('shipping_state_id'))
                    ->ignore($cityId),
            ],
            'name_en' => ['required', 'string', 'max:191'],
            'name_ar' => ['required', 'string', 'max:191'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }
}
