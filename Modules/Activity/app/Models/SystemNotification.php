<?php

namespace Modules\Activity\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class SystemNotification extends Model
{
    protected $table = 'system_notifications';

    protected $fillable = [
        'uuid',
        'type',
        'level',
        'title',
        'message',
        'payload',
        'is_read',
        'read_at',
        'user_id',
        'notifiable_id',
        'channel',
    ];

    protected $casts = [
        'payload' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    protected $appends = [
        'category',
    ];

    protected static function booted(): void
    {
        static::creating(function (SystemNotification $notification) {
            $notification->uuid ??= (string) Str::uuid();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\Modules\User\Models\User::class);
    }

    public function notifiable(): BelongsTo
    {
        return $this->belongsTo(\Modules\User\Models\User::class, 'notifiable_id');
    }

    public function getCategoryAttribute(): string
    {
        return self::categoryForType($this->type);
    }

    public static function categoryForType(?string $type): string
    {
        return match ($type) {
            'update', 'report' => 'updates',
            'audit', 'log' => 'logs',
            default => 'alerts',
        };
    }
}
