<?php

namespace Modules\Core\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\View\View;
use Modules\Post\Models\Post;
use Modules\User\Models\User;

class DashboardController extends Controller
{
    /**
     * Display the dashboard home page with job posting statistics
     */
    public function index(): View
    {
        // Total Posts
        $totalPosts = Post::count();

        // Active Posts (approved, not expired, and properly paid if paid package)
        // - Free packages: approved
        // - Paid packages: approved AND is_paid = true
        $activePosts = Post::where('status', 'approved')
            ->where(function($query) {
                $query->where('end_date', '>=', now())
                      ->orWhereNull('end_date');
            })
            ->where(function ($query) {
                // Free packages (no package OR package price = 0)
                $query->whereDoesntHave('package')
                    ->orWhereHas('package', function ($q) {
                        $q->where('price', '<=', 0);
                    })
                    // OR paid packages that have been paid
                    ->orWhere('is_paid', true);
            })
            ->count();

        // Posts Under Review - only pending posts that are ready for review
        // (free packages OR paid packages with is_paid = true)
        $pendingPosts = Post::where('status', 'pending')
            ->where(function ($query) {
                // Free packages
                $query->whereDoesntHave('package')
                    ->orWhereHas('package', function ($q) {
                        $q->where('price', '<=', 0);
                    })
                    // OR paid packages that have been paid
                    ->orWhere('is_paid', true);
            })
            ->count();

        // Expired Posts
        $expiredPosts = Post::where('status', 'expired')
            ->orWhere(function($query) {
                $query->where('end_date', '<', now())
                      ->whereNotNull('end_date');
            })
            ->count();

        // Featured Posts (with package price > 0 AND is_paid = true)
        $featuredPosts = Post::where('is_paid', true)
            ->whereHas('package', function ($q) {
                $q->where('price', '>', 0);
            })
            ->count();

        // Total Revenue (sum of package prices for paid posts)
        $totalRevenue = Post::where('is_paid', true)
            ->with('package')
            ->get()
            ->sum(function($post) {
                return $post->package->price ?? 0;
            });

        // Total Members
        $totalMembers = User::count();

        // Posts by Status for table - exclude awaiting_payment
        // For 'pending', only count posts ready for review (paid or free)
        $byStatus = [];
        
        // Get approved count
        $byStatus['approved'] = Post::where('status', 'approved')
            ->where(function ($query) {
                $query->whereDoesntHave('package')
                    ->orWhereHas('package', function ($q) {
                        $q->where('price', '<=', 0);
                    })
                    ->orWhere('is_paid', true);
            })
            ->count();
        
        // Get pending count (only ready for review)
        $byStatus['pending'] = Post::where('status', 'pending')
            ->where(function ($query) {
                $query->whereDoesntHave('package')
                    ->orWhereHas('package', function ($q) {
                        $q->where('price', '<=', 0);
                    })
                    ->orWhere('is_paid', true);
            })
            ->count();
        
        // Get rejected count
        $byStatus['rejected'] = Post::where('status', 'rejected')->count();
        
        // Get expired count
        $byStatus['expired'] = Post::where('status', 'expired')->count();

        // Posts by Category for table (top 10) - only active/approved posts
        $locale = app()->getLocale();
        $byCategory = Post::where('status', 'approved')
            ->where(function($query) {
                $query->where('end_date', '>=', now())
                      ->orWhereNull('end_date');
            })
            ->where(function ($query) {
                $query->whereDoesntHave('package')
                    ->orWhereHas('package', function ($q) {
                        $q->where('price', '<=', 0);
                    })
                    ->orWhere('is_paid', true);
            })
            ->selectRaw("category_id, COUNT(*) as count")
            ->with('category')
            ->groupBy('category_id')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->map(function($item) use ($locale) {
                return [
                    'category' => $item->category?->title ?? __('dashboard.uncategorized'),
                    'count' => $item->count,
                ];
            })
            ->toArray();

        // Latest Posts Under Review (for table) - only show posts ready for review:
        // - Free packages (price = 0) 
        // - OR Paid packages (price > 0) that are actually paid (is_paid = true)
        $latestPendingPosts = Post::where('status', 'pending')
            ->with(['user', 'category', 'postType', 'package'])
            ->where(function ($query) {
                // Free packages (no package OR package price = 0)
                $query->whereDoesntHave('package')
                    ->orWhereHas('package', function ($q) {
                        $q->where('price', '<=', 0);
                    })
                    // OR paid packages that have been paid
                    ->orWhere('is_paid', true);
            })
            ->latest('created_at')
            ->take(10)
            ->get();

        return view('authentication::dashboard.home', compact(
            'totalPosts',
            'activePosts',
            'pendingPosts',
            'expiredPosts',
            'featuredPosts',
            'totalRevenue',
            'totalMembers',
            'byStatus',
            'byCategory',
            'latestPendingPosts'
        ));
    }
}
