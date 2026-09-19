<?php

namespace Modules\HR\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\HR\Models\HrEosRule;
use Modules\HR\Models\HrSickLeaveTier;
use Modules\HR\Models\HrWorkPolicy;
use Modules\HR\Repositories\Contracts\WorkPolicyRepositoryInterface;

class WorkPolicyRepository implements WorkPolicyRepositoryInterface
{
    //======================
    /**
     * Get all policy records grouped by group identifier.
     */
    public function getGroupedPolicies(): array
    {
        $this->ensureDefaultPoliciesExist();

        $policies = HrWorkPolicy::orderBy('sort_order')->get();
        $grouped = [];

        foreach ($policies as $policy) {
            $val = match ($policy->type) {
                'number'  => is_numeric($policy->value) ? (str_contains((string)$policy->value, '.') ? (float)$policy->value : (int)$policy->value) : 0,
                'boolean' => filter_var($policy->value, FILTER_VALIDATE_BOOLEAN),
                default   => $policy->value,
            };

            $grouped[$policy->group][$policy->key] = $val;
            $grouped['_all'][$policy->key] = $policy;
        }

        return $grouped;
    }

    //======================
    /**
     * Get single policy value.
     */
    public function getPolicyValue(string $key, mixed $default = null): mixed
    {
        $this->ensureDefaultPoliciesExist();
        return HrWorkPolicy::getVal($key, $default);
    }

    //======================
    /**
     * Update or set a batch of policies.
     */
    public function updatePoliciesBatch(array $policies): void
    {
        DB::transaction(function () use ($policies) {
            foreach ($policies as $key => $value) {
                if (is_bool($value)) {
                    $valStr = $value ? '1' : '0';
                } elseif (is_null($value)) {
                    $valStr = null;
                } else {
                    $valStr = (string)$value;
                }

                HrWorkPolicy::where('key', $key)->update(['value' => $valStr]);
            }
        });
    }

    //======================
    /**
     * Get all sick leave tiers ordered by sort_order.
     */
    public function getSickLeaveTiers(): Collection
    {
        $this->ensureDefaultSickLeaveTiersExist();
        return HrSickLeaveTier::orderBy('sort_order')->get();
    }

    //======================
    /**
     * Synchronize sick leave tiers.
     */
    public function syncSickLeaveTiers(array $tiers): void
    {
        DB::transaction(function () use ($tiers) {
            HrSickLeaveTier::query()->delete();

            $order = 1;
            foreach ($tiers as $tier) {
                if (empty($tier['name_ar']) && empty($tier['days_count'])) {
                    continue;
                }

                HrSickLeaveTier::create([
                    'name_ar'           => $tier['name_ar'] ?? '',
                    'name_en'           => $tier['name_en'] ?? null,
                    'days_count'        => (int)($tier['days_count'] ?? 0),
                    'salary_percentage' => (float)($tier['salary_percentage'] ?? 100),
                    'sort_order'        => $order++,
                    'status'            => $tier['status'] ?? 'active',
                ]);
            }
        });
    }

    //======================
    /**
     * Get all End of Service calculation rules.
     */
    public function getEosRules(?string $type = null): Collection
    {
        $this->ensureDefaultEosRulesExist();

        $query = HrEosRule::orderBy('type')->orderBy('sort_order');
        if ($type) {
            $query->where('type', $type);
        }

        return $query->get();
    }

    //======================
    /**
     * Synchronize EOS calculation rules.
     */
    public function syncEosRules(array $rules): void
    {
        DB::transaction(function () use ($rules) {
            HrEosRule::query()->delete();

            $order = 1;
            foreach ($rules as $rule) {
                $type = $rule['type'] ?? 'resignation';
                $from = (float)($rule['from_years'] ?? 0);
                $to   = isset($rule['to_years']) && $rule['to_years'] !== '' && $rule['to_years'] !== null ? (float)$rule['to_years'] : null;
                $pct  = (float)($rule['percentage'] ?? 0);

                HrEosRule::create([
                    'type'       => $type,
                    'name_ar'    => $rule['name_ar'] ?? null,
                    'name_en'    => $rule['name_en'] ?? null,
                    'from_years' => $from,
                    'to_years'   => $to,
                    'percentage' => $pct,
                    'sort_order' => $order++,
                    'status'     => 'active',
                ]);
            }
        });
    }

