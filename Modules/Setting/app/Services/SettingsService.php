<?php

namespace Modules\Setting\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Setting\Http\Requests\UpdateSettingsRequest;
use Modules\Setting\Services\Media\MediaUploader;

class SettingsService
{
    public function __construct(
        protected SettingStore $settings,
        protected MediaUploader $uploader
    ) {
    }

    /**
     * Data required to render the settings screen.
     *
     * @return array<string, mixed>
     */
    public function screenData(): array
    {
        $settings = $this->settings->all();
        $availableLocales = config('setting.defaults.supported_locales', [
            'en' => ['name' => 'English', 'native' => 'English', 'dir' => 'ltr'],
        ]);
        $selectedLocales = array_keys(Arr::get($settings, 'supported_locales', $availableLocales));

        if (isset($availableLocales['en']) && ! in_array('en', $selectedLocales, true)) {
            array_unshift($selectedLocales, 'en');
        }

        return [
            'settings' => $settings,
            'availableLocales' => $availableLocales,
            'selectedLocales' => array_values(array_unique($selectedLocales)),
            'branding' => Arr::get($settings, 'branding', []),
            'contact' => Arr::get($settings, 'contact', []),
            'mail' => Arr::get($settings, 'mail', []),
            'social' => Arr::get($settings, 'social_links', []),
            'customCode' => Arr::get($settings, 'custom_code', []),
            'mailDrivers' => [
                'smtp' => 'SMTP',
                'log' => 'Log',
                'array' => 'Array',
                'ses' => 'Amazon SES',
                'mailgun' => 'Mailgun',
                'postmark' => 'Postmark',
            ],
            'mailEncryptions' => [
                'ssl' => 'SSL',
                'tls' => 'TLS',
                'none' => 'None',
            ],
            'stats' => [
                'normal' => $this->countUsersWithoutRoles(),
                'admins' => $this->countUsersWithRoles(),
                'deleted' => $this->countDeletedUsers(),
            ],
        ];
    }

    public function update(UpdateSettingsRequest $request): void
    {
        $payload = $request->validated();
        $existingSettings = $this->settings->all();
        $currentBranding = Arr::get($existingSettings, 'branding', []);

        $supported = $this->buildLocales($payload['supported_locales']);

        $appName = $this->fillLocalizedField($payload['app_name'], $payload['default_locale']);
        $appDescription = $this->fillLocalizedField($payload['app_description'] ?? [], $payload['default_locale']);

        $general = [
            'app_name' => $appName,
            'app_description' => $appDescription,
            'default_locale' => $payload['default_locale'],
            'fallback_locale' => $payload['fallback_locale'],
            'supported_locales' => $supported,
        ];
        $this->settings->setMany($general, 'general');

        $contact = [
            'address' => $payload['addresses'],
            'inbox_email' => $payload['contact']['inbox_email'],
            'whatsapp' => $payload['contact']['whatsapp'] ?? null,
            'phone' => $payload['contact']['phone'] ?? null,
            'support_line' => $payload['contact']['support_line'] ?? null,
        ];
        $this->settings->set('contact', $contact, 'contact');

        $mail = $payload['mail'];
        $existingMail = Arr::get($existingSettings, 'mail', []);
        if (blank($mail['password'] ?? null) && isset($existingMail['password'])) {
            $mail['password'] = $existingMail['password'];
        }
        if (($mail['encryption'] ?? null) === 'none') {
            $mail['encryption'] = null;
        }
        $this->settings->set('mail', $mail, 'mail');

        $branding = $this->prepareBranding($request, $currentBranding);
        $this->settings->setMany([
            'branding' => $branding,
            'logo' => $branding['logo'] ?? null,
            'logo_white' => $branding['logo_white'] ?? null,
            'logo_mobile' => $branding['logo'] ?? null,
            'favicon' => $branding['favicon'] ?? null,
        ], 'branding');

        $social = $payload['social'] ?? [];
        $this->settings->set('social_links', $social, 'social');

        $this->settings->set('custom_code', $payload['custom_code'] ?? [], 'custom');
    }

