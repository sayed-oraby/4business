<?php

namespace Modules\User\Database\Seeders;

use Modules\User\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => config('auth.super_admin_email', 'admin@gavankit.test')],
            [
                'name' => 'Super Admin',
                'password' => Hash::make(config('auth.super_admin_password', 'password')),
            ]
        );
    }
}
