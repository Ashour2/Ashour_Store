<?php

namespace App\Models;

use App\Traits\Trans;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory, Trans;

    protected $guarded = [];

    protected $casts = [
        'is_locked' => 'boolean',
    ];

    /** The site owner: always holds every permission, and cannot be edited. */
    const SUPER_ADMIN = 'super_admin';
    const ADMIN = 'admin';
    const PHARMACIST = 'pharmacist';
    const WAREHOUSE = 'warehouse';

    function users() {
        return $this->hasMany(User::class);
    }

    function permissions() {
        return $this->belongsToMany(Permission::class);
    }

    function getIsSuperAdminAttribute() {
        return $this->slug === self::SUPER_ADMIN;
    }

    /** Roles whose permission set the owner is allowed to change. */
    function scopeEditable($query) {
        return $query->where('is_locked', false);
    }
}
