<?php

namespace Modules\User\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\User\Http\Requests\Api\ChangeAvatarRequest;
use Modules\User\Http\Requests\Api\ChangePasswordRequest;
use Modules\User\Http\Requests\Api\DeleteAccountRequest;
use Modules\User\Http\Requests\Api\UpdateProfileRequest;
use Modules\User\Http\Resources\UserResource;
use Modules\User\Services\ProfileService;

class ProfileController extends Controller
{
    use AuthorizesRequests;
    use ApiResponse;

    public function __construct(protected ProfileService $service)
    {
    }

    public function show(Request $request): JsonResponse
    {
        return $this->successResponse(
            data: ['user' => (new UserResource($request->user()))->resolve()],
            message: __('user::users.messages.profile_loaded')
        );
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->service->updateProfile($request->user(), $request->validated());

        if ($user->account_type === 'office') {
            $user->company_name = $request->company_name;
            $user->address = $request->address;
        }

        return $this->successResponse(
            data: ['user' => (new UserResource($user))->resolve()],
            message: __('user::users.messages.updated')
        );
    }

    public function updatePassword(ChangePasswordRequest $request): JsonResponse
    {
        $this->service->updatePassword($request->user(), $request->validated());

        return $this->successResponse(
            data: [],
            message: __('user::users.messages.password_changed')
        );
    }

    public function updateAvatar(ChangeAvatarRequest $request): JsonResponse
    {
        $avatarUrl = $this->service->updateAvatar(
            $request->user(),
            $request->file('avatar'),
            $request->boolean('remove_avatar')
        );

        return $this->successResponse(
            data: ['avatar_url' => $avatarUrl],
            message: __('user::users.messages.avatar_updated')
        );
    }

    public function destroy(DeleteAccountRequest $request): JsonResponse
    {
        $this->service->deleteAccount($request->user(), $request->string('password')->toString());

        return $this->successResponse(
            data: [],
            message: __('user::users.messages.deleted')
        );
    }
}
