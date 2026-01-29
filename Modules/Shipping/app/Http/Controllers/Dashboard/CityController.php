<?php

namespace Modules\Shipping\Http\Controllers\Dashboard;

use App\Support\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Shipping\Http\Requests\Dashboard\StoreCityRequest;
use Modules\Shipping\Http\Requests\Dashboard\UpdateCityRequest;
use Modules\Shipping\Models\ShippingCity;
use Modules\Shipping\Models\ShippingState;

class CityController extends Controller
{
    use AuthorizesRequests;
    use ApiResponse;

    public function data(Request $request): JsonResponse
    {
        $stateId = $request->integer('state_id');
        $search = $request->input('search');

        $cities = ShippingCity::query()
            ->with('state')
            ->when($stateId, fn ($q) => $q->where('shipping_state_id', $stateId))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name_en', 'like', "%{$search}%")
                        ->orWhere('name_ar', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->orderBy('name_en')
            ->get();

        return $this->successResponse(
            data: ['cities' => $cities],
            message: __('shipping::messages.cities_loaded')
        );
    }

    public function store(StoreCityRequest $request): JsonResponse
    {
        $city = ShippingCity::create($request->validated());

        return $this->successResponse(
            data: ['city' => $city],
            message: __('shipping::messages.city_created'),
            status: 201
        );
    }

    public function update(UpdateCityRequest $request, ShippingCity $city): JsonResponse
    {
        $city->update($request->validated());

        return $this->successResponse(
            data: ['city' => $city->fresh()],
            message: __('shipping::messages.city_updated')
        );
    }

    public function destroy(ShippingCity $city): JsonResponse
    {
        $this->authorize('update', $city->state->country);
        $city->delete();

        return $this->successResponse(
            data: null,
            message: __('shipping::messages.city_deleted')
        );
    }
}
