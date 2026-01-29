<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\User\Models\User;

class ProductGallery extends Model
{
    protected $fillable = [
        'product_id',
        'upload_token',
        'image_path',
        'sort_order',
        'uploaded_by',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getImageUrlAttribute(): ?string
    {
        return setting_media_url($this->image_path);
    }
}
