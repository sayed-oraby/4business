<?php

namespace Modules\Shipping\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\User\Models\User;

class UserAddress extends Model
{
    protected $fillable = [
        'user_id',
        'label',
        'full_name',
        'phone',
        'shipping_country_id',
        'country_iso2',
        'state_code',
        'state_name_en',
        'state_name_ar',
        'city_code',
        'city_name_en',
        'city_name_ar',
        'block',
        'street',
        'avenue',
        'building',
        'floor',
        'apartment',
        'postal_code',
        'additional_details',
        'lat',
        'lng',
        'is_default_shipping',
        'is_default_billing',
    ];

    protected $casts = [
        'is_default_shipping' => 'boolean',
        'is_default_billing' => 'boolean',
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(ShippingCountry::class, 'shipping_country_id');
    }
}
