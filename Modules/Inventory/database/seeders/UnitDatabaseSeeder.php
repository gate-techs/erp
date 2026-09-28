<?php

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Inventory\Models\Unit;

class UnitDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            // ——— عدد / Count ———
            ['symbol' => 'قطعة',  'ar' => 'قطعة',          'en' => 'Piece'],
            ['symbol' => 'حبة',   'ar' => 'حبة',           'en' => 'Item'],
            ['symbol' => 'علبة',  'ar' => 'علبة',          'en' => 'Box'],
            ['symbol' => 'كرتون', 'ar' => 'كرتون',         'en' => 'Carton'],
            ['symbol' => 'دزينة', 'ar' => 'دزينة',         'en' => 'Dozen'],
            ['symbol' => 'رول',   'ar' => 'رول',           'en' => 'Roll'],
            ['symbol' => 'طرد',   'ar' => 'طرد / عبوة',   'en' => 'Package'],
            ['symbol' => 'زجاجة', 'ar' => 'زجاجة',        'en' => 'Bottle'],
            ['symbol' => 'كيس',   'ar' => 'كيس',           'en' => 'Bag'],

            // ——— وزن / Weight ———
            ['symbol' => 'kg',  'ar' => 'كيلوجرام',  'en' => 'Kilogram'],
            ['symbol' => 'g',   'ar' => 'جرام',       'en' => 'Gram'],
            ['symbol' => 'ton', 'ar' => 'طن',         'en' => 'Ton'],
            ['symbol' => 'lb',  'ar' => 'رطل',        'en' => 'Pound'],

            // ——— حجم / Volume ———
            ['symbol' => 'L',    'ar' => 'لتر',       'en' => 'Liter'],
            ['symbol' => 'mL',   'ar' => 'مليلتر',    'en' => 'Milliliter'],
            ['symbol' => 'gal',  'ar' => 'جالون',     'en' => 'Gallon'],

            // ——— طول / Length ———
            ['symbol' => 'm',   'ar' => 'متر',        'en' => 'Meter'],
            ['symbol' => 'cm',  'ar' => 'سنتيمتر',    'en' => 'Centimeter'],
            ['symbol' => 'mm',  'ar' => 'مليمتر',     'en' => 'Millimeter'],

            // ——— مساحة / Area ———
            ['symbol' => 'm²',  'ar' => 'متر مربع',  'en' => 'Square Meter'],

            // ——— خدمات / Services ———
            ['symbol' => 'ساعة', 'ar' => 'ساعة',     'en' => 'Hour'],
            ['symbol' => 'يوم',  'ar' => 'يوم',      'en' => 'Day'],
            ['symbol' => 'شهر',  'ar' => 'شهر',      'en' => 'Month'],
        ];

        foreach ($units as $data) {
            $existing = Unit::where('symbol', $data['symbol'])->first();

            if ($existing) {
                $this->command?->line("  ⤷ [skip] وحدة موجودة: {$data['symbol']}");
                continue;
            }

            $unit = Unit::create([
                'symbol' => $data['symbol'],
                'status' => 'active',
            ]);

            $unit->translateOrNew('ar')->name = $data['ar'];
            $unit->translateOrNew('en')->name = $data['en'];
            $unit->save();

            $this->command?->line("  ✓ تم إضافة وحدة: {$data['ar']} ({$data['symbol']})");
        }

        $this->command?->info('✅ تم إنشاء وحدات القياس بنجاح.');
    }
}
