<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WholesaleOrderItem extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    protected $casts = ['price' => 'decimal:2', 'total' => 'decimal:2'];
}
