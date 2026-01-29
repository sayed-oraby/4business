<?php

namespace Modules\Post\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostAttachment extends Model
{
    protected $fillable = ['post_id', 'title', 'file_path', 'file_type'];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
