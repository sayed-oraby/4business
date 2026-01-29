<?php

namespace Modules\Activity\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    protected $fillable = [
        'uuid',
        'user_id',
        'guard',
        'action',
        'description',
        'context',
        'properties',
        'ip_address',
        'user_agent',
        'device',
        'platform',
        'browser',
        'occurred_at',
    ];

    protected $casts = [
        'properties' => 'array',
        'occurred_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (AuditLog $log) {
            $log->uuid ??= (string) Str::uuid();
            $log->occurred_at ??= now();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\Modules\User\Models\User::class);
    }
}
