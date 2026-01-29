<?php

namespace Modules\Shipping\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ListStatesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'country' => ['required', 'string', 'size:2'],
            'search' => ['nullable', 'string', 'max:191'],
        ];
    }
}
