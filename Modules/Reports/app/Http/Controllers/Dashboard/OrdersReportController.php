<?php

namespace Modules\Reports\Http\Controllers\Dashboard;

use App\Support\ApiResponse;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Reports\Services\OrdersReportService;

class OrdersReportController extends Controller
{
    use ApiResponse;
    use AuthorizesRequests;

    public function __construct(protected OrdersReportService $service) {}

    public function index(Request $request): View
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        $byStatus = $this->service->getOrdersByStatus($startDate, $endDate);
        $byPaymentStatus = $this->service->getOrdersByPaymentStatus($startDate, $endDate);
        $paymentFailures = $this->service->getPaymentFailures($startDate, $endDate);

        return view('reports::dashboard.orders.index', compact(
            'byStatus',
            'byPaymentStatus',
            'paymentFailures',
            'startDate',
            'endDate'
        ));
    }

    public function byStatus(Request $request): JsonResponse
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        $data = $this->service->getOrdersByStatus($startDate, $endDate);

        return $this->successResponse(
            data: ['orders' => $data],
            message: __('reports::messages.orders_by_status_loaded')
        );
    }

    public function byPaymentStatus(Request $request): JsonResponse
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        $data = $this->service->getOrdersByPaymentStatus($startDate, $endDate);

        return $this->successResponse(
            data: ['orders' => $data],
            message: __('reports::messages.orders_by_payment_status_loaded')
        );
    }

    public function paymentFailures(Request $request): JsonResponse
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        $data = $this->service->getPaymentFailures($startDate, $endDate);

        return $this->successResponse(
            data: ['failures' => $data],
            message: __('reports::messages.payment_failures_loaded')
        );
    }

    public function details(Request $request): JsonResponse
    {
        $filters = $request->only(['start_date', 'end_date', 'status', 'payment_status', 'country']);
        
        if (isset($filters['start_date'])) {
            $filters['start_date'] = Carbon::parse($filters['start_date']);
        }
        if (isset($filters['end_date'])) {
            $filters['end_date'] = Carbon::parse($filters['end_date']);
        }

        $data = $this->service->getOrdersDetails($filters);

        return $this->successResponse(
            data: ['orders' => $data],
            message: __('reports::messages.orders_details_loaded')
        );
    }
}
