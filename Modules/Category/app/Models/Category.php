<?php

namespace Modules\Category\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'parent_id',
        'title',
        'image_path',
        'status',
        'is_featured',
        'featured_order',
        'position',
    ];

    protected $casts = [
        'title' => 'array',
        'is_featured' => 'boolean',
    ];

    public const STATUSES = ['draft', 'active', 'archived'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Get all descendant category IDs recursively (including nested children)
     */
    public function getDescendantIds(): array
    {
        $ids = [$this->id];

        $children = self::where('parent_id', $this->id)
            ->whereNull('deleted_at')
            ->get();

        foreach ($children as $child) {
            $ids = array_merge($ids, $child->getDescendantIds());
        }

        return $ids;
    }

    /**
     * Static method to get all descendant category IDs for a given category ID
     */
    public static function getDescendantIdsFor(int $categoryId): array
    {
        $category = self::find($categoryId);

        if (! $category) {
            return [$categoryId];
        }

        return $category->getDescendantIds();
    }

    public function getTitleAttribute($value): ?string
    {
        return $this->localizedValue($value);
    }

    /**
     * Alias for title - for frontend compatibility.
     */
    public function getNameAttribute(): ?string
    {
        return $this->title;
    }

    /**
     * Generate a slug from the category ID.
     */
    public function getSlugAttribute(): string
    {
        return (string) $this->id;
    }

    public function getTitleTranslationsAttribute(): array
    {
        return $this->asArray('title');
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
