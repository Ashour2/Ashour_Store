<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $guarded = [];
    protected $casts = ['is_active' => 'boolean'];
    public function stocks() { return $this->hasMany(WarehouseStock::class); }
    public function orders() { return $this->hasMany(WholesaleOrder::class); }
    public function users() { return $this->hasMany(User::class); }
}
