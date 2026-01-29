<?php

namespace Modules\Authorization\Models;

use Spatie\Permission\Models\Role as BaseRole;

class Role extends BaseRole
{
    protected $fillable = [
        'name',
        'guard_name',
    ];
}
