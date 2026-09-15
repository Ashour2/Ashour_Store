<?php

namespace App\Models;

use App\Traits\Trans;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory, Trans;

    protected $guarded = [];

    function roles() {
        return $this->belongsToMany(Role::class);
    }

    /** Permissions laid out by feature area, the way the matrix renders them. */
    public static function byGroup()
    {
        return static::orderBy('id')->get()->groupBy('group');
    }
}
