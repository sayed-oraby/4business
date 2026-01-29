<?php

namespace Modules\Shipping\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingState extends Model
{
    protected $fillable = [
        'shipping_country_id',
        'code',
        'name_en',
        'name_ar',
        'lat',
        'lng',
    ];

    protected $casts = [
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(ShippingCountry::class, 'shipping_country_id');
    }

    public function cities(): HasMany
    {
        return $this->hasMany(ShippingCity::class, 'shipping_state_id');
    }

    public function getNameAttribute(): string
    {
        return $this->{'name_'.app()->getLocale()} ?? $this->name_en;
    }
}
