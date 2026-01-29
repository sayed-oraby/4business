<?php

namespace Modules\Banner\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Banner extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'image_path',
        'title',
        'description',
        'button_label',
        'button_url',
        'placement',
        'starts_at',
        'ends_at',
        'status',
        'targetable_id',
        'targetable_type',
        'sort_order',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public const STATUSES = ['active', 'inactive'];

    public function targetable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getTitleAttribute($value): ?string
    {
        return $this->valueForLocale($value);
    }

    public function getDescriptionAttribute($value): ?string
    {
        return $this->valueForLocale($value);
    }

    public function getTitleTranslationsAttribute(): array
    {
        return $this->rawLocalized('title');
    }

    public function getDescriptionTranslationsAttribute(): array
    {
        return $this->rawLocalized('description');
    }

    protected function valueForLocale($value): ?string
    {
        $value = $this->ensureArray($value);

        if (! is_array($value)) {
            return $value;
        }

        $locale = app()->getLocale();

        if (! empty($value[$locale])) {
            return $value[$locale];
        }

        $fallback = config('app.fallback_locale');
        if ($fallback && ! empty($value[$fallback])) {
            return $value[$fallback];
        }

        foreach ($value as $translation) {
            if (! empty($translation)) {
                return $translation;
            }
        }

        return null;
    }

    protected function rawLocalized(string $key): array
    {
        $value = $this->getAttributeFromArray($key);

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

            return [$this->defaultLocale() => $value];
        }

        return $value;
    }

    protected function defaultLocale(): string
    {
        return config('app.locale', 'en');
    }

    public function scopePlacement(Builder $query, string $placement): Builder
    {
        return $query->where('placement', $placement);
    }

    public function scopeActiveNow(Builder $query): Builder
    {
        $now = Carbon::now();

        return $query
        // ->where(function (Builder $q) use ($now) {
        //     $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
        // })
        // ->where(function (Builder $q) use ($now) {
        //     $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
        // })
        ->where('status', 'active');
    }
}
