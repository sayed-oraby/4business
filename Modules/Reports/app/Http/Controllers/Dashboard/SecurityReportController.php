<?php

namespace Modules\Reports\Http\Controllers\Dashboard;

use App\Support\ApiResponse;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Reports\Services\SecurityReportService;

class SecurityReportController extends Controller
{
    use ApiResponse;
    use AuthorizesRequests;

    public function __construct(protected SecurityReportService $service) {}

    public function index(Request $request): View
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        $loginAttempts = $this->service->getLoginAttempts($startDate, $endDate);
        $bans = $this->service->getBansReport($startDate, $endDate);
        $adminActions = $this->service->getAdminActionsSummary($startDate, $endDate);

        return view('reports::dashboard.security.index', compact(
            'loginAttempts',
            'bans',
            'adminActions',
            'startDate',
            'endDate'
        ));
    }

    public function loginAttempts(Request $request): JsonResponse
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        $data = $this->service->getLoginAttempts($startDate, $endDate);

        return $this->successResponse(
            data: ['attempts' => $data],
            message: __('reports::messages.login_attempts_loaded')
        );
    }

    public function bans(Request $request): JsonResponse
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        $data = $this->service->getBansReport($startDate, $endDate);

        return $this->successResponse(
            data: ['bans' => $data],
            message: __('reports::messages.bans_loaded')
        );
    }

    public function auditLog(Request $request): JsonResponse
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;
        $filters = $request->only(['action', 'context', 'user_id']);

        $data = $this->service->getAdminAuditLog($startDate, $endDate, $filters);

        return $this->successResponse(
            data: ['logs' => $data],
            message: __('reports::messages.audit_log_loaded')
        );
    }

    public function adminActions(Request $request): JsonResponse
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        $data = $this->service->getAdminActionsSummary($startDate, $endDate);

        return $this->successResponse(
            data: ['actions' => $data],
            message: __('reports::messages.admin_actions_loaded')
        );
    }
}
