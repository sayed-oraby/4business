<?php

namespace Modules\Authorization\Database\Seeders;

use Modules\User\Models\User;
use Illuminate\Database\Seeder;
use Modules\Authorization\Services\PermissionService;

class AuthorizationDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var PermissionService $permissions */
        
        $permissions = app(PermissionService::class);

        $modules = [
            'dashboard' => ['access'],
            'users' => ['view', 'create', 'update', 'delete', 'restore'],
            'settings' => ['view', 'update'],
            'authorization' => ['view', 'update'],
            'banners' => ['view', 'create', 'update', 'delete'],
            'pages' => ['view', 'create', 'update', 'delete'],
            'blogs' => ['view', 'create', 'update', 'delete'],
            'categories' => ['view', 'create', 'update', 'delete'],
            'brands' => ['view', 'create', 'update', 'delete'],
            'products' => ['view', 'create', 'update', 'delete'],
            'shipping_countries' => ['view', 'create', 'update', 'delete'],
            'orders' => ['view', 'create', 'update', 'delete'],
            'order_statuses' => ['view', 'create', 'update', 'delete'],
        ];

        foreach ($modules as $module => $abilities) {
            $permissions->syncModulePermissions($module, $abilities);
        }

        $superAdminEmail = config('auth.super_admin_email', 'admin@gavankit.test');

        $user = User::query()->where('email', $superAdminEmail)->first();

        if ($user) {
            $user->assignRole('super-admin');
        }
    }
}
