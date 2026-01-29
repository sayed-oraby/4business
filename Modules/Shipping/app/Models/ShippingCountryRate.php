<?php

namespace Modules\Shipping\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingCountryRate extends Model
{
    protected $fillable = [
        'shipping_country_id',
        'calculation_type',
        'base_price',
        'price_per_kg',
        'free_shipping_over',
        'currency',
        'delivery_estimate_en',
        'delivery_estimate_ar',
        'is_active',
        'shipping_state_id',
        'shipping_city_id',
    ];

    protected $casts = [
        'base_price' => 'decimal:3',
        'price_per_kg' => 'decimal:3',
        'free_shipping_over' => 'decimal:3',
        'is_active' => 'boolean',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(ShippingCountry::class, 'shipping_country_id');
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(ShippingState::class, 'shipping_state_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(ShippingCity::class, 'shipping_city_id');
    }
}
