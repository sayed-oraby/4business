<?php

namespace Modules\Setting\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Setting\Services\SettingStore;

class SettingDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var SettingStore $settings */
        $settings = app(SettingStore::class);

        foreach (config('setting.defaults', []) as $key => $value) {
            $settings->set($key, $value);
        }
    }
}
