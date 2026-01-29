<?php

namespace Modules\Setting\View\Composers;

use Illuminate\View\View;
use Modules\Setting\Services\SettingStore;

class AppSettingsComposer
{
    public function __construct(
        protected SettingStore $settings
    ) {
    }

    public function compose(View $view): void
    {
        $settings = $this->settings->all();

        $settings['app_name'] = setting_localized('app_name', config('app.name'));
        $settings['app_description'] = setting_localized('app_description', config('app.description'));

        $view->with('appSettings', $settings);
    }
}
