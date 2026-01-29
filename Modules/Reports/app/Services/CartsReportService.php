<?php

namespace Modules\Reports\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Modules\Cart\Models\Cart;

class CartsReportService
{
    public function stats(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = Cart::query()->withCount('items');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $carts = $query->get();

        return [
            'total' => $carts->count(),
            'active' => $carts->where('status', Cart::STATUS_ACTIVE)->count(),
            'abandoned' => $carts->where('status', Cart::STATUS_ABANDONED)->count(),
            'expired' => $carts->where('status', Cart::STATUS_EXPIRED)->count(),
            'checked_out' => $carts->where('status', Cart::STATUS_CHECKED_OUT)->count(),
            'total_value' => (float) $carts->sum('grand_total'),
            'average_value' => $carts->count() ? (float) $carts->avg('grand_total') : 0,
            'average_items' => $carts->count() ? (float) $carts->avg('items_count') : 0,
        ];
    }

    public function topCarts(string $status = Cart::STATUS_ABANDONED, int $limit = 10): array
    {
        return Cart::query()
            ->with(['user'])
            ->where('status', $status)
            ->orderByDesc('grand_total')
            ->limit($limit)
            ->get()
            ->map(fn (Cart $cart) => $this->mapCart($cart))
            ->toArray();
    }

    public function latest(int $limit = 10): array
    {
        return Cart::query()
            ->with(['user'])
            ->latest('updated_at')
            ->limit($limit)
            ->get()
            ->map(fn (Cart $cart) => $this->mapCart($cart))
            ->toArray();
    }

    protected function mapCart(Cart $cart): array
    {
        return [
            'id' => $cart->id,
            'user' => $cart->user?->name ?: ($cart->guest_uuid ?? 'Guest'),
            'status' => $cart->status,
            'grand_total' => (float) $cart->grand_total,
            'currency' => $cart->currency ?? 'KWD',
            'items_count' => (int) $cart->items_count ?? $cart->items()->count(),
            'updated_at' => optional($cart->updated_at)->toDateTimeString(),
        ];
    }
}
