<?php

namespace Modules\Shipping\Http\Controllers\Dashboard;

use App\Support\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Shipping\Http\Requests\Dashboard\StoreCountryRequest;
use Modules\Shipping\Http\Requests\Dashboard\UpdateCountryRequest;
use Modules\Shipping\Http\Resources\CountryResource;
use Modules\Shipping\Models\ShippingCountry;
use Modules\Shipping\Models\ShippingState;
use Modules\Shipping\Services\LocationImporter;

class CountryController extends Controller
{
    use AuthorizesRequests;
    use ApiResponse;

    public function index()
    {
        $this->authorize('viewAny', ShippingCountry::class);

        return view('shipping::dashboard.countries.index', [
            'statuses' => [
                'active' => __('shipping::messages.active'),
                'inactive' => __('shipping::messages.inactive'),
            ],
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ShippingCountry::class);

        $shippingEnabled = $request->input('shipping_enabled');
        $search = $request->input('search');

        $countries = ShippingCountry::query()
            ->when($shippingEnabled !== null && $shippingEnabled !== '', function ($query) use ($shippingEnabled) {
                $query->where('is_shipping_enabled', (bool) $shippingEnabled);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('name_en', 'like', "%{$search}%")
                        ->orWhere('name_ar', 'like', "%{$search}%")
                        ->orWhere('iso2', 'like', "%{$search}%")
                        ->orWhere('iso3', 'like', "%{$search}%");
                });
            })
            ->orderBy('sort_order')
            ->orderBy('name_en')
            ->get();

        return $this->successResponse(
            data: ['countries' => CountryResource::collection($countries)->resolve()],
            message: __('shipping::messages.countries_loaded')
        );
    }

    public function store(StoreCountryRequest $request): JsonResponse
    {
        $country = ShippingCountry::create($request->validated());

        return $this->successResponse(
            data: ['country' => (new CountryResource($country))->resolve()],
            message: __('shipping::messages.country_created'),
            status: 201
        );
    }

    public function update(UpdateCountryRequest $request, ShippingCountry $country): JsonResponse
    {
        $country->update($request->validated());

        return $this->successResponse(
            data: ['country' => (new CountryResource($country))->resolve()],
            message: __('shipping::messages.country_updated')
        );
    }

    public function destroy(ShippingCountry $country): JsonResponse
    {
        $this->authorize('delete', $country);
        $country->delete();

        return $this->successResponse(
            data: null,
            message: __('shipping::messages.country_deleted')
        );
    }

    public function importLocations(ShippingCountry $country, LocationImporter $importer): JsonResponse
    {
        $this->authorize('update', $country);
        $result = $importer->import($country);

        return $this->successResponse(
            data: $result,
            message: __('shipping::messages.locations_imported')
        );
    }

    public function states(ShippingCountry $country): JsonResponse
    {
        $this->authorize('view', $country);

        $states = $country->states()
            ->with('cities')
            ->orderBy('name_en')
            ->get();

        return $this->successResponse(
            data: ['states' => $states],
            message: __('shipping::messages.states_loaded')
        );
    }
}
