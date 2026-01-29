<?php

namespace Modules\Authorization\Policies;

use Modules\User\Models\User;
use Modules\Authorization\Models\Role;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('authorization.view');
    }

    public function view(User $user, Role $role): bool
    {
        return $user->can('authorization.view');
    }

    public function create(User $user): bool
    {
        return $user->can('authorization.update');
    }

    public function update(User $user, Role $role): bool
    {
        return $user->can('authorization.update');
    }

    public function delete(User $user, Role $role): bool
    {
        return $user->can('authorization.update');
    }
}
