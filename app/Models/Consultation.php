<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use HasFactory;

    protected $guarded = [];

    function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class)->withDefault();
    }

    function product()
    {
        return $this->belongsTo(Product::class)->withDefault();
    }

    // Public URL of the uploaded prescription, or null
    function getPrescriptionUrlAttribute()
    {
        return $this->prescription ? asset('prescriptions/' . $this->prescription) : null;
    }

    // Whether the uploaded prescription is an image (vs. a PDF)
    function getPrescriptionIsImageAttribute()
    {
        return $this->prescription
            && ! str_ends_with(strtolower($this->prescription), '.pdf');
    }
}
