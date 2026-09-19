<?php

namespace Modules\HR\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\HR\Models\HrSetting;

class HrSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'allowance-types' => [
                ['en' => 'Housing Allowance', 'ar' => 'بدل سكن'],
                ['en' => 'Transportation Allowance', 'ar' => 'بدل مواصلات'],
                ['en' => 'Phone Allowance', 'ar' => 'بدل هاتف'],
                ['en' => 'Nature of Work Allowance', 'ar' => 'بدل طبيعة عمل'],
            ],
            'document-types' => [
                ['en' => 'Civil ID / Residence', 'ar' => 'الهوية / الإقامة'],
                ['en' => 'Passport', 'ar' => 'جواز السفر'],
                ['en' => 'Employment Contract', 'ar' => 'عقد العمل'],
                ['en' => 'Qualification Certificate', 'ar' => 'شهادة المؤهل'],
            ],
            'reward-types' => [
                ['en' => 'Performance Bonus', 'ar' => 'مكافأة أداء استثنائي'],
                ['en' => 'Productivity & Target Incentive', 'ar' => 'حافز إنتاجية ومستهدفات'],
                ['en' => 'Employee of the Month / Spot Bonus', 'ar' => 'مكافأة موظف الشهر / التميز'],
                ['en' => 'Seasonal & Holiday Grant', 'ar' => 'منحة أعياد ومناسبات'],
                ['en' => 'Employee Referral Bonus', 'ar' => 'مكافأة استقطاب كفاءات'],
            ],
            'penalty-types' => [
                ['en' => 'Written Warning', 'ar' => 'إنذار كتابي'],
                ['en' => 'Unexcused Delay Penalty', 'ar' => 'جزاء تأخير غير مبرر'],
                ['en' => 'Unauthorized Absence Deduction', 'ar' => 'خصم غياب بدون إذن'],
                ['en' => 'Negligence & Safety Violation', 'ar' => 'مخالفة إهمال وتعليمات السلامة'],
                ['en' => 'Misconduct & Insubordination', 'ar' => 'سوء سلوك ومخالفة لوائح'],
            ],
        ];

        foreach ($settings as $type => $items) {
            foreach ($items as $item) {
                $setting = HrSetting::where('type', $type)
                    ->whereHas('translations', fn ($query) => $query
                        ->where('locale', 'en')
                        ->where('name', $item['en']))
                    ->first() ?? new HrSetting();

                $setting->fill(['type' => $type, 'status' => 'active']);
                $setting->translateOrNew('en')->name = $item['en'];
                $setting->translateOrNew('ar')->name = $item['ar'];
                $setting->save();
            }
        }
    }
}
