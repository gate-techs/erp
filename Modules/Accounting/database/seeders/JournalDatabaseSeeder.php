<?php

namespace Modules\Accounting\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Accounting\Enums\AccountType;
use Modules\Accounting\Enums\JournalType;
use Modules\Accounting\Models\Account;
use Modules\Accounting\Models\Entity;
use Modules\Accounting\Models\Journal;

class JournalDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $entities = Entity::all();
        if ($entities->isEmpty()) {
            return;
        }

        foreach ($entities as $entity) {
            $this->seedJournalsForEntity($entity);
        }
    }

    /**
     * Seed standardized Odoo-compliant Journals for an entity.
     */
    protected function seedJournalsForEntity(Entity $entity): void
    {
        $entityId = $entity->id;
        $currency = $entity->currency ?? 'KWD';

        // 1. Primary Bank Accounts (boubyan, kfh, or general bank asset)
        $boubyanAccount = Account::where('entity_id', $entityId)
            ->where('is_group', false)
            ->where('account_type', AccountType::ASSET)
            ->where(function ($q) {
                $q->where('code', '1101020101')
                    ->orWhere(function ($sub) {
                        $sub->where('code', 'like', '110102%')
                            ->whereTranslationLike('name', '%بوبيان%');
                    });
            })
            ->first();

        $kfhAccount = Account::where('entity_id', $entityId)
            ->where('is_group', false)
            ->where('account_type', AccountType::ASSET)
            ->where(function ($q) {
                $q->where('code', '1101020201')
                    ->orWhere(function ($sub) {
                        $sub->where('code', 'like', '110102%')
                            ->whereTranslationLike('name', '%التمويل%');
                    });
            })
            ->first();

        $primaryBankAccount = $boubyanAccount
            ?? Account::where('entity_id', $entityId)
                ->where('is_group', false)
                ->where('account_type', AccountType::ASSET)
                ->where('code', 'like', '110102%')
                ->first()
            ?? Account::where('entity_id', $entityId)
                ->where('is_group', false)
                ->where('account_type', AccountType::ASSET)
                ->where(function ($q) {
                    $q->whereTranslationLike('name', '%بنك%')
                        ->orWhereTranslationLike('name', '%Bank%');
                })
                ->first();

        // 2. Main Cash Safe (الصندوق الرئيسي)
        $cashAccount = Account::where('entity_id', $entityId)
            ->where('is_group', false)
            ->where('account_type', AccountType::ASSET)
            ->where(function ($q) {
                $q->where('code', '11010101')
                    ->orWhere('code', '110101')
                    ->orWhere(function ($sub) {
                        $sub->where('code', 'like', '1101%')
                            ->where(function ($w) {
                                $w->whereTranslationLike('name', '%الصندوق%')
                                    ->orWhereTranslationLike('name', '%الخزينة%')
                                    ->orWhereTranslationLike('name', '%Cash%');
                            });
                    });
            })
            ->first();

        // 3. Petty Cash Custody (العهد النقدية)
        $pettyCashAccount = Account::where('entity_id', $entityId)
            ->where('is_group', false)
            ->where('account_type', AccountType::ASSET)
            ->where(function ($q) {
                $q->where('code', '11010301')
                    ->orWhere('code', '110103')
                    ->orWhere(function ($sub) {
                        $sub->where('code', 'like', '1101%')
                            ->where(function ($w) {
                                $w->whereTranslationLike('name', '%عهدة%')
                                    ->orWhereTranslationLike('name', '%نثرية%')
                                    ->orWhereTranslationLike('name', '%Petty%');
                            });
                    });
            })
            ->first() ?? $cashAccount;

        // 4. Online Payment Gateways (بوابات الدفع الإلكتروني)
        $paymentGatewayAccount = Account::where('entity_id', $entityId)
            ->where('is_group', false)
            ->where('account_type', AccountType::ASSET)
            ->where(function ($q) {
                $q->where('code', '1104020101')
                    ->orWhere('code', '1104020102')
                    ->orWhere('code', 'like', '11040201%');
            })
            ->orderBy('code')
            ->first();

        // 5. Tuition / Primary Sales Revenue (دخل الرسوم الدراسية / المبيعات)
        $incomeAccount = Account::where('entity_id', $entityId)
            ->where('account_type', AccountType::INCOME)
            ->where('is_group', false)
            ->where(function ($q) {
                $q->where('code', '41010101')
                    ->orWhere('code', 'like', '4101%')
                    ->orWhere('code', 'like', '41%')
                    ->orWhereTranslationLike('name', '%رسوم%')
                    ->orWhereTranslationLike('name', '%دراسي%');
            })
            ->orderBy('code')
            ->first() ?? Account::where('entity_id', $entityId)
                ->where('account_type', AccountType::INCOME)
                ->where('is_group', false)
                ->first();

        // 6. Direct / Educational Purchases and Supplies (مصروفات المستلزمات والمشتريات التشغيلية)
        $expenseAccount = Account::where('entity_id', $entityId)
            ->where('account_type', AccountType::EXPENSE)
            ->where('is_group', false)
            ->where('code', 'not like', '510101%') // استبعاد الرواتب من دفتر المشتريات
            ->where(function ($q) {
                $q->where('code', '51010501') // كتب ومستلزمات
                    ->orWhere('code', '51010401') // يونيفورم
                    ->orWhere('code', 'like', '510105%')
                    ->orWhere('code', 'like', '510102%')
                    ->orWhereTranslationLike('name', '%مستلزمات%')
                    ->orWhereTranslationLike('name', '%مشتريات%');
            })
            ->orderBy('code')
            ->first() ?? Account::where('entity_id', $entityId)
                ->where('account_type', AccountType::EXPENSE)
                ->where('is_group', false)
                ->where('code', 'not like', '510101%')
                ->first();

        // 7. Payroll / Teacher & Staff Salaries (رواتب واجور الكادر التعليمي)
        $payrollAccount = Account::where('entity_id', $entityId)
            ->where('account_type', AccountType::EXPENSE)
            ->where('is_group', false)
            ->where(function ($q) {
                $q->where('code', '51010101')
                    ->orWhereTranslationLike('name', '%رواتب%')
                    ->orWhereTranslationLike('name', '%أجور%');
            })
            ->first();

        // 8. Suspense Account (حساب وسيط للتسويات والمطابقة)
        $suspenseAccount = Account::where('entity_id', $entityId)
            ->where('is_group', false)
            ->where('code', '900007') // حساب وسيط تسوية حسابات الطلاب
            ->first()
            ?? Account::where('entity_id', $entityId)
                ->where('is_group', false)
                ->where(function ($q) {
                    $q->where('code', 'like', '9%')
                        ->orWhereTranslationLike('name', '%تسوية%')
                        ->orWhereTranslationLike('name', '%وسيط%');
                })
                ->orderBy('code')
                ->first();

        // 9. Bank Fees & Loss Account (العمولات البنكية ومصروفات الدفع الإلكتروني)
        $bankFeeLossAccount = Account::where('entity_id', $entityId)
            ->where('account_type', AccountType::EXPENSE)
            ->where('is_group', false)
            ->where(function ($q) {
                $q->where('code', '5109')
                    ->orWhere('code', '55')
                    ->orWhereTranslationLike('name', '%مصروفات بنكية%')
                    ->orWhereTranslationLike('name', '%رسوم دفع%')
                    ->orWhereTranslationLike('name', '%عمولات%');
            })
            ->orderBy('code')
            ->first();

        // 10. Other Income / Currency Gain (أرباح فروق وإيرادات متنوعة)
        $otherIncomeProfitAccount = Account::where('entity_id', $entityId)
            ->where('account_type', AccountType::INCOME)
            ->where('is_group', false)
            ->where(function ($q) {
                $q->where('code', '410301')
                    ->orWhere('code', '45')
                    ->orWhereTranslationLike('name', '%اخرى%')
                    ->orWhereTranslationLike('name', '%أخرى%');
            })
            ->orderBy('code')
            ->first();

        $journals = [
            // 1. Customer Invoices (Sales Journal)
            [
                'code' => 'INV',
                'type' => JournalType::SALE,
                'currency' => $currency,
                'sequence_prefix' => 'INV/%Y/',
                'sequence_number_next' => 1,
                'refund_sequence' => true,
                'payment_sequence' => false,
                'default_account_id' => $incomeAccount?->id,
                'suspense_account_id' => null,
                'profit_account_id' => null,
                'loss_account_id' => null,
                'allow_manual_entries' => true,
                'reconcile' => false,
                'restrict_mode_hash_table' => false,
                'is_active' => true,
                'ar' => [
                    'name' => 'فواتير العملاء والمبيعات',
                    'description' => 'الدفتر المحاسبي الرئيسي لتسجيل فواتير المبيعات والرسوم الدراسية للعملاء وإشعارات الدائن.',
                ],
                'en' => [
                    'name' => 'Customer Invoices & Sales',
                    'description' => 'Primary journal for customer sales invoices and credit notes.',
                ],
            ],

            // 2. Vendor Bills (Purchase Journal)
            [
                'code' => 'BILL',
                'type' => JournalType::PURCHASE,
                'currency' => $currency,
                'sequence_prefix' => 'BILL/%Y/',
                'sequence_number_next' => 1,
                'refund_sequence' => true,
                'payment_sequence' => false,
                'default_account_id' => $expenseAccount?->id,
                'suspense_account_id' => null,
                'profit_account_id' => null,
                'loss_account_id' => null,
                'allow_manual_entries' => true,
                'reconcile' => false,
                'restrict_mode_hash_table' => false,
                'is_active' => true,
                'ar' => [
                    'name' => 'فواتير الموردين والمشتريات',
                    'description' => 'دفتر تسجيل فواتير المشتريات ومصروفات الموردين وإشعارات المدين.',
                ],
                'en' => [
                    'name' => 'Vendor Bills & Purchases',
                    'description' => 'Journal for recording vendor purchases and debit notes.',
                ],
            ],

            // 3. Bank Journal - Boubyan / Commercial Bank (بنك بوبيان)
            [
                'code' => 'BNK',
                'type' => JournalType::BANK,
                'currency' => $currency,
                'sequence_prefix' => 'BNK/%Y/',
                'sequence_number_next' => 1,
                'refund_sequence' => false,
                'payment_sequence' => true,
                'default_account_id' => $boubyanAccount?->id ?? $primaryBankAccount?->id,
                'suspense_account_id' => $suspenseAccount?->id,
                'profit_account_id' => $otherIncomeProfitAccount?->id,
                'loss_account_id' => $bankFeeLossAccount?->id,
                'allow_manual_entries' => true,
                'reconcile' => true,
                'restrict_mode_hash_table' => false,
                'is_active' => true,
                'ar' => [
                    'name' => $boubyanAccount ? 'بنك بوبيان' : 'البنك التجاري',
                    'description' => 'دفتر العمليات والحركات البنكية وكشوف الحساب والمطابقة والتسوية.',
                ],
                'en' => [
                    'name' => $boubyanAccount ? 'Boubyan Bank' : 'Commercial Bank',
                    'description' => 'Bank operations, statements, and automated reconciliation journal.',
                ],
            ],

            // 4. KFH Bank (بنك بيت التمويل الكويتي)
            [
                'code' => 'KFH',
                'type' => JournalType::BANK,
                'currency' => $currency,
                'sequence_prefix' => 'KFH/%Y/',
                'sequence_number_next' => 1,
                'refund_sequence' => false,
                'payment_sequence' => true,
                'default_account_id' => $kfhAccount?->id,
                'suspense_account_id' => $suspenseAccount?->id,
                'profit_account_id' => $otherIncomeProfitAccount?->id,
                'loss_account_id' => $bankFeeLossAccount?->id,
                'allow_manual_entries' => true,
                'reconcile' => true,
                'restrict_mode_hash_table' => false,
                'is_active' => true,
                'ar' => [
                    'name' => 'بنك بيت التمويل الكويتي (KFH)',
                    'description' => 'دفتر العمليات البنكية لحساب بيت التمويل الكويتي والتسويات والمطابقات.',
                ],
                'en' => [
                    'name' => 'Kuwait Finance House (KFH)',
                    'description' => 'Bank operations and statements journal for KFH account.',
                ],
            ],

            // 5. Online Payment Gateways (بوابات الدفع الإلكتروني)
            [
                'code' => 'GATE',
                'type' => JournalType::BANK,
                'currency' => $currency,
                'sequence_prefix' => 'GATE/%Y/',
                'sequence_number_next' => 1,
                'refund_sequence' => false,
                'payment_sequence' => true,
                'default_account_id' => $paymentGatewayAccount?->id,
                'suspense_account_id' => $suspenseAccount?->id,
                'profit_account_id' => $otherIncomeProfitAccount?->id,
                'loss_account_id' => $bankFeeLossAccount?->id,
                'allow_manual_entries' => true,
                'reconcile' => true,
                'restrict_mode_hash_table' => false,
                'is_active' => true,
                'ar' => [
                    'name' => 'بوابات الدفع الإلكتروني (يوباي / ديمة)',
                    'description' => 'دفتر تحصيلات وتسويات بوابات الدفع الإلكتروني والمدفوعات الإلكترونية.',
                ],
                'en' => [
                    'name' => 'Online Payment Gateways (UPay / Deemah)',
                    'description' => 'Payment gateway transactions and online collection settlements.',
                ],
            ],

            // 6. Main Cash Safe (الصندوق الرئيسي)
            [
                'code' => 'CSH',
                'type' => JournalType::CASH,
                'currency' => $currency,
                'sequence_prefix' => 'CSH/%Y/',
                'sequence_number_next' => 1,
                'refund_sequence' => false,
                'payment_sequence' => true,
                'default_account_id' => $cashAccount?->id,
                'suspense_account_id' => $suspenseAccount?->id,
                'profit_account_id' => $otherIncomeProfitAccount?->id,
                'loss_account_id' => $bankFeeLossAccount?->id,
                'allow_manual_entries' => true,
                'reconcile' => true,
                'restrict_mode_hash_table' => false,
                'is_active' => true,
                'ar' => [
                    'name' => 'الخزينة الرئيسية (الصندوق)',
                    'description' => 'دفتر المقبوضات والمدفوعات النقدية اليومية وجرد الصندوق.',
                ],
                'en' => [
                    'name' => 'Main Cash Safe',
                    'description' => 'Daily cash receipts, payments, and cash count verification.',
                ],
            ],

            // 7. Petty Cash Custody (العهدة النقدية)
            [
                'code' => 'PET',
                'type' => JournalType::CASH,
                'currency' => $currency,
                'sequence_prefix' => 'PET/%Y/',
                'sequence_number_next' => 1,
                'refund_sequence' => false,
                'payment_sequence' => true,
                'default_account_id' => $pettyCashAccount?->id,
                'suspense_account_id' => null,
                'profit_account_id' => null,
                'loss_account_id' => null,
                'allow_manual_entries' => true,
                'reconcile' => true,
                'restrict_mode_hash_table' => false,
                'is_active' => true,
                'ar' => [
                    'name' => 'عهدة المصروفات النثرية',
                    'description' => 'دفتر تسوية وصرف عهد المصروفات التشغيلية والنثرية السريعة.',
                ],
                'en' => [
                    'name' => 'Petty Cash Custody',
                    'description' => 'Petty cash and small daily operational expenses journal.',
                ],
            ],

            // 8. General Operations / Miscellaneous (العمليات العامة والتسويات)
            [
                'code' => 'GEN',
                'type' => JournalType::GENERAL,
                'currency' => $currency,
                'sequence_prefix' => 'GEN/%Y/',
                'sequence_number_next' => 1,
                'refund_sequence' => false,
                'payment_sequence' => false,
                'default_account_id' => null,
                'suspense_account_id' => null,
                'profit_account_id' => null,
                'loss_account_id' => null,
                'allow_manual_entries' => true,
                'reconcile' => false,
                'restrict_mode_hash_table' => false,
                'is_active' => true,
                'ar' => [
                    'name' => 'العمليات والتسويات العامة',
                    'description' => 'دفتر القيود اليومية العامة والتسويات الجردية والإهلاك.',
                ],
                'en' => [
                    'name' => 'General Operations & Adjustments',
                    'description' => 'Miscellaneous journal entries, depreciations, and period-end adjustments.',
                ],
            ],

            // 9. Payroll & Salaries (الرواتب والأجور)
            [
                'code' => 'PAY',
                'type' => JournalType::GENERAL,
                'currency' => $currency,
                'sequence_prefix' => 'PAY/%Y/',
                'sequence_number_next' => 1,
                'refund_sequence' => false,
                'payment_sequence' => false,
                'default_account_id' => $payrollAccount?->id,
                'suspense_account_id' => null,
                'profit_account_id' => null,
                'loss_account_id' => null,
                'allow_manual_entries' => true,
                'reconcile' => false,
                'restrict_mode_hash_table' => false,
                'is_active' => true,
                'ar' => [
                    'name' => 'مسيرات الرواتب والأجور',
                    'description' => 'دفتر تسجيل استحقاقات ومسيرات رواتب الموظفين والبدلات والتأمينات.',
                ],
                'en' => [
                    'name' => 'Payroll & Salaries',
                    'description' => 'Journal for monthly payroll processing, allowances, and social insurance.',
                ],
            ],

            // 10. Opening Balances (الأرصدة الافتتاحية)
            [
                'code' => 'OPEN',
                'type' => JournalType::GENERAL,
                'currency' => $currency,
                'sequence_prefix' => 'OPEN/%Y/',
                'sequence_number_next' => 1,
                'refund_sequence' => false,
                'payment_sequence' => false,
                'default_account_id' => null,
                'suspense_account_id' => null,
                'profit_account_id' => null,
                'loss_account_id' => null,
                'allow_manual_entries' => true,
                'reconcile' => false,
                'restrict_mode_hash_table' => false,
                'is_active' => true,
                'ar' => [
                    'name' => 'الأرصدة الافتتاحية',
                    'description' => 'دفتر إدخال وتدوير الأرصدة الافتتاحية عند بداية النشاط أو السنة المالية.',
                ],
                'en' => [
                    'name' => 'Opening Balances',
                    'description' => 'Journal for recording opening balances and year-end roll forwards.',
                ],
            ],
        ];

        foreach ($journals as $journalData) {
            $arData = $journalData['ar'];
            $enData = $journalData['en'];
            unset($journalData['ar'], $journalData['en']);

            $journalData['entity_id'] = $entityId;
            $journalData['created_by'] = 1;

            $journal = Journal::updateOrCreate(
                [
                    'entity_id' => $entityId,
                    'code' => $journalData['code'],
                ],
                $journalData
            );

            $journal->translateOrNew('ar')->name = $arData['name'];
            $journal->translateOrNew('ar')->description = $arData['description'];
            $journal->translateOrNew('en')->name = $enData['name'];
            $journal->translateOrNew('en')->description = $enData['description'];
            $journal->save();
        }
    }
}
