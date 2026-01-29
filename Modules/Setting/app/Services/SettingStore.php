<?php

namespace Modules\Setting\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Modules\Setting\Models\Setting;

class SettingStore
{
    public function __construct(
        protected string $cacheKey = 'core.settings.cache'
    ) {
        $this->cacheKey = config('core.cache.settings_key', $this->cacheKey);
    }

    /**
     * Get a setting value.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return Arr::get($this->all(), $key, $default);
    }

    /**
     * Persist a setting value and clear cache.
     */
    public function set(string $key, mixed $value, string $group = 'general', ?string $type = null): Setting
    {
        $setting = $this->persist($key, $value, $group, $type);

        $this->flush();

        return $setting;
    }

    /**
     * Persist many settings at once and flush cache once.
     *
     * @param  array<string, mixed>  $values
     */
    public function setMany(array $values, string $group = 'general'): void
    {
        foreach ($values as $key => $value) {
            $this->persist($key, $value, $group);
        }

        $this->flush();
    }

    /**
     * All cached settings flattened by key.
     */
    public function all(): array
    {
        if (! Schema::hasTable('settings')) {
            return $this->defaultSettings();
        }

        try {
            return Cache::rememberForever($this->cacheKey, function (): array {
                return $this->loadSettingsFromDatabase();
            });
        } catch (\Exception $e) {
            // Fallback: Load settings without cache if cache driver fails (e.g., Redis unavailable)
            report($e);
            return $this->loadSettingsFromDatabase();
        }
    }

    /**
     * Load settings directly from database.
     */
    protected function loadSettingsFromDatabase(): array
    {
        return Setting::query()
            ->orderBy('key')
            ->get()
            ->mapWithKeys(function (Setting $setting) {
                return [$setting->key => $this->castValue($setting->value, $setting->type)];
            })
            ->toArray();
    }

    public function flush(): void
    {
        Cache::forget($this->cacheKey);
    }

    protected function defaultSettings(): array
    {
        $defaults = config('setting.defaults', []);

        if (Arr::isAssoc($defaults)) {
            return $defaults;
        }

        return collect($defaults)
            ->keyBy('key')
            ->map(fn ($item) => $item['value'])
            ->toArray();
    }

    protected function prepareValue(mixed $value, string $type): string
    {
        return match ($type) {
            'boolean' => $value ? '1' : '0',
            'number' => (string) $value,
            'json', 'array' => json_encode($value),
            default => (string) $value,
        };
    }

    protected function castValue(?string $value, string $type): mixed
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'number' => $value !== null ? (float) $value : null,
            'json', 'array' => json_decode($value ?? '[]', true) ?? [],
            default => $value,
        };
    }

    protected function detectType(mixed $value): string
    {
        return match (true) {
            is_bool($value) => 'boolean',
            is_numeric($value) => 'number',
            is_array($value) => 'json',
            default => 'text',
        };
    }

    protected function persist(string $key, mixed $value, string $group = 'general', ?string $type = null): Setting
    {
        $type ??= $this->detectType($value);

        return Setting::query()->updateOrCreate(
            ['key' => $key],
            [
                'value' => $this->prepareValue($value, $type),
                'type' => $type,
                'group' => $group,
            ]
        );
    }
}
