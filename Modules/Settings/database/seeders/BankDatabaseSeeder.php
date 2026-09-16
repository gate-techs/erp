<?php

namespace Modules\Settings\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Settings\Models\Bank;

class BankDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds for Banks and Bank Branches.
     */
    public function run(): void
    {
        $banks = [
            [
                'code' => 'NBK',
                'swift_code' => 'NBKOKWKW',
                'status' => 'active',
                'ar' => [
                    'name' => 'بنك الكويت الوطني',
                    'address' => 'مدينة الكويت - شارع الشهداء - برج الراية',
                ],
                'en' => [
                    'name' => 'National Bank of Kuwait (NBK)',
                    'address' => 'Kuwait City - Al Shuhada Street - Al Raya Tower',
                ],
                'branches' => [
                    [
                        'code' => 'NBK-001',
                        'swift_code' => 'NBKOKWKW001',
                        'status' => 'active',
                        'ar' => ['name' => 'فرع العاصمة (الرئيسي)', 'address' => 'شرق - شارع عبدالله الأحمد'],
                        'en' => ['name' => 'Capital Main Branch', 'address' => 'Sharq - Abdullah Al Ahmad St'],
                    ],
                    [
                        'code' => 'NBK-002',
                        'swift_code' => 'NBKOKWKW002',
                        'status' => 'active',
                        'ar' => ['name' => 'فرع حولي', 'address' => 'حولي - شارع بيروت'],
                        'en' => ['name' => 'Hawally Branch', 'address' => 'Hawally - Beirut St'],
                    ],
                    [
                        'code' => 'NBK-003',
                        'swift_code' => 'NBKOKWKW003',
                        'status' => 'active',
                        'ar' => ['name' => 'فرع الفروانية', 'address' => 'الفروانية - شارع حبيب مناور'],
                        'en' => ['name' => 'Farwaniya Branch', 'address' => 'Farwaniya - Habib Munawer St'],
                    ],
                ],
            ],
            [
                'code' => 'KFH',
                'swift_code' => 'KFHOKWKW',
                'status' => 'active',
                'ar' => [
                    'name' => 'بيت التمويل الكويتي (بيتك)',
                    'address' => 'مدينة الكويت - شارع عبدالله المبارك - برج بيتك',
                ],
                'en' => [
                    'name' => 'Kuwait Finance House (KFH)',
                    'address' => 'Kuwait City - Abdullah Al Mubarak St - Baitak Tower',
                ],
                'branches' => [
                    [
                        'code' => 'KFH-001',
                        'swift_code' => 'KFHOKWKW001',
                        'status' => 'active',
                        'ar' => ['name' => 'فرع المرقاب (الرئيسي)', 'address' => 'المرقاب - شارع مبارك الكبير'],
                        'en' => ['name' => 'Al-Mirqab Main Branch', 'address' => 'Al-Mirqab - Mubarak Al Kabeer St'],
                    ],
                    [
                        'code' => 'KFH-002',
                        'swift_code' => 'KFHOKWKW002',
                        'status' => 'active',
                        'ar' => ['name' => 'فرع السالمية', 'address' => 'السالمية - شارع سالم المبارك'],
                        'en' => ['name' => 'Salmiya Branch', 'address' => 'Salmiya - Salem Al Mubarak St'],
                    ],
                    [
                        'code' => 'KFH-003',
                        'swift_code' => 'KFHOKWKW003',
                        'status' => 'active',
                        'ar' => ['name' => 'فرع الأحمدي', 'address' => 'الفحيحيل - مجمع الدبوس'],
                        'en' => ['name' => 'Ahmadi / Fahaheel Branch', 'address' => 'Fahaheel - Al Dabbous Complex'],
                    ],
                ],
            ],
            [
                'code' => 'GBK',
                'swift_code' => 'GULFBKKW',
                'status' => 'active',
                'ar' => [
                    'name' => 'بنك الخليج',
                    'address' => 'مدينة الكويت - مبارك الكبير',
                ],
                'en' => [
                    'name' => 'Gulf Bank',
                    'address' => 'Kuwait City - Mubarak Al Kabeer',
                ],
                'branches' => [
                    [
                        'code' => 'GBK-001',
                        'swift_code' => 'GULFBKKW001',
                        'status' => 'active',
                        'ar' => ['name' => 'فرع المباركية', 'address' => 'المباركية - شارع فهد السالم'],
                        'en' => ['name' => 'Mubarakiya Branch', 'address' => 'Mubarakiya - Fahad Al Salem St'],
                    ],
                    [
                        'code' => 'GBK-002',
                        'swift_code' => 'GULFBKKW002',
                        'status' => 'active',
                        'ar' => ['name' => 'فرع الجابرية', 'address' => 'الجابرية - الدائري الرابع'],
                        'en' => ['name' => 'Jabriya Branch', 'address' => 'Jabriya - 4th Ring Road'],
                    ],
                ],
            ],
            [
                'code' => 'BOUBYAN',
                'swift_code' => 'BOUBKKW',
                'status' => 'active',
                'ar' => [
                    'name' => 'بنك بوبيان',
                    'address' => 'مدينة الكويت - برج بوبيان',
                ],
                'en' => [
                    'name' => 'Boubyan Bank',
                    'address' => 'Kuwait City - Boubyan Tower',
                ],
                'branches' => [
                    [
                        'code' => 'BOUB-001',
                        'swift_code' => 'BOUBKKW001',
                        'status' => 'active',
                        'ar' => ['name' => 'فرع الشويخ', 'address' => 'الشويخ الصناعية - شارع البنوك'],
                        'en' => ['name' => 'Shuwaikh Branch', 'address' => 'Industrial Shuwaikh - Banks St'],
                    ],
                ],
            ],
            [
                'code' => 'WARBA',
                'swift_code' => 'WARBAKKW',
                'status' => 'active',
                'ar' => [
                    'name' => 'بنك وربة',
                    'address' => 'مدينة الكويت - برج الراية',
                ],
                'en' => [
                    'name' => 'Warba Bank',
                    'address' => 'Kuwait City - Al Raya Tower',
                ],
                'branches' => [
                    [
                        'code' => 'WARBA-001',
                        'swift_code' => 'WARBAKKW001',
                        'status' => 'active',
                        'ar' => ['name' => 'فرع القبلة', 'address' => 'القبلة - شارع أحمد الجابر'],
                        'en' => ['name' => 'Qibla Branch', 'address' => 'Qibla - Ahmed Al Jaber St'],
                    ],
                ],
            ],
        ];

        foreach ($banks as $bankData) {
            $branches = $bankData['branches'] ?? [];
            unset($bankData['branches']);

            // Find or create main bank
            $existingBank = Bank::mainBanks()->where('code', $bankData['code'])->first();
            if (!$existingBank) {
                $bank = Bank::create($bankData);
            } else {
                $bank = $existingBank;
                $bank->update($bankData);
            }

            // Create or update branches
            foreach ($branches as $branchData) {
                $branchData['parent_id'] = $bank->id;
                $existingBranch = Bank::bankBranches()->where('code', $branchData['code'])->first();
                if (!$existingBranch) {
                    Bank::create($branchData);
                } else {
                    $existingBranch->update($branchData);
                }
            }
        }
    }
}
