<?php

namespace Modules\Accounting\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Accounting\Enums\JournalEntryStatus;
use Modules\Accounting\Enums\JournalEntryType;
use Modules\Accounting\Models\Account;
use Modules\Accounting\Models\CostCenter;
use Modules\Accounting\Models\Entity;
use Modules\Accounting\Models\FiscalYear;
use Modules\Accounting\Models\Journal;
use Modules\Accounting\Models\JournalEntry;
use Modules\Accounting\Models\JournalEntryLine;

class JournalEntryDatabaseSeeder extends Seeder
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

        $fiscalYear = FiscalYear::where('entity_id', $entity->id)->where('status', 'open')->first()
            ?? FiscalYear::where('entity_id', $entity->id)->first();

        $genJournal = Journal::where('entity_id', $entity->id)->where('type', 'general')->first();
        $cashJournal = Journal::where('entity_id', $entity->id)->where('type', 'cash')->first();

        $costCenter = CostCenter::where('entity_id', $entity->id)->first();

        // 1. Opening Balance Entry (قيد افتتاحي متوازن ومرحل)
        if ($genJournal) {
            $capitalAccount = Account::where('entity_id', $entity->id)->where('code', '3101')->first()
                ?? Account::where('entity_id', $entity->id)->where('account_type', 'equity')->first();

            $cashAccount = Account::where('entity_id', $entity->id)->where('code', '1101')->first()
                ?? Account::where('entity_id', $entity->id)->where('account_type', 'current_asset')->first();

            $bankAccount = Account::where('entity_id', $entity->id)->where('code', '1102')->first()
                ?? Account::where('entity_id', $entity->id)->where('account_type', 'current_asset')->skip(1)->first();

            if ($capitalAccount && $cashAccount && $bankAccount) {
                $entry1 = JournalEntry::updateOrCreate(
                    [
                        'entity_id' => $entity->id,
                        'entry_number' => 'GEN/2026/00001',
                    ],
                    [
                        'fiscal_year_id' => $fiscalYear?->id,
                        'journal_id' => $genJournal->id,
                        'entry_date' => '2026-01-01',
                        'accounting_date' => '2026-01-01',
                        'entry_type' => JournalEntryType::MANUAL->value,
                        'status' => JournalEntryStatus::POSTED->value,
                        'reference' => 'OP-2026-001',
                        'narration' => 'القيد الافتتاحي لأرصدة أول المدة للسنة المالية 2026',
                        'currency' => $entity->currency ?? 'KWD',
                        'exchange_rate' => 1.000000,
                        'total_debit' => 500000.00,
                        'total_credit' => 500000.00,
                        'is_balanced' => true,
                        'posted_at' => now(),
                    ]
                );

                $entry1->lines()->delete();

                // Line 1: Cash Debit 200,000
                JournalEntryLine::create([
                    'journal_entry_id' => $entry1->id,
                    'account_id' => $cashAccount->id,
                    'cost_center_id' => $costCenter?->id,
                    'description' => 'رصيد الخزينة الرئيسي الافتتاحي',
                    'debit' => 200000.00,
                    'credit' => 0.00,
                    'line_order' => 1,
                ]);

                // Line 2: Bank Debit 300,000
                JournalEntryLine::create([
                    'journal_entry_id' => $entry1->id,
                    'account_id' => $bankAccount->id,
                    'cost_center_id' => $costCenter?->id,
                    'description' => 'رصيد الحساب الجاري بالبنك الافتتاحي',
                    'debit' => 300000.00,
                    'credit' => 0.00,
                    'line_order' => 2,
                ]);

                // Line 3: Capital Credit 500,000
                JournalEntryLine::create([
                    'journal_entry_id' => $entry1->id,
                    'account_id' => $capitalAccount->id,
                    'cost_center_id' => null,
                    'description' => 'رأس المال المدفوع',
                    'debit' => 0.00,
                    'credit' => 500000.00,
                    'line_order' => 3,
                ]);
            }
        }

        // 2. Draft Operational Expense Entry (قيد مسودة لمصروفات الصيانة)
        if ($cashJournal) {
            $expenseAccount = Account::where('entity_id', $entity->id)->where('account_type', 'expense')->first();
            $cashAccount = Account::where('entity_id', $entity->id)->where('code', '1101')->first()
                ?? Account::where('entity_id', $entity->id)->where('account_type', 'current_asset')->first();

            if ($expenseAccount && $cashAccount) {
                $entry2 = JournalEntry::updateOrCreate(
                    [
                        'entity_id' => $entity->id,
                        'entry_number' => 'CSH/2026/00001',
                    ],
                    [
                        'fiscal_year_id' => $fiscalYear?->id,
                        'journal_id' => $cashJournal->id,
                        'entry_date' => '2026-03-15',
                        'accounting_date' => '2026-03-15',
                        'entry_type' => JournalEntryType::MANUAL->value,
                        'status' => JournalEntryStatus::DRAFT->value,
                        'reference' => 'EXP-REC-4421',
                        'narration' => 'سداد مصاريف صيانة ونظافة المقر الرئيسي نقداً (مسودة قيد للمراجعة)',
                        'currency' => $entity->currency ?? 'KWD',
                        'exchange_rate' => 1.000000,
                        'total_debit' => 3500.00,
                        'total_credit' => 3500.00,
                        'is_balanced' => true,
                    ]
                );

                $entry2->lines()->delete();

                JournalEntryLine::create([
                    'journal_entry_id' => $entry2->id,
                    'account_id' => $expenseAccount->id,
                    'cost_center_id' => $costCenter?->id,
                    'description' => 'مصروفات صيانة ونظافة دورية',
                    'debit' => 3500.00,
                    'credit' => 0.00,
                    'line_order' => 1,
                ]);

                JournalEntryLine::create([
                    'journal_entry_id' => $entry2->id,
                    'account_id' => $cashAccount->id,
                    'cost_center_id' => null,
                    'description' => 'صرف نقدي من الخزينة الرئيسية',
                    'debit' => 0.00,
                    'credit' => 3500.00,
                    'line_order' => 2,
                ]);
            }
        }
    }
}
