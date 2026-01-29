<?php

namespace Modules\Authentication\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // 'email' => ['required', 'email'],
            // 'password' => ['required', 'string'],
            // 'guest_uuid' => ['nullable', 'uuid'],
            'phone' => 'required|string',
            'password' => 'required|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'phone' => __('user::users.form.mobile'),
            'password' => __('authentication::messages.fields.password'),
        ];
    }
}
