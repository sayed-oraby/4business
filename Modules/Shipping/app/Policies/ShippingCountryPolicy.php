<?php

namespace Modules\Shipping\Policies;

use Modules\Shipping\Models\ShippingCountry;
use Modules\User\Models\User;

class ShippingCountryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('shipping_countries.view');
    }

    public function view(User $user, ShippingCountry $country): bool
    {
        return $user->can('shipping_countries.view');
    }

    public function create(User $user): bool
    {
        return $user->can('shipping_countries.create');
    }

    public function update(User $user, ShippingCountry $country): bool
    {
        return $user->can('shipping_countries.update');
    }

    public function delete(User $user, ShippingCountry $country): bool
    {
        return $user->can('shipping_countries.delete');
    }
}
