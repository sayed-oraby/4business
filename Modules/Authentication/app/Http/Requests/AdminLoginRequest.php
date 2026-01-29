<?php

namespace Modules\Authentication\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminLoginRequest extends FormRequest
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
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('remember')) {
            $this->merge([
                'remember' => $this->boolean('remember'),
            ]);
        }
    }

    public function attributes(): array
    {
        return [
            'email' => __('authentication::messages.fields.email'),
            'password' => __('authentication::messages.fields.password'),
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => __('authentication::validation.email.required'),
            'email.email' => __('authentication::validation.email.email'),
            'password.required' => __('authentication::validation.password.required'),
            'remember.boolean' => __('authentication::validation.remember.boolean'),
        ];
    }
}
