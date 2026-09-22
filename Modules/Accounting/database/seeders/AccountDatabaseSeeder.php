<?php

namespace Modules\Accounting\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Accounting\Enums\AccountType;
use Modules\Accounting\Models\Account;
use Modules\Accounting\Models\Entity;

class AccountDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $entity = Entity::first();
        if (!$entity) {
            return;
        }

        $entityId = $entity->id;

        // Don't duplicate if accounts already exist for this entity
        if (Account::where('entity_id', $entityId)->exists()) {
            return;
        }

        $chart = [
            // 1. ASSETS
            [
                'code' => '1',
                'name_ar' => 'الأصول',
                'name_en' => 'Assets',
                'type' => AccountType::ASSET,
                'is_group' => true,
                'children' => [
                    [
                        'code' => '11',
                        'name_ar' => 'الأصول المتداولة',
                        'name_en' => 'Current Assets',
                        'type' => AccountType::ASSET,
                        'is_group' => true,
                        'children' => [
                            [
                                'code' => '1101',
                                'name_ar' => 'النقدية وما في حكمها',
                                'name_en' => 'Cash and Cash Equivalents',
                                'type' => AccountType::ASSET,
                                'is_group' => true,
                                'children' => [
                                    [
                                        'code' => '110101',
                                        'name_ar' => 'الصندوق الرئيسي (الخزينة)',
                                        'name_en' => 'Main Cash Safe',
                                        'type' => AccountType::ASSET,
                                        'is_group' => false,
                                        'reconcile' => true,
                                    ],
                                    [
                                        'code' => '110102',
                                        'name_ar' => 'الحساب البنكي التجاري',
                                        'name_en' => 'Commercial Bank Account',
                                        'type' => AccountType::ASSET,
                                        'is_group' => false,
                                        'reconcile' => true,
                                    ],
                                ],
                            ],
                            [
                                'code' => '1102',
                                'name_ar' => 'العملاء والمدينون',
                                'name_en' => 'Accounts Receivable',
                                'type' => AccountType::ASSET,
                                'is_group' => false,
                                'reconcile' => true,
                            ],
                        ],
                    ],
                    [
                        'code' => '12',
                        'name_ar' => 'الأصول غير المتداولة (الثابتة)',
                        'name_en' => 'Non-Current Assets',
                        'type' => AccountType::ASSET,
                        'is_group' => true,
                        'children' => [
                            [
                                'code' => '1201',
                                'name_ar' => 'الأثاث والتجهيزات',
                                'name_en' => 'Furniture and Fixtures',
                                'type' => AccountType::ASSET,
                                'is_group' => false,
                            ],
                            [
                                'code' => '1202',
                                'name_ar' => 'الأجهزة والمعدات الإلكترونية',
                                'name_en' => 'Electronic Devices and Equipment',
                                'type' => AccountType::ASSET,
                                'is_group' => false,
                            ],
                        ],
                    ],
                ],
            ],

            // 2. LIABILITIES
            [
                'code' => '2',
                'name_ar' => 'الالتزامات والخصوم',
                'name_en' => 'Liabilities',
                'type' => AccountType::LIABILITY,
                'is_group' => true,
                'children' => [
                    [
                        'code' => '21',
                        'name_ar' => 'الالتزامات المتداولة',
                        'name_en' => 'Current Liabilities',
                        'type' => AccountType::LIABILITY,
                        'is_group' => true,
                        'children' => [
                            [
                                'code' => '2101',
                                'name_ar' => 'الموردون والدائنون',
                                'name_en' => 'Accounts Payable (Vendors)',
                                'type' => AccountType::LIABILITY,
                                'is_group' => false,
                                'reconcile' => true,
                            ],
                            [
                                'code' => '2102',
                                'name_ar' => 'الرواتب والأجور المستحقة',
                                'name_en' => 'Accrued Salaries and Wages',
                                'type' => AccountType::LIABILITY,
                                'is_group' => false,
                            ],
                        ],
                    ],
                ],
            ],

            // 3. EQUITY
            [
                'code' => '3',
                'name_ar' => 'حقوق الملكية',
                'name_en' => 'Equity',
                'type' => AccountType::EQUITY,
                'is_group' => true,
                'children' => [
                    [
                        'code' => '31',
                        'name_ar' => 'رأس المال المدفوع',
                        'name_en' => 'Paid-in Capital',
                        'type' => AccountType::EQUITY,
                        'is_group' => false,
                    ],
                    [
                        'code' => '32',
                        'name_ar' => 'الأرباح المحتجزة / المدورة',
                        'name_en' => 'Retained Earnings',
                        'type' => AccountType::EQUITY,
                        'is_group' => false,
                    ],
                ],
            ],

            // 4. INCOME
            [
                'code' => '4',
                'name_ar' => 'الإيرادات',
                'name_en' => 'Revenue / Income',
                'type' => AccountType::INCOME,
                'is_group' => true,
                'children' => [
                    [
                        'code' => '41',
                        'name_ar' => 'إيرادات اشتراكات الحضانة',
                        'name_en' => 'Nursery Tuition & Subscription Fees',
                        'type' => AccountType::INCOME,
                        'is_group' => false,
                    ],
                    [
                        'code' => '42',
                        'name_ar' => 'إيرادات الأنشطة والخدمات الإضافية',
                        'name_en' => 'Additional Services & Activities Revenue',
                        'type' => AccountType::INCOME,
                        'is_group' => false,
                    ],
                ],
            ],

            // 5. EXPENSES
            [
                'code' => '5',
                'name_ar' => 'المصروفات',
                'name_en' => 'Expenses',
                'type' => AccountType::EXPENSE,
                'is_group' => true,
                'children' => [
                    [
                        'code' => '51',
                        'name_ar' => 'المصروفات التشغيلية والإدارية',
                        'name_en' => 'Operating and Administrative Expenses',
                        'type' => AccountType::EXPENSE,
                        'is_group' => true,
                        'children' => [
                            [
                                'code' => '5101',
                                'name_ar' => 'رواتب وأجور المعلمات والموظفين',
                                'name_en' => 'Staff Salaries and Wages',
                                'type' => AccountType::EXPENSE,
                                'is_group' => false,
                            ],
                            [
                                'code' => '5102',
                                'name_ar' => 'إيجار مبنى الحضانة',
                                'name_en' => 'Premises Rent Expense',
                                'type' => AccountType::EXPENSE,
                                'is_group' => false,
                            ],
                            [
                                'code' => '5103',
                                'name_ar' => 'مستلزمات وأدوات تعليمية وترفيهية',
                                'name_en' => 'Educational & Recreational Supplies',
                                'type' => AccountType::EXPENSE,
                                'is_group' => false,
                            ],
                            [
                                'code' => '5104',
                                'name_ar' => 'مصروفات الكهرباء والماء والمرافق',
                                'name_en' => 'Electricity, Water and Utilities',
                                'type' => AccountType::EXPENSE,
                                'is_group' => false,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $createRecursive = function ($node, $parentId = null, $level = 1) use (&$createRecursive, $entityId) {
            $isGroup = $node['is_group'] ?? false;
            $type = $node['type'];

            $account = Account::create([
                'entity_id'      => $entityId,
                'parent_id'      => $parentId,
                'code'           => $node['code'],
                'account_type'   => $type->value,
                'normal_balance' => $type->normalBalance(),
                'level'          => $level,
                'is_group'       => $isGroup,
                'allow_posting'  => !$isGroup,
                'reconcile'      => $node['reconcile'] ?? false,
                'is_active'      => true,
                'is_deprecated'  => false,
                'sort_order'     => (int) $node['code'],
            ]);

            $account->translateOrNew('ar')->name = $node['name_ar'];
            $account->translateOrNew('en')->name = $node['name_en'];
            $account->save();

            if (!empty($node['children'])) {
                foreach ($node['children'] as $child) {
                    $createRecursive($child, $account->id, $level + 1);
                }
            }
        };

        foreach ($chart as $root) {
            $createRecursive($root, null, 1);
        }
    }
}
