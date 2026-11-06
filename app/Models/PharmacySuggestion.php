<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PharmacySuggestion extends Model { protected $guarded=[]; public function pharmacy(){return $this->belongsTo(Pharmacy::class);} public function user(){return $this->belongsTo(User::class);} }