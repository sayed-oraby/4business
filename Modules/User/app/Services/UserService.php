<?php

namespace Modules\User\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Activity\Jobs\RecordAuditLogJob;
use Modules\Setting\Services\Media\MediaUploader;
use Modules\User\Models\User;
use Modules\User\Repositories\UserRepository;

class UserService
{
    public function __construct(
        protected UserRepository $repository,
        protected MediaUploader $uploader
    ) {
    }

    /**
     * Build the DataTable payload for the users listing.
     *
     * @param  array<string, mixed>  $input
     */
    public function list(array $input): array
    {
        $query = $this->repository
            ->baseQuery((bool) ($input['with_deleted'] ?? false));

        if ($search = $input['search']['value'] ?? null) {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        if ($role = data_get($input, 'filters.role')) {
            if ($role === '__without_roles') {
                $query->whereDoesntHave('roles');
            } else {
                $query->whereHas('roles', fn ($builder) => $builder->where('name', $role));
            }
        }

        if ($status = data_get($input, 'filters.status')) {
            if ($status === 'deleted') {
                $query->onlyTrashed();
            } elseif ($status === 'active') {
                $query->whereNull('deleted_at');
            }
        }

        if ($from = data_get($input, 'filters.date_from')) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = data_get($input, 'filters.date_to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $orderColumns = ['id', 'name', 'mobile', 'roles', 'status', 'actions'];
        $orderIndex = (int) data_get($input, 'order.0.column', 2);
        $orderBy = $orderColumns[$orderIndex] ?? 'name';

        if ($orderBy === 'roles') {
            $orderBy = 'name';
        }

        if (! in_array($orderBy, ['name', 'email', 'mobile', 'status'], true)) {
            $orderBy = 'name';
        }

        $direction = data_get($input, 'order.0.dir', 'asc');
        $length = (int) ($input['length'] ?? 10);
        $start = (int) ($input['start'] ?? 0);

        $recordsTotal = $this->repository->countAll();
        $recordsFiltered = (clone $query)->count();

        $users = $query->orderBy($orderBy, $direction)
            ->skip($start)
            ->take($length)
            ->get();

        return [
            'draw' => (int) ($input['draw'] ?? 0),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $users,
        ];
    }

    /**
     * Create a new user.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data, ?UploadedFile $avatar, int $actorId): void
    {
        DB::transaction(function () use ($data, $avatar, $actorId) {
            $payload = [
                'name' => $data['name'],
                'email' => $data['email'],
                'mobile' => $data['mobile'] ?? null,
                'birthdate' => $data['birthdate'] ?? null,
                'gender' => $data['gender'] ?? null,
                'password' => Hash::make($data['password']),
            ];

            if ($avatar) {
                $upload = $this->uploader->upload($avatar, 'users/avatars', ['max_width' => 512]);
                $payload['avatar'] = $upload->path();
            }

            $user = $this->repository->create($payload, $data['roles'] ?? null);

            RecordAuditLogJob::dispatch(
                $actorId,
                'users.create',
                'Created user '.$user->email,
                ['context' => 'users']
            );
        });
    }

    /**
     * Update an existing user.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(User $user, array $data, ?UploadedFile $avatar, bool $removeAvatar, int $actorId): void
    {
        DB::transaction(function () use ($user, $data, $avatar, $removeAvatar, $actorId) {
            $payload = [
                'name' => $data['name'],
                'email' => $data['email'],
                'mobile' => $data['mobile'] ?? null,
                'birthdate' => $data['birthdate'] ?? null,
                'gender' => $data['gender'] ?? null,
            ];

            // Handle Office Request Status
            // if (isset($data['office_request_status'])) {
            //     $payload['office_request_status'] = $data['office_request_status'];
            //     if ($data['office_request_status'] === 'approved') {
            //         $payload['account_type'] = 'office';
            //     }
            // }
            
            // if (isset($data['office_rejection_reason'])) {
            //     $payload['office_rejection_reason'] = $data['office_rejection_reason'];
            // }

            if (! empty($data['password'])) {
                $payload['password'] = Hash::make($data['password']);
            }

            if ($avatar) {
                $upload = $this->uploader->upload($avatar, 'users/avatars', ['max_width' => 512]);
                $payload['avatar'] = $upload->path();
            } elseif ($removeAvatar) {
                $payload['avatar'] = null;
            }

            $roles = array_key_exists('roles', $data) ? ($data['roles'] ?? []) : null;

            $this->repository->update($user, $payload, $roles);

            RecordAuditLogJob::dispatch(
                $actorId,
                'users.update',
                'Updated user '.$user->email,
                ['context' => 'users']
            );
        });
    }

    public function delete(User $user, int $actorId): void
    {
        $this->repository->delete($user);

        RecordAuditLogJob::dispatch(
            $actorId,
            'users.delete',
            'Deleted user '.$user->email,
            ['context' => 'users']
        );
    }

    /**
     * @param  int[]  $ids
     */
    public function bulkDelete(array $ids, int $actorId): int
    {
        $count = $this->repository->bulkDelete($ids, $actorId);

        if ($count > 0) {
            RecordAuditLogJob::dispatch(
                $actorId,
                'users.bulk_delete',
                'Bulk deleted '.$count.' users',
                ['context' => 'users']
            );
        }

        return $count;
    }

    public function restore(int $userId, int $actorId): void
    {
        $user = $this->repository->restore($userId);

        RecordAuditLogJob::dispatch(
            $actorId,
            'users.restore',
            'Restored user '.$user->email,
            ['context' => 'users']
        );
    }
}
