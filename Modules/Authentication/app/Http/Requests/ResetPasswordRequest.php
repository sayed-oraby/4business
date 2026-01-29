<?php

namespace Modules\Authentication\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
            'otp' => ['required', 'digits:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => __('authentication::messages.fields.email'),
            'otp' => __('authentication::messages.fields.otp'),
            'password' => __('authentication::messages.fields.password'),
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => __('authentication::validation.email.required'),
            'email.email' => __('authentication::validation.email.email'),
            'email.exists' => __('authentication::validation.email.exists'),
            'otp.required' => __('authentication::validation.otp.required'),
            'otp.digits' => __('authentication::validation.otp.digits'),
            'password.required' => __('authentication::validation.password.required'),
            'password.min' => __('authentication::validation.password.min'),
            'password.confirmed' => __('authentication::validation.password.confirmed'),
        ];
    }
}
