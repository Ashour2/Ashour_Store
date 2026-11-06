<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WholesaleOrder extends Model
{
    public const STATUSES = ['new' => 'جديد', 'accepted' => 'مقبول', 'preparing' => 'قيد التجهيز', 'completed' => 'مكتمل', 'cancelled' => 'ملغي'];
    protected $guarded = [];
    protected $casts = ['total' => 'decimal:2'];
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function pharmacy() { return $this->belongsTo(Pharmacy::class); }
    public function items() { return $this->hasMany(WholesaleOrderItem::class); }
    public function getStatusLabelAttribute(): string { return self::STATUSES[$this->status] ?? $this->status; }
}
