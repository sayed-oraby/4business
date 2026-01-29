<?php

namespace Modules\Reports\Http\Controllers\Dashboard;

use App\Support\ApiResponse;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Reports\Services\FinancialReportService;

class FinancialReportController extends Controller
{
    use ApiResponse;
    use AuthorizesRequests;

    public function __construct(protected FinancialReportService $service) {}

    public function index(Request $request): View
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        $revenue = $this->service->getRevenueReport($startDate, $endDate);
        $tax = $this->service->getTaxReport($startDate, $endDate);
        $wallet = $this->service->getWalletReport($startDate, $endDate);

        return view('reports::dashboard.financial.index', compact(
            'revenue',
            'tax',
            'wallet',
            'startDate',
            'endDate'
        ));
    }

    public function revenue(Request $request): JsonResponse
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        $data = $this->service->getRevenueReport($startDate, $endDate);

        return $this->successResponse(
            data: ['revenue' => $data],
            message: __('reports::messages.revenue_loaded')
        );
    }

    public function tax(Request $request): JsonResponse
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        $data = $this->service->getTaxReport($startDate, $endDate);

        return $this->successResponse(
            data: ['tax' => $data],
            message: __('reports::messages.tax_loaded')
        );
    }

    public function wallet(Request $request): JsonResponse
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        $data = $this->service->getWalletReport($startDate, $endDate);

        return $this->successResponse(
            data: ['wallet' => $data],
            message: __('reports::messages.wallet_loaded')
        );
    }
}
