<?php

namespace Modules\Blog\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class StoreTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('blogs.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'array'],
            'title.en' => ['required_without:title.ar', 'string', 'max:100'],
            'title.ar' => ['required_without:title.en', 'string', 'max:100'],
        ];
    }
}
