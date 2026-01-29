<?php

namespace Modules\Reports\Http\Controllers\Dashboard;

use App\Support\ApiResponse;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Reports\Services\UsersReportService;

class UsersReportController extends Controller
{
    use ApiResponse;
    use AuthorizesRequests;

    public function __construct(protected UsersReportService $service) {}

    public function index(Request $request): View
    {
        $period = $request->input('period', 'monthly');
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        $signups = $this->service->getUserSignups($period, $startDate, $endDate);
        $topBuyers = $this->service->getTopBuyers(10, $startDate, $endDate);
        $behavior = $this->service->getUserBehavior($startDate, $endDate);

        return view('reports::dashboard.users.index', compact(
            'signups',
            'topBuyers',
            'behavior',
            'period',
            'startDate',
            'endDate'
        ));
    }

    public function signups(Request $request): JsonResponse
    {
        $period = $request->input('period', 'monthly');
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        $data = $this->service->getUserSignups($period, $startDate, $endDate);

        return $this->successResponse(
            data: ['signups' => $data],
            message: __('reports::messages.user_signups_loaded')
        );
    }

    public function topBuyers(Request $request): JsonResponse
    {
        $limit = (int) ($request->input('limit', 10));
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        $data = $this->service->getTopBuyers($limit, $startDate, $endDate);

        return $this->successResponse(
            data: ['buyers' => $data],
            message: __('reports::messages.top_buyers_loaded')
        );
    }

    public function behavior(Request $request): JsonResponse
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        $data = $this->service->getUserBehavior($startDate, $endDate);

        return $this->successResponse(
            data: ['behavior' => $data],
            message: __('reports::messages.user_behavior_loaded')
        );
    }
}
