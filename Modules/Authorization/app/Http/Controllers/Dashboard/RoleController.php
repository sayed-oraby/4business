<?php

namespace Modules\Authorization\Http\Controllers\Dashboard;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Authorization\Http\Requests\Role\StoreRoleRequest;
use Modules\Authorization\Http\Requests\Role\SyncRolePermissionsRequest;
use Modules\Authorization\Http\Requests\Role\UpdateRoleRequest;
use Modules\Authorization\Models\Role;
use Modules\Authorization\Services\RoleService;

class RoleController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected RoleService $service)
    {
        $this->middleware(['auth:admin', 'permission:authorization.view'])->only(['index', 'data', 'permissions', 'availablePermissions']);
        $this->middleware(['auth:admin', 'permission:authorization.update'])->only(['store', 'update', 'destroy', 'syncPermissions', 'createPermission']);
    }

    public function index()
    {
        return view('authorization::dashboard.index');
    }

    public function data(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Role::class);
        return response()->json($this->service->list($request->all()));
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $this->authorize('create', Role::class);
        $this->service->create(
            $request->string('name')->toString(),
            $request->input('permissions', [])
        );

        return response()->json([
            'message' => __('authorization::authorization.messages.role_created'),
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        $this->authorize('update', $role);
        $this->service->update(
            $role,
            $request->string('name')->toString(),
            $request->filled('permissions') ? $request->input('permissions', []) : null
        );

        return response()->json([
            'message' => __('authorization::authorization.messages.role_updated'),
        ]);
    }

    public function destroy(Role $role): JsonResponse
    {
        $this->authorize('delete', $role);
        $this->service->delete($role);

        return response()->json([
            'message' => __('authorization::authorization.messages.role_deleted'),
        ]);
    }

    public function permissions(Role $role): JsonResponse
    {
        $this->authorize('view', $role);
        $assigned = $this->service->permissionsForRole($role);

        return response()->json([
            'role' => $role->only(['id', 'name']),
            'permissions' => $assigned,
        ]);
    }

    public function availablePermissions(): JsonResponse
    {
        $this->authorize('viewAny', Role::class);
        return response()->json([
            'permissions' => $this->service->availablePermissions(),
        ]);
    }

    public function syncPermissions(SyncRolePermissionsRequest $request, Role $role): JsonResponse
    {
        $this->authorize('update', $role);
        $this->service->syncPermissions($role, $request->input('permissions', []));

        return response()->json([
            'message' => __('authorization::authorization.messages.permissions_synced'),
        ]);
    }

    public function createPermission(Request $request): JsonResponse
    {
        $this->authorize('create', Role::class);
        $request->validate([
            'name' => ['required', 'string', 'max:150', 'unique:permissions,name'],
        ]);

        $this->service->createPermission($request->string('name')->toString());

        return response()->json([
            'message' => __('authorization::authorization.messages.permission_created'),
        ]);
    }
}
