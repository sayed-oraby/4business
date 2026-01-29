<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Setting\Services\SettingStore;

if (! function_exists('setting')) {
    /**
     * Fetch a setting value with an optional default fallback.
     */
    function setting(string $key, mixed $default = null): mixed
    {
        if (! App::bound(SettingStore::class)) {
            return $default;
        }

        return app(SettingStore::class)->get($key, $default);
    }
}

if (! function_exists('available_locales')) {
    /**
     * Return the list of available locales from settings or config.
     *
     * @return array<string, array<string, string>>
     */
    function available_locales(): array
    {
        $fromSettings = setting('supported_locales');

        if (is_array($fromSettings) && ! empty($fromSettings)) {
            return $fromSettings;
        }

        return config('setting.defaults.supported_locales', config('app.available_locales', ['en' => ['name' => 'English', 'native' => 'English', 'dir' => 'ltr']]));
    }
}

if (! function_exists('is_supported_locale')) {
    /**
     * Determine whether the provided locale exists in the allowed set.
     */
    function is_supported_locale(?string $locale): bool
    {
        if ($locale === null) {
            return false;
        }

        return array_key_exists($locale, available_locales());
    }
}

if (! function_exists('is_rtl')) {
    /**
     * Return the direction (rtl/ltr) for the given or current locale.
     */
    function is_rtl(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $locales = available_locales();

        return ($locales[$locale]['dir'] ?? 'ltr') === 'rtl' ? 'rtl' : 'ltr';
    }
}

if (! function_exists('is_active_route')) {
    /**
     * Return an "active" class when the current route matches.
     *
     * @param  string|array  $routes
     */
    function is_active_route(string|array $routes, string $activeClass = 'active'): string
    {
        $current = request()->route()?->getName() ?? '';
        $routes = (array) $routes;

        foreach ($routes as $route) {
            if (Str::is($route, $current)) {
                return $activeClass;
            }
        }

        return '';
    }
}

if (! function_exists('setting_media_url')) {
    /**
     * Resolve a media path that may be stored on disk or as an absolute URL.
     */
    function setting_media_url(?string $path, ?string $default = null): ?string
    {
        if (blank($path)) {
            return $default;
        }

        if (Str::startsWith($path, ['http://', 'https://', 'data:'])) {
            return $path;
        }

        $disk = Storage::disk('public');

        if ($disk->exists($path)) {
            return $disk->url($path);
        }

        if (file_exists(public_path($path))) {
            return asset($path);
        }

        return $default ?? $path;
    }
}

if (! function_exists('setting_localized')) {
    /**
     * Resolve a translated setting value stored as an associative array.
     */
    function setting_localized(string $key, string|array|null $default = null, ?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();
        $value = setting($key);

        $resolveFromArray = static function (array $translations, string $locale): ?string {
            if (array_key_exists($locale, $translations) && filled($translations[$locale])) {
                return trim((string) $translations[$locale]);
            }

            foreach ($translations as $translation) {
                if (filled($translation)) {
                    return trim((string) $translation);
                }
            }

            return null;
        };

        if (is_array($value)) {
            $resolved = $resolveFromArray($value, $locale);
            if ($resolved !== null) {
                return $resolved;
            }
        } elseif ($value !== null && $value !== '') {
            return (string) $value;
        }

        if (is_array($default)) {
            return $resolveFromArray($default, $locale);
        }

        return $value !== null && $value !== '' ? (string) $value : $default;
    }
}
