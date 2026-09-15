<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticlesSeeder extends Seeder
{
    /**
     * Seed a few demo health articles, reusing generated placeholder images.
     */
    public function run(): void
    {
        $images = collect(glob(public_path('images/fake_*.png')))
            ->map(fn ($p) => basename($p))
            ->shuffle()
            ->take(3)
            ->values();

        $data = [
            [
                'title'   => ['ar' => '5 نصائح لتقوية المناعة', 'en' => '5 Tips to Boost Your Immunity'],
                'content' => [
                    'ar' => "المناعة القوية تبدأ من نمط حياة صحي: نوم كافٍ، تغذية متوازنة غنية بالفيتامينات، ترطيب جيد، ونشاط بدني منتظم. تجنّب التدخين وقلّل التوتر، واحرص على غسل اليدين بانتظام للوقاية من العدوى.\n\nاستشر الصيدلي قبل تناول أي مكمل غذائي.",
                    'en' => "Strong immunity starts with a healthy lifestyle: enough sleep, balanced nutrition rich in vitamins, good hydration, and regular activity. Avoid smoking, reduce stress, and wash your hands often.\n\nAsk your pharmacist before taking any supplement.",
                ],
            ],
            [
                'title'   => ['ar' => 'كيف تستخدم المضادات الحيوية بأمان', 'en' => 'How to Use Antibiotics Safely'],
                'content' => [
                    'ar' => "المضادات الحيوية لا تُصرف إلا بوصفة طبية، وتُستخدم للعدوى البكتيرية فقط. أكمل الجرعة كاملة حتى لو تحسّنت، ولا تشاركها مع الآخرين، ولا تحتفظ بالبقايا لاستخدام لاحق.\n\nالاستخدام الخاطئ يسبب مقاومة البكتيريا.",
                    'en' => "Antibiotics require a prescription and treat bacterial infections only. Complete the full course even if you feel better, do not share them, and do not keep leftovers.\n\nMisuse causes antibiotic resistance.",
                ],
            ],
            [
                'title'   => ['ar' => 'إدارة ضغط الدم المرتفع', 'en' => 'Managing High Blood Pressure'],
                'content' => [
                    'ar' => "قِس ضغطك بانتظام، قلّل الملح، مارس الرياضة، وحافظ على وزن صحي. تناول أدويتك في مواعيدها ولا توقفها من تلقاء نفسك.\n\nراجع طبيبك أو صيدليك عند أي أعراض غير معتادة.",
                    'en' => "Measure your pressure regularly, cut down on salt, exercise, and keep a healthy weight. Take your medication on time and never stop it on your own.\n\nSee your doctor or pharmacist for unusual symptoms.",
                ],
            ],
        ];

        foreach ($data as $i => $d) {
            $article = Article::firstOrCreate(
                ['name' => json_encode($d['title'], JSON_UNESCAPED_UNICODE)],
                [
                    'description' => json_encode($d['content'], JSON_UNESCAPED_UNICODE),
                    'is_published' => true,
                ]
            );

            if ($images->has($i) && ! $article->image()->exists()) {
                $article->image()->create(['path' => $images[$i]]);
            }
        }

        $this->command->info('Seeded ' . Article::count() . ' articles.');
    }
}
