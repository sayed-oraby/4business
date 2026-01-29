<?php

namespace Modules\Reports\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Post\Models\JobOffer;

class JobOffersReportService
{
    /**
     * Get job offers by status
     */
    public function getOffersByStatus(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = JobOffer::query();
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        return $query->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();
    }
    
    /**
     * Get job offers over time
     */
    public function getOffersOverTime(string $interval = 'day', ?Carbon $startDate = null, ?Carbon $endDate = null): array
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
        
        return JobOffer::whereBetween('created_at', [$startDate, $endDate])
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
     * Get top employers (users who send the most offers)
     */
    public function getTopEmployers(?Carbon $startDate = null, ?Carbon $endDate = null, int $limit = 10): array
    {
        $query = JobOffer::with('user');
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        return $query->select('user_id', DB::raw('count(*) as offers_count'))
            ->groupBy('user_id')
            ->orderByDesc('offers_count')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'user_id' => $item->user_id,
                    'user_name' => $item->user->name ?? 'Unknown',
                    'offers_count' => $item->offers_count
                ];
            })
            ->toArray();
    }
    
    /**
     * Get posts with most job offers
     */
    public function getPostsWithMostOffers(?Carbon $startDate = null, ?Carbon $endDate = null, int $limit = 10): array
    {
        $query = JobOffer::with('post.user');
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        return $query->select('post_id', DB::raw('count(*) as offers_count'))
            ->groupBy('post_id')
            ->orderByDesc('offers_count')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'post_id' => $item->post_id,
                    'post_title' => $item->post->title ?? 'Unknown',
                    'post_owner' => $item->post->user->name ?? 'Unknown',
                    'offers_count' => $item->offers_count
                ];
            })
            ->toArray();
    }
    
    /**
     * Get acceptance rate statistics
     */
    public function getAcceptanceRate(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = JobOffer::query();
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        $total = $query->count();
        $accepted = $query->clone()->where('status', 'accepted')->count();
        $rejected = $query->clone()->where('status', 'rejected')->count();
        $pending = $query->clone()->where('status', 'pending')->count();
        
        $acceptanceRate = $total > 0 ? round(($accepted / $total) * 100, 2) : 0;
        $rejectionRate = $total > 0 ? round(($rejected / $total) * 100, 2) : 0;
        
        return [
            'total_offers' => $total,
            'accepted' => $accepted,
            'rejected' => $rejected,
            'pending' => $pending,
            'acceptance_rate' => $acceptanceRate,
            'rejection_rate' => $rejectionRate,
        ];
    }
    
    /**
     * Get average salary offered
     */
    public function getAverageSalary(?Carbon $startDate = null, ?Carbon $endDate = null): float
    {
        $query = JobOffer::query();
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        return round($query->avg('salary') ?? 0, 2);
    }
    
    /**
     * Get summary statistics
     */
    public function getSummary(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $acceptanceData = $this->getAcceptanceRate($startDate, $endDate);
        $avgSalary = $this->getAverageSalary($startDate, $endDate);
        
        return array_merge($acceptanceData, [
            'average_salary' => $avgSalary,
        ]);
    }
}
