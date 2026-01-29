<?php

namespace Modules\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderAddress extends Model
{
    protected $fillable = [
        'order_id',
        'user_address_id',
        'type',
        'full_name',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function userAddress(): BelongsTo
    {
        return $this->belongsTo(\Modules\Shipping\Models\UserAddress::class, 'user_address_id');
    }
}
