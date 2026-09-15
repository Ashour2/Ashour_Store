<?php

namespace App\Models;

use App\Traits\Trans;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A pharmacy branch holding stock. Internal to the dashboard — branches and
 * their quantities are never exposed on the public site.
 */
class Pharmacy extends Model
{
    use HasFactory, Trans;

    protected $table = 'pharmacies';

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    function products() {
        return $this->belongsToMany(Product::class, 'pharmacy_product')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    /** Branches that actually hold at least one unit of something. */
    function scopeActive($query) {
        return $query->where('is_active', true);
    }

    /** Digits-only phone, ready for a wa.me link. */
    function getWaPhoneAttribute() {
        return preg_replace('/[^0-9]/', '', $this->phone);
    }
}
