<?php

namespace Modules\Order\Services;

use Illuminate\Support\Facades\DB;
use Modules\Cart\Models\Cart;
use Modules\Order\Models\InventoryReservation;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderAddress;
use Modules\Order\Models\OrderItem;
use Modules\Order\Models\OrderStatus;

class OrderService
{
    public function createFromCart(
        Cart $cart,
        array $addresses = [],
        array $meta = [],
        ?float $shippingAmount = null
    ): Order
    {
        return DB::transaction(function () use ($cart, $addresses, $meta, $shippingAmount) {
            $status = OrderStatus::query()->where('is_default', true)->first();

            $shippingData = $addresses['shipping'] ?? null;
            $billingData = $addresses['billing'] ?? $shippingData;

            $shippingTotal = $shippingAmount ?? $cart->shipping_total ?? 0;
            $grandTotal = ($cart->grand_total ?? 0) + $shippingTotal;

            $order = Order::create([
                'user_id' => $cart->user_id,
                'guest_uuid' => $cart->guest_uuid,
                'order_status_id' => $status?->id,
                'payment_status' => 'pending',
                'currency' => 'KWD',
                'subtotal' => $cart->subtotal ?? 0,
                'discount_total' => $cart->discount_total ?? 0,
                'shipping_total' => $shippingTotal,
                'tax_total' => $cart->tax_total ?? 0,
                'grand_total' => $grandTotal,
                'items_count' => $cart->items?->count() ?? $cart->items()->count(),
                'items_qty' => $cart->items?->sum('quantity') ?? $cart->items()->sum('quantity'),
                'shipping_address' => $shippingData,
                'billing_address' => $billingData,
                'meta' => $meta,
                'placed_at' => now(),
            ]);

            // Create shipping address
            if ($shippingData) {
                $addressCreated = false;

                // If user_address_id is provided, use it (user saved address)
                if (isset($shippingData['user_address_id']) && $shippingData['user_address_id'] && $cart->user_id) {
                    $userAddressId = $shippingData['user_address_id'];
                    // Load the saved address to copy its data
                    $userAddress = \Modules\Shipping\Models\UserAddress::find($userAddressId);
                    if ($userAddress && $userAddress->user_id === $cart->user_id) {
                        OrderAddress::create([
                            'order_id' => $order->id,
                            'user_address_id' => $userAddressId,
                            'type' => 'shipping',
                            'full_name' => $userAddress->full_name,
                            'phone' => $userAddress->phone,
                            'address' => $this->formatAddressFromUserAddress($userAddress),
                            'city' => $userAddress->city_name_en ?? $userAddress->city_name_ar,
                            'state' => $userAddress->state_name_en ?? $userAddress->state_name_ar ?? $userAddress->state_code,
                            'country' => $userAddress->country_iso2,
                            'postal_code' => $userAddress->postal_code,
                        ]);
                        $addressCreated = true;
                    }
                }

                // Guest address or new address data (if not using saved address)
                if (! $addressCreated) {
                    OrderAddress::create([
                        'order_id' => $order->id,
                        'user_address_id' => null,
                        'type' => 'shipping',
                        'full_name' => $shippingData['full_name'] ?? '',
                        'phone' => $shippingData['phone'] ?? '',
                        'address' => $shippingData['address'] ?? null,
                        'city' => $shippingData['city'] ?? null,
                        'state' => $shippingData['state'] ?? null,
                        'country' => $shippingData['country'] ?? null,
                        'postal_code' => $shippingData['postal_code'] ?? null,
                    ]);
                }
            }

            // Billing address (if provided)
            if ($billingData && empty($billingData['user_address_id'])) {
                OrderAddress::create([
                    'order_id' => $order->id,
                    'user_address_id' => $billingData['user_address_id'] ?? null,
                    'type' => 'billing',
                    'full_name' => $billingData['full_name'] ?? '',
                    'phone' => $billingData['phone'] ?? '',
                    'address' => $billingData['address'] ?? null,
                    'city' => $billingData['city'] ?? null,
                    'state' => $billingData['state'] ?? null,
                    'country' => $billingData['country'] ?? null,
                    'postal_code' => $billingData['postal_code'] ?? null,
                ]);
            }

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'sku' => $item->product?->sku,
                    'title' => $item->product?->title ?? $item->product?->title_en ?? __('order::messages.product_fallback'),
                    'qty' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'line_total' => $item->line_total,
                    'discount_total' => 0,
                    'tax_total' => 0,
                    'weight' => $item->product->weight ?? 0,
                    'meta' => [
                        'snapshot' => $item->product?->toArray(),
                    ],
                ]);

                // hold inventory immediately
                if ($item->product_id) {
                    $item->product()->decrement('qty', $item->quantity);
                }

                InventoryReservation::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'qty_reserved' => $item->quantity,
                ]);
            }

            return $order->fresh(['items', 'addresses']);
        });
    }

    public function formatAddressFromUserAddress(\Modules\Shipping\Models\UserAddress $userAddress): string
    {
        $parts = array_filter([
            $userAddress->block ? "Block {$userAddress->block}" : null,
            $userAddress->street ? "Street {$userAddress->street}" : null,
            $userAddress->avenue ? "Avenue {$userAddress->avenue}" : null,
            $userAddress->building ? "Building {$userAddress->building}" : null,
            $userAddress->floor ? "Floor {$userAddress->floor}" : null,
            $userAddress->apartment ? "Apartment {$userAddress->apartment}" : null,
        ]);

        $address = implode(', ', $parts);

        if ($userAddress->additional_details) {
            $address .= ($address ? '. ' : '').$userAddress->additional_details;
        }

        return $address ?: '';
    }
}
