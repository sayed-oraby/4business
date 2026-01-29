<?php

namespace Modules\Post\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PostPayment extends Model
{
    protected $fillable = [
        'post_id',
        'ref_number',
        'provider',
        'amount',
        'currency',
        'status',
        'invoice_url',
        'callback_url',
        'payload',
        'meta',
    ];

    protected $casts = [
        'payload' => 'array',
        'meta' => 'array',
        'amount' => 'decimal:2',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(PostPaymentLog::class);
    }
}
