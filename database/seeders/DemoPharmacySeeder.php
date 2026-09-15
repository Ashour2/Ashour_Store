<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DemoPharmacySeeder extends Seeder
{
    /**
     * Seed a few demo categories and medicines for previewing the site.
     */
    public function run(): void
    {
        $categories = [
            ['ar' => 'مسكنات وخافضات حرارة', 'en' => 'Painkillers & Fever'],
            ['ar' => 'مضادات حيوية', 'en' => 'Antibiotics'],
            ['ar' => 'فيتامينات ومكملات', 'en' => 'Vitamins & Supplements'],
        ];

        $catIds = [];
        foreach ($categories as $c) {
            $cat = Category::firstOrCreate(
                ['name' => json_encode($c, JSON_UNESCAPED_UNICODE)],
                ['description' => json_encode($c, JSON_UNESCAPED_UNICODE)]
            );
            $catIds[] = $cat->id;
        }

        $medicines = [
            [
                'name' => ['ar' => 'باراسيتامول 500 مجم', 'en' => 'Paracetamol 500mg'],
                'desc' => ['ar' => 'مسكن للألم وخافض للحرارة يستخدم للصداع وآلام الجسم.', 'en' => 'Pain reliever and fever reducer for headache and body aches.'],
                'dosage' => ['ar' => 'قرص إلى قرصين كل 6 ساعات عند الحاجة.', 'en' => '1-2 tablets every 6 hours as needed.'],
                'price' => 12.50, 'ai' => 'Paracetamol', 'mf' => 'Cairo Pharma', 'form' => 'أقراص', 'rx' => false, 'cat' => 0,
            ],
            [
                'name' => ['ar' => 'أموكسيسيلين 500 مجم', 'en' => 'Amoxicillin 500mg'],
                'desc' => ['ar' => 'مضاد حيوي واسع الطيف يُصرف بوصفة طبية.', 'en' => 'Broad-spectrum antibiotic, dispensed by prescription.'],
                'dosage' => ['ar' => 'كبسولة كل 8 ساعات حسب إرشادات الطبيب.', 'en' => 'One capsule every 8 hours as directed by a physician.'],
                'price' => 28.00, 'ai' => 'Amoxicillin', 'mf' => 'Global Meds', 'form' => 'كبسولات', 'rx' => true, 'cat' => 1,
            ],
            [
                'name' => ['ar' => 'فيتامين سي 1000 مجم', 'en' => 'Vitamin C 1000mg'],
                'desc' => ['ar' => 'مكمل غذائي يدعم المناعة.', 'en' => 'Dietary supplement that supports immunity.'],
                'dosage' => ['ar' => 'قرص فوّار واحد يومياً.', 'en' => 'One effervescent tablet daily.'],
                'price' => 35.00, 'ai' => 'Ascorbic Acid', 'mf' => 'VitaLife', 'form' => 'أقراص فوّارة', 'rx' => false, 'cat' => 2,
            ],
            [
                'name' => ['ar' => 'إيبوبروفين 400 مجم', 'en' => 'Ibuprofen 400mg'],
                'desc' => ['ar' => 'مضاد للالتهاب ومسكن للألم.', 'en' => 'Anti-inflammatory and pain reliever.'],
                'dosage' => ['ar' => 'قرص كل 8 ساعات بعد الأكل.', 'en' => 'One tablet every 8 hours after food.'],
                'price' => 18.75, 'ai' => 'Ibuprofen', 'mf' => 'Cairo Pharma', 'form' => 'أقراص', 'rx' => false, 'cat' => 0,
            ],
        ];

        foreach ($medicines as $m) {
            Product::firstOrCreate(
                ['name' => json_encode($m['name'], JSON_UNESCAPED_UNICODE)],
                [
                    'description' => json_encode($m['desc'], JSON_UNESCAPED_UNICODE),
                    'recommended_dosage' => json_encode($m['dosage'], JSON_UNESCAPED_UNICODE),
                    'price' => $m['price'],
                    'quantity' => 100,
                    'category_id' => $catIds[$m['cat']],
                    'active_ingredient' => $m['ai'],
                    'manufacturer' => $m['mf'],
                    'dosage_form' => $m['form'],
                    'requires_prescription' => $m['rx'],
                    'expiry_date' => now()->addYears(2),
                ]
            );
        }
    }
}
