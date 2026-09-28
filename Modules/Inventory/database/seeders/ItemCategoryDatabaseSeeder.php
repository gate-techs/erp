<?php

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Inventory\Models\ItemCategory;

class ItemCategoryDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'code' => 'CAT-FOOD',
                'ar'   => 'مواد غذائية',
                'en'   => 'Food & Beverages',
                'children' => [
                    ['code' => 'CAT-FOOD-DRY',   'ar' => 'مواد جافة',          'en' => 'Dry Goods'],
                    ['code' => 'CAT-FOOD-FRESH',  'ar' => 'منتجات طازجة',       'en' => 'Fresh Products'],
                    ['code' => 'CAT-FOOD-DAIRY',  'ar' => 'منتجات الألبان',     'en' => 'Dairy Products'],
                    ['code' => 'CAT-FOOD-BEV',    'ar' => 'مشروبات',            'en' => 'Beverages'],
                    ['code' => 'CAT-FOOD-FROZEN',  'ar' => 'منتجات مجمدة',      'en' => 'Frozen Products'],
                ],
            ],
            [
                'code' => 'CAT-CLEAN',
                'ar'   => 'منظفات ومواد تنظيف',
                'en'   => 'Cleaning Supplies',
                'children' => [
                    ['code' => 'CAT-CLEAN-HOME',  'ar' => 'منظفات منزلية',       'en' => 'Home Cleaners'],
                    ['code' => 'CAT-CLEAN-TOOLS',  'ar' => 'أدوات تنظيف',        'en' => 'Cleaning Tools'],
                ],
            ],
            [
                'code' => 'CAT-STATIONARY',
                'ar'   => 'قرطاسية ومكتبيات',
                'en'   => 'Stationery & Office Supplies',
                'children' => [
                    ['code' => 'CAT-STAT-PAPER',  'ar' => 'ورق وملفات',          'en' => 'Paper & Files'],
                    ['code' => 'CAT-STAT-PENS',   'ar' => 'أقلام ومستلزمات',    'en' => 'Pens & Supplies'],
                ],
            ],
            [
                'code' => 'CAT-ELEC',
                'ar'   => 'إلكترونيات وكهربائيات',
                'en'   => 'Electronics & Electrical',
                'children' => [
                    ['code' => 'CAT-ELEC-DEVICES',  'ar' => 'أجهزة إلكترونية',  'en' => 'Electronic Devices'],
                    ['code' => 'CAT-ELEC-ACC',      'ar' => 'ملحقات وإكسسوارات', 'en' => 'Accessories'],
                ],
            ],
            [
                'code' => 'CAT-PACK',
                'ar'   => 'مواد تغليف وتعبئة',
                'en'   => 'Packaging Materials',
                'children' => [
                    ['code' => 'CAT-PACK-BOX',   'ar' => 'صناديق وكراتين',      'en' => 'Boxes & Cartons'],
                    ['code' => 'CAT-PACK-BAG',   'ar' => 'أكياس وحقائب',        'en' => 'Bags'],
                    ['code' => 'CAT-PACK-WRAP',  'ar' => 'أغلفة ولفائف',        'en' => 'Wraps & Rolls'],
                ],
            ],
            [
                'code' => 'CAT-OTHER',
                'ar'   => 'متنوعات',
                'en'   => 'Miscellaneous',
                'children' => [],
            ],
        ];

        foreach ($categories as $catData) {
            $parent = $this->createOrSkip($catData, null);

            foreach (($catData['children'] ?? []) as $childData) {
                $this->createOrSkip($childData, $parent?->id);
            }
        }

        $this->command?->info('✅ تم إنشاء فئات الأصناف بنجاح.');
    }

    //------------------------------------------
    protected function createOrSkip(array $data, ?int $parentId): ?ItemCategory
    {
        $existing = ItemCategory::where('code', $data['code'])->first();

        if ($existing) {
            $this->command?->line("  ⤷ [skip] فئة موجودة: {$data['ar']}");
            return $existing;
        }

        $category = ItemCategory::create([
            'parent_id'  => $parentId,
            'code'       => $data['code'],
            'sort_order' => 0,
            'status'     => 'active',
        ]);

        $category->translateOrNew('ar')->name = $data['ar'];
        $category->translateOrNew('en')->name = $data['en'];
        $category->save();

        $icon = $parentId ? '  └─' : '📁';
        $this->command?->line("  {$icon} تم إضافة: {$data['ar']}");

        return $category;
    }
}
