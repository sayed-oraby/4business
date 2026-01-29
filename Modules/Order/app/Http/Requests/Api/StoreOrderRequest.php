<?php

namespace Modules\Order\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->user();
        $usingSavedAddress = (bool) $this->input('user_address_id');

        $rules = [
            'guest_uuid' => ['nullable', 'uuid'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'user_address_id' => [
                'nullable',
                'integer',
                'exists:user_addresses,id',
                function ($attribute, $value, $fail) use ($user) {
                    if ($value && $user) {
                        $userAddress = \Modules\Shipping\Models\UserAddress::find($value);
                        if ($userAddress && $userAddress->user_id !== $user->id) {
                            $fail(__('order::validation.shipping.user_address_id.belongs_to_user'));
                        }
                    }
                },
            ],
        ];

        // If no top-level user_address_id was provided, rely on inline shipping data
        if (! $usingSavedAddress) {
            $rules = array_merge($rules, [
                'shipping' => ['required', 'array'],
                'shipping.user_address_id' => [
                    'nullable',
                    'integer',
                    'exists:user_addresses,id',
                    function ($attribute, $value, $fail) use ($user) {
                        if ($value && $user) {
                            $userAddress = \Modules\Shipping\Models\UserAddress::find($value);
                            if ($userAddress && $userAddress->user_id !== $user->id) {
                                $fail(__('order::validation.shipping.user_address_id.belongs_to_user'));
                            }
                        }
                    },
                ],
                'shipping.full_name' => ['required_without:shipping.user_address_id', 'string', 'max:255'],
                'shipping.phone' => ['required_without:shipping.user_address_id', 'string', 'max:30'],
                'shipping.address' => ['nullable', 'string', 'max:500'],
                'shipping.city' => ['nullable', 'string', 'max:255'],
                'shipping.state' => ['nullable', 'string', 'max:255'],
                'shipping.country' => ['nullable', 'string', 'max:255'],
                'shipping.postal_code' => ['nullable', 'string', 'max:20'],
            ]);
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'guest_uuid.uuid' => __('order::validation.guest_uuid.uuid'),
            'payment_method.string' => __('order::validation.payment_method.string'),
            'payment_method.max' => __('order::validation.payment_method.max', ['max' => 50]),
            'shipping.required' => __('order::validation.shipping.required'),
            'shipping.required_without' => __('order::validation.shipping.required'),
            'shipping.array' => __('order::validation.shipping.array'),
            'shipping.full_name.required' => __('order::validation.shipping.full_name.required'),
            'shipping.full_name.required_without_all' => __('order::validation.shipping.full_name.required'),
            'shipping.full_name.string' => __('order::validation.shipping.full_name.string'),
            'shipping.full_name.max' => __('order::validation.shipping.full_name.max', ['max' => 255]),
            'shipping.phone.required' => __('order::validation.shipping.phone.required'),
            'shipping.phone.required_without_all' => __('order::validation.shipping.phone.required'),
            'shipping.phone.string' => __('order::validation.shipping.phone.string'),
            'shipping.phone.max' => __('order::validation.shipping.phone.max', ['max' => 30]),
            'shipping.address.string' => __('order::validation.shipping.address.string'),
            'shipping.address.max' => __('order::validation.shipping.address.max', ['max' => 500]),
            'shipping.city.string' => __('order::validation.shipping.city.string'),
            'shipping.city.max' => __('order::validation.shipping.city.max', ['max' => 255]),
            'shipping.state.string' => __('order::validation.shipping.state.string'),
            'shipping.state.max' => __('order::validation.shipping.state.max', ['max' => 255]),
            'shipping.country.string' => __('order::validation.shipping.country.string'),
            'shipping.country.max' => __('order::validation.shipping.country.max', ['max' => 255]),
            'shipping.postal_code.string' => __('order::validation.shipping.postal_code.string'),
            'shipping.postal_code.max' => __('order::validation.shipping.postal_code.max', ['max' => 20]),
            'user_address_id.integer' => __('order::validation.shipping.user_address_id.integer'),
            'user_address_id.exists' => __('order::validation.shipping.user_address_id.exists'),
        ];
    }

    protected function prepareForValidation(): void
    {
        // If top-level user_address_id is provided, map it into shipping for validation/processing
        if ($this->filled('user_address_id') && ! $this->filled('shipping')) {
            $this->merge([
                'shipping' => [
                    'user_address_id' => $this->input('user_address_id'),
                ],
            ]);
        }
    }
}
