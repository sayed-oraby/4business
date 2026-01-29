<?php

namespace Modules\Reports\Http\Controllers\Dashboard;

use App\Support\ApiResponse;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Reports\Services\ProductsReportService;

class ProductsReportController extends Controller
{
    use ApiResponse;
    use AuthorizesRequests;

    public function __construct(protected ProductsReportService $service) {}

    public function index(): View
    {
        $inventory = $this->service->getInventoryStatus();
        $pricingByCountry = $this->service->getProductPricingByCountry();
        $priceChanges = $this->service->getPriceChanges();

        return view('reports::dashboard.products.index', compact(
            'inventory',
            'pricingByCountry',
            'priceChanges'
        ));
    }

    public function inventory(): JsonResponse
    {
        $data = $this->service->getInventoryStatus();

        return $this->successResponse(
            data: ['inventory' => $data],
            message: __('reports::messages.inventory_loaded')
        );
    }

    public function pricingByCountry(): JsonResponse
    {
        $data = $this->service->getProductPricingByCountry();

        return $this->successResponse(
            data: ['pricing' => $data],
            message: __('reports::messages.pricing_by_country_loaded')
        );
    }

    public function priceChanges(Request $request): JsonResponse
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;

        $data = $this->service->getPriceChanges($startDate, $endDate);

        return $this->successResponse(
            data: ['changes' => $data],
            message: __('reports::messages.price_changes_loaded')
        );
    }
}
