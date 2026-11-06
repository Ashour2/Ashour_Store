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
        'subscription_ends_at' => 'date',
    ];

    function products() {
        return $this->belongsToMany(Product::class, 'pharmacy_product')
            ->withPivot('quantity', 'is_allowed', 'is_available', 'price', 'show_price', 'notes')
            ->withTimestamps();
    }

    /** Branches that actually hold at least one unit of something. */
    function scopeActive($query) {
        return $query->where('is_active', true);
    }

    function scopeOrderable($query) {
        return $query->active()->where('subscription_status', '!=', 'suspended')
            ->where(fn ($subscriptions) => $subscriptions->whereNull('subscription_ends_at')->orWhereDate('subscription_ends_at', '>=', today()));
    }

    /** Digits-only phone, ready for a wa.me link. */
    function getWaPhoneAttribute() {
        return preg_replace('/[^0-9]/', '', $this->phone);
    }
    function orders() {
        return $this->hasMany(Order::class);
    }

    function users() {
        return $this->hasMany(User::class);
    }

    function getSubscriptionIsActiveAttribute(): bool {
        return $this->is_active
            && $this->subscription_status !== 'suspended'
            && (! $this->subscription_ends_at || $this->subscription_ends_at->isFuture());
    }
}
