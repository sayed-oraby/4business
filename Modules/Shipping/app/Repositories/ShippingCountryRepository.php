<?php

namespace Modules\Shipping\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\Shipping\Models\ShippingCountry;

class ShippingCountryRepository
{
    public function allActive(): Collection
    {
        return ShippingCountry::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function enabledForShipping(): Collection
    {
        return ShippingCountry::query()
            ->where('is_active', true)
            ->where('is_shipping_enabled', true)
            ->orderBy('sort_order')
            ->get();
    }
}
