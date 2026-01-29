<?php

namespace Modules\Blog\Http\Requests\Api;

use App\Http\Requests\ApiFormRequest;

class ListBlogRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['nullable', 'string', 'in:draft,published,archived,all'],
            'tag_id' => ['nullable', 'integer', 'exists:blog_tags,id'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
            'page' => ['nullable', 'integer', 'min:1'],
            'pagination' => ['nullable', 'integer', 'min:1'],
            'search' => ['nullable', 'string', 'max:255'],
        ];
    }
}
