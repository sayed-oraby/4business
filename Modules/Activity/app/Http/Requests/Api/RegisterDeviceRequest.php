<?php

namespace Modules\Activity\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterDeviceRequest extends FormRequest
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
            'device_uuid' => ['required', 'string', 'max:191'],
            'device_token' => ['required', 'string'],
            'device_type' => ['required', Rule::in(['ios', 'android', 'web'])],
            'app_version' => ['nullable', 'string', 'max:50'],
            'language' => ['nullable', 'string', 'max:10'],
            'guest_uuid' => ['nullable', 'string', 'max:191'],
            'notifications_enabled' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'notifications_enabled' => $this->boolean('notifications_enabled', true),
        ]);
    }
}
