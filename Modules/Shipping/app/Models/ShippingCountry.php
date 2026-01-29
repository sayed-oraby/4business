<?php

namespace Modules\Shipping\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingCountry extends Model
{
    protected $fillable = [
        'iso2',
        'iso3',
        'phone_code',
        'name_en',
        'name_ar',
        'flag_svg',
        'is_active',
        'is_shipping_enabled',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_shipping_enabled' => 'boolean',
    ];

    public function rates(): HasMany
    {
        return $this->hasMany(ShippingCountryRate::class);
    }

    public function states(): HasMany
    {
        return $this->hasMany(ShippingState::class);
    }

    public function cities(): HasMany
    {
        return $this->hasManyThrough(ShippingCity::class, ShippingState::class);
    }

    public function activeRate()
    {
        return $this->rates()->where('is_active', true)->first();
    }
}
