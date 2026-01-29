<?php

namespace Modules\Activity\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDeviceRequest extends FormRequest
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
            'device_token' => ['nullable', 'string'],
            'device_type' => ['nullable', Rule::in(['ios', 'android', 'web'])],
            'app_version' => ['nullable', 'string', 'max:50'],
            'language' => ['nullable', 'string', 'max:10'],
            'notifications_enabled' => ['nullable', 'boolean'],
            'guest_uuid' => ['nullable', 'string', 'max:191'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('notifications_enabled')) {
            $this->merge([
                'notifications_enabled' => $this->boolean('notifications_enabled'),
            ]);
        }
    }
}
