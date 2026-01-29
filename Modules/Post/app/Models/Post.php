<?php

namespace Modules\Post\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Category\Models\Category;
use Modules\Shipping\Models\ShippingCity;
use Modules\Shipping\Models\ShippingState;
use Modules\User\Models\User;
use Spatie\Translatable\HasTranslations;

class Post extends Model
{
    use HasTranslations, HasUuids, SoftDeletes;

    public function uniqueIds()
    {
        return ['uuid'];
    }

    protected $fillable = [
        'user_id',
        'category_id',
        'post_type_id',
        'package_id',
        'city_id',
        'state_id',
        'title',
        'description',
        'years_of_experience',
        'nationality',
        'gender',
        'full_name',
        'mobile_number',
        'birthdate',
        'display_personal_details',
        'cover_image',
        'status',
        'rejection_reason',
        'start_date',
        'end_date',
        'is_paid',
        'price',
        'views_count',
    ];

    protected $casts = [
        'display_personal_details' => 'boolean',
        'is_paid' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'birthdate' => 'date',
    ];

    protected $appends = ['cover_image_url'];

    public $translatable = ['title', 'description'];

    /**
     * Get the full URL for the cover image.
     */
    public function getCoverImageUrlAttribute(): ?string
    {
        if ($this->cover_image) {
            // If already a full URL, return as-is
            if (str_starts_with($this->cover_image, 'http')) {
                return $this->cover_image;
            }

            return asset('storage/'.$this->cover_image);
        }

        // Fallback to first attachment
        $firstAttachment = $this->attachments->first();
        if ($firstAttachment) {
            return asset('storage/'.$firstAttachment->file_path);
        }

        return null;
    }

    /**
     * Resolve the model for route model binding (supports both ID and UUID).
     */
    public function resolveRouteBinding($value, $field = null)
    {
        // If a specific field is requested, use it
        if ($field) {
            return $this->where($field, $value)->first();
        }

        // Try to find by ID first (if numeric), then by UUID
        if (is_numeric($value)) {
            return $this->where('id', $value)->first()
                ?? $this->where('uuid', $value)->first();
        }

        // If not numeric, search by UUID
        return $this->where('uuid', $value)->first();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function postType(): BelongsTo
    {
        return $this->belongsTo(PostType::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(ShippingCity::class, 'city_id');
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(ShippingState::class, 'state_id');
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'post_skills');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(PostAttachment::class);
    }

    public function jobOffers(): HasMany
    {
        return $this->hasMany(JobOffer::class);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('posts.status', ['approved', 'active'])
            ->where('posts.start_date', '<=', now())
            ->where('posts.end_date', '>=', now());
    }

    /**
     * Check if the post is currently in its "top" period (paid featured position)
     */
    public function isInTopPeriod(): bool
    {
        if (! $this->package || ! $this->start_date) {
            return false;
        }

        $topDays = $this->package->top_days ?? 0;
        if ($topDays === 0) {
            return false;
        }

        $topEndDate = $this->start_date->copy()->addDays($topDays);

        return now()->lt($topEndDate);
    }

    /**
     * Check if the post is from a paid package (not free)
     */
    public function isPaidPackage(): bool
    {
        return $this->package && ! $this->package->is_free;
    }

    /**
     * Scope to order posts: paid/top first, then by date
     * Paid posts in their top period come first, sorted by date
     * Then regular posts sorted by date
     */
    public function scopeOrderByPriority($query)
    {
        return $query
            ->leftJoin('packages', 'posts.package_id', '=', 'packages.id')
            ->select('posts.*')
            ->orderByRaw('
                CASE 
                    WHEN packages.is_free = 0 AND packages.top_days > 0 
                         AND posts.start_date IS NOT NULL
                         AND DATE_ADD(posts.start_date, INTERVAL packages.top_days DAY) > NOW()
                    THEN 0
                    ELSE 1
                END ASC
            ')
            ->orderBy('posts.created_at', 'desc');
    }
}
