<?php

namespace Modules\Cart\Http\Controllers\Api;

use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Cart\Http\Controllers\Concerns\ResolvesCartOwner;
use Modules\Cart\Http\Requests\Api\WishlistToggleRequest;
use Modules\Cart\Http\Resources\WishlistResource;
use Modules\Cart\Services\WishlistService;

class WishlistController extends Controller
{
    use ApiResponse;
    use ResolvesCartOwner;

    public function __construct(
        protected WishlistService $wishlistService
    ) {
    }

    public function show(Request $request): JsonResponse
    {
        $user = $this->resolveUser($request);
        $guestUuid = $this->resolveGuestUuid($request);

        $result = $this->wishlistService->resolveWishlist($user, $guestUuid);

        return $this->successResponse(
            data: [
                'wishlist' => (new WishlistResource($result['wishlist']))->resolve(),
                'guest_uuid' => $result['guest_uuid'],
            ],
            message: __('cart::wishlist.messages.loaded'),
            request: $request
        );
    }

    public function toggle(WishlistToggleRequest $request): JsonResponse
    {
        $user = $this->resolveUser($request);
        $guestUuid = $this->resolveGuestUuid($request) ?? $request->input('guest_uuid');

        $result = $this->wishlistService->resolveWishlist($user, $guestUuid);
        $wishlist = $this->wishlistService->toggleItem($result['wishlist'], $request->integer('product_id'));

        return $this->successResponse(
            data: [
                'wishlist' => (new WishlistResource($wishlist->load('items.product')))->resolve(),
                'guest_uuid' => $user ? null : $wishlist->guest_uuid,
            ],
            message: __('cart::wishlist.messages.updated'),
            request: $request
        );
    }
}
