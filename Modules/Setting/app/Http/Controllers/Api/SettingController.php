<?php

namespace Modules\Setting\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Modules\Setting\Services\SettingsService;

class SettingController extends Controller
{
    use ApiResponse;

    public function __construct(protected SettingsService $service)
    {
    }

    public function show(): JsonResponse
    {
        return $this->successResponse(
            data: ['settings' => $this->service->publicPayload()],
            message: __('setting::settings.messages.public_payload_loaded')
        );
    }
}
