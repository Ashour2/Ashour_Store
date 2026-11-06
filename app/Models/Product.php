<?php

namespace App\Models;

use App\Traits\Trans;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, Trans;

     protected $guarded=[];

    protected $casts = [
        'requires_prescription' => 'boolean',
        'show_price' => 'boolean',
        'is_active' => 'boolean',
        'expiry_date' => 'date',
    ];

    // Whether stock info should be shown at all on the public site
    function getShowStockAttribute() {
        return $this->stock_display !== 'hidden';
    }

    function getInStockAttribute() {
        return $this->available_quantity > 0;
    }

    /** A cart must be fulfillable by one eligible pharmacy, not several combined. */
    function getAvailableQuantityAttribute(): int {
        $this->loadMissing('pharmacies');
        if ($this->pharmacies->isEmpty()) {
            return max(0, (int) $this->quantity);
        }

        return (int) $this->pharmacies->filter(fn ($pharmacy) => $pharmacy->is_active
            && $pharmacy->subscription_status !== 'suspended'
            && (! $pharmacy->subscription_ends_at || $pharmacy->subscription_ends_at->gte(today()))
            && $pharmacy->pivot->is_allowed && $pharmacy->pivot->is_available)
            ->max('pivot.quantity');
    }

    // Bilingual contraindications and warnings (stored as JSON like name/description).
    function getTransContraindicationsWarningsAttribute() {
        return json_decode($this->contraindications_warnings ?: '{}', true)[app()->getLocale()] ?? '';
    }

    function getContraindicationsWarningsEnAttribute() {
        return json_decode($this->contraindications_warnings ?: '{}', true)['en'] ?? '';
    }

    function getContraindicationsWarningsArAttribute() {
        return json_decode($this->contraindications_warnings ?: '{}', true)['ar'] ?? '';
    }

    /**
     * Branches stocking this medicine, with the quantity held at each.
     * Dashboard-only information.
     */
    function pharmacies() {
        return $this->belongsToMany(Pharmacy::class, 'pharmacy_product')
            ->withPivot('quantity', 'is_allowed', 'is_available', 'price', 'show_price', 'notes')
            ->withTimestamps();
    }

    /** Staff member who first added this medicine to the catalogue. */
    function addedBy() {
        return $this->belongsTo(User::class, 'added_by_id')->withDefault();
    }

    /** Scope a pharmacist to medicines carried by their own pharmacy. */
    function scopeVisibleTo($query, User $user) {
        if (! $user->isPharmacist()) {
            return $query;
        }

        if (! $user->pharmacy_id) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('pharmacies', fn ($pharmacies) => $pharmacies->whereKey($user->pharmacy_id)->where('pharmacy_product.is_allowed', true));
    }

    /** Products allowed to appear on the public pharmacy site. */
    function scopePubliclyVisible($query) {
        return $query->where('is_active', true);
    }

    /** Total units across every branch holding this medicine. */
    function getBranchStockAttribute() {
        return (int) $this->pharmacies->sum('pivot.quantity');
    }

    /**
     * Replace the branch distribution from a [pharmacy_id => quantity] map.
     *
     * $selected is the list of branches ticked on the medicine form: those are
     * stocked even at zero units, which is how a branch that carries the
     * medicine but has run out is recorded. When it is null — the stock sheet,
     * which has no tick boxes — a quantity of zero means "not stocked here".
     *
     * Ids that are not real branches are ignored either way, so a tampered
     * form cannot break the foreign key.
     */
    function syncBranchStock(array $quantities, ?array $selected = null): void {
        $branches = Pharmacy::pluck('id')->all();

        if ($selected === null) {
            $stock = collect($quantities)
                ->only($branches)
                ->filter(fn ($quantity) => (int) $quantity > 0)
                ->mapWithKeys(fn ($quantity, $pharmacy_id) => [
                    (int) $pharmacy_id => ['quantity' => (int) $quantity],
                ])
                ->all();
        } else {
            $stock = collect($selected)
                ->map(fn ($pharmacy_id) => (int) $pharmacy_id)
                ->intersect($branches)
                ->mapWithKeys(fn ($pharmacy_id) => [
                    $pharmacy_id => ['quantity' => max(0, (int) ($quantities[$pharmacy_id] ?? 0))],
                ])
                ->all();
        }

        $this->pharmacies()->sync($stock);
    }

    function consultations() {
        return $this->hasMany(Consultation::class);
    }

    function category() {
        return $this->belongsTo(Category::class)->withDefault();
    }

    function image() {
        return $this->morphOne(Image::class, 'imageable')->where('type', 'main');
    }

    function gallery() {
        return $this->morphMany(Image::class, 'imageable')->where('type', 'gallery');
    }

    function reviews() {
        return $this->HasMany(Review::class);
    }

    function order_details() {
        return $this->HasMany(OrderDetail::class);
    }

    function getImgPathAttribute() {
        return $this->image?->path ? asset('images/'. $this->image->path) : null;
    }
}


