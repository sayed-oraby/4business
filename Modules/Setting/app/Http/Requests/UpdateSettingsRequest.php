<?php

namespace Modules\Setting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin') !== null;
    }

    public function rules(): array
    {
        $availableLocales = array_keys(config('setting.defaults.supported_locales', ['en']));

        return [
            'app_name' => ['required', 'array'],
            'app_name.*' => ['nullable', 'string', 'max:120'],
            'app_description' => ['nullable', 'array'],
            'app_description.*' => ['nullable', 'string', 'max:255'],
            'default_locale' => ['required', Rule::in($availableLocales)],
            'fallback_locale' => ['required', Rule::in($availableLocales)],
            'supported_locales' => ['required', 'array', 'min:1'],
            'supported_locales.*' => [Rule::in($availableLocales)],

            'addresses' => ['required', 'array'],
            'addresses.en' => ['nullable', 'string', 'max:255'],
            'addresses.ar' => ['nullable', 'string', 'max:255'],

            'contact' => ['required', 'array'],
            'contact.inbox_email' => ['required', 'email'],
            'contact.whatsapp' => ['nullable', 'string', 'max:50'],
            'contact.phone' => ['nullable', 'string', 'max:50'],
            'contact.support_line' => ['nullable', 'string', 'max:50'],

            'mail' => ['required', 'array'],
            'mail.driver' => ['required', 'string'],
            'mail.encryption' => ['nullable', 'string', Rule::in(['ssl', 'tls', 'none'])],
            'mail.host' => ['required', 'string', 'max:120'],
            'mail.port' => ['required', 'integer'],
            'mail.username' => ['nullable', 'string', 'max:120'],
            'mail.password' => ['nullable', 'string', 'max:250'],
            'mail.from' => ['required', 'array'],
            'mail.from.address' => ['required', 'email'],
            'mail.from.name' => ['required', 'string', 'max:120'],

            'branding_footer' => ['nullable', 'string', 'max:190'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'logo_white' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'favicon' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,ico,svg', 'max:1024'],
            'logo_url' => ['nullable', 'url'],
            'logo_white_url' => ['nullable', 'url'],
            'favicon_url' => ['nullable', 'url'],
            'remove_logo' => ['nullable', 'boolean'],
            'remove_logo_white' => ['nullable', 'boolean'],
            'remove_favicon' => ['nullable', 'boolean'],

            'social' => ['nullable', 'array'],
            'social.*' => ['nullable', 'url'],

            'custom_code' => ['nullable', 'array'],
            'custom_code.head_css' => ['nullable', 'string'],
            'custom_code.head_js' => ['nullable', 'string'],
            'custom_code.body_js' => ['nullable', 'string'],
        ];
    }
}
