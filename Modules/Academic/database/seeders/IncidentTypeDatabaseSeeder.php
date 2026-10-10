<?php

namespace Modules\Academic\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Settings\Enums\GeneralSettingType;
use Modules\Settings\Models\GeneralSetting;

class IncidentTypeDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $incidentTypes = [
            [
                'ar' => 'سقوط أو تعثر',
                'en' => 'Fall or Trip',
            ],
            [
                'ar' => 'كدمات ورضوض',
                'en' => 'Bruises and Contusions',
            ],
            [
                'ar' => 'جروح وخدوش بسيطة',
                'en' => 'Minor Cuts and Scratches',
            ],
            [
                'ar' => 'نزيف أنف (رعاف)',
                'en' => 'Nosebleed (Epistaxis)',
            ],
            [
                'ar' => 'حروق طفيفة',
                'en' => 'Minor Burns',
            ],
            [
                'ar' => 'حساسية مفاجئة',
                'en' => 'Allergic Reaction',
            ],
            [
                'ar' => 'عارض صحي مفاجئ / إغماء',
                'en' => 'Sudden Illness / Fainting',
            ],
            [
                'ar' => 'أخرى',
                'en' => 'Other',
            ],
        ];

        foreach ($incidentTypes as $type) {
            $existing = GeneralSetting::ofType(GeneralSettingType::INCIDENT_TYPE)
                ->whereTranslation('name', $type['ar'])
                ->first();

            if (! $existing) {
                GeneralSetting::create([
                    'type' => GeneralSettingType::INCIDENT_TYPE,
                    'ar' => ['name' => $type['ar']],
                    'en' => ['name' => $type['en']],
                ]);
            }
        }
    }
}
