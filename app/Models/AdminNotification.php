<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminNotification extends Model
{
    protected $guarded = [];
    protected $casts = ['read_at' => 'datetime'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function getIsUnreadAttribute(): bool { return is_null($this->read_at); }
}