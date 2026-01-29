<?php

namespace Modules\Brand\Policies;

use Modules\Brand\Models\Brand;
use Modules\User\Models\User;

class BrandPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('brands.view');
    }

    public function view(User $user, Brand $brand): bool
    {
        return $user->can('brands.view');
    }

    public function create(User $user): bool
    {
        return $user->can('brands.create');
    }

    public function update(User $user, Brand $brand): bool
    {
        return $user->can('brands.update');
    }

    public function delete(User $user, Brand $brand): bool
    {
        return $user->can('brands.delete');
    }
}
