<?php

namespace Modules\User\Http\Controllers\Dashboard;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Authorization\Models\Role;
use Modules\User\Http\Requests\StoreUserRequest;
use Modules\User\Http\Requests\UpdateUserRequest;
use Modules\User\Http\Resources\DataTableResource;
use Modules\User\Http\Resources\UserResource;
use Modules\User\Models\User;
use Modules\User\Repositories\UserRepository;
use Modules\User\Services\UserService;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware(['auth:admin', 'permission:users.view'])->only(['index', 'data', 'show']);
        $this->middleware(['auth:admin', 'permission:users.create'])->only(['store']);
        $this->middleware(['auth:admin', 'permission:users.update'])->only(['update', 'restore']);
        $this->middleware(['auth:admin', 'permission:users.delete'])->only(['destroy', 'bulkDestroy']);
    }

    public function index()
    {
        return view('user::dashboard.index', [
            'roles' => Role::orderBy('name')->get(),
            'stats' => [
                'normal' => User::doesntHave('roles')->count(),
                'admins' => User::whereHas('roles')->count(),
                'deleted' => User::onlyTrashed()->count(),
            ],
        ]);
    }

    public function data(Request $request, UserService $service): JsonResponse
    {
        $payload = $service->list($request->all());

        return response()->json((new DataTableResource($payload))->resolve());
    }

    public function store(StoreUserRequest $request, UserService $service): JsonResponse
    {
        $this->authorize('create', User::class);

        $service->create($request->validated(), $request->file('avatar'), auth('admin')->id());

        return response()->json(['message' => __('user::users.messages.created')]);
    }

    public function show(int $userId, UserRepository $repository): JsonResponse
    {
        $user = $repository->findWithTrashed($userId);

        $this->authorize('view', $user);

        return response()->json([
            'user' => (new UserResource($user))->resolve(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user, UserService $service): JsonResponse
    {
        $this->authorize('update', $user);

        $service->update(
            $user,
            $request->validated(),
            $request->file('avatar'),
            $request->boolean('remove_avatar'),
            auth('admin')->id()
        );

        return response()->json(['message' => __('user::users.messages.updated')]);
    }

    public function destroy(User $user, UserService $service): JsonResponse
    {
        $this->authorize('delete', $user);

        $service->delete($user, auth('admin')->id());

        return response()->json(['message' => __('user::users.messages.deleted')]);
    }

    public function bulkDestroy(Request $request, UserService $service): JsonResponse
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['message' => 'No users selected.'], 422);
        }

        $count = $service->bulkDelete($ids, auth('admin')->id());

        return response()->json([
            'message' => __('user::users.messages.bulk_deleted'),
            'count' => $count,
        ]);
    }

    public function restore(int $userId, UserRepository $repository, UserService $service): JsonResponse
    {
        $user = $repository->findWithTrashed($userId);
        
        $this->authorize('restore', $user);

        $service->restore($userId, auth('admin')->id());

        return response()->json(['message' => __('user::users.messages.restored')]);
    }
    public function officeRequests()
    {
        return view('user::dashboard.office_requests.index');
    }

    public function officeRequestsData(Request $request): JsonResponse
    {
        $query = User::whereNotNull('office_request_status')
            ->orderByRaw("FIELD(office_request_status, 'pending', 'rejected', 'approved')")
            ->latest();

        if ($status = $request->input('status')) {
             $query->where('office_request_status', $status);
        }

        if ($searchValue = $request->input('search.value')) {
            $query->where(function($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                  ->orWhere('email', 'like', "%{$searchValue}%")
                  ->orWhere('company_name', 'like', "%{$searchValue}%");
            });
        }

        $recordsTotal = User::whereNotNull('office_request_status')->count();
        $recordsFiltered = $query->count();

        $users = $query->skip($request->input('start', 0))
            ->take($request->input('length', 10))
            ->get();

        return response()->json([
            'draw' => (int) $request->input('draw', 0),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $users,
        ]);
    }

    public function updateOfficeRequestStatus(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:1000'
        ]);

        $updateData = [
            'office_request_status' => $validated['status']
        ];

        if ($validated['status'] === 'approved') {
            $updateData['account_type'] = 'office';
            $updateData['office_rejection_reason'] = null;
        } else {
            $updateData['office_rejection_reason'] = $validated['rejection_reason'];
        }

        $user->update($updateData);

        return response()->json(['message' => 'تم تحديث حالة الطلب بنجاح']);
    }

    public function destroyOfficeRequest(User $user): JsonResponse
    {
        $user->update([
            'office_request_status' => null,
            'office_rejection_reason' => null
        ]);

        return response()->json(['message' => 'تم حذف الطلب بنجاح']);
    }
}
