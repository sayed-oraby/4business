<?php

namespace Modules\Category\Http\Requests\Api;

use App\Http\Requests\ApiFormRequest;

class ListCategoryRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['nullable', 'string', 'in:draft,active,archived,all'],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'featured' => ['nullable', 'boolean'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
            'page' => ['nullable', 'integer', 'min:1'],
            'pagination' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
