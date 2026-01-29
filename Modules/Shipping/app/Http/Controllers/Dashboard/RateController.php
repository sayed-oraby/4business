<?php

namespace Modules\Shipping\Http\Controllers\Dashboard;

use App\Support\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Shipping\Http\Requests\Dashboard\StoreRateRequest;
use Modules\Shipping\Http\Requests\Dashboard\UpdateRateRequest;
use Modules\Shipping\Models\ShippingCity;
use Modules\Shipping\Models\ShippingCountry;
use Modules\Shipping\Models\ShippingCountryRate;
use Modules\Shipping\Models\ShippingState;

class RateController extends Controller
{
    use AuthorizesRequests;
    use ApiResponse;

    public function index(ShippingCountry $country): JsonResponse
    {
        $this->authorize('view', $country);

        $rates = $country->rates()
            ->with(['state', 'city'])
            ->orderByDesc('is_active')
            ->get();

        return $this->successResponse(
            data: [
                'rates' => $rates,
            ],
            message: __('shipping::messages.rates_loaded')
        );
    }

    public function store(StoreRateRequest $request, ShippingCountry $country): JsonResponse
    {
        $this->authorize('update', $country);
        $payload = $this->normalizeRegionInput($country, $request->validated());
        $rate = $country->rates()->create($payload);

        return $this->successResponse(
            data: ['rate' => $rate],
            message: __('shipping::messages.rate_created'),
            status: 201
        );
    }

    public function update(UpdateRateRequest $request, ShippingCountryRate $rate): JsonResponse
    {
        $this->authorize('update', $rate->country);
        $payload = $this->normalizeRegionInput($rate->country, $request->validated());
        $rate->update($payload);

        return $this->successResponse(
            data: ['rate' => $rate->fresh()->load('state', 'city')],
            message: __('shipping::messages.rate_updated')
        );
    }

    public function destroy(ShippingCountryRate $rate): JsonResponse
    {
        $this->authorize('update', $rate->country);
        $rate->delete();

        return $this->successResponse(
            data: null,
            message: __('shipping::messages.rate_deleted')
        );
    }

    protected function normalizeRegionInput(ShippingCountry $country, array $payload): array
    {
        $stateId = $payload['shipping_state_id'] ?? null;
        $cityId = $payload['shipping_city_id'] ?? null;

        if ($cityId) {
            $city = ShippingCity::query()
                ->whereKey($cityId)
                ->whereHas('state', fn ($q) => $q->where('shipping_country_id', $country->id))
                ->firstOrFail();
            $payload['shipping_city_id'] = $city->id;
            $payload['shipping_state_id'] = $city->shipping_state_id;
        } elseif ($stateId) {
            $state = ShippingState::query()
                ->where('shipping_country_id', $country->id)
                ->whereKey($stateId)
                ->firstOrFail();
            $payload['shipping_state_id'] = $state->id;
            $payload['shipping_city_id'] = null;
        } else {
            $payload['shipping_state_id'] = null;
            $payload['shipping_city_id'] = null;
        }

        return $payload;
    }
}
