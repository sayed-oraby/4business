<?php

namespace Modules\User\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Modules\User\Models\User;

class UserRepository
{
    public function baseQuery(bool $withDeleted = false): Builder
    {
        $query = User::query()->with('roles');

        return $withDeleted ? $query->withTrashed() : $query;
    }

    public function countAll(): int
    {
        return User::count();
    }

    public function findWithTrashed(int $id): User
    {
        return User::withTrashed()->with('roles')->findOrFail($id);
    }

    public function create(array $data, ?array $roles): User
    {
        $user = User::create($data);

        if ($roles !== null) {
            $user->syncRoles($roles);
        }

        return $user;
    }

    public function update(User $user, array $data, ?array $roles): void
    {
        $user->update($data);

        if ($roles !== null) {
            $user->syncRoles($roles);
        }
    }

    public function delete(User $user): void
    {
        $user->delete();
    }

    public function bulkDelete(array $ids, int $currentAdminId): int
    {
        return User::whereIn('id', $ids)
            ->where('id', '!=', $currentAdminId)
            ->whereDoesntHave('roles', fn (Builder $q) => $q->where('name', 'super-admin'))
            ->delete();
    }

    public function restore(int $id): User
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return $user;
    }
}
