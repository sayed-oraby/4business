<?php

namespace Modules\Authorization\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Lang;
use Modules\Authorization\Models\Permission;
use Modules\Authorization\Models\Role;

class RoleRepository
{
    public function baseQuery(): Builder
    {
        return Role::query()->withCount('users');
    }

    public function paginate(array $input): array
    {
        $query = $this->baseQuery();

        if ($search = $input['search']['value'] ?? null) {
            $query->where('name', 'like', "%{$search}%");
        }

        $recordsTotal = Role::count();
        $recordsFiltered = (clone $query)->count();

        $length = (int) ($input['length'] ?? 10);
        $start = (int) ($input['start'] ?? 0);

        $roles = $query->orderBy('name')
            ->skip($start)
            ->take($length)
            ->get();

        return [
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'roles' => $roles,
        ];
    }

    public function create(array $payload): Role
    {
        return Role::create($payload);
    }

    public function update(Role $role, array $payload): void
    {
        $role->update($payload);
    }

    public function delete(Role $role): void
    {
        $role->delete();
    }

    public function permissionsForRole(Role $role): array
    {
        return $role->permissions()->pluck('name')->all();
    }

    public function availablePermissions(): array
    {
        $labels = Lang::get('authorization::authorization.permissions_list');

        return Permission::query()
            ->orderBy('name')
            ->get()
            ->groupBy(fn (Permission $permission) => explode('.', $permission->name)[0] ?? 'general')
            ->map(function ($group, $module) use ($labels) {
                return $group->map(function (Permission $permission) use ($module, $labels) {
                    $key = "{$module}.{$permission->name}";
                    $label = data_get($labels, $key, $permission->name);

                    return [
                        'name' => $permission->name,
                        'label' => $label,
                    ];
                });
            })
            ->toArray();
    }
}
