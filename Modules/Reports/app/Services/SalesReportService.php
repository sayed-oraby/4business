<?php

namespace Modules\Reports\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderItem;
use Modules\Order\Models\Payment;

class SalesReportService
{
    /**
     * Get sales statistics for a given period
     */
    public function getSalesStats(string $period = 'daily', ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = Order::query()->where('payment_status', 'paid');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } else {
            [$from, $to] = $this->getPeriodRange($period);
            $query->whereBetween('created_at', [$from, $to]);
        }

        $orders = $query->get();

        $totalSales = $orders->sum('grand_total');
        $ordersCount = $orders->count();
        $averageOrderValue = $ordersCount > 0 ? $totalSales / $ordersCount : 0;

        // Previous period comparison
        $previousPeriod = $this->getPreviousPeriodStats($period, $startDate, $endDate);
        $growth = $previousPeriod['total_sales'] > 0
            ? (($totalSales - $previousPeriod['total_sales']) / $previousPeriod['total_sales']) * 100
            : 0;

        return [
            'total_sales' => $totalSales,
            'orders_count' => $ordersCount,
            'average_order_value' => $averageOrderValue,
            'previous_period' => $previousPeriod,
            'growth_percentage' => $growth,
        ];
    }

    /**
     * Get sales by product
     */
    public function getSalesByProduct(?Carbon $startDate = null, ?Carbon $endDate = null, int $limit = 10): array
    {
        $query = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'paid');

        if ($startDate && $endDate) {
            $query->whereBetween('orders.created_at', [$startDate, $endDate]);
        }

        return $query
            ->select([
                'order_items.product_id',
                'order_items.title',
                'order_items.sku',
                DB::raw('SUM(order_items.qty) as total_qty'),
                DB::raw('SUM(order_items.line_total) as total_revenue'),
                DB::raw('COUNT(DISTINCT orders.id) as orders_count'),
            ])
            ->groupBy('order_items.product_id', 'order_items.title', 'order_items.sku')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'title' => $item->title,
                    'sku' => $item->sku,
                    'total_qty' => (int) $item->total_qty,
                    'total_revenue' => (float) $item->total_revenue,
                    'orders_count' => (int) $item->orders_count,
                    'profit_margin' => $this->calculateProfitMargin($item->product_id, $item->total_revenue),
                ];
            })
            ->toArray();
    }

    /**
     * Get sales by country
     */
    public function getSalesByCountry(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = Order::query()
            ->join('order_addresses', 'orders.id', '=', 'order_addresses.order_id')
            ->where('orders.payment_status', 'paid')
            ->where('order_addresses.type', 'shipping');

        if ($startDate && $endDate) {
            $query->whereBetween('orders.created_at', [$startDate, $endDate]);
        }

        return $query
            ->select([
                'order_addresses.country',
                DB::raw('COUNT(DISTINCT orders.id) as orders_count'),
                DB::raw('SUM(orders.grand_total) as total_revenue'),
                DB::raw('AVG(orders.grand_total) as average_order_value'),
                DB::raw('GROUP_CONCAT(DISTINCT orders.currency) as currencies'),
            ])
            ->groupBy('order_addresses.country')
            ->orderByDesc('total_revenue')
            ->get()
            ->map(function ($item) {
                return [
                    'country' => $item->country,
                    'orders_count' => (int) $item->orders_count,
                    'total_revenue' => (float) $item->total_revenue,
                    'average_order_value' => (float) $item->average_order_value,
                    'currencies' => explode(',', $item->currencies ?? ''),
                ];
            })
            ->toArray();
    }

    /**
     * Get sales by payment method
     */
    public function getSalesByPaymentMethod(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = Payment::query()
            ->join('orders', 'payments.order_id', '=', 'orders.id')
            ->where('payments.status', 'paid');

        if ($startDate && $endDate) {
            $query->whereBetween('payments.created_at', [$startDate, $endDate]);
        }

        return $query
            ->select([
                'payments.provider',
                DB::raw('COUNT(DISTINCT payments.id) as transactions_count'),
                DB::raw('SUM(payments.amount) as total_amount'),
                DB::raw('AVG(payments.amount) as average_amount'),
            ])
            ->groupBy('payments.provider')
            ->orderByDesc('total_amount')
            ->get()
            ->map(function ($item) {
                return [
                    'provider' => $item->provider,
                    'transactions_count' => (int) $item->transactions_count,
                    'total_amount' => (float) $item->total_amount,
                    'average_amount' => (float) $item->average_amount,
                ];
            })
            ->toArray();
    }

    /**
     * Get sales by time (hourly, daily, weekly)
     */
    public function getSalesByTime(string $groupBy = 'hour', ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = Order::query()->where('payment_status', 'paid');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $dateFormat = match ($groupBy) {
            'hour' => '%Y-%m-%d %H:00:00',
            'day' => '%Y-%m-%d',
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            default => '%Y-%m-%d',
        };

        return $query
            ->select([
                DB::raw("DATE_FORMAT(created_at, '{$dateFormat}') as time_period"),
                DB::raw('COUNT(*) as orders_count'),
                DB::raw('SUM(grand_total) as total_revenue'),
            ])
            ->groupBy('time_period')
            ->orderBy('time_period')
            ->get()
            ->map(function ($item) {
                return [
                    'time_period' => $item->time_period,
                    'orders_count' => (int) $item->orders_count,
                    'total_revenue' => (float) $item->total_revenue,
                ];
            })
            ->toArray();
    }

    protected function getPeriodFilter(string $period): array
    {
        return ['created_at', '>=', Carbon::today()];
    }

    protected function getPeriodRange(string $period): array
    {
        $now = Carbon::now();

        return match ($period) {
            'daily' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'weekly' => [$now->copy()->startOfWeek(), $now->copy()->endOfDay()],
            'monthly' => [$now->copy()->startOfMonth(), $now->copy()->endOfDay()],
            'yearly' => [$now->copy()->startOfYear(), $now->copy()->endOfDay()],
            default => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
        };
    }

    protected function getPreviousPeriodStats(string $period, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = Order::query()->where('payment_status', 'paid');

        if ($startDate && $endDate) {
            $duration = $endDate->diffInDays($startDate);
            $query->whereBetween('created_at', [
                $startDate->copy()->subDays($duration),
                $startDate->copy()->subDay(),
            ]);
        } else {
            [$from, $to] = $this->getPreviousPeriodRange($period);
            $query->whereBetween('created_at', [$from, $to]);
        }

        $orders = $query->get();

        return [
            'total_sales' => $orders->sum('grand_total'),
            'orders_count' => $orders->count(),
        ];
    }

    protected function getPreviousPeriodFilter(string $period): array
    {
        return ['created_at', '>=', Carbon::yesterday()];
    }

    protected function getPreviousPeriodRange(string $period): array
    {
        return match ($period) {
            'daily' => [Carbon::yesterday()->startOfDay(), Carbon::yesterday()->endOfDay()],
            'weekly' => [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()],
            'monthly' => [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()],
            'yearly' => [Carbon::now()->subYear()->startOfYear(), Carbon::now()->subYear()->endOfYear()],
            default => [Carbon::yesterday()->startOfDay(), Carbon::yesterday()->endOfDay()],
        };
    }

    protected function calculateProfitMargin(?int $productId, float $revenue): float
    {
        // TODO: Implement profit margin calculation based on product cost
        // For now, return a placeholder
        return 0.0;
    }
}
