<?php

namespace Modules\Activity\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\User\Models\User;

class NotificationDevice extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'device_uuid',
        'device_token',
        'device_type',
        'app_version',
        'language',
        'guest_uuid',
        'notifications_enabled',
        'last_seen_at',
    ];

    protected $casts = [
        'notifications_enabled' => 'boolean',
        'last_seen_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
