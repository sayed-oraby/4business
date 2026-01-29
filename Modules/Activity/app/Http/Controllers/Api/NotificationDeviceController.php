<?php

namespace Modules\Activity\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Activity\Http\Requests\Api\RegisterDeviceRequest;
use Modules\Activity\Http\Requests\Api\UpdateDeviceRequest;
use Modules\Activity\Models\NotificationDevice;
use Modules\Activity\Services\NotificationDeviceService;

class NotificationDeviceController extends Controller
{
    use ApiResponse;

    public function __construct(protected NotificationDeviceService $service)
    {
    }

    public function store(RegisterDeviceRequest $request): JsonResponse
    {
        $device = $this->service->register($request->user(), $request->validated());

        return $this->successResponse(
            data: ['device' => ['device_uuid' => $device->device_uuid]],
            message: __('activity::messages.notifications.device_registered'),
            status: 201
        );
    }

    public function update(UpdateDeviceRequest $request, string $uuid): JsonResponse
    {
        $device = NotificationDevice::where('device_uuid', $uuid)->firstOrFail();
        $this->authorizeDevice($request, $device);

        $device = $this->service->update($device, $request->validated(), $request->user());

        return $this->successResponse(
            data: ['device' => ['device_uuid' => $device->device_uuid]],
            message: __('activity::messages.notifications.device_updated')
        );
    }

    public function destroy(Request $request, string $uuid): JsonResponse
    {
        $device = NotificationDevice::where('device_uuid', $uuid)->firstOrFail();
        $this->authorizeDevice($request, $device);

        $this->service->delete($device);

        return $this->successResponse(
            data: null,
            message: __('activity::messages.notifications.device_deleted')
        );
    }

    protected function authorizeDevice(Request $request, NotificationDevice $device): void
    {
        $user = $request->user();

        if ($device->user_id && $user && $device->user_id === $user->id) {
            return;
        }

        if (! $device->user_id && $request->input('guest_uuid')) {
            abort_if($device->guest_uuid !== $request->input('guest_uuid'), 403);

            return;
        }

        if (! $device->user_id && ! $device->guest_uuid) {
            return;
        }

        abort(403);
    }
}
