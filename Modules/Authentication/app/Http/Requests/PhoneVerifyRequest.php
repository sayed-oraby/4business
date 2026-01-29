<?php

namespace Modules\Authentication\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PhoneVerifyRequest extends FormRequest
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
            'phone' => 'required|string',
            'otp' => 'required|string', // Accepting string "1234"
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
