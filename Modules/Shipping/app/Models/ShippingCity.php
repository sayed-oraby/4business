<?php

namespace Modules\Shipping\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingCity extends Model
{
    protected $fillable = [
        'shipping_state_id',
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

    public function state(): BelongsTo
    {
        return $this->belongsTo(ShippingState::class, 'shipping_state_id');
    }

    public function country(): BelongsTo
    {
        return $this->state()->getResults()?->country();
    }

    public function getNameAttribute()
    {
        return $this->{'name_' . app()->getLocale()} ?? $this->name_en;
    }
}
