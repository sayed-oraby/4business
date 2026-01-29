<?php

namespace Modules\Post\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreFavouriteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
           'post_id' => 'required|exists:posts,uuid',
        ];
    }
}
