<?php

namespace Modules\Accounting\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AccountingPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guardName = 'web';

        // Complete list of Accounting module screens and their actions
        $screens = [
            // 1. لوحة المؤشرات والقيادة المالية
            'accounting_dashboard' => [
                'accounting.dashboard.view' => 'عرض لوحة المؤشرات والقيادة المالية',
            ],

            // 2. الكيانات المحاسبية
            'accounting_entities' => [
                'accounting.entities.view'   => 'عرض الكيانات المحاسبية',
                'accounting.entities.create' => 'إضافة كيان محاسبي',
                'accounting.entities.edit'   => 'تعديل كيان محاسبي',
                'accounting.entities.delete' => 'حذف كيان محاسبي',
            ],

            // 3. السنوات المالية
            'accounting_fiscal_years' => [
                'accounting.fiscal-years.view'          => 'عرض السنوات المالية',
                'accounting.fiscal-years.create'        => 'إضافة سنة مالية',
                'accounting.fiscal-years.edit'          => 'تعديل سنة مالية',
                'accounting.fiscal-years.delete'        => 'حذف سنة مالية',
                'accounting.fiscal-years.toggle-status' => 'إقفال/تغيير حالة السنة المالية',
            ],

            // 4. تواريخ الإقفال وحماية الفترات
            'accounting_lock_dates' => [
                'accounting.lock-dates.view'               => 'عرض شاشة تواريخ الإقفال',
                'accounting.lock-dates.edit'               => 'تعديل وحفظ تواريخ الإقفال',
                'accounting.lock-dates.exceptions.create' => 'منح استثناء مؤقت لفترة مقفلة',
                'accounting.lock-dates.exceptions.revoke' => 'إلغاء وسحب استثناء الفترة المقفلة',
            ],

            // 5. مراكز التكلفة
            'accounting_cost_centers' => [
                'accounting.cost-centers.view'          => 'عرض شجرة مراكز التكلفة',
                'accounting.cost-centers.create'        => 'إضافة مركز تكلفة',
                'accounting.cost-centers.edit'          => 'تعديل مركز تكلفة',
                'accounting.cost-centers.delete'        => 'حذف مركز تكلفة',
                'accounting.cost-centers.toggle-status' => 'تفعيل/تعطيل مركز التكلفة',
            ],

            // 6. دفاتر اليومية
            'accounting_journals' => [
                'accounting.journals.view'          => 'عرض دفاتر اليومية',
                'accounting.journals.create'        => 'إضافة دفتر يومية',
                'accounting.journals.edit'          => 'تعديل دفتر يومية',
                'accounting.journals.delete'        => 'حذف دفتر يومية',
                'accounting.journals.toggle-status' => 'تفعيل/تعطيل دفتر اليومية',
            ],

            // 7. الخزائن والحسابات البنكية
            'accounting_cash_accounts' => [
                'accounting.cash-accounts.view'          => 'عرض الخزائن والحسابات البنكية',
                'accounting.cash-accounts.create'        => 'إضافة خزينة أو حساب بنكي',
                'accounting.cash-accounts.edit'          => 'تعديل حساب بنكي أو خزينة',
                'accounting.cash-accounts.delete'        => 'حذف حساب بنكي أو خزينة',
                'accounting.cash-accounts.toggle-status' => 'تفعيل/تعطيل الحساب أو الخزينة',
            ],

            // 8. دليل وشجرة الحسابات
            'accounting_accounts' => [
                'accounting.accounts.view'          => 'عرض شجرة ودليل الحسابات',
                'accounting.accounts.create'        => 'إضافة حساب مالي جديد',
                'accounting.accounts.edit'          => 'تعديل بيانات الحساب المالي',
                'accounting.accounts.delete'        => 'حذف الحساب المالي',
                'accounting.accounts.toggle-status' => 'تفعيل/تعطيل الحساب',
                'accounting.accounts.export'        => 'تصدير دليل الحسابات إلى Excel',
                'accounting.accounts.import'        => 'استيراد دليل الحسابات من ملف',
            ],

            // 9. قيود اليومية العامة
            'accounting_journal_entries' => [
                'accounting.journal-entries.view'    => 'عرض قيود اليومية العامة',
                'accounting.journal-entries.create'  => 'إنشاء قيد يومية يدوي',
                'accounting.journal-entries.edit'    => 'تعديل قيد اليومية المسودة',
                'accounting.journal-entries.delete'  => 'حذف قيد اليومية المسودة',
                'accounting.journal-entries.post'    => 'اعتماد وترحيل قيد اليومية',
                'accounting.journal-entries.cancel'  => 'إلغاء قيد اليومية',
                'accounting.journal-entries.reverse' => 'عكس قيد اليومية المرحّل (Reverse Entry)',
            ],

            // 10. الأرصدة الافتتاحية
            'accounting_opening_balances' => [
                'accounting.opening-balances.view'   => 'عرض شاشة الأرصدة الافتتاحية',
                'accounting.opening-balances.create' => 'حفظ وترحيل الأرصدة الافتتاحية',
                'accounting.opening-balances.reopen' => 'إعادة فتح وتعديل الأرصدة الافتتاحية',
                'accounting.opening-balances.print'  => 'طباعة تقرير الأرصدة الافتتاحية',
            ],

            // 11. التسويات والمطابقات البنكية
            'accounting_bank_reconciliations' => [
                'accounting.bank-reconciliations.view'   => 'عرض جلسات التسوية البنكية',
                'accounting.bank-reconciliations.create' => 'بدء جلسة تسوية بنكية جديدة',
                'accounting.bank-reconciliations.edit'   => 'مطابقة وتعديل حركات كشف البنك',
                'accounting.bank-reconciliations.delete' => 'حذف جلسة التسوية البنكية',
                'accounting.bank-reconciliations.reopen' => 'إعادة فتح تسوية بنكية معتمدة',
                'accounting.bank-reconciliations.print'  => 'طباعة مذكرة التسوية البنكية',
            ],

            // 12. مطابقة وتخصيص المدفوعات
            'accounting_payment_matchings' => [
                'accounting.payment-matchings.view'   => 'استعراض شاشة مطابقة وتخصيص المدفوعات',
                'accounting.payment-matchings.create' => 'إجراء مطابقة دفعة مع فاتورة',
                'accounting.payment-matchings.delete' => 'فك وإلغاء مطابقة دفعة',
            ],

            // 13. التقارير المالية والختامية
            'accounting_reports' => [
                'accounting.reports.view'             => 'عرض مركز التقارير المالية',
                'accounting.reports.general-ledger'   => 'تقرير دفتر الأستاذ العام',
                'accounting.reports.trial-balance'    => 'تقرير ميزان المراجعة',
                'accounting.reports.income-statement' => 'تقرير قائمة الدخل والأرباح والخسائر',
                'accounting.reports.balance-sheet'    => 'تقرير الميزانية العمومية والمركز المالي',
                'accounting.reports.cash-flow'        => 'تقرير قائمة التدفقات النقدية',
                'accounting.reports.account-statement'=> 'تقرير كشف حساب تحليلي',
                'accounting.reports.cost-center'      => 'تقرير أداء وربحية مراكز التكلفة',
                'accounting.reports.export'           => 'تصدير التقارير المالية Excel',
                'accounting.reports.print'            => 'طباعة التقارير المالية PDF',
            ],

            // 14. سجل الرقابة وتدقيق الحسابات
            'accounting_audit_logs' => [
                'accounting.audit-logs.view' => 'استعراض سجل الرقابة وتدقيق العمليات المالية',
            ],

            // 15. المساعد المالي والمرشد الذكي
            'accounting_ai_assistant' => [
                'accounting.ai-assistant.view' => 'استخدام شاشة المساعد المالي الذكي',
                'accounting.ai-assistant.ask'  => 'طرح استفسارات وتوجيه أسئلة للذكاء الاصطناعي',
            ],

            // 16. دليل النظام المحاسبي
            'accounting_guide' => [
                'accounting.guide.view' => 'استعراض دليل وإرشادات النظام المحاسبي',
            ],
        ];

        $allPermissionNames = [];

        // Insert or update all permissions safely
        foreach ($screens as $screenKey => $permissions) {
            foreach ($permissions as $permName => $permLabel) {
                Permission::firstOrCreate(
                    [
                        'name' => $permName,
                        'guard_name' => $guardName,
                    ]
                );
                $allPermissionNames[] = $permName;
            }
        }

        // Assign to Super Admin and Admin roles automatically
        $adminRoles = Role::whereIn('name', ['Super Admin', 'super-admin', 'super_admin', 'Admin', 'admin'])
            ->where('guard_name', $guardName)
            ->get();

        foreach ($adminRoles as $role) {
            $role->givePermissionTo($allPermissionNames);
        }

        // Also create / update a dedicated "Accountant" role with all accounting permissions
        $accountantRole = Role::firstOrCreate([
            'name' => 'محاسب',
            'guard_name' => $guardName,
        ]);
        $accountantRole->syncPermissions($allPermissionNames);

        // Clear permission cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