    //======================
    /**
     * Reset policies and tiers to system defaults.
     */
    public function resetToDefaults(): void
    {
        DB::transaction(function () {
            HrWorkPolicy::query()->delete();
            HrSickLeaveTier::query()->delete();
            HrEosRule::query()->delete();

            $this->ensureDefaultPoliciesExist(true);
            $this->ensureDefaultSickLeaveTiersExist(true);
            $this->ensureDefaultEosRulesExist(true);
        });
    }

    //==========================================================================
    // Private Seeder / Initialization Helpers
    //==========================================================================

    protected function ensureDefaultPoliciesExist(bool $force = false): void
    {
        if (!$force && HrWorkPolicy::where('group', 'loans')->exists()) {
            return;
        }

        $defaults = [
            // 1. سياسات الإجازات (Leaves)
            [
                'group'      => 'leaves',
                'key'        => 'annual_leave_days',
                'value'      => '21',
                'type'       => 'number',
                'label_ar'   => 'رصيد الإجازة السنوية المستحقة (أيام)',
                'label_en'   => 'Annual Leave Entitlement (Days)',
                'sort_order' => 1,
            ],
            [
                'group'      => 'leaves',
                'key'        => 'first_year_eligibility_months',
                'value'      => '3',
                'type'       => 'number',
                'label_ar'   => 'فترة استحقاق الإجازة للسنة الأولى (أشهر)',
                'label_en'   => 'First Year Leave Eligibility Period (Months)',
                'sort_order' => 2,
            ],
            [
                'group'      => 'leaves',
                'key'        => 'allow_annual_rollover',
                'value'      => '1',
                'type'       => 'boolean',
                'label_ar'   => 'السماح بترحيل رصيد الإجازات السنوية',
                'label_en'   => 'Allow Annual Leave Rollover',
                'sort_order' => 3,
            ],
            [
                'group'      => 'leaves',
                'key'        => 'max_rollover_days',
                'value'      => '15',
                'type'       => 'number',
                'label_ar'   => 'الحد الأقصى لأيام الترحيل (أيام)',
                'label_en'   => 'Maximum Rollover Days',
                'sort_order' => 4,
            ],
            [
                'group'      => 'leaves',
                'key'        => 'enable_casual_leave',
                'value'      => '1',
                'type'       => 'boolean',
                'label_ar'   => 'تفعيل الإجازة العارضة / الطارئة',
                'label_en'   => 'Enable Casual / Emergency Leave',
                'sort_order' => 5,
            ],
            [
                'group'      => 'leaves',
                'key'        => 'casual_leave_annual_days',
                'value'      => '6',
                'type'       => 'number',
                'label_ar'   => 'رصيد الإجازة العارضة سنوياً (أيام)',
                'label_en'   => 'Annual Casual Leave Days',
                'sort_order' => 6,
            ],
            [
                'group'      => 'leaves',
                'key'        => 'allow_consecutive_casual_days',
                'value'      => '0',
                'type'       => 'boolean',
                'label_ar'   => 'السماح بأيام متتالية للإجازة العارضة',
                'label_en'   => 'Allow Consecutive Casual Leave Days',
                'sort_order' => 7,
            ],
            [
                'group'      => 'leaves',
                'key'        => 'maternity_leave_days',
                'value'      => '70',
                'type'       => 'number',
                'label_ar'   => 'إجازة الوضع / الأمومة للموظفات (أيام)',
                'label_en'   => 'Maternity Leave Days',
                'sort_order' => 8,
            ],
            [
                'group'      => 'leaves',
                'key'        => 'hajj_leave_days',
                'value'      => '10',
                'type'       => 'number',
                'label_ar'   => 'عدد أيام إجازة الحج (أيام)',
                'label_en'   => 'Hajj Leave Days',
                'sort_order' => 9,
            ],
            [
                'group'      => 'leaves',
                'key'        => 'hajj_min_service_years',
                'value'      => '2',
                'type'       => 'number',
                'label_ar'   => 'الحد الأدنى لسنوات الخدمة لاستحقاق الحج (سنوات)',
                'label_en'   => 'Min Service Years for Hajj',
                'sort_order' => 10,
            ],
            [
                'group'      => 'leaves',
                'key'        => 'hajj_max_times',
                'value'      => '1',
                'type'       => 'number',
                'label_ar'   => 'مرات الاستحقاق طوال فترة الخدمة (مرات)',
                'label_en'   => 'Hajj Frequency in Service',
                'sort_order' => 11,
            ],
            [
                'group'      => 'leaves',
                'key'        => 'hajj_salary_percentage',
                'value'      => '100',
                'type'       => 'number',
                'label_ar'   => 'نسبة الأجر المدفوع لإجازة الحج (%)',
                'label_en'   => 'Hajj Paid Salary Percentage (%)',
                'sort_order' => 12,
            ],
            [
                'group'      => 'leaves',
                'key'        => 'hajj_require_official_permit',
                'value'      => '1',
                'type'       => 'boolean',
                'label_ar'   => 'إلزامية إرفاق تصريح الحج الرسمي',
                'label_en'   => 'Mandatory Official Hajj Permit Attachment',
                'sort_order' => 13,
            ],
            [
                'group'      => 'leaves',
                'key'        => 'marriage_leave_days',
                'value'      => '5',
                'type'       => 'number',
                'label_ar'   => 'إجازة الزواج (أيام)',
                'label_en'   => 'Marriage Leave Days',
                'sort_order' => 14,
            ],
            [
                'group'      => 'leaves',
                'key'        => 'bereavement_leave_days',
                'value'      => '5',
                'type'       => 'number',
                'label_ar'   => 'إجازة الوفاة (أيام)',
                'label_en'   => 'Bereavement Leave Days',
                'sort_order' => 15,
            ],
            [
                'group'      => 'leaves',
                'key'        => 'paternity_leave_days',
                'value'      => '3',
                'type'       => 'number',
                'label_ar'   => 'إجازة الأبوة للمولود الجديد (أيام)',
                'label_en'   => 'Paternity Leave Days',
                'sort_order' => 16,
            ],

            // 2. سياسة الغياب والجزاءات (Absence)
            [
                'group'      => 'absence',
                'key'        => 'max_monthly_deduction_days',
                'value'      => '5',
                'type'       => 'number',
                'label_ar'   => 'الحد الأقصى للخصم الشهري (أيام راتب)',
                'label_en'   => 'Max Monthly Absence Deduction (Days)',
                'sort_order' => 1,
            ],
            [
                'group'      => 'absence',
                'key'        => 'continuous_absence_termination_days',
                'value'      => '15',
                'type'       => 'number',
                'label_ar'   => 'أيام الغياب المتصل لاعتبار الموظف مستقيلاً (أيام)',
                'label_en'   => 'Continuous Absence Days for Termination',
                'sort_order' => 2,
            ],
            [
                'group'      => 'absence',
                'key'        => 'intermittent_absence_termination_days',
                'value'      => '30',
                'type'       => 'number',
                'label_ar'   => 'أيام الغياب المتقطع لاعتبار الموظف مستقيلاً (أيام)',
                'label_en'   => 'Intermittent Absence Days for Termination',
                'sort_order' => 3,
            ],
            [
                'group'      => 'absence',
                'key'        => 'unauthorized_absence_rate',
                'value'      => '1.0',
                'type'       => 'number',
                'label_ar'   => 'معامل خصم يوم الغياب غير المبرر (مضاعف)',
                'label_en'   => 'Unauthorized Absence Deduction Multiplier',
                'sort_order' => 4,
            ],

            // 3. فترة الإشعار ومكافأة نهاية الخدمة (Notice and EOS)
            [
                'group'      => 'notice_and_eos',
                'key'        => 'notice_period_monthly_months',
                'value'      => '3',
                'type'       => 'number',
                'label_ar'   => 'مهلة إشعار الموظف ذو الراتب الشهري (بالأشهر)',
                'label_en'   => 'Notice Period for Monthly Employee (Months)',
                'sort_order' => 1,
            ],
            [
                'group'      => 'notice_and_eos',
                'key'        => 'notice_period_non_monthly_months',
                'value'      => '1',
                'type'       => 'number',
                'label_ar'   => 'مهلة إشعار الموظف غير الشهري (بالأشهر)',
                'label_en'   => 'Notice Period for Non-Monthly Employee (Months)',
                'sort_order' => 2,
            ],
            [
                'group'      => 'notice_and_eos',
                'key'        => 'eos_days_monthly_first_5_years',
                'value'      => '15',
                'type'       => 'number',
                'label_ar'   => 'أيام المكافأة للموظف الشهري (للخمس سنوات الأولى)',
                'label_en'   => 'EOS Days Monthly Employee (First 5 Years)',
                'sort_order' => 3,
            ],
            [
                'group'      => 'notice_and_eos',
                'key'        => 'eos_days_monthly_after_5_years',
                'value'      => '30',
                'type'       => 'number',
                'label_ar'   => 'أيام المكافأة للموظف الشهري (للخمس سنوات التالية فما بعد)',
                'label_en'   => 'EOS Days Monthly Employee (After 5 Years)',
                'sort_order' => 4,
            ],
            [
                'group'      => 'notice_and_eos',
                'key'        => 'eos_days_non_monthly_first_5_years',
                'value'      => '10',
                'type'       => 'number',
                'label_ar'   => 'أيام المكافأة للموظف غير الشهري (للخمس سنوات الأولى)',
                'label_en'   => 'EOS Days Non-Monthly Employee (First 5 Years)',
                'sort_order' => 5,
            ],
            [
                'group'      => 'notice_and_eos',
                'key'        => 'eos_days_non_monthly_after_5_years',
                'value'      => '15',
                'type'       => 'number',
                'label_ar'   => 'أيام المكافأة للموظف غير الشهري (للخمس سنوات التالية فما بعد)',
                'label_en'   => 'EOS Days Non-Monthly Employee (After 5 Years)',
                'sort_order' => 6,
            ],

            // 4. سياسات وقواعد الأذونات (Permissions)
            [
                'group'      => 'permissions',
                'key'        => 'max_monthly_permission_hours',
                'value'      => '4',
                'type'       => 'number',
                'label_ar'   => 'الحد الأقصى لساعات الإذن شهرياً (ساعات)',
                'label_en'   => 'Max Monthly Permission Hours',
                'sort_order' => 1,
            ],
            [
                'group'      => 'permissions',
                'key'        => 'max_monthly_permission_count',
                'value'      => '3',
                'type'       => 'number',
                'label_ar'   => 'الحد الأقصى لعدد مرات الإذن شهرياً (مرات)',
                'label_en'   => 'Max Monthly Permission Requests Count',
                'sort_order' => 2,
            ],
            [
                'group'      => 'permissions',
                'key'        => 'max_single_permission_hours',
                'value'      => '2',
                'type'       => 'number',
                'label_ar'   => 'الحد الأقصى لطلب الإذن الواحد (ساعات)',
                'label_en'   => 'Max Single Permission Duration (Hours)',
                'sort_order' => 3,
            ],
            [
                'group'      => 'permissions',
                'key'        => 'permission_financial_policy',
                'value'      => 'paid_with_excess_deduction',
                'type'       => 'select',
                'label_ar'   => 'سياسة المعالجة المالية للأذونات',
                'label_en'   => 'Permission Financial Policy',
                'options'    => [
                    'paid_with_excess_deduction' => 'ساعات مجانية شهرياً وما زاد يخصم من الراتب',
                    'always_deducted'            => 'تخصم ساعات الإذن دائماً من الراتب',
                    'deduct_from_annual_leaves'  => 'تخصم الساعات التراكمية من رصيد الإجازات السنوية',
                    'always_paid'                => 'أذونات مدفوعة الأجر بالكامل دون خصم',
                ],
                'sort_order' => 4,
            ],
            [
                'group'      => 'permissions',
                'key'        => 'morning_grace_period_minutes',
                'value'      => '15',
                'type'       => 'number',
                'label_ar'   => 'فترة السماح الصباحية قبل احتساب التأخير (دقائق)',
                'label_en'   => 'Morning Grace Period (Minutes)',
                'sort_order' => 5,
            ],
            [
                'group'      => 'permissions',
                'key'        => 'min_advance_notice_hours',
                'value'      => '24',
                'type'       => 'number',
                'label_ar'   => 'الحد الأدنى للإشعار المسبق (ساعات)',
                'label_en'   => 'Minimum Advance Notice (Hours)',
                'sort_order' => 6,
            ],
            [
                'group'      => 'permissions',
                'key'        => 'approval_workflow',
                'value'      => 'manager_and_hr',
                'type'       => 'select',
                'label_ar'   => 'مسار دورة الاعتماد (Approval Workflow)',
                'label_en'   => 'Approval Workflow',
                'options'    => [
                    'manager_and_hr' => 'موافقة المدير المباشر + إدارة الموارد البشرية (HR)',
                    'manager_only'   => 'موافقة المدير المباشر فقط',
                    'hr_only'        => 'موافقة إدارة الموارد البشرية (HR) فقط',
                    'auto_approve'   => 'اعتماد تلقائي طالما ضمن الرصيد الشهري المسموح',
                ],
                'sort_order' => 7,
            ],
            [
                'group'      => 'permissions',
                'key'        => 'max_monthly_emergency_permissions',
                'value'      => '1',
                'type'       => 'number',
                'label_ar'   => 'الحد الأقصى للأذونات الطارئة شهرياً',
                'label_en'   => 'Max Emergency Permissions Monthly',
                'sort_order' => 8,
            ],

            // 5. سياسات ولوائح السلف والقروض (Loans & Advances)
            [
                'group'      => 'loans',
                'key'        => 'min_service_months',
                'value'      => '3',
                'type'       => 'number',
                'label_ar'   => 'الحد الأدنى لخدمة الموظف لاستحقاق السلفة (أشهر)',
                'label_en'   => 'Minimum Service Months for Loan Eligibility',
                'sort_order' => 1,
            ],
            [
                'group'      => 'loans',
                'key'        => 'max_salary_multiplier',
                'value'      => '3.0',
                'type'       => 'number',
                'label_ar'   => 'الحد الأقصى لسلفة الأقساط (مضاعف الراتب الأساسي)',
                'label_en'   => 'Max Installment Loan Multiplier (x Basic Salary)',
                'sort_order' => 2,
            ],
            [
                'group'      => 'loans',
                'key'        => 'max_advance_multiplier',
                'value'      => '1.0',
                'type'       => 'number',
                'label_ar'   => 'الحد الأقصى لسلفة الراتب العاجلة (مضاعف الراتب الأساسي)',
                'label_en'   => 'Max Salary Advance Multiplier (x Basic Salary)',
                'sort_order' => 3,
            ],
            [
                'group'      => 'loans',
                'key'        => 'max_monthly_deduction_percentage',
                'value'      => '33.3',
                'type'       => 'number',
                'label_ar'   => 'الحد الأقصى للاستقطاع الشهري للقسط من الراتب (%)',
                'label_en'   => 'Max Monthly Installment Deduction Ceiling (%)',
                'sort_order' => 4,
            ],
            [
                'group'      => 'loans',
                'key'        => 'max_installments_months',
                'value'      => '24',
                'type'       => 'number',
                'label_ar'   => 'الحد الأقصى لمدة سداد الأقساط (أشهر)',
                'label_en'   => 'Max Repayment Installment Duration (Months)',
                'sort_order' => 5,
            ],
            [
                'group'      => 'loans',
                'key'        => 'allow_multiple_loans',
                'value'      => '0',
                'type'       => 'boolean',
                'label_ar'   => 'السماح بطلب سلفة جديدة مع وجود سلفة نشطة غير مسددة',
                'label_en'   => 'Allow Loan Request While Another Loan Is Active',
                'sort_order' => 6,
            ],
            [
                'group'      => 'loans',
                'key'        => 'loan_approval_workflow',
                'value'      => 'manager_and_hr_and_finance',
                'type'       => 'select',
                'label_ar'   => 'مسار دورة اعتماد السلف والقروض',
                'label_en'   => 'Loan Approval Workflow Route',
                'options'    => [
                    'manager_and_hr_and_finance' => 'موافقة المدير المباشر + الموارد البشرية + الإدارة المالية',
                    'hr_and_finance'             => 'موافقة الموارد البشرية + الإدارة المالية',
                    'manager_and_hr'            => 'موافقة المدير المباشر + الموارد البشرية',
                    'hr_only'                   => 'موافقة إدارة الموارد البشرية فقط',
                ],
                'sort_order' => 7,
            ],
            [
                'group'      => 'loans',
                'key'        => 'disbursement_method_default',
                'value'      => 'bank_transfer',
                'type'       => 'select',
                'label_ar'   => 'طريقة الصرف الافتراضية',
                'label_en'   => 'Default Disbursement Method',
                'options'    => [
                    'bank_transfer' => 'تحويل بنكي (Bank Transfer)',
                    'cash'          => 'نقداً من الخزينة (Cash)',
                    'cheque'        => 'شيك مصرفي (Cheque)',
                ],
                'sort_order' => 8,
            ],
        ];

        foreach ($defaults as $data) {
            HrWorkPolicy::firstOrCreate(['key' => $data['key']], $data);
        }
    }

