<?php

namespace Modules\User\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Setting\Models\ContactMessage;
use Modules\User\Http\Requests\Api\ChangeAvatarRequest;
use Modules\User\Http\Requests\Api\ChangePasswordRequest;
use Modules\User\Http\Requests\Api\ContactUsRequest;
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

        if($request->hasFile('avatar') && $request->avatar != null) {
            $avatarUrl = $this->service->updateAvatar(
                $request->user(),
                $request->file('avatar'),
                true
            );
        }

        // if ($user->account_type === 'office') {
        //     $user->company_name = $request->company_name;
        //     $user->address = $request->address;
        // }

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

    /**
     * Update user's contact methods (WhatsApp & Phone calls)
     */
    public function updateContactMethods(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'whatsapp_enabled' => 'required|boolean',
            'whatsapp_number' => 'nullable|numeric',
            'call_enabled' => 'required|boolean',
            'call_number' => 'nullable|numeric',
        ]);

        $user = $request->user();

        // If enabled, number is required
        if ($validated['whatsapp_enabled'] && empty($validated['whatsapp_number'])) {
            return $this->errorResponse(
                message: __('user::users.messages.whatsapp_number_required'),
                status: 422
            );
        }

        if ($validated['call_enabled'] && empty($validated['call_number'])) {
            return $this->errorResponse(
                message: __('user::users.messages.call_number_required'),
                status: 422
            );
        }

        $user->update([
            'whatsapp_enabled' => $validated['whatsapp_enabled'],
            'whatsapp_number' => $validated['whatsapp_number'],
            'call_enabled' => $validated['call_enabled'],
            'call_number' => $validated['call_number'],
        ]);

        return $this->successResponse(
            data: [
                'whatsapp' => [
                    'enabled' => $user->whatsapp_enabled,
                    'number' => $user->whatsapp_number,
                ],
                'call' => [
                    'enabled' => $user->call_enabled,
                    'number' => $user->call_number,
                ],
            ],
            message: __('user::users.messages.contact_methods_updated')
        );
    }

    /**
     * Update user's notification settings
     */
    public function updateNotificationSettings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'notify_ad_status' => 'required|boolean',  // حالة الإعلان
            'notify_messages' => 'required|boolean',    // رسائل / تواصل
            'notify_ad_expiry' => 'required|boolean',   // انتهاء الإعلان
        ]);

        $user = $request->user();

        $user->update([
            'notify_ad_status' => $validated['notify_ad_status'],
            'notify_messages' => $validated['notify_messages'],
            'notify_ad_expiry' => $validated['notify_ad_expiry'],
        ]);

        return $this->successResponse(
            data: [
                'ad_status' => $user->notify_ad_status,
                'messages' => $user->notify_messages,
                'ad_expiry' => $user->notify_ad_expiry,
            ],
            message: __('user::users.messages.notification_settings_updated')
        );
    }

    /**
     * Get user's contact methods
     */
    public function getContactMethods(Request $request): JsonResponse
    {
        $user = $request->user();

        return $this->successResponse(
            data: [
                'whatsapp' => [
                    'enabled' => $user->whatsapp_enabled,
                    'number' => $user->whatsapp_number,
                ],
                'call' => [
                    'enabled' => $user->call_enabled,
                    'number' => $user->call_number,
                ],
            ]
        );
    }

    /**
     * Get user's notification settings
     */
    public function getNotificationSettings(Request $request): JsonResponse
    {
        $user = $request->user();

        return $this->successResponse(
            data: [
                'ad_status' => $user->notify_ad_status,
                'messages' => $user->notify_messages,
                'ad_expiry' => $user->notify_ad_expiry,
            ]
        );
    }

    /**
     * Submit contact us form
     * حفظ رسالة اتصل بنا
     */
    public function contactUs(ContactUsRequest $request): JsonResponse
    {
        $contactMessage = ContactMessage::create([
            'user_id' => auth('sanctum')->check() ? auth('sanctum')->id() : null,
            'name' => $request->name,
            'email' => $request->email,
            'country_code' => $request->country_code ?? '+965',
            'phone' => $request->phone,
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        return $this->successResponse(
            data: [
                'id' => $contactMessage->id,
                'name' => $contactMessage->name,
                'email' => $contactMessage->email,
                'phone' => $contactMessage->full_phone,
                'subject' => $contactMessage->subject,
                'message' => $contactMessage->message,
                'created_at' => $contactMessage->created_at->toISOString(),
            ],
            message: __('user::users.messages.contact_message_sent')
        );
    }
}

