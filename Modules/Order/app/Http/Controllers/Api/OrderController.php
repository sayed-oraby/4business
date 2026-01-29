<?php

namespace Modules\Order\Http\Controllers\Api;

use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Cart\Services\CartService;
use Modules\Order\Http\Requests\Api\StoreOrderRequest;
use Modules\Order\Http\Resources\OrderResource;
use Modules\Order\Models\Order;
use Modules\Order\Services\OrderService;
use Modules\Order\Services\PaymentService;
use Modules\Shipping\Models\UserAddress;
use Modules\Shipping\Services\ShippingCalculator;
use Modules\User\Models\User;
use Modules\Activity\Services\AuditLogger;

class OrderController extends Controller
{
    use ApiResponse;

    public function store(
        StoreOrderRequest $request,
        CartService $cartService,
        OrderService $orderService,
        PaymentService $paymentService,
        ShippingCalculator $shippingCalculator,
        AuditLogger $auditLogger
    ): JsonResponse {
        /** @var User|null $user */
        $user = $request->user('sanctum') ?? $request->user();
        $guestUuid = $request->input('guest_uuid');

        // Allow top-level user_address_id for convenience
        if (! $request->filled('shipping') && $request->filled('user_address_id')) {
            $request->merge([
                'shipping' => ['user_address_id' => $request->input('user_address_id')],
            ]);
        }

        $cartContext = $cartService->getCart($user, $guestUuid, false);
        $cart = $cartContext['cart'];
        $guestUuid = $cartContext['guest_uuid'];

        if (! $cart || $cart->items()->count() === 0) {
            return $this->errorResponse(
                message: __('cart::cart.errors.empty'),
                status: 422
            );
        }

        $shippingAddress = $request->input('shipping', []);
        $billingAddress = $request->input('billing', $shippingAddress);
        $shippingAmount = null;

        if (isset($shippingAddress['user_address_id']) && $user) {
            $userAddress = UserAddress::query()
                ->where('user_id', $user->id)
                ->with('country')
                ->findOrFail($shippingAddress['user_address_id']);

            $estimate = $shippingCalculator->calculate($cart->load('items.product'), $userAddress);
            $shippingAmount = $estimate['amount'];

            $shippingAddress = [
                'user_address_id' => $userAddress->id,
                'full_name' => $userAddress->full_name,
                'phone' => $userAddress->phone,
                'address' => $orderService->formatAddressFromUserAddress($userAddress),
                'city' => $userAddress->city_name_en ?? $userAddress->city_name_ar,
                'state' => $userAddress->state_name_en ?? $userAddress->state_name_ar ?? $userAddress->state_code,
                'country' => $userAddress->country_iso2,
                'postal_code' => $userAddress->postal_code,
            ];

            $billingAddress = $billingAddress['user_address_id'] ?? null
                ? $shippingAddress
                : $billingAddress;
        }

        $order = $orderService->createFromCart(
            $cart->load('items.product'),
            [
                'shipping' => $shippingAddress,
                'billing' => $billingAddress,
            ],
            ['payment_method' => $request->input('payment_method', 'sadad')],
            $shippingAmount
        );

        // Audit log: order created
        $auditLogger->log(
            $user?->id,
            'orders.create',
            __('order::messages.order_created'),
            [
                'context' => 'orders',
                'notification_type' => 'important',
                'notification_message_key' => 'order::messages.order_created',
                'notification_message_params' => ['order_id' => $order->id],
                'title_key' => 'order::messages.order_created',
                'title_params' => ['order_id' => $order->id],
            ]
        );

        // Get shipping address for payment
        $shippingAddress = $order->shippingAddress;
        $payment = $paymentService->createSadadPayment(
            $order,
            [
                'name' => $shippingAddress?->full_name ?? $request->input('shipping.full_name'),
                'mobile' => $shippingAddress?->phone ?? $request->input('shipping.phone'),
                'email' => $user?->email,
            ],
            route('api.orders.payments.sadad.result')
        );

        return $this->successResponse(
            data: [
                'order' => (new OrderResource($order->load('items', 'payments', 'shippingAddress')))->resolve(),
                'payment' => $payment,
                'payment_link' => $payment->invoice_url,
                'guest_uuid' => $guestUuid,
            ],
            message: __('order::messages.order_created'),
            status: 201
        );
    }

    public function show(Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        return $this->successResponse(
            data: [
                'order' => new OrderResource($order->load('items', 'payments', 'shippingAddress')),
            ],
            message: __('order::messages.order_loaded'));
    }
}