    /**
     * Public subset of settings exposed via API.
     *
     * @return array<string, mixed>
     */
    public function publicPayload(): array
    {
        $settings = $this->settings->all();

        return [
            'app' => [
                'name' => Arr::get($settings, 'app_name'),
                'description' => Arr::get($settings, 'app_description'),
                'default_locale' => Arr::get($settings, 'default_locale', config('app.locale')),
                'supported_locales' => Arr::get($settings, 'supported_locales', []),
            ],
            'branding' => [
                'logo' => Arr::get($settings, 'branding.logo'),
                'logo_white' => Arr::get($settings, 'branding.logo_white'),
                'favicon' => Arr::get($settings, 'branding.favicon'),
            ],
            'contact' => Arr::only($settings['contact'] ?? [], [
                'address',
                'inbox_email',
                'whatsapp',
                'phone',
                'support_line',
            ]),
            'social' => Arr::get($settings, 'social_links', []),
        ];
    }

    protected function prepareBranding(UpdateSettingsRequest $request, array $current): array
    {
        $branding = $current;

        $branding['logo'] = $this->handleMedia($request, 'logo', $current['logo'] ?? null, [
            'max_width' => 600,
            'directory' => 'branding/logo',
        ]);

        $branding['logo_white'] = $this->handleMedia($request, 'logo_white', $current['logo_white'] ?? null, [
            'max_width' => 600,
            'directory' => 'branding/logo-white',
        ]);

        $branding['favicon'] = $this->handleMedia($request, 'favicon', $current['favicon'] ?? null, [
            'max_width' => 256,
            'max_height' => 256,
            'directory' => 'branding/favicon',
        ]);

        $branding['footer'] = $request->input('branding_footer') ?? ($current['footer'] ?? null);

        return $branding;
    }

    protected function handleMedia(
        UpdateSettingsRequest $request,
        string $field,
        ?string $current = null,
        array $options = []
    ): ?string {
        $removeFlag = $request->boolean("remove_{$field}");
        $urlField = $request->input("{$field}_url");

        if ($removeFlag) {
            $this->deleteMedia($current);

            return null;
        }

        if ($request->hasFile($field)) {
            $upload = $this->uploader->upload($request->file($field), $options['directory'] ?? 'branding', $options);
            $this->deleteMedia($current);

            return $upload->path();
        }

        if (! blank($urlField)) {
            $this->deleteMedia($this->localPath($current));

            return $urlField;
        }

        return $current;
    }

    protected function deleteMedia(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'http')) {
            Storage::disk('public')->delete($path);
        }
    }

    protected function localPath(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        return str_starts_with($path, ['http://', 'https://']) ? null : $path;
    }

    /**
     * Map the selected codes to the rich locale definition.
     *
     * @param  array<int, string>  $selected
     * @return array<string, array<string, string>>
     */
    protected function buildLocales(array $selected): array
    {
        $catalog = config('setting.defaults.supported_locales', []);
        if (! in_array('en', $selected, true) && isset($catalog['en'])) {
            array_unshift($selected, 'en');
        }

        return collect($selected)
            ->filter(fn ($code) => isset($catalog[$code]))
            ->unique()
            ->mapWithKeys(fn ($code) => [$code => $catalog[$code]])
            ->toArray();
    }

    protected function fillLocalizedField(array $values, string $defaultLocale): array
    {
        $filtered = collect($values)
            ->map(fn ($value) => is_string($value) ? trim($value) : $value)
            ->filter()
            ->toArray();

        if (! array_key_exists($defaultLocale, $filtered) || blank($filtered[$defaultLocale])) {
            $fallback = reset($filtered);
            $filtered[$defaultLocale] = $fallback === false ? '' : $fallback;
        }

        return $filtered;
    }

    protected function countUsersWithoutRoles(): int
    {
        return DB::table('users')
            ->leftJoin('model_has_roles', function ($join) {
                $join->on('users.id', '=', 'model_id')
                    ->where('model_type', '=', 'App\\Models\\User');
            })
            ->whereNull('model_has_roles.role_id')
            ->whereNull('users.deleted_at')
            ->count();
    }

    protected function countUsersWithRoles(): int
    {
        return DB::table('users')
            ->join('model_has_roles', function ($join) {
                $join->on('users.id', '=', 'model_id')
                    ->where('model_type', '=', 'App\\Models\\User');
            })
            ->whereNull('users.deleted_at')
            ->distinct('users.id')
            ->count('users.id');
    }

    protected function countDeletedUsers(): int
    {
        return DB::table('users')
            ->whereNotNull('deleted_at')
            ->count();
    }
}
