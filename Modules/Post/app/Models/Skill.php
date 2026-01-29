<?php

namespace Modules\Post\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Skill extends Model
{
    use SoftDeletes, HasTranslations;

    protected $fillable = ['name', 'slug'];

    public $translatable = ['name'];
}
