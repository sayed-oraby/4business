<?php

namespace Modules\User\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
        $user = request()->user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'avatar' => 'nullable|image|max:2048',
        ];

        if ($user->account_type === 'office') {
            $rules['company_name'] = 'required|string|max:255';
            $rules['address'] = 'nullable|string|max:500';
        }

        $user->save();

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'name' => __('user::users.form.name'),
            'mobile' => __('user::users.form.mobile'),
            'birthdate' => __('user::users.form.birthdate'),
            'gender' => __('user::users.form.gender'),
        ];
    }
}
