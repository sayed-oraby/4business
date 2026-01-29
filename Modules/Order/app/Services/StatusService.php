<?php

namespace Modules\Order\Services;

use DomainException;
use Illuminate\Support\Facades\DB;
use Modules\Order\Models\InventoryReservation;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderStatus;
use Modules\Order\Models\OrderStatusHistory;

class StatusService
{
    public function changeStatus(Order $order, OrderStatus $targetStatus, ?int $actorId = null, ?string $comment = null): Order
    {
        return DB::transaction(function () use ($order, $targetStatus, $actorId, $comment) {
            $current = $order->fresh('status')->status;

            // If already at target status, return early (idempotent operation)
            if ($current && $current->id === $targetStatus->id) {
                return $order->fresh('status');
            }

            $this->guardTransition($order, $targetStatus, $current);

            $order->update([
                'order_status_id' => $targetStatus->id,
            ]);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'order_status_id' => $targetStatus->id,
                'user_id' => $actorId,
                'comment' => $comment,
            ]);

            if ($targetStatus->is_cancel || $targetStatus->is_refund) {
                $this->releaseInventory($order);
            }

            return $order->fresh('status');
        });
    }

    protected function guardTransition(Order $order, OrderStatus $target, ?OrderStatus $current = null): void
    {
        $current = $current ?? $order->fresh('status')->status;

        // Allow transitions between final statuses (e.g., from "completed" to "refunded")
        // Only block if trying to change FROM a final status TO a non-final status
        if ($current && $current->is_final && ! $target->is_final) {
            throw new DomainException('order::messages.status_final');
        }

        // Prevent changing from canceled/refunded status to active status
        if (($current?->is_cancel || $current?->is_refund) && ! $target->is_cancel && ! $target->is_refund) {
            throw new DomainException('order::messages.status_canceled');
        }
    }

    public function releaseInventory(Order $order): void
    {
        $reservations = InventoryReservation::query()
            ->where('order_id', $order->id)
            ->whereNull('released_at')
            ->whereNull('consumed_at')
            ->get();

        foreach ($reservations as $reservation) {
            \Modules\Product\Models\Product::query()
                ->where('id', $reservation->product_id)
                ->increment('qty', $reservation->qty_reserved);

            $reservation->update(['released_at' => now()]);
        }
    }

    public function consumeInventory(Order $order): void
    {
        InventoryReservation::query()
            ->where('order_id', $order->id)
            ->whereNull('consumed_at')
            ->update(['consumed_at' => now()]);
    }
}
