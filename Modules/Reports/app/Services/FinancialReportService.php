<?php

namespace Modules\Reports\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Post\Models\Post;
use Modules\Post\Models\Package;

class FinancialReportService
{
    /**
     * Scope for paid posts (is_paid = true AND package.price > 0)
     */
    protected function scopePaidPosts($query)
    {
        return $query->where('is_paid', true)
            ->whereHas('package', function ($q) {
                $q->where('price', '>', 0);
            });
    }

    /**
     * Get total revenue
     */
    public function getTotalRevenue(?Carbon $startDate = null, ?Carbon $endDate = null): float
    {
        $query = Post::where('is_paid', true)
            ->whereHas('package', function ($q) {
                $q->where('price', '>', 0);
            })
            ->with('package');
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        return $query->get()->sum(function($post) {
            return $post->package->price ?? 0;
        });
    }
    
    /**
     * Get revenue over time
     */
    public function getRevenueOverTime(string $interval = 'day', ?Carbon $startDate = null, ?Carbon $endDate = null): array
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
        
        $posts = Post::where('is_paid', true)
            ->whereHas('package', function ($q) {
                $q->where('price', '>', 0);
            })
            ->with('package')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('id', 'package_id', 'created_at')
            ->get();
        
        $grouped = $posts->groupBy(function($post) use ($dateFormat) {
            return $post->created_at->format(str_replace('%', '', $dateFormat));
        });
        
        return $grouped->map(function($group) {
            return [
                'revenue' => $group->sum(function($post) {
                    return $post->package->price ?? 0;
                })
            ];
        })->toArray();
    }
    
    /**
     * Get revenue by package
     */
    public function getRevenueByPackage(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = Post::where('is_paid', true)
            ->whereHas('package', function ($q) {
                $q->where('price', '>', 0);
            })
            ->with('package');
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        $posts = $query->get();
        
        $grouped = $posts->groupBy('package_id');
        
        return $grouped->map(function($group, $packageId) {
            $package = $group->first()->package;
            return [
                'package_id' => $packageId,
                'package_name' => $package->title ?? 'Unknown',
                'sales_count' => $group->count(),
                'total_revenue' => $group->sum(function($post) {
                    return $post->package->price ?? 0;
                })
            ];
        })->values()->toArray();
    }
    
    /**
     * Get average revenue per user
     */
    public function getAverageRevenuePerUser(?Carbon $startDate = null, ?Carbon $endDate = null): float
    {
        $query = Post::where('is_paid', true)
            ->whereHas('package', function ($q) {
                $q->where('price', '>', 0);
            })
            ->with('package');
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        $totalRevenue = $query->get()->sum(function($post) {
            return $post->package->price ?? 0;
        });
        
        $uniqueUsers = $query->distinct('user_id')->count('user_id');
        
        return $uniqueUsers > 0 ? round($totalRevenue / $uniqueUsers, 2) : 0;
    }
    
    /**
     * Get most popular packages (by sales)
     */
    public function getMostPopularPackages(?Carbon $startDate = null, ?Carbon $endDate = null, int $limit = 5): array
    {
        $revenueByPackage = $this->getRevenueByPackage($startDate, $endDate);
        
        usort($revenueByPackage, function($a, $b) {
            return $b['sales_count'] - $a['sales_count'];
        });
        
        return array_slice($revenueByPackage, 0, $limit);
    }
    
    /**
     * Get summary statistics
     */
    public function getSummary(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $totalRevenue = $this->getTotalRevenue($startDate, $endDate);
        $avgRevenuePerUser = $this->getAverageRevenuePerUser($startDate, $endDate);
        $topPackages = $this->getMostPopularPackages($startDate, $endDate, 3);
        
        // Paid posts = is_paid AND package.price > 0
        $paidPostsCount = Post::where('is_paid', true)
            ->whereHas('package', function ($q) {
                $q->where('price', '>', 0);
            })
            ->when($startDate && $endDate, function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->count();
        
        return [
            'total_revenue' => $totalRevenue,
            'paid_posts_count' => $paidPostsCount,
            'average_revenue_per_user' => $avgRevenuePerUser,
            'top_packages' => $topPackages,
        ];
    }
}
