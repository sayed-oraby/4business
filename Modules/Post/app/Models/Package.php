<?php

namespace Modules\Post\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Package extends Model
{
    use HasTranslations, SoftDeletes;

    protected $fillable = [
        'title',
        'price',
        'period_days',
        'top_days',
        'label_color',
        'card_color',
        'description',
        'cover_image',
        'status',
        'is_featured',
        'is_free',
        'free_credits_per_user',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'period_days' => 'integer',
        'top_days' => 'integer',
        'status' => 'boolean',
        'is_featured' => 'boolean',
        'is_free' => 'boolean',
        'free_credits_per_user' => 'integer',
    ];

    public $translatable = ['title', 'description'];

    protected $appends = ['cover_image_url', 'free_days'];

    public function getCoverImageUrlAttribute()
    {
        return $this->cover_image ? asset('storage/'.$this->cover_image) : null;
    }

    /**
     * Get the remaining free days (total period - top days)
     */
    public function getFreeDaysAttribute(): int
    {
        return max(0, $this->period_days - $this->top_days);
    }

    /**
     * Check if there's already an active free package
     */
    public static function hasActiveFreePackage(?int $excludeId = null): bool
    {
        $query = static::where('is_free', true)->where('status', true);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Get the active free package
     */
    public static function getActiveFreePackage(): ?self
    {
        return static::where('is_free', true)->where('status', true)->first();
    }

    /**
     * Scope for active packages
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for paid packages
     */
    public function scopePaid($query)
    {
        return $query->where('is_free', false);
    }

    /**
     * Scope for free packages
     */
    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }
}
