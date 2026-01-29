<?php

namespace Modules\Blog\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\User\Models\User;

class Blog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'short_description',
        'description',
        'image_path',
        'status',
        'created_by',
    ];

    protected $casts = [
        'title' => 'array',
        'short_description' => 'array',
        'description' => 'array',
    ];

    public const STATUSES = ['draft', 'published', 'archived'];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function galleries(): HasMany
    {
        return $this->hasMany(BlogGallery::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(BlogTag::class, 'blog_tag_pivot', 'blog_id', 'tag_id');
    }

    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function getTitleAttribute($value): ?string
    {
        return $this->localizedValue($value);
    }

    public function getShortDescriptionAttribute($value): ?string
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

    public function getShortDescriptionTranslationsAttribute(): array
    {
        return $this->asArray('short_description');
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
