<?php

namespace Modules\Brand\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'status',
        'image_path',
        'position',
    ];

    protected $casts = [
        'title' => 'array',
    ];

    public const STATUSES = ['draft', 'active', 'archived'];

    public function getTitleAttribute($value): ?string
    {
        return $this->localizedValue($value);
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
