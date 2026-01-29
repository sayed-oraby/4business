<?php

namespace Modules\Reports\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Order\Models\Order;
use Modules\User\Models\User;

class UsersReportService
{
    /**
     * Get user signups report
     */
    public function getUserSignups(string $period = 'daily', ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = User::query();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } else {
            [$from, $to] = $this->getPeriodRange($period);
            $query->whereBetween('created_at', [$from, $to]);
        }

        $totalSignups = $query->count();

        // By country (if country field exists in users table or via addresses)
        $byCountry = $this->getSignupsByCountry($startDate, $endDate);

        // By device (if device tracking exists)
        $byDevice = $this->getSignupsByDevice($startDate, $endDate);

        return [
            'total_signups' => $totalSignups,
            'by_country' => $byCountry,
            'by_device' => $byDevice,
        ];
    }

    /**
     * Get top buyers report
     */
    public function getTopBuyers(int $limit = 10, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = Order::query()
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.payment_status', 'paid');

        if ($startDate && $endDate) {
            $query->whereBetween('orders.created_at', [$startDate, $endDate]);
        }

        return $query
            ->select([
                'users.id',
                'users.name',
                'users.email',
                DB::raw('COUNT(orders.id) as orders_count'),
                DB::raw('SUM(orders.grand_total) as total_spent'),
                DB::raw('AVG(orders.grand_total) as average_order_value'),
            ])
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total_spent')
            ->limit($limit)
            ->get()
            ->map(function ($user) {
                return [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'orders_count' => (int) $user->orders_count,
                    'total_spent' => (float) $user->total_spent,
                    'average_order_value' => (float) $user->average_order_value,
                    'monthly_spending' => $this->calculateMonthlySpending($user->id),
                ];
            })
            ->toArray();
    }

    /**
     * Get user behavior analytics
     */
    public function getUserBehavior(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        // This would require tracking views, page visits, etc.
        // For now, return placeholder structure
        return [
            'most_viewed_products' => [],
            'most_visited_pages' => [],
            'conversion_rate' => 0.0,
            'message' => 'User behavior analytics - requires tracking implementation',
        ];
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

    protected function getSignupsByCountry(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        // This would require country field in users table or via addresses
        // For now, return empty array
        return [];
    }

    protected function getSignupsByDevice(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        // This would require device tracking in users table or activity logs
        // For now, return placeholder
        return [
            'ios' => 0,
            'android' => 0,
            'web' => 0,
        ];
    }

    protected function calculateMonthlySpending(int $userId): float
    {
        return (float) Order::query()
            ->where('user_id', $userId)
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->sum('grand_total');
    }
    
    /**
     * Get users growth over time
     */
    public function getUsersOverTime(string $interval = 'day', ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? now()->subDays(30);
        $endDate = $endDate ?? now();
        
        $dateFormat = match($interval) {
            'hour' => '%Y-%m-%d %H:00:00',
            'day' => '%Y-%m-%d',
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            'year' => '%Y',
            default => '%Y-%m-%d',
        };
        
        return User::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw("DATE_FORMAT(created_at, '{$dateFormat}') as period"),
                DB::raw('count(*) as count')
            )
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->toArray();
    }
    
    /**
     * Get summary statistics for dashboard
     */
    public function getSummary(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = User::query();
        
        $totalUsers = User::count();
        $newUsers = $startDate && $endDate 
            ? User::whereBetween('created_at', [$startDate, $endDate])->count()
            : User::whereDate('created_at', today())->count();
        
        // Active users - users created in the last 30 days
        $activeUsers = User::where('created_at', '>=', now()->subDays(30))->count();
        
        // Growth rate calculation
        $growthRate = 0;
        if ($startDate && $endDate) {
            $daysDiff = $startDate->diffInDays($endDate);
            $prevStartDate = $startDate->copy()->subDays($daysDiff);
            $prevEndDate = $startDate->copy()->subDay();
            $previousPeriodUsers = User::whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();
            
            if ($previousPeriodUsers > 0) {
                $growthRate = round((($newUsers - $previousPeriodUsers) / $previousPeriodUsers) * 100, 2);
            } else {
                $growthRate = $newUsers > 0 ? 100 : 0;
            }
        }
        
        return [
            'total_users' => $totalUsers,
            'new_registrations' => $newUsers,
            'active_users' => $activeUsers,
            'growth_rate' => $growthRate,
        ];
    }
}

