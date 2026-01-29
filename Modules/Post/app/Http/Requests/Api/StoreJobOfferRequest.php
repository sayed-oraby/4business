<?php

namespace Modules\Post\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'joining_date' => 'required|date|after:today',
            'salary' => 'required|numeric|min:0',
            'description' => 'required|string|min:10',
        ];
    }
}
