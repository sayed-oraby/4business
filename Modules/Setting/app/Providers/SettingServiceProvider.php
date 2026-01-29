<?php

namespace Modules\Setting\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Modules\Setting\Services\SettingStore;
use Modules\Setting\View\Composers\AppSettingsComposer;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class SettingServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Setting';

    protected string $nameLower = 'setting';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerViewComposers();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));

        $this->app->booted(function () {
            if ($this->app->bound(SettingStore::class)) {
                try {
                    $this->applyRuntimeConfiguration(
                        $this->app->make(SettingStore::class)->all()
                    );
                } catch (\Exception $e) {
                    // Silently fail if settings cannot be loaded (e.g., during package discovery)
                    // Default config values will be used instead
                    if (app()->bound('log')) {
                        report($e);
                    }
                }
            }
        });
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->singleton(SettingStore::class, fn () => new SettingStore());
        $this->app->alias(SettingStore::class, 'settings');

        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
    }

    protected function registerViewComposers(): void
    {
        $this->app->booted(function () {
            view()->composer('*', AppSettingsComposer::class);
        });
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        // $this->commands([]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/'.$this->nameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->name, 'lang'), $this->nameLower);
            $this->loadJsonTranslationsFrom(module_path($this->name, 'lang'));
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $configPath = module_path($this->name, config('modules.paths.generator.config.path'));

        if (is_dir($configPath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configPath));

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $config = str_replace($configPath.DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $config_key = str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $config);
                    $segments = explode('.', $this->nameLower.'.'.$config_key);

                    // Remove duplicated adjacent segments
                    $normalized = [];
                    foreach ($segments as $segment) {
                        if (end($normalized) !== $segment) {
                            $normalized[] = $segment;
                        }
                    }

                    $key = ($config === 'config.php') ? $this->nameLower : implode('.', $normalized);

                    $this->publishes([$file->getPathname() => config_path($config)], 'config');
                    $this->merge_config_from($file->getPathname(), $key);
                }
            }
        }
    }

    /**
     * Merge config from the given path recursively.
     */
    protected function merge_config_from(string $path, string $key): void
    {
        $existing = config($key, []);
        $module_config = require $path;

        config([$key => array_replace_recursive($existing, $module_config)]);
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/'.$this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);

        Blade::componentNamespace(config('modules.namespace').'\\' . $this->name . '\\View\\Components', $this->nameLower);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->nameLower)) {
                $paths[] = $path.'/modules/'.$this->nameLower;
            }
        }

        return $paths;
    }

    protected function applyRuntimeConfiguration(array $settings): void
    {
        config([
            'app.name' => $this->stringValue($settings['app_name'] ?? config('app.name')),
            'app.locale' => Arr::get($settings, 'default_locale', config('app.locale')),
            'app.fallback_locale' => Arr::get($settings, 'fallback_locale', config('app.fallback_locale')),
            'core.localization.supported_locales' => Arr::get(
                $settings,
                'supported_locales',
                config('setting.defaults.supported_locales', [])
            ),
            'mail.default' => Arr::get($settings, 'mail.driver', config('mail.default')),
            'mail.mailers.smtp.host' => Arr::get($settings, 'mail.host', config('mail.mailers.smtp.host')),
            'mail.mailers.smtp.port' => Arr::get($settings, 'mail.port', config('mail.mailers.smtp.port')),
            'mail.mailers.smtp.encryption' => Arr::get($settings, 'mail.encryption', config('mail.mailers.smtp.encryption')),
            'mail.mailers.smtp.username' => Arr::get($settings, 'mail.username', config('mail.mailers.smtp.username')),
            'mail.mailers.smtp.password' => Arr::get($settings, 'mail.password', config('mail.mailers.smtp.password')),
            'mail.from.address' => Arr::get($settings, 'mail.from.address', config('mail.from.address')),
            'mail.from.name' => Arr::get($settings, 'mail.from.name', config('mail.from.name')),
        ]);
    }

    protected function stringValue(mixed $value): string
    {
        if (is_array($value)) {
            $locale = app()->getLocale();
            return $value[$locale] ?? reset($value) ?? config('app.name');
        }

        return (string) $value;
    }
}
