<?php

namespace Modules\Banner\Http\Requests\Api;

use App\Http\Requests\ApiFormRequest;

class ListBannerRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'placement' => ['nullable', 'string', 'max:100'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:20'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
