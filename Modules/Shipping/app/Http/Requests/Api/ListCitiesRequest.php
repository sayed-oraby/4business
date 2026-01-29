<?php

namespace Modules\Shipping\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ListCitiesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'country' => ['required', 'string', 'size:2'],
            'state' => ['nullable', 'string', 'max:10'],
            'search' => ['nullable', 'string', 'max:191'],
        ];
    }
}
