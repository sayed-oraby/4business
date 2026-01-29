<?php

namespace Modules\Reports\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Order\Models\Order;
use Modules\Order\Models\Payment;

class OrdersReportService
{
    /**
     * Get orders statistics by status
     */
    public function getOrdersByStatus(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = Order::query();

        if ($startDate && $endDate) {
            $query->whereBetween('orders.created_at', [$startDate, $endDate]);
        }

        return $query
            ->join('order_statuses', 'orders.order_status_id', '=', 'order_statuses.id')
            ->select([
                'order_statuses.code',
                'order_statuses.title',
                'order_statuses.color',
                DB::raw('COUNT(orders.id) as orders_count'),
                DB::raw('SUM(orders.grand_total) as total_revenue'),
            ])
            ->groupBy('order_statuses.id', 'order_statuses.code', 'order_statuses.title', 'order_statuses.color')
            ->get()
            ->map(function ($item) {
                $title = $this->normalizeLocalized($item->title);

                return [
                    'status_code' => $item->code,
                    'status_title' => $title,
                    'status_color' => $item->color,
                    'orders_count' => (int) $item->orders_count,
                    'total_revenue' => (float) $item->total_revenue,
                ];
            })
            ->toArray();
    }

    /**
     * Get orders by payment status
     */
    public function getOrdersByPaymentStatus(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = Order::query();

        if ($startDate && $endDate) {
            $query->whereBetween('orders.created_at', [$startDate, $endDate]);
        }

        return $query
            ->select([
                'payment_status',
                DB::raw('COUNT(*) as orders_count'),
                DB::raw('SUM(grand_total) as total_amount'),
            ])
            ->groupBy('payment_status')
            ->get()
            ->map(function ($item) {
                return [
                    'payment_status' => $item->payment_status,
                    'orders_count' => (int) $item->orders_count,
                    'total_amount' => (float) $item->total_amount,
                ];
            })
            ->toArray();
    }

    /**
     * Get payment failures report
     */
    public function getPaymentFailures(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = Payment::query()
            ->join('orders', 'payments.order_id', '=', 'orders.id')
            ->leftJoin('order_addresses', function ($join) {
                $join->on('orders.id', '=', 'order_addresses.order_id')
                    ->where('order_addresses.type', 'shipping');
            })
            ->where('payments.status', 'failed');

        if ($startDate && $endDate) {
            $query->whereBetween('payments.created_at', [$startDate, $endDate]);
        }

        return $query
            ->select([
                'payments.id',
                'payments.order_id',
                'payments.provider',
                'payments.amount',
                'payments.currency',
                'order_addresses.country',
                'payments.payload',
                'payments.created_at',
            ])
            ->orderByDesc('payments.created_at')
            ->get()
            ->map(function ($item) {
                $failureReason = $this->extractFailureReason($item->payload);

                return [
                    'payment_id' => $item->id,
                    'order_id' => $item->order_id,
                    'provider' => $item->provider,
                    'amount' => (float) $item->amount,
                    'currency' => $item->currency,
                    'country' => $item->country,
                    'failure_reason' => $failureReason,
                    'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                ];
            })
            ->toArray();
    }

    /**
     * Get detailed orders report
     */
    public function getOrdersDetails(array $filters = []): array
    {
        $query = Order::query()
            ->with(['status', 'shippingAddress', 'payments']);

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->whereBetween('orders.created_at', [$filters['start_date'], $filters['end_date']]);
        }

        if (isset($filters['status'])) {
            $query->whereHas('status', fn ($q) => $q->where('code', $filters['status']));
        }

        if (isset($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (isset($filters['country'])) {
            $query->whereHas('shippingAddress', fn ($q) => $q->where('country', $filters['country']));
        }

        return $query
            ->orderByDesc('orders.created_at')
            ->get()
            ->map(function ($order) {
                $title = $this->normalizeLocalized($order->status?->title ?? '—');

                return [
                    'id' => $order->id,
                    'customer' => $order->user_id ? "#{$order->user_id}" : $order->guest_uuid,
                    'status' => $title,
                    'payment_status' => $order->payment_status,
                    'payment_method' => $order->payments->first()?->provider ?? '—',
                    'amount' => $order->grand_total,
                    'currency' => $order->currency,
                    'country' => $order->shippingAddress?->country ?? '—',
                    'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                ];
            })
            ->toArray();
    }

    protected function extractFailureReason(?array $payload): string
    {
        if (!$payload) {
            return 'Unknown';
        }

        // Try to extract failure reason from common payment provider responses
        $reasons = [
            'declined' => 'Card Declined',
            'insufficient_funds' => 'Insufficient Funds',
            'expired_card' => 'Expired Card',
            'invalid_card' => 'Invalid Card',
            'error' => 'Payment Error',
            'cancelled' => 'Cancelled',
        ];

        foreach ($reasons as $key => $label) {
            if (isset($payload[$key]) || (isset($payload['status']) && stripos($payload['status'], $key) !== false)) {
                return $label;
            }
        }

        return $payload['message'] ?? $payload['error'] ?? 'Unknown';
    }

    protected function normalizeLocalized($value): string
    {
        if (is_array($value)) {
            return $value[app()->getLocale()] ?? reset($value) ?? '—';
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded[app()->getLocale()] ?? reset($decoded) ?? $value;
            }
        }

        return (string) $value;
    }
}
