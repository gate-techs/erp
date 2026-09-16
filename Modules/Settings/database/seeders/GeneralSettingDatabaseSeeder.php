<?php

namespace Modules\Settings\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Settings\Enums\GeneralSettingType;
use Modules\Settings\Models\GeneralSetting;

class GeneralSettingDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds for all general setting types except OTHER.
     */
    public function run(): void
    {
        // 1. Governorates of Kuwait (محافظات دولة الكويت الست)
        $governorates = [
            [
                'code' => 'KWT-CAP',
                'ar' => 'محافظة العاصمة',
                'en' => 'Al Asimah (Capital)',
                'cities' => [
                    ['code' => 'SHARQ', 'ar' => 'شرق', 'en' => 'Sharq'],
                    ['code' => 'DASMAN', 'ar' => 'دسمان', 'en' => 'Dasman'],
                    ['code' => 'SALHIYA', 'ar' => 'الصالحية', 'en' => 'Al Salhiya'],
                    ['code' => 'MIRQAB', 'ar' => 'المرقاب', 'en' => 'Al Mirqab'],
                    ['code' => 'BNEID_QAR', 'ar' => 'بنيد القار', 'en' => 'Bneid Al Qar'],
                    ['code' => 'KAIFAN', 'ar' => 'كيفان', 'en' => 'Kaifan'],
                    ['code' => 'YARMOUK', 'ar' => 'اليرموك', 'en' => 'Yarmouk'],
                    ['code' => 'SHAMIYA', 'ar' => 'الشامية', 'en' => 'Shamiya'],
                    ['code' => 'DASMA', 'ar' => 'الدسمة', 'en' => 'Dasma'],
                    ['code' => 'RAWDA', 'ar' => 'الروضة', 'en' => 'Rawda'],
                    ['code' => 'ADAILIYA', 'ar' => 'العديلية', 'en' => 'Adailiya'],
                    ['code' => 'KHALDIYA', 'ar' => 'الخالدية', 'en' => 'Khaldiya'],
                    ['code' => 'QURTUBA', 'ar' => 'قرطبة', 'en' => 'Qurtuba'],
                    ['code' => 'SURRA', 'ar' => 'السرة', 'en' => 'Surra'],
                ],
            ],
            [
                'code' => 'KWT-HAW',
                'ar' => 'محافظة حولي',
                'en' => 'Hawalli',
                'cities' => [
                    ['code' => 'SALMIYA', 'ar' => 'السالمية', 'en' => 'Salmiya'],
                    ['code' => 'HAWALLI', 'ar' => 'حولي', 'en' => 'Hawalli'],
                    ['code' => 'RUMAITHIYA', 'ar' => 'الرميثية', 'en' => 'Rumaithiya'],
                    ['code' => 'JABRIYA', 'ar' => 'الجابرية', 'en' => 'Jabriya'],
                    ['code' => 'SALWA', 'ar' => 'سلوى', 'en' => 'Salwa'],
                    ['code' => 'BAYAN', 'ar' => 'بيان', 'en' => 'Bayan'],
                    ['code' => 'MISHREF', 'ar' => 'مشرف', 'en' => 'Mishref'],
                    ['code' => 'SHAAB', 'ar' => 'الشعب', 'en' => 'Shaab'],
                    ['code' => 'SALAM', 'ar' => 'السلام', 'en' => 'Al Salam'],
                    ['code' => 'HATEEN', 'ar' => 'حطين', 'en' => 'Hateen'],
                    ['code' => 'SHUHADA', 'ar' => 'الشهداء', 'en' => 'Al Shuhada'],
                    ['code' => 'ZAHRA', 'ar' => 'الزهراء', 'en' => 'Al Zahra'],
                    ['code' => 'SIDDEEQ', 'ar' => 'الصديق', 'en' => 'Al Siddeeq'],
                ],
            ],
            [
                'code' => 'KWT-FAR',
                'ar' => 'محافظة الفروانية',
                'en' => 'Al Farwaniyah',
                'cities' => [
                    ['code' => 'FARWANIYAH', 'ar' => 'الفروانية', 'en' => 'Al Farwaniyah'],
                    ['code' => 'KHAITAN', 'ar' => 'خيطان', 'en' => 'Khaitan'],
                    ['code' => 'ANDALUS', 'ar' => 'الأندلس', 'en' => 'Al Andalus'],
                    ['code' => 'ISHBILIYA', 'ar' => 'إشبيلية', 'en' => 'Ishbiliya'],
                    ['code' => 'JLEEB', 'ar' => 'جليب الشيوخ', 'en' => 'Jleeb Al-Shuyoukh'],
                    ['code' => 'SABAH_NASSER', 'ar' => 'صباح الناصر', 'en' => 'Sabah Al-Nasser'],
                    ['code' => 'OMARIYA', 'ar' => 'العمرية', 'en' => 'Al Omariya'],
                    ['code' => 'RABIYA', 'ar' => 'الرابية', 'en' => 'Al Rabiya'],
                    ['code' => 'ARDIYA', 'ar' => 'العارضية', 'en' => 'Al Ardiya'],
                    ['code' => 'FIRDOUS', 'ar' => 'الفردوس', 'en' => 'Al Firdous'],
                    ['code' => 'ABDULLAH_MUBARAK', 'ar' => 'عبدالله المبارك', 'en' => 'Abdullah Al Mubarak'],
                ],
            ],
            [
                'code' => 'KWT-AHM',
                'ar' => 'محافظة الأحمدي',
                'en' => 'Al Ahmadi',
                'cities' => [
                    ['code' => 'FAHAHEEL', 'ar' => 'الفحيحيل', 'en' => 'Fahaheel'],
                    ['code' => 'AHMADI', 'ar' => 'الأحمدي', 'en' => 'Al Ahmadi'],
                    ['code' => 'MANGAF', 'ar' => 'المنقف', 'en' => 'Mangaf'],
                    ['code' => 'MAHBOULA', 'ar' => 'المهبولة', 'en' => 'Mahboula'],
                    ['code' => 'ABU_HALIFA', 'ar' => 'أبو حليفة', 'en' => 'Abu Halifa'],
                    ['code' => 'SABAHIYA', 'ar' => 'الصباحية', 'en' => 'Al Sabahiya'],
                    ['code' => 'RIQQAH', 'ar' => 'الرقة', 'en' => 'Al Riqqah'],
                    ['code' => 'SABAH_AHMAD', 'ar' => 'مدينة صباح الأحمد', 'en' => 'Sabah Al Ahmad City'],
                    ['code' => 'KHIRAN', 'ar' => 'الخيران', 'en' => 'Al Khiran'],
                    ['code' => 'WAFRA', 'ar' => 'الوفرة', 'en' => 'Al Wafra'],
                    ['code' => 'EGAILA', 'ar' => 'العقيلة', 'en' => 'Egaila'],
                ],
            ],
            [
                'code' => 'KWT-MUB',
                'ar' => 'محافظة مبارك الكبير',
                'en' => 'Mubarak Al-Kabeer',
                'cities' => [
                    ['code' => 'MUBARAK_KABEER', 'ar' => 'مبارك الكبير', 'en' => 'Mubarak Al-Kabeer'],
                    ['code' => 'ADAN', 'ar' => 'العدان', 'en' => 'Al Adan'],
                    ['code' => 'QUSOOR', 'ar' => 'القصور', 'en' => 'Al Qusoor'],
                    ['code' => 'QURAIN', 'ar' => 'القرين', 'en' => 'Al Qurain'],
                    ['code' => 'SABAH_SALEM', 'ar' => 'صباح السالم', 'en' => 'Sabah Al Salem'],
                    ['code' => 'MESSILA', 'ar' => 'المسيلة', 'en' => 'Al Messila'],
                    ['code' => 'ABU_HASANIYA', 'ar' => 'أبو الحصانية', 'en' => 'Abu Al Hasaniya'],
                    ['code' => 'FNAITEES', 'ar' => 'الفنيطيس', 'en' => 'Fnaitees'],
                ],
            ],
            [
                'code' => 'KWT-JAH',
                'ar' => 'محافظة الجهراء',
                'en' => 'Al Jahra',
                'cities' => [
                    ['code' => 'JAHRA', 'ar' => 'الجهراء', 'en' => 'Al Jahra'],
                    ['code' => 'WAHA', 'ar' => 'الواحة', 'en' => 'Al Waha'],
                    ['code' => 'OYOUN', 'ar' => 'العيون', 'en' => 'Al Oyoun'],
                    ['code' => 'QASR', 'ar' => 'القصر', 'en' => 'Al Qasr'],
                    ['code' => 'NASEEM', 'ar' => 'النسيم', 'en' => 'Al Naseem'],
                    ['code' => 'TAIMAA', 'ar' => 'تيماء', 'en' => 'Taimaa'],
                    ['code' => 'SULAIBIYA', 'ar' => 'الصليبية', 'en' => 'Al Sulaibiya'],
                    ['code' => 'SAAD_ABDULLAH', 'ar' => 'مدينة سعد العبدالله', 'en' => 'Saad Al Abdullah City'],
                    ['code' => 'MUTLAA', 'ar' => 'المطلاع', 'en' => 'Al Mutlaa'],
                    ['code' => 'KABAD', 'ar' => 'كبد', 'en' => 'Kabad'],
                    ['code' => 'ABDALI', 'ar' => 'العبدلي', 'en' => 'Al Abdali'],
                ],
            ],
        ];

        foreach ($governorates as $govData) {
            $gov = GeneralSetting::create([
                'type' => GeneralSettingType::GOVERNMENT,
                'value' => $govData['code'],
                'ar' => ['name' => $govData['ar']],
                'en' => ['name' => $govData['en']],
            ]);

            // Seed 2. Cities for this Governorate (parent_id = $gov->id)
            foreach ($govData['cities'] as $cityData) {
                GeneralSetting::create([
                    'type' => GeneralSettingType::CITY,
                    'parent_id' => $gov->id,
                    'value' => $cityData['code'],
                    'ar' => ['name' => $cityData['ar']],
                    'en' => ['name' => $cityData['en']],
                ]);
            }
        }

        // 3. Nationalities (الجنسيات)
        $nationalities = [
            ['code' => 'KWT', 'ar' => 'كويتي', 'en' => 'Kuwaiti'],
            ['code' => 'SAU', 'ar' => 'سعودي', 'en' => 'Saudi'],
            ['code' => 'ARE', 'ar' => 'إماراتي', 'en' => 'Emirati'],
            ['code' => 'QAT', 'ar' => 'قطري', 'en' => 'Qatari'],
            ['code' => 'BHR', 'ar' => 'بحريني', 'en' => 'Bahraini'],
            ['code' => 'OMN', 'ar' => 'عماني', 'en' => 'Omani'],
            ['code' => 'EGY', 'ar' => 'مصري', 'en' => 'Egyptian'],
            ['code' => 'JOR', 'ar' => 'أردني', 'en' => 'Jordanian'],
            ['code' => 'LBN', 'ar' => 'لبناني', 'en' => 'Lebanese'],
            ['code' => 'SYR', 'ar' => 'سوري', 'en' => 'Syrian'],
            ['code' => 'PSE', 'ar' => 'فلسطيني', 'en' => 'Palestinian'],
            ['code' => 'IRQ', 'ar' => 'عراقي', 'en' => 'Iraqi'],
            ['code' => 'YEM', 'ar' => 'يمني', 'en' => 'Yemeni'],
            ['code' => 'TUN', 'ar' => 'تونسي', 'en' => 'Tunisian'],
            ['code' => 'MAR', 'ar' => 'مغربي', 'en' => 'Moroccan'],
            ['code' => 'SDN', 'ar' => 'سوداني', 'en' => 'Sudanese'],
            ['code' => 'IND', 'ar' => 'هندي', 'en' => 'Indian'],
            ['code' => 'PAK', 'ar' => 'باكستاني', 'en' => 'Pakistani'],
            ['code' => 'BGD', 'ar' => 'بنغلاديشي', 'en' => 'Bangladeshi'],
            ['code' => 'PHL', 'ar' => 'فلبيني', 'en' => 'Filipino'],
            ['code' => 'LKA', 'ar' => 'سريلانكي', 'en' => 'Sri Lankan'],
            ['code' => 'NPL', 'ar' => 'نيبالي', 'en' => 'Nepali'],
            ['code' => 'GBR', 'ar' => 'بريطاني', 'en' => 'British'],
            ['code' => 'USA', 'ar' => 'أمريكي', 'en' => 'American'],
            ['code' => 'CAN', 'ar' => 'كندي', 'en' => 'Canadian'],
        ];

        foreach ($nationalities as $nat) {
            GeneralSetting::create([
                'type' => GeneralSettingType::NATIONALITY,
                'value' => $nat['code'],
                'ar' => ['name' => $nat['ar']],
                'en' => ['name' => $nat['en']],
            ]);
        }

        // 4. Religions (الديانات)
        $religions = [
            ['code' => 'ISLAM', 'ar' => 'مسلم', 'en' => 'Muslim'],
            ['code' => 'CHRISTIAN', 'ar' => 'مسيحي', 'en' => 'Christian'],
            ['code' => 'OTHER', 'ar' => 'ديانة أخرى', 'en' => 'Other Religion'],
        ];

        foreach ($religions as $rel) {
            GeneralSetting::create([
                'type' => GeneralSettingType::RELIGION,
                'value' => $rel['code'],
                'ar' => ['name' => $rel['ar']],
                'en' => ['name' => $rel['en']],
            ]);
        }

        // 5. Blood Types (فصائل الدم)
        $bloodTypes = [
            ['code' => 'A+', 'ar' => 'A إيجابي (A+)', 'en' => 'A Positive (A+)'],
            ['code' => 'A-', 'ar' => 'A سلبي (A-)', 'en' => 'A Negative (A-)'],
            ['code' => 'B+', 'ar' => 'B إيجابي (B+)', 'en' => 'B Positive (B+)'],
            ['code' => 'B-', 'ar' => 'B سلبي (B-)', 'en' => 'B Negative (B-)'],
            ['code' => 'O+', 'ar' => 'O إيجابي (O+)', 'en' => 'O Positive (O+)'],
            ['code' => 'O-', 'ar' => 'O سلبي (O-)', 'en' => 'O Negative (O-)'],
            ['code' => 'AB+', 'ar' => 'AB إيجابي (AB+)', 'en' => 'AB Positive (AB+)'],
            ['code' => 'AB-', 'ar' => 'AB سلبي (AB-)', 'en' => 'AB Negative (AB-)'],
        ];

        foreach ($bloodTypes as $bt) {
            GeneralSetting::create([
                'type' => GeneralSettingType::BLOOD_TYPE,
                'value' => $bt['code'],
                'ar' => ['name' => $bt['ar']],
                'en' => ['name' => $bt['en']],
            ]);
        }

        // 6. Genders (النوع / الجنس)
        $genders = [
            ['code' => 'MALE', 'ar' => 'ذكر', 'en' => 'Male'],
            ['code' => 'FEMALE', 'ar' => 'أنثى', 'en' => 'Female'],
        ];

        foreach ($genders as $g) {
            GeneralSetting::create([
                'type' => GeneralSettingType::GENDER,
                'value' => $g['code'],
                'ar' => ['name' => $g['ar']],
                'en' => ['name' => $g['en']],
            ]);
        }

        // 7. Education Levels (المستوى التعليمي)
        $educationLevels = [
            ['code' => 'PRESCHOOL', 'ar' => 'تمهيدي / روضة', 'en' => 'Preschool / Kindergarten'],
            ['code' => 'PRIMARY', 'ar' => 'المرحلة الابتدائية', 'en' => 'Primary School'],
            ['code' => 'INTERMEDIATE', 'ar' => 'المرحلة المتوسطة', 'en' => 'Intermediate School'],
            ['code' => 'SECONDARY', 'ar' => 'المرحلة الثانوية', 'en' => 'Secondary / High School'],
            ['code' => 'DIPLOMA', 'ar' => 'دبلوم', 'en' => 'Diploma'],
            ['code' => 'BACHELOR', 'ar' => 'بكالوريوس / ليسانس', 'en' => "Bachelor's Degree"],
            ['code' => 'MASTER', 'ar' => 'ماجستير', 'en' => "Master's Degree"],
            ['code' => 'DOCTORATE', 'ar' => 'دكتوراه', 'en' => 'Doctorate / PhD'],
        ];

        foreach ($educationLevels as $edu) {
            GeneralSetting::create([
                'type' => GeneralSettingType::EDUCATION_LEVEL,
                'value' => $edu['code'],
                'ar' => ['name' => $edu['ar']],
                'en' => ['name' => $edu['en']],
            ]);
        }

        // 8. Relationships (صلة القرابة)
        $relationships = [
            ['code' => 'FATHER', 'ar' => 'أب', 'en' => 'Father'],
            ['code' => 'MOTHER', 'ar' => 'أم', 'en' => 'Mother'],
            ['code' => 'BROTHER', 'ar' => 'أخ', 'en' => 'Brother'],
            ['code' => 'SISTER', 'ar' => 'أخت', 'en' => 'Sister'],
            ['code' => 'GRANDFATHER', 'ar' => 'جد', 'en' => 'Grandfather'],
            ['code' => 'GRANDMOTHER', 'ar' => 'جدة', 'en' => 'Grandmother'],
            ['code' => 'UNCLE_PATERNAL', 'ar' => 'عم', 'en' => 'Uncle (Paternal)'],
            ['code' => 'AUNT_PATERNAL', 'ar' => 'عمة', 'en' => 'Aunt (Paternal)'],
            ['code' => 'UNCLE_MATERNAL', 'ar' => 'خال', 'en' => 'Uncle (Maternal)'],
            ['code' => 'AUNT_MATERNAL', 'ar' => 'خالة', 'en' => 'Aunt (Maternal)'],
            ['code' => 'GUARDIAN', 'ar' => 'وصي قانوني', 'en' => 'Legal Guardian'],
        ];

        foreach ($relationships as $rel) {
            GeneralSetting::create([
                'type' => GeneralSettingType::RELATIONSHIP,
                'value' => $rel['code'],
                'ar' => ['name' => $rel['ar']],
                'en' => ['name' => $rel['en']],
            ]);
        }

        // 9. Marital Statuses (الحالة الاجتماعية)
        $maritalStatuses = [
            ['code' => 'SINGLE', 'ar' => 'أعزب / عزباء', 'en' => 'Single'],
            ['code' => 'MARRIED', 'ar' => 'متزوج / متزوجة', 'en' => 'Married'],
            ['code' => 'DIVORCED', 'ar' => 'مطلق / مطلقة', 'en' => 'Divorced'],
            ['code' => 'WIDOWED', 'ar' => 'أرمل / أرملة', 'en' => 'Widowed'],
        ];

        foreach ($maritalStatuses as $ms) {
            GeneralSetting::create([
                'type' => GeneralSettingType::MARITAL_STATUS,
                'value' => $ms['code'],
                'ar' => ['name' => $ms['ar']],
                'en' => ['name' => $ms['en']],
            ]);
        }
    }
}
