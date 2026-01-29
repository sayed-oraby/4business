<?php

namespace Modules\Reports\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Reports\Services\PostsReportService;
use Modules\Reports\Services\JobOffersReportService;
use Modules\Reports\Services\UsersReportService;
use Modules\Reports\Services\FinancialReportService;

class ReportsController extends Controller
{
    public function __construct(
        protected PostsReportService $postsReport,
        protected JobOffersReportService $jobOffersReport,
        protected UsersReportService $usersReport,
        protected FinancialReportService $financialReport
    ) {}

    /**
     * Display reports dashboard
     */
    public function index(Request $request)
    {
        [$startDate, $endDate] = $this->getDateRange($request);
        
        // Get summary from all services
        $postsSummary = $this->postsReport->getSummary($startDate, $endDate);
        $jobOffersSummary = $this->jobOffersReport->getSummary($startDate, $endDate);
        $usersSummary = $this->usersReport->getSummary($startDate, $endDate);
        $financialSummary = $this->financialReport->getSummary($startDate, $endDate);
        
        return view('reports::dashboard.index', compact(
            'postsSummary',
            'jobOffersSummary',
            'usersSummary',
            'financialSummary',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Display posts reports
     */
    public function posts(Request $request)
    {
        [$startDate, $endDate] = $this->getDateRange($request);
        
        $summary = $this->postsReport->getSummary($startDate, $endDate);
        $byStatus = $this->postsReport->getPostsByStatus($startDate, $endDate);
        $byCategory = $this->postsReport->getPostsByCategory($startDate, $endDate);
        $byType = $this->postsReport->getPostsByType($startDate, $endDate);
        $byCity = $this->postsReport->getPostsByCity($startDate, $endDate);
        $overTime = $this->postsReport->getPostsOverTime('day', $startDate, $endDate);
        $mostActive = $this->postsReport->getMostActiveUsers($startDate, $endDate);
        
        return view('reports::dashboard.posts', compact(
            'summary',
            'byStatus',
            'byCategory',
            'byType',
            'byCity',
            'overTime',
            'mostActive',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Display job offers reports
     */
    public function jobOffers(Request $request)
    {
        [$startDate, $endDate] = $this->getDateRange($request);
        
        $summary = $this->jobOffersReport->getSummary($startDate, $endDate);
        $byStatus = $this->jobOffersReport->getOffersByStatus($startDate, $endDate);
        $overTime = $this->jobOffersReport->getOffersOverTime('day', $startDate, $endDate);
        $topEmployers = $this->jobOffersReport->getTopEmployers($startDate, $endDate);
        $topPosts = $this->jobOffersReport->getPostsWithMostOffers($startDate, $endDate);
        
        return view('reports::dashboard.job-offers', compact(
            'summary',
            'byStatus',
            'overTime',
            'topEmployers',
            'topPosts',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Display users reports
     */
    public function users(Request $request)
    {
        [$startDate, $endDate] = $this->getDateRange($request);
        
        $summary = $this->usersReport->getSummary($startDate, $endDate);
        $overTime = $this->usersReport->getUsersOverTime('day', $startDate, $endDate);
        
        return view('reports::dashboard.users', compact(
            'summary',
            'overTime',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Display financial reports
     */
    public function financial(Request $request)
    {
        [$startDate, $endDate] = $this->getDateRange($request);
        
        $summary = $this->financialReport->getSummary($startDate, $endDate);
        $byPackage = $this->financialReport->getRevenueByPackage($startDate, $endDate);
        $overTime = $this->financialReport->getRevenueOverTime('day', $startDate, $endDate);
        
        return view('reports::dashboard.financial', compact(
            'summary',
            'byPackage',
            'overTime',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Get date range from request or default to all time
     */
    protected function getDateRange(Request $request): array
    {
        $range = $request->input('range', 'all_time');
        
        return match($range) {
            'today' => [now()->startOfDay(), now()->endOfDay()],
            'yesterday' => [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()],
            'this_week' => [now()->startOfWeek(), now()->endOfWeek()],
            'last_week' => [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()],
            'this_month' => [now()->startOfMonth(), now()->endOfMonth()],
            'last_month' => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
            'this_year' => [now()->startOfYear(), now()->endOfYear()],
            'last_30_days' => [now()->subDays(30), now()],
            'custom' => [
                $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null,
                $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null
            ],
            default => [null, null], // all_time = no date filter
        };
    }
}
