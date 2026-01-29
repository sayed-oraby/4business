<?php

namespace Modules\Authorization\Services;

use Illuminate\Support\Facades\DB;
use Modules\Authorization\Http\Resources\RoleListItemResource;
use Modules\Authorization\Models\Permission;
use Modules\Authorization\Models\Role;
use Modules\Authorization\Repositories\RoleRepository;

class RoleService
{
    public function __construct(protected RoleRepository $repository)
    {
    }

    /**
     * Build DataTable payload for roles list.
     *
     * @param  array<string, mixed>  $input
     */
    public function list(array $input): array
    {
        $result = $this->repository->paginate($input);

        return [
            'draw' => (int) ($input['draw'] ?? 0),
            'recordsTotal' => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data' => RoleListItemResource::collection($result['roles'])->resolve(),
        ];
    }

    public function create(string $name, array $permissions): void
    {
        DB::transaction(function () use ($name, $permissions) {
            $role = $this->repository->create([
                'name' => $name,
                'guard_name' => config('auth.defaults.guard', 'admin'),
            ]);

            $role->syncPermissions($permissions);
        });
    }

    public function update(Role $role, string $name, ?array $permissions = null): void
    {
        DB::transaction(function () use ($role, $name, $permissions) {
            $this->repository->update($role, ['name' => $name]);

            if (is_array($permissions)) {
                $role->syncPermissions($permissions);
            }
        });
    }

    public function delete(Role $role): void
    {
        if ($role->name === 'super-admin') {
            abort(422, __('authorization::authorization.messages.cannot_delete_super_admin'));
        }

        $this->repository->delete($role);
    }

    public function permissionsForRole(Role $role): array
    {
        return $this->repository->permissionsForRole($role);
    }

    public function availablePermissions(): array
    {
        return $this->repository->availablePermissions();
    }

    public function syncPermissions(Role $role, array $permissions): void
    {
        $role->syncPermissions($permissions);
    }

    public function createPermission(string $name): void
    {
        Permission::create([
            'name' => $name,
            'guard_name' => config('auth.defaults.guard', 'admin'),
        ]);
    }
}