    protected function ensureDefaultSickLeaveTiersExist(bool $force = false): void
    {
        if (!$force && HrSickLeaveTier::count() > 0) {
            return;
        }

        $tiers = [
            [
                'name_ar'           => '15 يوماً بأجر كامل (100%)',
                'name_en'           => '15 Days with Full Salary (100%)',
                'days_count'        => 15,
                'salary_percentage' => 100.00,
                'sort_order'        => 1,
            ],
            [
                'name_ar'           => '10 أيام بنسبة 75% من الأجر',
                'name_en'           => '10 Days with 75% of Salary',
                'days_count'        => 10,
                'salary_percentage' => 75.00,
                'sort_order'        => 2,
            ],
            [
                'name_ar'           => '10 أيام بنسبة 50% من الأجر',
                'name_en'           => '10 Days with 50% of Salary',
                'days_count'        => 10,
                'salary_percentage' => 50.00,
                'sort_order'        => 3,
            ],
            [
                'name_ar'           => '10 أيام بنسبة 25% من الأجر',
                'name_en'           => '10 Days with 25% of Salary',
                'days_count'        => 10,
                'salary_percentage' => 25.00,
                'sort_order'        => 4,
            ],
        ];

        foreach ($tiers as $tier) {
            HrSickLeaveTier::create($tier);
        }
    }

