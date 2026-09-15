<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class FakeCatalogSeeder extends Seeder
{
    /**
     * Seed 10 categories, each with 10 fake medicines and a generated image.
     */
    public function run(): void
    {
        $categories = [
            ['ar' => 'مسكنات وخافضات حرارة', 'en' => 'Painkillers & Fever',       'color' => [239, 68, 68]],
            ['ar' => 'مضادات حيوية',          'en' => 'Antibiotics',               'color' => [59, 130, 246]],
            ['ar' => 'فيتامينات ومكملات',     'en' => 'Vitamins & Supplements',    'color' => [34, 197, 94]],
            ['ar' => 'الجهاز الهضمي',         'en' => 'Digestive Health',          'color' => [245, 158, 11]],
            ['ar' => 'البرد والسعال',         'en' => 'Cold & Cough',              'color' => [6, 182, 212]],
            ['ar' => 'العناية بالبشرة',       'en' => 'Skin Care',                 'color' => [236, 72, 153]],
            ['ar' => 'الضغط والقلب',          'en' => 'Heart & Blood Pressure',    'color' => [139, 92, 246]],
            ['ar' => 'العناية بمرضى السكري',  'en' => 'Diabetes Care',             'color' => [20, 184, 166]],
            ['ar' => 'الأم والطفل',           'en' => 'Mother & Baby',             'color' => [249, 115, 22]],
            ['ar' => 'العناية بالفم والأسنان','en' => 'Oral Care',                 'color' => [14, 165, 164]],
        ];

        $stems = [
            'Panadol', 'Amoxil', 'Zithromax', 'Nurofen', 'Cetal', 'Augmentin', 'Ventolin', 'Concor',
            'Glucophage', 'Vitacid', 'Omepra', 'Flagyl', 'Claritine', 'Voltaren', 'Brufen', 'Rennie',
            'Fucidin', 'Betadine', 'Sensodyne', 'Neurobion', 'Adol', 'Cataflam', 'Zinnat', 'Lipitor',
            'Nexium', 'Ceporex', 'Telfast', 'Aspocid', 'Danset', 'Maalox',
        ];
        $ingredients = ['Paracetamol', 'Amoxicillin', 'Azithromycin', 'Ibuprofen', 'Omeprazole',
            'Metformin', 'Diclofenac', 'Cetirizine', 'Atorvastatin', 'Ascorbic Acid'];
        $manufacturers = ['Cairo Pharma', 'Global Meds', 'VitaLife', 'MediCore', 'PharmaPlus', 'HealWell'];
        $forms = ['أقراص', 'شراب', 'كبسولات', 'مرهم', 'حقن', 'أقراص فوّارة'];
        $strengths = [50, 100, 125, 250, 400, 500, 1000];

        $imgDir = public_path('images');
        if (! is_dir($imgDir)) {
            mkdir($imgDir, 0777, true);
        }

        $n = 0;
        foreach ($categories as $ci => $c) {
            $cat = Category::firstOrCreate(
                ['name' => json_encode(['ar' => $c['ar'], 'en' => $c['en']], JSON_UNESCAPED_UNICODE)],
                ['description' => json_encode(['ar' => $c['ar'], 'en' => $c['en']], JSON_UNESCAPED_UNICODE)]
            );

            for ($i = 1; $i <= 10; $i++) {
                $stem = $stems[$n % count($stems)];
                $strength = $strengths[($n + $i) % count($strengths)];
                $label = $stem . ' ' . $strength;

                $nameEn = $stem . ' ' . $strength . 'mg';
                $nameAr = $stem . ' ' . $strength . ' مجم';

                $img_name = $this->makeImage($imgDir, $label, $c['color'], $ci * 10 + $i);

                $product = Product::create([
                    'name' => json_encode(['en' => $nameEn, 'ar' => $nameAr], JSON_UNESCAPED_UNICODE),
                    'description' => json_encode([
                        'en' => 'A demo product in the "' . $c['en'] . '" category for testing the catalog.',
                        'ar' => 'منتج تجريبي ضمن قسم «' . $c['ar'] . '» لاختبار الكتالوج.',
                    ], JSON_UNESCAPED_UNICODE),
                    'recommended_dosage' => json_encode([
                        'en' => 'One dose every 8 hours after meals, or as directed.',
                        'ar' => 'جرعة واحدة كل 8 ساعات بعد الأكل أو حسب إرشادات الطبيب.',
                    ], JSON_UNESCAPED_UNICODE),
                    'price' => rand(500, 12000) / 100,
                    'show_price' => true,
                    'quantity' => rand(0, 150),
                    'stock_display' => 'quantity',
                    'category_id' => $cat->id,
                    'active_ingredient' => $ingredients[$n % count($ingredients)],
                    'manufacturer' => $manufacturers[$n % count($manufacturers)],
                    'dosage_form' => $forms[$n % count($forms)],
                    'requires_prescription' => ($n % 3 === 0),
                    'barcode' => '62' . str_pad((string) rand(0, 99999999), 8, '0', STR_PAD_LEFT),
                    'expiry_date' => now()->addMonths(rand(6, 36)),
                ]);

                $product->image()->create(['path' => $img_name]);
                $n++;
            }
        }

        $this->command->info("Seeded {$n} fake products across " . count($categories) . ' categories.');
    }

    /**
     * Generate a simple placeholder PNG (colored background + capsule + label).
     */
    private function makeImage(string $dir, string $label, array $rgb, int $seed): string
    {
        $w = 400;
        $h = 300;
        $im = imagecreatetruecolor($w, $h);

        // Background: slightly darkened category color
        $bg = imagecolorallocate($im, (int) ($rgb[0] * 0.85), (int) ($rgb[1] * 0.85), (int) ($rgb[2] * 0.85));
        imagefilledrectangle($im, 0, 0, $w, $h, $bg);

        // Soft lighter panel
        $panel = imagecolorallocatealpha($im, 255, 255, 255, 110);
        imagefilledrectangle($im, 30, 30, $w - 30, $h - 30, $panel);

        // Capsule shape (two halves)
        $white = imagecolorallocate($im, 255, 255, 255);
        $accent = imagecolorallocate($im, $rgb[0], $rgb[1], $rgb[2]);
        $cx = $w / 2;
        $cy = 130;
        imagefilledellipse($im, $cx - 34, $cy, 70, 70, $white);
        imagefilledellipse($im, $cx + 34, $cy, 70, 70, $accent);
        imagefilledrectangle($im, $cx - 34, $cy - 35, $cx, $cy + 35, $white);
        imagefilledrectangle($im, $cx, $cy - 35, $cx + 34, $cy + 35, $accent);

        // Label text (built-in latin font)
        $textColor = imagecolorallocate($im, 30, 41, 59);
        $font = 5;
        $tw = imagefontwidth($font) * strlen($label);
        imagestring($im, $font, (int) (($w - $tw) / 2), 215, $label, $textColor);
        imagestring($im, 3, (int) (($w - imagefontwidth(3) * 12) / 2), 245, 'Line Pharm', $textColor);

        $filename = 'fake_' . $seed . '_' . uniqid() . '.png';
        imagepng($im, $dir . DIRECTORY_SEPARATOR . $filename);
        imagedestroy($im);

        return $filename;
    }
}
