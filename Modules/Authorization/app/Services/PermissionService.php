<?php

namespace Modules\Authorization\Services;

use Modules\Authorization\Models\Permission;
use Modules\Authorization\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionService
{
    public function __construct(
        protected PermissionRegistrar $registrar
    ) {
    }

    /**
     * Create permissions for a module and assign them to a role.
     *
     * @param  list<string>  $abilities
     */
    public function syncModulePermissions(string $module, array $abilities = ['view', 'create', 'update', 'delete'], string $role = 'super-admin'): void
    {
        $created = collect($abilities)
            ->map(function (string $ability) use ($module) {
                return $this->registerPermission(sprintf('%s.%s', $module, $ability));
            })
            ->filter();

        if ($created->isEmpty()) {
            return;
        }

        $roleModel = Role::firstOrCreate([
            'name' => $role,
            'guard_name' => config('auth.defaults.guard', 'admin'),
        ]);

        $roleModel->givePermissionTo($created->all());
        $this->registrar->forgetCachedPermissions();
    }

    protected function registerPermission(string $name): Permission
    {
        return Permission::firstOrCreate([
            'name' => $name,
            'guard_name' => config('auth.defaults.guard', 'admin'),
        ]);
    }
}
