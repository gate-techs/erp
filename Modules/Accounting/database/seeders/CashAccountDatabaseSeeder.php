<?php

namespace Modules\Accounting\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Accounting\Enums\CashAccountType;
use Modules\Accounting\Models\Account;
use Modules\Accounting\Models\CashAccount;
use Modules\Accounting\Models\Entity;
use Modules\Accounting\Models\Journal;

class CashAccountDatabaseSeeder extends Seeder
{
    //--------------------------------------------------
    public function run(): void
    {
        $entities = Entity::all();
        if ($entities->isEmpty()) {
            return;
        }

        $adminUser = User::first();

        foreach ($entities as $entity) {
            $this->seedCashAccountsForEntity($entity, $adminUser?->id);
        }
    }

    //--------------------------------------------------
    protected function seedCashAccountsForEntity(Entity $entity, ?int $userId = null): void
    {
        $entityId = $entity->id;
        $currency = $entity->currency ?: 'SAR';

        // GL Accounts
        $cashGLAccount = Account::where('entity_id', $entityId)
            ->where(function ($q) {
                $q->where('code', '110101')
                    ->orWhereTranslationLike('name', '%الصندوق%')
                    ->orWhereTranslationLike('name', '%الخزينة%')
                    ->orWhereTranslationLike('name', '%Cash%');
            })
            ->where('is_group', false)
            ->first();

        $bankGLAccount = Account::where('entity_id', $entityId)
            ->where(function ($q) {
                $q->where('code', '110102')
                    ->orWhereTranslationLike('name', '%بنك%')
                    ->orWhereTranslationLike('name', '%Bank%');
            })
            ->where('is_group', false)
            ->first();

        $pettyCashGLAccount = Account::where('entity_id', $entityId)
            ->where(function ($q) {
                $q->where('code', '110103')
                    ->orWhereTranslationLike('name', '%عهدة%')
                    ->orWhereTranslationLike('name', '%نثرية%')
                    ->orWhereTranslationLike('name', '%Petty%');
            })
            ->where('is_group', false)
            ->first();

        // Journals
        $cashJournal = Journal::where('entity_id', $entityId)->where('type', 'cash')->first();
        $bankJournal = Journal::where('entity_id', $entityId)->where('type', 'bank')->first();

        $accountsData = [
            // 1. Main Cash Safe
            [
                'code' => 'CSH-MAIN',
                'type' => CashAccountType::CASH,
                'account_id' => $cashGLAccount?->id,
                'journal_id' => $cashJournal?->id,
                'bank_name' => null,
                'account_number' => null,
                'iban' => null,
                'swift_code' => null,
                'currency' => $currency,
                'opening_balance' => 50000.0000,
                'responsible_user_id' => $userId,
                'is_active' => true,
                'translations' => [
                    'ar' => [
                        'name' => 'الخزينة الرئيسية (الإدارة)',
                        'description' => 'الخزينة المركزية بالمقر الرئيسي لاستلام وإيداع السيولة النقدية.',
                    ],
                    'en' => [
                        'name' => 'Main Headquarter Safe',
                        'description' => 'Central treasury safe for main office cash receipts and disbursements.',
                    ],
                ],
            ],

            // 2. POS Cash Register
            [
                'code' => 'CSH-POS',
                'type' => CashAccountType::CASH,
                'account_id' => $cashGLAccount?->id,
                'journal_id' => $cashJournal?->id,
                'bank_name' => null,
                'account_number' => null,
                'iban' => null,
                'swift_code' => null,
                'currency' => $currency,
                'opening_balance' => 10000.0000,
                'responsible_user_id' => $userId,
                'is_active' => true,
                'translations' => [
                    'ar' => [
                        'name' => 'خزينة مبيعات الفرع / الصندوق اليومي',
                        'description' => 'صندوق متحصلات المبيعات اليومية المباشرة.',
                    ],
                    'en' => [
                        'name' => 'Branch POS Daily Cash Register',
                        'description' => 'Cash register for daily point of sales transactions.',
                    ],
                ],
            ],

            // 3. Petty Cash Custody
            [
                'code' => 'CSH-PETTY',
                'type' => CashAccountType::CASH,
                'account_id' => $pettyCashGLAccount?->id ?? $cashGLAccount?->id,
                'journal_id' => $cashJournal?->id,
                'bank_name' => null,
                'account_number' => null,
                'iban' => null,
                'swift_code' => null,
                'currency' => $currency,
                'opening_balance' => 5000.0000,
                'responsible_user_id' => $userId,
                'is_active' => true,
                'translations' => [
                    'ar' => [
                        'name' => 'عهدة المصروفات النثرية',
                        'description' => 'سلفة المصروفات التشغيلية والمشتريات الصغيرة المستعجلة.',
                    ],
                    'en' => [
                        'name' => 'Petty Cash Custody',
                        'description' => 'Petty cash advance for small routine operational expenses.',
                    ],
                ],
            ],

            // 4. Al Rajhi Bank Account
            [
                'code' => 'BNK-RAJHI',
                'type' => CashAccountType::BANK,
                'account_id' => $bankGLAccount?->id,
                'journal_id' => $bankJournal?->id,
                'bank_name' => 'مصرف الراجحي (Al Rajhi Bank)',
                'account_number' => '284000010928374',
                'iban' => 'SA4480000284000010928374',
                'swift_code' => 'RJHISARI',
                'currency' => $currency,
                'opening_balance' => 250000.0000,
                'responsible_user_id' => $userId,
                'is_active' => true,
                'translations' => [
                    'ar' => [
                        'name' => 'مصرف الراجحي - الحساب الجاري التجاري',
                        'description' => 'الحساب البنكي الرئيسي للعمليات التشغيلية والتحويلات ومدفوعات الموردين.',
                    ],
                    'en' => [
                        'name' => 'Al Rajhi Bank - Commercial Current Account',
                        'description' => 'Main operating bank account for collections and supplier payments.',
                    ],
                ],
            ],

            // 5. SNB Bank Account
            [
                'code' => 'BNK-SNB',
                'type' => CashAccountType::BANK,
                'account_id' => $bankGLAccount?->id,
                'journal_id' => $bankJournal?->id,
                'bank_name' => 'البنك الأهلي السعودي (SNB)',
                'account_number' => '10293847561001',
                'iban' => 'SA0310000010293847561001',
                'swift_code' => 'NCBKSAJE',
                'currency' => $currency,
                'opening_balance' => 150000.0000,
                'responsible_user_id' => $userId,
                'is_active' => true,
                'translations' => [
                    'ar' => [
                        'name' => 'البنك الأهلي السعودي - حساب الرواتب والمخصصات',
                        'description' => 'حساب بنكي مخصص لمسيرات الرواتب والعمليات الحكومية.',
                    ],
                    'en' => [
                        'name' => 'SNB - Payroll & Provisions Account',
                        'description' => 'Dedicated bank account for payroll disbursements and governmental fees.',
                    ],
                ],
            ],
        ];

        foreach ($accountsData as $accData) {
            $translations = $accData['translations'];
            unset($accData['translations']);

            $accData['entity_id'] = $entityId;
            $accData['created_by'] = $userId;

            $cashAccount = CashAccount::firstOrNew([
                'entity_id' => $entityId,
                'code' => $accData['code'],
            ]);

            $cashAccount->fill($accData);
            $cashAccount->save();

            foreach ($translations as $locale => $trans) {
                $t = $cashAccount->translateOrNew($locale);
                $t->name = $trans['name'];
                $t->description = $trans['description'] ?? null;
            }

            $cashAccount->save();
        }
    }
}
