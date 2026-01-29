<?php

namespace Modules\Reports\Http\Controllers\Dashboard;

use App\Support\ApiResponse;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Reports\Services\SalesReportService;

class SalesReportController extends Controller
{
    use ApiResponse;
    use AuthorizesRequests;

    public function __construct(protected SalesReportService $service) {}

    /**
     * Display sales reports page
     */
    public function index(Request $request): View
    {
        $period = $request->input('period', 'monthly');
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        $stats = $this->service->getSalesStats($period, $startDate, $endDate);
        $byProduct = $this->service->getSalesByProduct($startDate, $endDate);
        $byCountry = $this->service->getSalesByCountry($startDate, $endDate);
        $byPaymentMethod = $this->service->getSalesByPaymentMethod($startDate, $endDate);
        $byTime = $this->service->getSalesByTime('day', $startDate, $endDate);

        return view('reports::dashboard.sales.index', compact(
            'stats',
            'byProduct',
            'byCountry',
            'byPaymentMethod',
            'byTime',
            'period',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Get sales statistics API
     */
    public function stats(Request $request): JsonResponse
    {
        $period = $request->input('period', 'monthly');
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        $stats = $this->service->getSalesStats($period, $startDate, $endDate);

        return $this->successResponse(
            data: ['stats' => $stats],
            message: __('reports::messages.sales_stats_loaded')
        );
    }

    /**
     * Get sales by product API
     */
    public function byProduct(Request $request): JsonResponse
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;
        $limit = (int) ($request->input('limit', 10));

        $data = $this->service->getSalesByProduct($startDate, $endDate, $limit);

        return $this->successResponse(
            data: ['products' => $data],
            message: __('reports::messages.sales_by_product_loaded')
        );
    }

    /**
     * Get sales by country API
     */
    public function byCountry(Request $request): JsonResponse
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        $data = $this->service->getSalesByCountry($startDate, $endDate);

        return $this->successResponse(
            data: ['countries' => $data],
            message: __('reports::messages.sales_by_country_loaded')
        );
    }

    /**
     * Get sales by payment method API
     */
    public function byPaymentMethod(Request $request): JsonResponse
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        $data = $this->service->getSalesByPaymentMethod($startDate, $endDate);

        return $this->successResponse(
            data: ['payment_methods' => $data],
            message: __('reports::messages.sales_by_payment_method_loaded')
        );
    }

    /**
     * Get sales by time API
     */
    public function byTime(Request $request): JsonResponse
    {
        $groupBy = $request->input('group_by', 'day');
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        $data = $this->service->getSalesByTime($groupBy, $startDate, $endDate);

        return $this->successResponse(
            data: ['time_series' => $data],
            message: __('reports::messages.sales_by_time_loaded')
        );
    }
}
