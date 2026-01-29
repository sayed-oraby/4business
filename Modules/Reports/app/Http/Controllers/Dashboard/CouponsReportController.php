<?php

namespace Modules\Reports\Http\Controllers\Dashboard;

use App\Support\ApiResponse;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Reports\Services\CouponsReportService;

class CouponsReportController extends Controller
{
    use ApiResponse;
    use AuthorizesRequests;

    public function __construct(protected CouponsReportService $service) {}

    public function index(Request $request): View
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        $stats = $this->service->getCouponsStats($startDate, $endDate);

        return view('reports::dashboard.coupons.index', compact('stats', 'startDate', 'endDate'));
    }

    public function stats(Request $request): JsonResponse
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        $data = $this->service->getCouponsStats($startDate, $endDate);

        return $this->successResponse(
            data: ['stats' => $data],
            message: __('reports::messages.coupons_stats_loaded')
        );
    }

    public function byUser(int $user, Request $request): JsonResponse
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        $data = $this->service->getCouponsByUser($user, $startDate, $endDate);

        return $this->successResponse(
            data: ['coupons' => $data],
            message: __('reports::messages.coupons_by_user_loaded')
        );
    }

    public function byProduct(Request $request): JsonResponse
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        $data = $this->service->getCouponsByProduct($startDate, $endDate);

        return $this->successResponse(
            data: ['coupons' => $data],
            message: __('reports::messages.coupons_by_product_loaded')
        );
    }
}
