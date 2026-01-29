<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->can('users.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', 'min:8'],
            'birthdate' => ['nullable', 'date'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('user::users.form.name'),
            'email' => __('user::users.form.email'),
            'mobile' => __('user::users.form.mobile'),
            'password' => __('user::users.form.password'),
            'password_confirmation' => __('user::users.form.password_confirmation'),
            'birthdate' => __('user::users.form.birthdate'),
            'gender' => __('user::users.form.gender'),
            'roles' => __('user::users.form.roles'),
            'roles.*' => __('user::users.form.roles'),
            'avatar' => __('user::users.form.avatar'),
        ];
    }
}
