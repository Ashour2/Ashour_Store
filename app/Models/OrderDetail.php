<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    function user() {
        return $this->belongsTo(User::class)->withDefault();
    }

    function product() {
        return $this->belongsTo(Product::class)->withDefault();
    }

    function order() {
        return $this->belongsTo(Order::class)->withDefault();
    }

    /**
     * Medicine name for the current locale, taken from the snapshot saved with
     * the order so it survives a rename or a deleted medicine.
     */
    function getTransProductNameAttribute() {
        $name = json_decode($this->product_name, true);

        if (is_array($name)) {
            return $name[app()->getLocale()] ?? reset($name);
        }

        return $this->product_name ?: $this->product->trans_name;
    }
}
