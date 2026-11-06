<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Trans;


class Category extends Model
{
    use HasFactory ,Trans;

     protected $guarded=[];

    function products(){
        return $this->hasMany(Product::class);
    }

    /** Returns the saved Font Awesome code, or no icon for a new blank category. */
    function getIconClassAttribute(): string
    {
        $customIcon = trim((string) $this->icon_code);

        if ($customIcon !== '') {
            preg_match_all('/\bfa(?:-[a-z0-9]+)+\b/i', $customIcon, $matches);
            $classes = array_unique($matches[0]);

            if ($classes) {
                return implode(' ', $classes);
            }
        }

        return '';
    }

    /** The fixed line-icon key used by the catalogue cards. */
    function getLineIconAttribute(): string
    {
        $code = trim((string) $this->icon_code);

        return preg_match('/^line:([a-z-]+)$/', $code, $matches) ? $matches[1] : '';
    }

    /** Suggest a suitable Font Awesome icon from the Arabic or English category name. */
    function suggestedIconClass(): string
    {
        $name = mb_strtolower($this->name_en . ' ' . $this->name_ar);
        $icons = [
            ['terms' => ['pain', 'fever', 'مسكن', 'حرارة'], 'icon' => 'fa-solid fa-temperature-half'],
            ['terms' => ['antibiotic', 'مضاد حيو'], 'icon' => 'fa-solid fa-capsules'],
            ['terms' => ['vitamin', 'supplement', 'فيتامين', 'مكمل'], 'icon' => 'fa-solid fa-tablets'],
            ['terms' => ['digestive', 'stomach', 'هضم', 'المعدة', 'الجهاز الهضمي'], 'icon' => 'fa-solid fa-bowl-food'],
            ['terms' => ['cold', 'cough', 'برد', 'سعال'], 'icon' => 'fa-solid fa-head-side-cough'],
            ['terms' => ['skin', 'بشرة', 'جلد'], 'icon' => 'fa-solid fa-pump-soap'],
            ['terms' => ['heart', 'blood pressure', 'قلب', 'ضغط'], 'icon' => 'fa-solid fa-heart-pulse'],
            ['terms' => ['diabetes', 'diabetic', 'سكري'], 'icon' => 'fa-solid fa-droplet'],
            ['terms' => ['mother', 'baby', 'أم', 'طفل'], 'icon' => 'fa-solid fa-baby'],
            ['terms' => ['oral', 'teeth', 'tooth', 'فم', 'أسنان'], 'icon' => 'fa-solid fa-tooth'],
        ];

        foreach ($icons as $item) {
            foreach ($item['terms'] as $term) {
                if (str_contains($name, $term)) {
                    return $item['icon'];
                }
            }
        }

        return 'fa-solid fa-pills';
    }

    function image(){
        return $this->morphOne(Image::class,'imageable');
    }
    // img_path
    function getImgPathAttribute() {
        return $this->image?->path ? asset('images/'. $this->image->path) : null;
    }
}
