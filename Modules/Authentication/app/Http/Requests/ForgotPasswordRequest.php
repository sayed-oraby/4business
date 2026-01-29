<?php

namespace Modules\Authentication\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
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
            'email' => ['required', 'email', 'exists:users,email'],
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => __('authentication::messages.fields.email'),
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => __('authentication::validation.email.required'),
            'email.email' => __('authentication::validation.email.email'),
            'email.exists' => __('authentication::validation.email.exists'),
        ];
    }
}
