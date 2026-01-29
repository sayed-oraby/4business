<?php

namespace Modules\Blog\Policies;

use Modules\Blog\Models\Blog;
use Modules\User\Models\User;

class BlogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('blogs.view');
    }

    public function view(User $user, Blog $blog): bool
    {
        return $user->can('blogs.view');
    }

    public function create(User $user): bool
    {
        return $user->can('blogs.create');
    }

    public function update(User $user, Blog $blog): bool
    {
        return $user->can('blogs.update');
    }

    public function delete(User $user, Blog $blog): bool
    {
        return $user->can('blogs.delete');
    }
}
