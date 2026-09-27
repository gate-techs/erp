<?php

namespace Modules\Accounting\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Modules\Accounting\Enums\AccountType;
use Modules\Accounting\Models\Account;
use Modules\Accounting\Models\Entity;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ChartOfAccountsTemplateService
{
    /**
     * Get the standard hierarchical Chart of Accounts definition.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getStandardTemplate(): array
    {
        return [
            // ==========================================
            // 1. ASSETS (الأصول)
            // ==========================================
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
                                    [
                                        'code' => '110103',
                                        'name_ar' => 'عهدة نقدية للمصروفات النثرية',
                                        'name_en' => 'Petty Cash Custody',
                                        'type' => AccountType::ASSET,
                                        'is_group' => false,
                                        'reconcile' => true,
                                    ],
                                ],
                            ],
                            [
                                'code' => '1102',
                                'name_ar' => 'العملاء والمدينون (أولياء الأمور)',
                                'name_en' => 'Accounts Receivable (Parents)',
                                'type' => AccountType::ASSET,
                                'is_group' => false,
                                'reconcile' => true,
                            ],
                            [
                                'code' => '1103',
                                'name_ar' => 'مصروفات مدفوعة مقدماً',
                                'name_en' => 'Prepaid Expenses',
                                'type' => AccountType::ASSET,
                                'is_group' => false,
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
                                'name_ar' => 'الأثاث والتجهيزات المكتبية',
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
                            [
                                'code' => '1203',
                                'name_ar' => 'تجهيزات ألعاب وساحات الحضانة',
                                'name_en' => 'Playground and Activity Equipment',
                                'type' => AccountType::ASSET,
                                'is_group' => false,
                            ],
                            [
                                'code' => '1204',
                                'name_ar' => 'وسائل النقل والحافلات',
                                'name_en' => 'Vehicles and School Buses',
                                'type' => AccountType::ASSET,
                                'is_group' => false,
                            ],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // 2. LIABILITIES (الالتزامات والخصوم)
            // ==========================================
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
                                'name_ar' => 'الموردون والدائنون التجاريون',
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
                            [
                                'code' => '2103',
                                'name_ar' => 'اشتراكات ورسوم دراسية محصلة مقدماً',
                                'name_en' => 'Unearned Tuition and Advance Fees',
                                'type' => AccountType::LIABILITY,
                                'is_group' => false,
                            ],
                            [
                                'code' => '2104',
                                'name_ar' => 'تأمينات مستردة لأولياء الأمور',
                                'name_en' => 'Refundable Security Deposits',
                                'type' => AccountType::LIABILITY,
                                'is_group' => false,
                            ],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // 3. EQUITY (حقوق الملكية)
            // ==========================================
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
                    [
                        'code' => '33',
                        'name_ar' => 'جاري المالك / الشركاء',
                        'name_en' => 'Owner / Partners Current Account',
                        'type' => AccountType::EQUITY,
                        'is_group' => false,
                    ],
                ],
            ],

            // ==========================================
            // 4. INCOME (الإيرادات)
            // ==========================================
            [
                'code' => '4',
                'name_ar' => 'الإيرادات',
                'name_en' => 'Revenue / Income',
                'type' => AccountType::INCOME,
                'is_group' => true,
                'children' => [
                    [
                        'code' => '41',
                        'name_ar' => 'إيرادات اشتراكات الحضانة والرسوم الدراسية',
                        'name_en' => 'Nursery Tuition & Subscription Fees',
                        'type' => AccountType::INCOME,
                        'is_group' => false,
                    ],
                    [
                        'code' => '42',
                        'name_ar' => 'إيرادات خدمات النقل والتوصيل (الباص)',
                        'name_en' => 'Transportation & Bus Services Revenue',
                        'type' => AccountType::INCOME,
                        'is_group' => false,
                    ],
                    [
                        'code' => '43',
                        'name_ar' => 'إيرادات الوجبات والتغذية',
                        'name_en' => 'Meals & Catering Revenue',
                        'type' => AccountType::INCOME,
                        'is_group' => false,
                    ],
                    [
                        'code' => '44',
                        'name_ar' => 'إيرادات الأنشطة والفعاليات الإضافية',
                        'name_en' => 'Additional Activities & Events Revenue',
                        'type' => AccountType::INCOME,
                        'is_group' => false,
                    ],
                    [
                        'code' => '45',
                        'name_ar' => 'إيرادات متنوعة وأخرى',
                        'name_en' => 'Other / Miscellaneous Income',
                        'type' => AccountType::INCOME,
                        'is_group' => false,
                    ],
                ],
            ],

            // ==========================================
            // 5. EXPENSES (المصروفات)
            // ==========================================
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
                                'name_ar' => 'إيجار مبنى الحضانة والمقرات',
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
                                'name_ar' => 'مصروفات الكهرباء والماء والمرافق العامة',
                                'name_en' => 'Electricity, Water and Utilities',
                                'type' => AccountType::EXPENSE,
                                'is_group' => false,
                            ],
                            [
                                'code' => '5105',
                                'name_ar' => 'مصروفات الوجبات والتغذية للأطفال',
                                'name_en' => 'Kids Meals & Nutrition Expenses',
                                'type' => AccountType::EXPENSE,
                                'is_group' => false,
                            ],
                            [
                                'code' => '5106',
                                'name_ar' => 'مصروفات صيانة وتشغيل الحافلات والسيارات',
                                'name_en' => 'Bus & Vehicle Maintenance and Fuel',
                                'type' => AccountType::EXPENSE,
                                'is_group' => false,
                            ],
                            [
                                'code' => '5107',
                                'name_ar' => 'نظافة ومستلزمات التعقيم والصحة العامة',
                                'name_en' => 'Cleaning, Sanitization & Hygiene Supplies',
                                'type' => AccountType::EXPENSE,
                                'is_group' => false,
                            ],
                            [
                                'code' => '5108',
                                'name_ar' => 'مصروفات دعاية وتسويق وعلاقات عامة',
                                'name_en' => 'Advertising and Marketing Expenses',
                                'type' => AccountType::EXPENSE,
                                'is_group' => false,
                            ],
                            [
                                'code' => '5109',
                                'name_ar' => 'مصروفات بنكية ورسوم دفع إلكتروني',
                                'name_en' => 'Bank Charges and Payment Gateway Fees',
                                'type' => AccountType::EXPENSE,
                                'is_group' => false,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Get default path for Astro chart of accounts excel file.
     */
    public function getDefaultExcelPath(): string
    {
        $base = base_path('file/دليل حسابات استرو النهائي.xlsx');
        if (file_exists($base)) {
            return $base;
        }

        $alt = 'D:\\xamp8.2\\htdocs\\gate\\erp\\file\\دليل حسابات استرو النهائي.xlsx';
        if (file_exists($alt)) {
            return $alt;
        }

        return $base;
    }

    /**
     * Parse and structure accounts from the Excel file following project conventions.
     *
     * @param string|null $filePath
     * @return array<int, array<string, mixed>>
     * @throws Exception
     */
    public function loadAccountsFromExcel(?string $filePath = null): array
    {
        $path = $filePath ?: $this->getDefaultExcelPath();

        if (!file_exists($path)) {
            throw new Exception("Chart of accounts Excel file not found at: {$path}");
        }

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getSheet(0);
        $highestRow = $sheet->getHighestRow();

        $rawRows = [];
        for ($r = 2; $r <= $highestRow; $r++) {
            $kind = trim((string) $sheet->getCell('A' . $r)->getValue());
            $code = trim((string) $sheet->getCell('B' . $r)->getValue());
            $name = trim((string) $sheet->getCell('C' . $r)->getValue());

            if ($code === '' && $name === '') {
                continue;
            }

            if ($code === '') {
                continue;
            }

            $rawRows[] = [
                'row'  => $r,
                'kind' => $kind,
                'code' => $code,
                'name' => $name,
            ];
        }

        if (empty($rawRows)) {
            throw new Exception("No account rows found in the Excel file.");
        }

        $codeMap = [];
        foreach ($rawRows as $item) {
            $codeMap[$item['code']] = $item;
        }

        $roots = ['1', '2', '3', '4', '5', '9'];

        $englishNames = [
            '1'      => 'Assets',
            '11'     => 'Current Assets',
            '1101'   => 'Cash and Cash Equivalents',
            '110101' => 'Cash in Hand',
            '110102' => 'Banks',
            '110103' => 'Petty Cash & Custodies',
            '1104'   => 'Accounts Receivable & Debtors',
            '110401' => 'Debtors',
            '12'     => 'Non-Current Assets (Fixed Assets)',
            '2'      => 'Liabilities',
            '21'     => 'Current Liabilities',
            '2101'   => 'Trade Creditors & Suppliers',
            '2102'   => 'Accrued Expenses & Provisions',
            '2103'   => 'Unearned Revenues',
            '22'     => 'Non-Current Liabilities',
            '3'      => 'Equity',
            '31'     => 'Capital',
            '3101'   => 'Company Capital',
            '32'     => 'Partners Accounts',
            '33'     => 'Retained Earnings',
            '34'     => 'Capital Reserves',
            '4'      => 'Revenues',
            '41'     => 'Operating Revenues',
            '5'      => 'Expenses',
            '51'     => 'Direct Operational Expenses',
            '52'     => 'Salaries & Employee Benefits',
            '53'     => 'Rent & Facility Expenses',
            '54'     => 'General & Administrative Expenses',
            '55'     => 'Bank Charges & Commission',
            '56'     => 'Bad Debts',
            '9'      => 'Clearing & Suspense Accounts',
        ];

        $parsed = [];
        foreach ($rawRows as $item) {
            $code = $item['code'];
            $nameAr = $item['name'];
            $kind = $item['kind'];

            $firstDigit = substr($code, 0, 1);
            $type = match ($firstDigit) {
                '1'     => AccountType::ASSET,
                '2'     => AccountType::LIABILITY,
                '3'     => AccountType::EQUITY,
                '4'     => AccountType::INCOME,
                '5'     => AccountType::EXPENSE,
                '9'     => AccountType::OFF_BALANCE,
                default => AccountType::ASSET,
            };

            $parentCode = null;
            if (!in_array($code, $roots, true)) {
                for ($len = strlen($code) - 1; $len >= 1; $len--) {
                    $prefix = substr($code, 0, $len);
                    if (isset($codeMap[$prefix])) {
                        $parentCode = $prefix;
                        break;
                    }
                }
            }

            $isGroup = ($kind !== 'فرعي');
            $allowPosting = !$isGroup;

            $reconcile = false;
            if (!$isGroup && (
                str_starts_with($code, '1101') ||
                str_contains($nameAr, 'بنك') ||
                str_contains($nameAr, 'صندوق') ||
                str_contains($nameAr, 'فيزا') ||
                str_contains($nameAr, 'بوابة') ||
                str_contains($nameAr, 'يوباي') ||
                str_contains($nameAr, 'ديمة')
            )) {
                $reconcile = true;
            }

            $nameEn = $englishNames[$code] ?? $nameAr;

            $parsed[] = [
                'code'           => $code,
                'name_ar'        => $nameAr,
                'name_en'        => $nameEn,
                'parent_code'    => $parentCode,
                'type'           => $type,
                'normal_balance' => $type->normalBalance(),
                'is_group'       => $isGroup,
                'allow_posting'  => $allowPosting,
                'reconcile'      => $reconcile,
            ];
        }

        // Calculate levels dynamically based on hierarchy
        $levelMap = [];
        $calcLevel = function (string $code) use (&$calcLevel, &$levelMap, $parsed): int {
            if (isset($levelMap[$code])) {
                return $levelMap[$code];
            }
            $parent = null;
            foreach ($parsed as $p) {
                if ($p['code'] === $code) {
                    $parent = $p['parent_code'];
                    break;
                }
            }
            if (!$parent) {
                $levelMap[$code] = 1;
                return 1;
            }
            $levelMap[$code] = $calcLevel($parent) + 1;
            return $levelMap[$code];
        };

        foreach ($parsed as &$p) {
            $p['level'] = $calcLevel($p['code']);
        }
        unset($p);

        // Sort accounts: lower levels first (parents first), then by code
        usort($parsed, function ($a, $b) {
            if ($a['level'] === $b['level']) {
                return strcmp($a['code'], $b['code']);
            }
            return $a['level'] <=> $b['level'];
        });

        return $parsed;
    }

    /**
     * Synchronize parsed accounts into the database for a specific entity.
     *
     * @param Entity|int $entity
     * @param array<int, array<string, mixed>> $parsedAccounts
     * @param int|null $userId
     * @return array{created: int, updated: int, total: int}
     */
    public function syncAccountsForEntity(Entity|int $entity, array $parsedAccounts, ?int $userId = null): array
    {
        $entityId = $entity instanceof Entity ? $entity->id : (int) $entity;
        $created = 0;
        $updated = 0;

        DB::transaction(function () use ($parsedAccounts, $entityId, $userId, &$created, &$updated) {
            $existing = Account::where('entity_id', $entityId)->get()->keyBy('code');
            $codeToId = [];

            foreach ($existing as $c => $acc) {
                $codeToId[$c] = $acc->id;
            }

            foreach ($parsedAccounts as $item) {
                $parentId = $item['parent_code'] ? ($codeToId[$item['parent_code']] ?? null) : null;

                $data = [
                    'entity_id'      => $entityId,
                    'parent_id'      => $parentId,
                    'code'           => $item['code'],
                    'account_type'   => $item['type'] instanceof AccountType ? $item['type']->value : (string) $item['type'],
                    'normal_balance' => $item['normal_balance'],
                    'level'          => $item['level'],
                    'is_group'       => (bool) $item['is_group'],
                    'allow_posting'  => (bool) $item['allow_posting'],
                    'reconcile'      => (bool) $item['reconcile'],
                    'is_active'      => true,
                    'is_deprecated'  => false,
                    'sort_order'     => (int) substr($item['code'], 0, 8),
                    'created_by'     => $userId,
                ];

                if (isset($existing[$item['code']])) {
                    $acc = $existing[$item['code']];
                    $acc->update($data);
                    $updated++;
                } else {
                    $acc = Account::create($data);
                    $created++;
                }

                $transAr = $acc->translateOrNew('ar');
                $transAr->name = $item['name_ar'];

                $transEn = $acc->translateOrNew('en');
                $transEn->name = $item['name_en'];

                $acc->save();
                $codeToId[$item['code']] = $acc->id;
            }

            // Mark any previous accounts not in the final chart as deprecated
            $excelCodes = array_column($parsedAccounts, 'code');
            Account::where('entity_id', $entityId)
                ->whereNotIn('code', $excelCodes)
                ->update([
                    'is_deprecated' => true,
                    'is_active'     => false,
                ]);
        });

        return [
            'created' => $created,
            'updated' => $updated,
            'total'   => count($parsedAccounts),
        ];
    }

    /**
     * Generate or sync Chart of Accounts from Excel for an entity.
     *
     * @param Entity|int $entity
     * @param string|null $filePath
     * @param int|null $userId
     * @param bool $force
     * @return array{created: int, updated: int, total: int}
     */
    public function generateFromExcelForEntity(Entity|int $entity, ?string $filePath = null, ?int $userId = null, bool $force = true): array
    {
        $entityId = $entity instanceof Entity ? $entity->id : (int) $entity;

        if (!$force && Account::where('entity_id', $entityId)->exists()) {
            return ['created' => 0, 'updated' => 0, 'total' => 0];
        }

        $parsedAccounts = $this->loadAccountsFromExcel($filePath);
        return $this->syncAccountsForEntity($entityId, $parsedAccounts, $userId);
    }

    /**
     * Generate chart of accounts for a given entity.
     * Prefers the Excel file if present; falls back to built-in standard template.
     *
     * @param Entity|int $entity
     * @param int|null $userId
     * @param bool $forceSync
     * @param string|null $excelPath
     * @return int Count of accounts processed/created
     */
    public function generateTemplateForEntity(Entity|int $entity, ?int $userId = null, bool $forceSync = false, ?string $excelPath = null): int
    {
        $entityId = $entity instanceof Entity ? $entity->id : (int) $entity;

        if (!$forceSync && Account::where('entity_id', $entityId)->exists()) {
            return 0;
        }

        $path = $excelPath ?: $this->getDefaultExcelPath();
        if (file_exists($path)) {
            $result = $this->generateFromExcelForEntity($entityId, $path, $userId, true);
            return $result['total'];
        }

        $chart = $this->getStandardTemplate();
        $count = 0;

        DB::transaction(function () use ($chart, $entityId, $userId, &$count) {
            $createRecursive = function (array $node, ?int $parentId = null, int $level = 1) use (&$createRecursive, $entityId, $userId, &$count) {
                $isGroup = (bool) ($node['is_group'] ?? false);
                $type = $node['type'] instanceof AccountType ? $node['type'] : AccountType::from((string) $node['type']);

                $account = Account::create([
                    'entity_id'      => $entityId,
                    'parent_id'      => $parentId,
                    'code'           => (string) $node['code'],
                    'account_type'   => $type->value,
                    'normal_balance' => $type->normalBalance(),
                    'level'          => $level,
                    'is_group'       => $isGroup,
                    'allow_posting'  => !$isGroup,
                    'reconcile'      => (bool) ($node['reconcile'] ?? false),
                    'is_active'      => true,
                    'is_deprecated'  => false,
                    'sort_order'     => (int) ($node['sort_order'] ?? $node['code']),
                    'created_by'     => $userId,
                ]);

                $account->translateOrNew('ar')->name = $node['name_ar'];
                if (!empty($node['description_ar'])) {
                    $account->translateOrNew('ar')->description = $node['description_ar'];
                }

                $account->translateOrNew('en')->name = $node['name_en'] ?? $node['name_ar'];
                if (!empty($node['description_en'])) {
                    $account->translateOrNew('en')->description = $node['description_en'];
                }

                $account->save();
                $count++;

                if (!empty($node['children']) && is_array($node['children'])) {
                    foreach ($node['children'] as $child) {
                        $createRecursive($child, $account->id, $level + 1);
                    }
                }
            };

            foreach ($chart as $root) {
                $createRecursive($root, null, 1);
            }
        });

        return $count;
    }

    /**
     * Clone all accounts and their hierarchical structure from a source entity to a target entity.
     *
     * @param int $sourceEntityId
     * @param Entity|int $targetEntity
     * @param int|null $userId
     * @return int Count of accounts cloned
     */
    public function cloneChartFromEntity(int $sourceEntityId, Entity|int $targetEntity, ?int $userId = null): int
    {
        $targetEntityId = $targetEntity instanceof Entity ? $targetEntity->id : (int) $targetEntity;

        if ($sourceEntityId === $targetEntityId) {
            return 0;
        }

        // Skip if target already has accounts
        if (Account::where('entity_id', $targetEntityId)->exists()) {
            return 0;
        }

        $sourceAccounts = Account::with('translations')
            ->where('entity_id', $sourceEntityId)
            ->orderBy('level')
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get();

        if ($sourceAccounts->isEmpty()) {
            return 0;
        }

        $idMap = [];
        $clonedCount = 0;

        DB::transaction(function () use ($sourceAccounts, $targetEntityId, $userId, &$idMap, &$clonedCount) {
            foreach ($sourceAccounts as $source) {
                $newParentId = $source->parent_id && isset($idMap[$source->parent_id])
                    ? $idMap[$source->parent_id]
                    : null;

                $newAccount = Account::create([
                    'entity_id'      => $targetEntityId,
                    'parent_id'      => $newParentId,
                    'code'           => $source->code,
                    'account_type'   => $source->account_type instanceof AccountType ? $source->account_type->value : (string) $source->account_type,
                    'normal_balance' => $source->normal_balance,
                    'level'          => $source->level,
                    'is_group'       => (bool) $source->is_group,
                    'allow_posting'  => (bool) $source->allow_posting,
                    'reconcile'      => (bool) $source->reconcile,
                    'is_active'      => (bool) $source->is_active,
                    'is_deprecated'  => (bool) $source->is_deprecated,
                    'sort_order'     => (int) $source->sort_order,
                    'created_by'     => $userId,
                ]);

                foreach ($source->translations as $trans) {
                    $newTrans = $newAccount->translateOrNew($trans->locale);
                    $newTrans->name = $trans->name;
                    $newTrans->description = $trans->description;
                }
                $newAccount->save();

                $idMap[$source->id] = $newAccount->id;
                $clonedCount++;
            }
        });

        return $clonedCount;
    }
}
