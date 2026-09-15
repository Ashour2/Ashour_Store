<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Seed default site settings (only fills missing keys).
     */
    public function run(): void
    {
        $defaults = [
            'site_name_ar'      => 'صيدلية عاشور',
            'site_name_en'      => 'Ashour Pharmacy',
            'whatsapp_number'   => '966500000000',
            'phone'             => '+966 50 000 0000',
            'email'             => 'info@ashour-pharmacy.com',
            'default_country_code' => '+966',
            'address_ar'        => 'الرياض، المملكة العربية السعودية',
            'address_en'        => 'Riyadh, Saudi Arabia',
            'hero_title_ar'     => 'دواؤك بين يديك، واستشارة الصيدلي بضغطة زر',
            'hero_title_en'     => 'Your medicine at hand, a pharmacist a click away',
            'hero_subtitle_ar'  => 'ابحث عن الأدوية، اطّلع على تفاصيلها، واطلب استشارة صيدلي مجاناً.',
            'hero_subtitle_en'  => 'Search medicines, view details, and request a free pharmacist consultation.',
            'about_ar'          => 'نوفّر لك معلومات موثوقة عن الأدوية وخدمة استشارة صيدلانية سريعة.',
            'about_en'          => 'Reliable medicine information and fast pharmacist consultation service.',
            'working_hours_ar'  => 'يومياً من 9 صباحاً حتى 12 منتصف الليل',
            'working_hours_en'  => 'Daily from 9 AM to 12 midnight',

            // Homepage slider: 'colors' keeps the gradient, 'image' uses hero_image
            'hero_mode'         => 'colors',
            'hero_color_from'   => '#0f766e',
            'hero_color_to'     => '#0ea5a4',
            'hero_overlay'      => '40',
            'hero_image'        => '',

            // Social media links (empty = hidden). Fill from the dashboard.
            'social_facebook'   => '',
            'social_instagram'  => '',
            'social_youtube'    => '',
            'social_telegram'   => '',
            'social_twitter'    => '',
            'social_tiktok'     => '',
            'social_snapchat'   => '',
            'social_linkedin'   => '',
        ];

        foreach ($defaults as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
