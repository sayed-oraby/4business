<?php

namespace Modules\Blog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class BlogTag extends Model
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
        static::creating(function (BlogTag $tag) {
            if (empty($tag->slug) && ! empty($tag->title)) {
                $locale = app()->getLocale();
                $base = $tag->title[$locale] ?? $tag->title['en'] ?? reset($tag->title) ?? Str::uuid()->toString();
                $tag->slug = Str::slug($base);
            }
        });
    }

    public function blogs(): BelongsToMany
    {
        return $this->belongsToMany(Blog::class, 'blog_tag_pivot', 'tag_id', 'blog_id');
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