    protected function ensureDefaultEosRulesExist(bool $force = false): void
    {
        if (!$force && HrEosRule::count() > 0) {
            return;
        }

        $rules = [
            [
                'type'       => 'resignation',
                'name_ar'    => 'أقل من 3 سنوات (بدون مستحقات)',
                'name_en'    => 'Less than 3 years (0%)',
                'from_years' => 0.00,
                'to_years'   => 3.00,
                'percentage' => 0.00,
                'sort_order' => 1,
            ],
            [
                'type'       => 'resignation',
                'name_ar'    => 'من 3 إلى أقل من 5 سنوات (50%)',
                'name_en'    => 'From 3 to less than 5 years (50%)',
                'from_years' => 3.00,
                'to_years'   => 5.00,
                'percentage' => 50.00,
                'sort_order' => 2,
            ],
            [
                'type'       => 'resignation',
                'name_ar'    => 'من 5 إلى أقل من 10 سنوات (66.67%)',
                'name_en'    => 'From 5 to less than 10 years (66.67%)',
                'from_years' => 5.00,
                'to_years'   => 10.00,
                'percentage' => 66.67,
                'sort_order' => 3,
            ],
            [
                'type'       => 'resignation',
                'name_ar'    => '10 سنوات فأكثر (100%)',
                'name_en'    => '10 years and above (100%)',
                'from_years' => 10.00,
                'to_years'   => null,
                'percentage' => 100.00,
                'sort_order' => 4,
            ],
            [
                'type'       => 'termination',
                'name_ar'    => 'إنهاء الخدمة بغير رغبة الموظف أو انتهاء العقد (100%)',
                'name_en'    => 'Employer Termination or Contract Expiry (100%)',
                'from_years' => 0.00,
                'to_years'   => null,
                'percentage' => 100.00,
                'sort_order' => 5,
            ],
        ];

        foreach ($rules as $rule) {
            HrEosRule::create($rule);
        }
    }
}
