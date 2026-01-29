<?php

namespace Modules\Post\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Authorization\Models\Permission;
use Modules\Authorization\Models\Role;

class PostDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'posts.view',
            'posts.create',
            'posts.update',
            'posts.delete',
            'posts.approve',
            'posts.reject',
            
            'post_types.view',
            'post_types.create',
            'post_types.update',
            'post_types.delete',
            
            'packages.view',
            'packages.create',
            'packages.update',
            'packages.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']);
        }

        // Assign to Super Admin
        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'admin']);
        $role->givePermissionTo($permissions);
    }
}
