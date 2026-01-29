<?php

namespace Modules\Authorization\Models;

use Spatie\Permission\Models\Permission as BasePermission;

class Permission extends BasePermission
{
    protected $fillable = [
        'name',
        'guard_name',
    ];
}
