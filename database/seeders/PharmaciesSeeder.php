<?php

namespace Database\Seeders;

use App\Models\Pharmacy;
use Illuminate\Database\Seeder;

class PharmaciesSeeder extends Seeder
{
    /**
     * A starter set of branches so stock can be distributed right away.
     */
    public function run(): void
    {
        $branches = [
            ['en' => 'Main Branch',      'ar' => 'الفرع الرئيسي',   'phone' => '+970594973536', 'address' => 'غزة - شارع عمر المختار'],
            ['en' => 'North Branch',     'ar' => 'فرع الشمال',      'phone' => '+970594973537', 'address' => 'جباليا - الشارع العام'],
            ['en' => 'Downtown Branch',  'ar' => 'فرع وسط البلد',   'phone' => '+970594973538', 'address' => 'خان يونس - الدوار الرئيسي'],
        ];

        foreach ($branches as $branch) {
            Pharmacy::firstOrCreate(
                ['name' => json_encode(['en' => $branch['en'], 'ar' => $branch['ar']], JSON_UNESCAPED_UNICODE)],
                ['phone' => $branch['phone'], 'address' => $branch['address'], 'is_active' => true]
            );
        }
    }
}
