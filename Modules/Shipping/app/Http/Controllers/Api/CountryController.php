<?php

namespace Modules\Shipping\Http\Controllers\Api;

use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Shipping\Http\Requests\Api\ListCitiesRequest;
use Modules\Shipping\Http\Requests\Api\ListStatesRequest;
use Modules\Shipping\Http\Resources\CountryResource;
use Modules\Shipping\Models\ShippingCountry;
use Modules\Shipping\Services\LocationProvider;

class CountryController extends Controller
{
    use ApiResponse;

    public function __construct(protected LocationProvider $locations)
    {
    }

    public function index(): JsonResponse
    {
        $countries = ShippingCountry::query()
            ->where('is_active', true)
            ->where('is_shipping_enabled', true)
            ->orderBy('sort_order')
            ->get();

        return $this->successResponse(
            data: [
                'countries' => CountryResource::collection($countries)->resolve(),
            ],
            message: __('shipping::messages.countries_loaded'));
    }

    public function package(): JsonResponse
    {
        return $this->successResponse(
            data: [
                'countries' => $this->locations->packageCountries(),
            ],
            message: __('shipping::messages.countries_loaded')
        );
    }

    public function states(ListStatesRequest $request): JsonResponse
    {
        $iso2 = (string) $request->string('country')->upper();
        $country = ShippingCountry::query()
            ->where('iso2', $iso2)
            ->first();

        if ($country && $country->states()->exists()) {
            $statesQuery = $country->states()->with('cities')->orderBy('name_en');
            if ($search = $request->input('search')) {
                $statesQuery->where(function ($q) use ($search) {
                    $q->where('name_en', 'like', "%{$search}%")
                        ->orWhere('name_ar', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            }
            $states = $statesQuery->get()->map(function ($state) {
                return [
                    'id' => $state->id,
                    'code' => $state->code,
                    'country_iso2' => $state->country->iso2,
                    'name_en' => $state->name_en,
                    'name_ar' => $state->name_ar,
                    'lat' => $state->lat,
                    'lng' => $state->lng,
                    'cities' => $state->cities->map(function ($city) use ($state) {
                        return [
                            'id' => $city->id,
                            'name_en' => $city->name_en,
                            'name_ar' => $city->name_ar,
                            'lat' => $city->lat,
                            'lng' => $city->lng,
                        ];
                    }),
                ];
            });
        } else {
            $states = $this->locations->states($iso2, $request->input('search'));
        }

        return $this->successResponse(
            data: ['states' => $states],
            message: __('shipping::messages.states_loaded')
        );
    }

    public function cities(ListCitiesRequest $request): JsonResponse
    {
        $iso2 = (string) $request->string('country')->upper();
        $stateCode = $request->input('state');
        $country = ShippingCountry::query()
            ->where('iso2', $iso2)
            ->first();

        if ($country && $stateCode) {
            $state = $country->states()->where('code', strtoupper($stateCode))->first();
            if ($state && $state->cities()->exists()) {
                $citiesQuery = $state->cities()->orderBy('name_en');
                if ($search = $request->input('search')) {
                    $citiesQuery->where(function ($q) use ($search) {
                        $q->where('name_en', 'like', "%{$search}%")
                            ->orWhere('name_ar', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
                }
                $cities = $citiesQuery->get()->map(function ($city) use ($state) {
                    return [
                        'id' => $city->id,
                        'code' => $city->code,
                        'state_code' => $state->code,
                        'country_iso2' => $state->country->iso2,
                        'name_en' => $city->name_en,
                        'name_ar' => $city->name_ar,
                        'lat' => $city->lat,
                        'lng' => $city->lng,
                    ];
                });
            } else {
                $cities = $this->locations->cities($iso2, $stateCode, $request->input('search'));
            }
        } else {
            $cities = $this->locations->cities($iso2, $stateCode, $request->input('search'));
        }

        return $this->successResponse(
            data: ['cities' => $cities],
            message: __('shipping::messages.cities_loaded')
        );
    }
}
