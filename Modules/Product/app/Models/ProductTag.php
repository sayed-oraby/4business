<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class ProductTag extends Model
{
    protected $fillable = [
        'slug',
        'title',
    ];

    protected $casts = [
        'title' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (ProductTag $tag) {
            if (empty($tag->slug)) {
                $locale = app()->getLocale();
                $translations = $tag->getAttributes()['title'] ?? null;
                $translations = $tag->ensureArray($translations);
                if (! is_array($translations)) {
                    $translations = [];
                }

                $base = $translations[$locale] ?? $translations['en'] ?? reset($translations) ?? Str::uuid()->toString();
                $slug = Str::slug($base);
                $candidate = $slug;
                $suffix = 1;
                while (self::where('slug', $candidate)->exists()) {
                    $candidate = $slug.'-'.$suffix;
                    $suffix++;
                }
                $tag->slug = $candidate;
            }
        });
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_tag_product', 'tag_id', 'product_id');
    }

    public function getTitleAttribute($value): ?string
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

    public function getTitleTranslationsAttribute(): array
    {
        $value = $this->getAttributeFromArray('title');
        $array = $this->ensureArray($value);

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
