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

        // Fetch primary accounts for this entity
        $bankAccount = Account::where('entity_id', $entityId)
            ->where(function ($q) {
                $q->where('code', '110102')
                    ->orWhereTranslationLike('name', '%بنك%')
                    ->orWhereTranslationLike('name', '%Bank%');
            })
            ->where('is_group', false)
            ->first();

        $cashAccount = Account::where('entity_id', $entityId)
            ->where(function ($q) {
                $q->where('code', '110101')
                    ->orWhereTranslationLike('name', '%الصندوق%')
                    ->orWhereTranslationLike('name', '%الخزينة%')
                    ->orWhereTranslationLike('name', '%Cash%');
            })
            ->where('is_group', false)
            ->first();

        $pettyCashAccount = Account::where('entity_id', $entityId)
            ->where(function ($q) {
                $q->where('code', '110103')
                    ->orWhereTranslationLike('name', '%عهدة%')
                    ->orWhereTranslationLike('name', '%نثرية%')
                    ->orWhereTranslationLike('name', '%Petty%');
            })
            ->where('is_group', false)
            ->first() ?? $cashAccount;

        $incomeAccount = Account::where('entity_id', $entityId)
            ->where('account_type', AccountType::INCOME)
            ->where('is_group', false)
            ->first();

        $expenseAccount = Account::where('entity_id', $entityId)
            ->where('account_type', AccountType::EXPENSE)
            ->where('is_group', false)
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
                    'description' => 'الدفتر المحاسبي الرئيسي لتسجيل فواتير المبيعات للعملاء وإشعارات الدائن.',
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

            // 3. Bank Journal (الحساب البنكي)
            [
                'code' => 'BNK',
                'type' => JournalType::BANK,
                'currency' => $currency,
                'sequence_prefix' => 'BNK/%Y/',
                'sequence_number_next' => 1,
                'refund_sequence' => false,
                'payment_sequence' => true,
                'default_account_id' => $bankAccount?->id,
                'suspense_account_id' => $bankAccount?->id,
                'profit_account_id' => null,
                'loss_account_id' => null,
                'allow_manual_entries' => true,
                'reconcile' => true,
                'restrict_mode_hash_table' => false,
                'is_active' => true,
                'ar' => [
                    'name' => 'البنك التجاري',
                    'description' => 'دفتر العمليات والحركات البنكية وكشوف الحساب والمطابقة والتسوية.',
                ],
                'en' => [
                    'name' => 'Commercial Bank',
                    'description' => 'Bank operations, statements, and automated reconciliation journal.',
                ],
            ],

            // 4. Main Cash Safe (الصندوق الرئيسي)
            [
                'code' => 'CSH',
                'type' => JournalType::CASH,
                'currency' => $currency,
                'sequence_prefix' => 'CSH/%Y/',
                'sequence_number_next' => 1,
                'refund_sequence' => false,
                'payment_sequence' => true,
                'default_account_id' => $cashAccount?->id,
                'suspense_account_id' => null,
                'profit_account_id' => null,
                'loss_account_id' => null,
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

            // 5. Petty Cash Custody (العهدة النقدية)
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

            // 6. General Operations / Miscellaneous (العمليات العامة والتسويات)
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

            // 7. Payroll & Salaries (الرواتب والأجور)
            [
                'code' => 'PAY',
                'type' => JournalType::GENERAL,
                'currency' => $currency,
                'sequence_prefix' => 'PAY/%Y/',
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
                    'name' => 'مسيرات الرواتب والأجور',
                    'description' => 'دفتر تسجيل استحقاقات ومسيرات رواتب الموظفين والبدلات والتأمينات.',
                ],
                'en' => [
                    'name' => 'Payroll & Salaries',
                    'description' => 'Journal for monthly payroll processing, allowances, and social insurance.',
                ],
            ],

            // 8. Opening Balances (الأرصدة الافتتاحية)
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
