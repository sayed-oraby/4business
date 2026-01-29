<?php

namespace Modules\Cart\Http\Controllers\Api;

use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Cart\Http\Controllers\Concerns\ResolvesCartOwner;
use Modules\Cart\Http\Requests\Api\AddCartItemRequest;
use Modules\Cart\Http\Requests\Api\CheckoutCartRequest;
use Modules\Cart\Http\Requests\Api\UpdateCartItemRequest;
use Modules\Cart\Http\Resources\CartResource;
use Modules\Cart\Models\CartItem;
use Modules\Cart\Services\CartService;
use Modules\Shipping\Models\UserAddress;

class CartController extends Controller
{
    use ApiResponse;
    use ResolvesCartOwner;

    public function __construct(
        protected CartService $cartService
    ) {
    }

    public function show(Request $request): JsonResponse
    {
        $user = $this->resolveUser($request);
        $guestUuid = $this->resolveGuestUuid($request);

        $result = $this->cartService->getCart($user, $guestUuid);

        return $this->successResponse(
            data: [
                'cart' => $result['cart'] ? (new CartResource($result['cart']))->resolve() : null,
                'guest_uuid' => $result['guest_uuid'],
            ],
            message: __('cart::cart.messages.loaded'),
            request: $request
        );
    }

    public function addItem(AddCartItemRequest $request): JsonResponse
    {
        $user = $this->resolveUser($request);
        $guestUuid = $this->resolveGuestUuid($request);

        $result = $this->cartService->addItem(
            $user,
            $guestUuid,
            $request->integer('product_id'),
            $request->integer('quantity'),
            $request->input('options', [])
        );

        return $this->successResponse(
            data: [
                'cart' => (new CartResource($result['cart']))->resolve(),
                'guest_uuid' => $result['guest_uuid'],
            ],
            message: __('cart::cart.messages.item_added'),
            status: 201,
            request: $request
        );
    }

    public function updateItem(UpdateCartItemRequest $request, int $item): JsonResponse
    {
        $user = $this->resolveUser($request);
        $guestUuid = $this->resolveGuestUuid($request);
        $cartItem = $this->cartService->resolveItemForOwner($user, $guestUuid, $item);

        $result = $this->cartService->updateItem(
            $user,
            $guestUuid,
            $cartItem,
            $request->integer('quantity'),
            $request->input('options', [])
        );

        return $this->successResponse(
            data: [
                'cart' => (new CartResource($result['cart']))->resolve(),
                'guest_uuid' => $result['guest_uuid'],
            ],
            message: __('cart::cart.messages.item_updated'),
            request: $request
        );
    }

    public function removeItem(CheckoutCartRequest $request, int $item): JsonResponse
    {
        $user = $this->resolveUser($request);
        $guestUuid = $this->resolveGuestUuid($request);
        $cartItem = $this->cartService->resolveItemForOwner($user, $guestUuid, $item);

        $result = $this->cartService->removeItem($user, $guestUuid, $cartItem);

        return $this->successResponse(
            data: [
                'cart' => (new CartResource($result['cart']))->resolve(),
                'guest_uuid' => $result['guest_uuid'],
            ],
            message: __('cart::cart.messages.item_removed'),
            request: $request
        );
    }

    public function refresh(CheckoutCartRequest $request): JsonResponse
    {
        $user = $this->resolveUser($request);
        $guestUuid = $this->resolveGuestUuid($request);

        $result = $this->cartService->refresh($user, $guestUuid);

        return $this->successResponse(
            data: [
                'cart' => $result['cart'] ? (new CartResource($result['cart']))->resolve() : null,
                'guest_uuid' => $result['guest_uuid'],
                'changes' => $result['changes'],
            ],
            message: __('cart::cart.messages.refreshed'),
            request: $request
        );
    }

    public function validateCheckout(CheckoutCartRequest $request): JsonResponse
    {
        $user = $this->resolveUser($request);
        $guestUuid = $this->resolveGuestUuid($request);
        $userAddressId = $request->integer('user_address_id');
        $cartId = $request->integer('cart_id');

        $result = $this->cartService->validateForCheckout($user, $guestUuid, $userAddressId, $cartId);

        return $this->successResponse(
            data: [
                'cart' => (new CartResource($result['cart']))->resolve(),
                'guest_uuid' => $result['guest_uuid'],
                'changes' => $result['changes'],
                'shipping' => $result['shipping'] ?? null,
            ],
            message: __('cart::cart.messages.checkout_ready'),
            request: $request
        );
    }
}
