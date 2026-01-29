<?php

namespace Modules\Authentication\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
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
            // 'birthdate' => ['nullable', 'date'],
            // 'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'account_type' => 'required|in:individual,office',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,mobile',
            'password' => 'required|string|min:6|confirmed',
            'email' => 'nullable|email|unique:users,email'
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('user::users.form.name'),
            'email' => __('authentication::messages.fields.email'),
            'password' => __('authentication::messages.fields.password'),
            'phone' => __('user::users.form.mobile'),
            'birthdate' => __('user::users.form.birthdate'),
            'gender' => __('user::users.form.gender'),
        ];
    }
}
