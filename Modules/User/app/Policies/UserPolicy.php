<?php

namespace Modules\User\Policies;

use Modules\User\Models\User;

class UserPolicy
{
    public function view(User $actor, User $subject): bool
    {
        return $actor->can('users.view');
    }

    public function create(User $actor): bool
    {
        return $actor->can('users.create');
    }

    public function update(User $actor, User $subject): bool
    {
        return $actor->can('users.update');
    }

    public function delete(User $actor, User $subject): bool
    {
        if ($actor->id === $subject->id) {
            return false;
        }

        if ($subject->hasRole('super-admin')) {
            return false;
        }

        return $actor->can('users.delete');
    }

    public function restore(User $actor, User $subject): bool
    {
        return $actor->can('users.update');
    }
}
