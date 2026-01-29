<?php

namespace Modules\Cart\Http\Controllers\Api;

use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Cart\Http\Controllers\Concerns\ResolvesCartOwner;
use Modules\Cart\Http\Requests\Api\EstimateShippingRequest;
use Modules\Cart\Services\CartService;
use Modules\Shipping\Models\UserAddress;
use Modules\Shipping\Services\ShippingCalculator;

class ShippingEstimateController extends Controller
{
    use ApiResponse;
    use ResolvesCartOwner;

    public function __invoke(
        EstimateShippingRequest $request,
        CartService $cartService,
        ShippingCalculator $calculator
    ): JsonResponse {
        $user = $this->resolveUser($request);
        $guestUuid = $this->resolveGuestUuid($request);

        $cartContext = $cartService->getCart($user, $guestUuid, false);
        $cart = $cartContext['cart'];

        if (! $cart || $cart->items()->count() === 0) {
            return $this->errorResponse(
                message: __('cart::cart.errors.empty'),
                status: 422);
        }

        $address = UserAddress::query()
            ->where('user_id', $request->user()?->id)
            ->findOrFail($request->integer('address_id'));

        $result = $calculator->calculate($cart->load('items.product'), $address->load('country'));

        return $this->successResponse(
            data: [
                'estimate' => $result,
                'guest_uuid' => $cartContext['guest_uuid'],
            ],
            message: __('shipping::messages.estimate_ready'));
    }
}
