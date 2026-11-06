<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseStock extends Model
{
    protected $guarded = [];
    protected $casts = ['price' => 'decimal:2', 'quantity' => 'integer', 'is_available' => 'boolean'];
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function product() { return $this->belongsTo(Product::class); }
}
