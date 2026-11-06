<?php

namespace App\Models;

use App\Traits\Trans;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory, Trans;

    protected $guarded = [];

    protected $casts = [
        'is_published' => 'boolean',
        'font_size' => 'string',
        'font_family' => 'string',
    ];

    public function getStyleClassAttribute(): string
    {
        $font = in_array($this->font_family, ['cairo', 'tajawal', 'serif'], true) ? $this->font_family : 'cairo';
        $size = in_array($this->font_size, ['small', 'medium', 'large', 'xlarge'], true) ? $this->font_size : 'medium';
        return "article-font-{$font} article-size-{$size}";
    }
    function image() {
        return $this->morphOne(Image::class, 'imageable')->withDefault([
            'path' => 'no-image.png',
        ])->where('type', 'main');
    }

    function getImgPathAttribute() {
        $url = 'https://via.placeholder.com/600x400';
        if ($this->image && $this->image->path && $this->image->path !== 'no-image.png') {
            $url = asset('images/' . $this->image->path);
        }
        return $url;
    }

    // Short plain-text excerpt from the localized content
    function getExcerptAttribute() {
        return \Illuminate\Support\Str::limit(strip_tags($this->trans_description), 120);
    }

    function scopePublished($query) {
        return $query->where('is_published', true);
    }
}
