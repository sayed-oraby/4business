<?php

namespace Modules\User\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ContactUsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'country_code' => ['nullable', 'string', 'max:10'],
            'phone' => ['required', 'string', 'max:20'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('user::users.validation.name_required'),
            'email.required' => __('user::users.validation.email_required'),
            'email.email' => __('user::users.validation.email_invalid'),
            'phone.required' => __('user::users.validation.phone_required'),
            'message.required' => __('user::users.validation.message_required'),
            'message.min' => __('user::users.validation.message_min'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => __('user::users.fields.name'),
            'email' => __('user::users.fields.email'),
            'phone' => __('user::users.fields.phone'),
            'country_code' => __('user::users.fields.country_code'),
            'subject' => __('user::users.fields.subject'),
            'message' => __('user::users.fields.message'),
        ];
    }
}
