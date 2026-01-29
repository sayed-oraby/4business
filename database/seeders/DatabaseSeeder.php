<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            \Modules\Setting\Database\Seeders\SettingDatabaseSeeder::class,
            \Modules\User\Database\Seeders\UserDatabaseSeeder::class,
            \Modules\Authorization\Database\Seeders\AuthorizationDatabaseSeeder::class,
        ]);
    }
}
