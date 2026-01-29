<?php

namespace Modules\Order\Services;

use Modules\Order\Models\InventoryReservation;
use Modules\Order\Models\Order;
use Modules\Product\Models\Product;

class InventoryService
{
    public function release(Order $order): void
    {
        $reservations = InventoryReservation::query()
            ->where('order_id', $order->id)
            ->whereNull('released_at')
            ->whereNull('consumed_at')
            ->get();

        foreach ($reservations as $reservation) {
            Product::query()
                ->where('id', $reservation->product_id)
                ->increment('qty', $reservation->qty_reserved);

            $reservation->update(['released_at' => now()]);
        }
    }

    public function consume(Order $order): void
    {
        InventoryReservation::query()
            ->where('order_id', $order->id)
            ->whereNull('consumed_at')
            ->update(['consumed_at' => now()]);
    }
}
