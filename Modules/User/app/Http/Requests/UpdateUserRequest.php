<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')?->can('users.update') ?? false;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'mobile' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'confirmed', 'min:8'],
            'birthdate' => ['nullable', 'date'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'remove_avatar' => ['nullable', 'boolean'],
            // 'office_request_status' => ['nullable', 'string', 'in:pending,approved,rejected'],
            // 'office_rejection_reason' => ['nullable', 'string', 'max:1000', 'required_if:office_request_status,rejected'],
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
            'remove_avatar' => __('user::users.form.remove_avatar'),
        ];
    }
}
