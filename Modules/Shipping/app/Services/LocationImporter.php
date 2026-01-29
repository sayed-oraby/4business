<?php

namespace Modules\Shipping\Services;

use Illuminate\Support\Facades\DB;
use Modules\Shipping\Models\ShippingCity;
use Modules\Shipping\Models\ShippingCountry;
use Modules\Shipping\Models\ShippingState;

class LocationImporter
{
    public function __construct(protected LocationProvider $provider)
    {
    }

    public function import(ShippingCountry $country, bool $withCities = true): array
    {
        $statesData = $this->provider->states($country->iso2);

        $createdStates = 0;
        $createdCities = 0;

        DB::transaction(function () use (&$createdStates, &$createdCities, $statesData, $country, $withCities) {
            foreach ($statesData as $stateData) {
                $code = strtoupper($stateData['code'] ?? $stateData['name_en']);
                if (! $code) {
                    continue;
                }

                $state = ShippingState::query()->updateOrCreate(
                    [
                        'shipping_country_id' => $country->id,
                        'code' => $code,
                    ],
                    [
                        'name_en' => $stateData['name_en'] ?? $code,
                        'name_ar' => $stateData['name_ar'] ?? $stateData['name_en'] ?? $code,
                        'lat' => $stateData['lat'] ?? null,
                        'lng' => $stateData['lng'] ?? null,
                    ]
                );

                $createdStates++;

                if ($withCities) {
                    $cities = $this->provider->cities($country->iso2, $code);
                    foreach ($cities as $cityData) {
                        $cityCode = isset($cityData['code']) ? strtoupper($cityData['code']) : null;
                        ShippingCity::query()->updateOrCreate(
                            [
                                'shipping_state_id' => $state->id,
                                'code' => $cityCode,
                            ],
                            [
                                'name_en' => $cityData['name_en'] ?? $cityCode ?? $cityData['name_ar'] ?? '',
                                'name_ar' => $cityData['name_ar'] ?? $cityData['name_en'] ?? $cityCode,
                                'lat' => $cityData['lat'] ?? null,
                                'lng' => $cityData['lng'] ?? null,
                            ]
                        );
                        $createdCities++;
                    }
                }
            }
        });

        $this->provider->clearCountryCache($country->iso2);

        return [
            'states' => $createdStates,
            'cities' => $createdCities,
        ];
    }
}
