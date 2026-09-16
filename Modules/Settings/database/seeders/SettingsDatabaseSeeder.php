<?php

namespace Modules\Settings\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Settings\Models\Setting;

class SettingsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::updateOrCreate(
            ['id' => 1],
            [
                // Contact Details
                'email' => 'info@astroleaderskw.com',
                'phone' => '+965 22200000',
                'whatsapp' => '+965 99990000',
                'emergency_phone' => '+965 99990001',

                // Visual Identity
                'logo' => null,
                'favicon' => null,
                'primary_color' => '#0b284e',

                // Regional & Financial
                'currency' => 'KWD',
                'currency_symbol' => 'د.ك',
                'timezone' => 'Asia/Kuwait',
                'default_locale' => 'ar',
                'tax_number' => 'KW-12345678',

                // Social Media
                'facebook' => 'https://facebook.com/astroleaderskw',
                'instagram' => 'https://instagram.com/astroleaderskw',
                'twitter' => 'https://twitter.com/astroleaderskw',
                'tiktok' => 'https://tiktok.com/@astroleaderskw',
                'youtube' => 'https://youtube.com/@astroleaderskw',

                // Translatable Data
                'ar' => [
                    'site_name' => 'أكاديمية أسترو ليدرز',
                    'slogan' => 'نصنع قادة الغد',
                    'address' => 'دولة الكويت - العاصمة - برج التجارية',
                    'footer_text' => 'جميع الحقوق محفوظة © ' . date('Y') . ' أكاديمية أسترو ليدرز للتعليم والتدريب',
                ],
                'en' => [
                    'site_name' => 'Astro Leaders Academy',
                    'slogan' => "Shaping Tomorrow's Leaders",
                    'address' => 'State of Kuwait - Capital City - Al Tijaria Tower',
                    'footer_text' => 'All rights reserved © ' . date('Y') . ' Astro Leaders Academy',
                ],
            ]
        );

        $this->call(GeneralSettingDatabaseSeeder::class);
    }
}
