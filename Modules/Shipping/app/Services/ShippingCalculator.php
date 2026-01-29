<?php

namespace Modules\Shipping\Services;

use DomainException;
use Illuminate\Support\Facades\App;
use Modules\Cart\Models\Cart;
use Modules\Shipping\Models\ShippingCity;
use Modules\Shipping\Models\ShippingCountry;
use Modules\Shipping\Models\ShippingCountryRate;
use Modules\Shipping\Models\ShippingState;
use Modules\Shipping\Models\UserAddress;

class ShippingCalculator
{
    public function calculate(Cart $cart, UserAddress $address): array
    {
        $country = $address->country;

        if (! $country || ! $country->is_shipping_enabled) {
            throw new DomainException(__('shipping::messages.country_not_supported'));
        }

        $region = $this->resolveRegion($country, $address);

        $rate = $this->resolveRate($country, $region['state'], $region['city']);

        if (! $rate) {
            throw new DomainException(__('shipping::messages.rate_not_configured'));
        }

        $totalWeight = $cart->items->sum(function ($item) {
            $weight = $item->product->weight ?? 0;
            return (float) $weight * $item->quantity;
        });

        $amount = $this->calculateUsingRate($rate, (float) $cart->grand_total, $totalWeight);

        return [
            'amount' => $amount,
            'currency' => $rate->currency,
            'estimate_en' => $rate->delivery_estimate_en,
            'estimate_ar' => $rate->delivery_estimate_ar,
            'meta' => [
                'total_weight' => $totalWeight,
                'calculation_type' => $rate->calculation_type,
            ],
        ];
    }

    protected function calculateUsingRate(ShippingCountryRate $rate, float $orderTotal, float $totalWeight): float
    {
        if ($rate->free_shipping_over && $orderTotal >= (float) $rate->free_shipping_over) {
            return 0.0;
        }

        return match ($rate->calculation_type) {
            'weight' => (float) $rate->base_price + (($rate->price_per_kg ?? 0) * $totalWeight),
            'order_total' => $this->calculateByOrderTotal($rate, $orderTotal),
            default => (float) $rate->base_price,
        };
    }

    protected function calculateByOrderTotal(ShippingCountryRate $rate, float $orderTotal): float
    {
        return (float) $rate->base_price;
    }

    protected function resolveRegion(ShippingCountry $country, UserAddress $address): array
    {
        $state = null;
        $city = null;

        if ($address->state_code) {
            $state = ShippingState::query()
                ->where('shipping_country_id', $country->id)
                ->where('code', strtoupper($address->state_code))
                ->first();
        }

        if ($address->city_code) {
            $city = ShippingCity::query()
                ->where('code', strtoupper($address->city_code))
                ->whereHas('state', fn ($q) => $q->where('shipping_country_id', $country->id))
                ->first();

            if ($city && ! $state) {
                $state = $city->state;
            }
        }

        return [
            'state' => $state,
            'city' => $city,
        ];
    }

    protected function resolveRate(ShippingCountry $country, ?ShippingState $state, ?ShippingCity $city): ?ShippingCountryRate
    {
        $query = $country->rates()->where('is_active', true);

        if ($city) {
            $cityRate = (clone $query)->where('shipping_city_id', $city->id)->first();
            if ($cityRate) {
                return $cityRate;
            }
        }

        if ($state) {
            $stateRate = (clone $query)
                ->whereNull('shipping_city_id')
                ->where('shipping_state_id', $state->id)
                ->first();
            if ($stateRate) {
                return $stateRate;
            }
        }

        return (clone $query)
            ->whereNull('shipping_state_id')
            ->whereNull('shipping_city_id')
            ->first();
    }
}
