<?php

namespace Modules\Post\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class PostType extends Model
{
    use SoftDeletes, HasTranslations;

    protected $fillable = ['name', 'slug', 'image_path', 'status'];

    protected $casts = [
        'status' => 'boolean',
    ];

    public $translatable = ['name'];

    /**
     * Default placeholder image for post types
     */
    protected const DEFAULT_IMAGE = 'metronic/media/svg/files/folder-document.svg';

    /**
     * Get the image URL with fallback to default
     */
    public function getImageUrlAttribute(): string
    {
        return setting_media_url($this->image_path, asset(self::DEFAULT_IMAGE));
    }
}
