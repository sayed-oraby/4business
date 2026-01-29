<?php

namespace Modules\Banner\Http\Controllers\Api;

use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Banner\Http\Requests\Api\ListBannerRequest;
use Modules\Banner\Http\Resources\BannerResource;
use Modules\Banner\Models\Banner;
use Modules\Banner\Repositories\BannerRepository;

class BannerController extends Controller
{
    use ApiResponse;

    public function __construct(protected BannerRepository $repository)
    {
    }

    public function index(ListBannerRequest $request): JsonResponse
    {
        // Get 10 random active banners
        $banners = Banner::activeNow()
            ->inRandomOrder()
            ->limit(10)
            ->get();

        return $this->successResponse(
            data: [
                'banners' => BannerResource::collection($banners)->resolve(),
            ],
            message: __('banner::banner.messages.listed')
        );
    }
}
