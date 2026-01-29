<?php

namespace Modules\Reports\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Post\Models\Post;

class PostsReportService
{
    /**
     * Apply ready posts filter (free OR is_paid)
     */
    protected function applyReadyPostsFilter($query)
    {
        return $query->where(function ($q) {
            // Free packages (no package OR package price = 0)
            $q->where(function ($subQ) {
                $subQ->whereDoesntHave('package');
            })
            ->orWhere(function ($subQ) {
                $subQ->whereHas('package', function ($pq) {
                    $pq->where('price', '<=', 0);
                });
            })
            // OR paid packages that have been paid
            ->orWhere('is_paid', true);
        });
    }

    /**
     * Get posts count by status - only ready posts
     */
    public function getPostsByStatus(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $result = [];
        
        // Build base date filter
        $dateFilter = function($query) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        };
        
        // Get approved count (ready posts only)
        $approvedQuery = Post::where('status', 'approved');
        $dateFilter($approvedQuery);
        $approvedQuery = $this->applyReadyPostsFilter($approvedQuery);
        $result['approved'] = $approvedQuery->count();
        
        // Get pending count (ready for review only)
        $pendingQuery = Post::where('status', 'pending');
        $dateFilter($pendingQuery);
        $pendingQuery = $this->applyReadyPostsFilter($pendingQuery);
        $result['pending'] = $pendingQuery->count();
        
        // Get rejected count (all rejected, no filter needed)
        $rejectedQuery = Post::where('status', 'rejected');
        $dateFilter($rejectedQuery);
        $result['rejected'] = $rejectedQuery->count();
        
        // Get expired count
        $expiredQuery = Post::where('status', 'expired');
        $dateFilter($expiredQuery);
        $result['expired'] = $expiredQuery->count();
        
        // Filter out zero counts
        return array_filter($result, fn($count) => $count > 0);
    }
    
    /**
     * Get posts over time
     */
    public function getPostsOverTime(string $interval = 'day', ?Carbon $startDate = null, ?Carbon $endDate = null): array
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
        
        return Post::whereBetween('created_at', [$startDate, $endDate])
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
     * Get posts by category - only active posts
     */
    public function getPostsByCategory(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = Post::with('category')
            ->where('status', 'approved')
            ->where(function($q) {
                $q->where('end_date', '>=', now())
                  ->orWhereNull('end_date');
            });
        
        // Only ready posts (free OR paid)
        $query = $this->applyReadyPostsFilter($query);
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        return $query->select('category_id', DB::raw('count(*) as count'))
            ->groupBy('category_id')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category->title ?? 'Unknown',
                    'count' => $item->count
                ];
            })
            ->toArray();
    }
    
    /**
     * Get posts by type (featured vs regular)
     * Featured = package price > 0 AND is_paid = true
     * Regular = free packages
     */
    public function getPostsByType(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $dateFilter = function($query) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        };
        
        // Regular = free packages (price = 0 or no package)
        $regularQuery = Post::query()
            ->where(function ($q) {
                $q->whereDoesntHave('package')
                    ->orWhereHas('package', function ($pq) {
                        $pq->where('price', '<=', 0);
                    });
            });
        $dateFilter($regularQuery);
        $regular = $regularQuery->count();
        
        // Featured = paid packages with is_paid = true
        $featuredQuery = Post::where('is_paid', true)
            ->whereHas('package', function ($q) {
                $q->where('price', '>', 0);
            });
        $dateFilter($featuredQuery);
        $featured = $featuredQuery->count();
        
        return [
            'regular' => $regular,
            'featured' => $featured,
        ];
    }
    
    /**
     * Get posts by city - only active posts
     */
    public function getPostsByCity(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = Post::with('city')
            ->where('status', 'approved')
            ->where(function($q) {
                $q->where('end_date', '>=', now())
                  ->orWhereNull('end_date');
            });
        
        $query = $this->applyReadyPostsFilter($query);
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        return $query->select('city_id', DB::raw('count(*) as count'))
            ->whereNotNull('city_id')
            ->groupBy('city_id')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'city' => $item->city->name ?? 'Unknown',
                    'count' => $item->count
                ];
            })
            ->toArray();
    }
    
    /**
     * Get most active users (by posts count) - only active posts
     */
    public function getMostActiveUsers(?Carbon $startDate = null, ?Carbon $endDate = null, int $limit = 10): array
    {
        $query = Post::with('user')
            ->where('status', 'approved');
        
        $query = $this->applyReadyPostsFilter($query);
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        return $query->select('user_id', DB::raw('count(*) as posts_count'))
            ->groupBy('user_id')
            ->orderByDesc('posts_count')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'user_id' => $item->user_id,
                    'user_name' => $item->user->name ?? 'Unknown',
                    'posts_count' => $item->posts_count
                ];
            })
            ->toArray();
    }
    
    /**
     * Get summary statistics - matching dashboard home logic
     */
    public function getSummary(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $dateFilter = function($query) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        };
        
        // Total posts
        $totalQuery = Post::query();
        $dateFilter($totalQuery);
        $totalPosts = $totalQuery->count();
        
        // Active: approved, not expired, and ready (free OR paid)
        $activeQuery = Post::where('status', 'approved')
            ->where(function($q) {
                $q->where('end_date', '>=', now())
                  ->orWhereNull('end_date');
            });
        $activeQuery = $this->applyReadyPostsFilter($activeQuery);
        $dateFilter($activeQuery);
        $activePosts = $activeQuery->count();
        
        // Pending: only ready for review (free OR paid)
        $pendingQuery = Post::where('status', 'pending');
        $pendingQuery = $this->applyReadyPostsFilter($pendingQuery);
        $dateFilter($pendingQuery);
        $pendingPosts = $pendingQuery->count();
        
        // Expired
        $expiredQuery = Post::where('status', 'expired');
        $dateFilter($expiredQuery);
        $expiredPosts = $expiredQuery->count();
        
        // Featured: package price > 0 AND is_paid = true
        $featuredQuery = Post::where('is_paid', true)
            ->whereHas('package', function ($q) {
                $q->where('price', '>', 0);
            });
        $dateFilter($featuredQuery);
        $featuredPosts = $featuredQuery->count();
        
        return [
            'total' => $totalPosts,
            'active' => $activePosts,
            'pending' => $pendingPosts,
            'expired' => $expiredPosts,
            'featured' => $featuredPosts,
        ];
    }
}
