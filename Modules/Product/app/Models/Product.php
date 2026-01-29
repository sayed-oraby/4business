<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Brand\Models\Brand;
use Modules\Category\Models\Category;
use Modules\User\Models\User;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'sku',
        'price',
        'qty',
        'status',
        'is_featured',
        'is_new_arrival',
        'is_trending',
        'position',
        'image_path',
        'category_id',
        'brand_id',
        'offer_type',
        'offer_price',
        'offer_starts_at',
        'offer_ends_at',
        'created_by',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'is_featured' => 'boolean',
        'is_new_arrival' => 'boolean',
        'is_trending' => 'boolean',
        'price' => 'decimal:2',
        'offer_price' => 'decimal:2',
        'offer_starts_at' => 'datetime',
        'offer_ends_at' => 'datetime',
    ];

    public const STATUSES = ['draft', 'active', 'archived'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function galleries(): HasMany
    {
        return $this->hasMany(ProductGallery::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(ProductTag::class, 'product_tag_product', 'product_id', 'tag_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTitleAttribute($value): ?string
    {
        return $this->localizedValue($value);
    }

    public function getDescriptionAttribute($value): ?string
    {
        return $this->localizedValue($value);
    }

    public function getTitleTranslationsAttribute(): array
    {
        return $this->asArray('title');
    }

    public function getDescriptionTranslationsAttribute(): array
    {
        return $this->asArray('description');
    }

    protected function localizedValue($value): ?string
    {
        $translations = $this->ensureArray($value);

        if (! is_array($translations)) {
            return $translations;
        }

        $locale = app()->getLocale();

        if (! empty($translations[$locale])) {
            return $translations[$locale];
        }

        $fallback = config('app.fallback_locale');
        if ($fallback && ! empty($translations[$fallback])) {
            return $translations[$fallback];
        }

        foreach ($translations as $translation) {
            if (! empty($translation)) {
                return $translation;
            }
        }

        return null;
    }

    protected function asArray(string $key): array
    {
        $raw = $this->getAttributeFromArray($key);
        $array = $this->ensureArray($raw);

        return is_array($array) ? $array : [];
    }

    protected function ensureArray($value)
    {
        if (is_array($value) || is_null($value)) {
            return $value;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }

            return [config('app.locale', 'en') => $value];
        }

        return $value;
    }
}
