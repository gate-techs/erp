<?php

namespace Modules\AccessControl\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Modules\AccessControl\Models\Permission;
use Modules\AccessControl\Models\Role;
use Modules\AccessControl\Repositories\Contracts\PermissionRepositoryInterface;
use Modules\AccessControl\Repositories\Contracts\RoleRepositoryInterface;

class AccessControlService
{
    public function __construct(
        private readonly RoleRepositoryInterface $roleRepository,
        private readonly PermissionRepositoryInterface $permissionRepository,
    ) {}

    public function getRoles(array $filters = []): Collection
    {
        return $this->roleRepository->getAll($filters);
    }

    public function getRolesQuery(array $filters = []): Builder
    {
        return $this->roleRepository->getQuery($filters);
    }

    public function createRole(array $data): Role
    {
        return $this->roleRepository->create($this->normalizeData($data));
    }

    public function updateRole(int|Role $role, array $data): Role
    {
        return $this->roleRepository->update($role, $this->normalizeData($data));
    }

    public function deleteRole(int|Role $role): bool
    {
        $roleModel = is_numeric($role) ? Role::find($role) : $role;
        if ($roleModel && $roleModel->isSystem()) {
            throw new \InvalidArgumentException(__('System roles cannot be deleted.'));
        }

        return $this->roleRepository->delete($role);
    }

    public function syncRolePermissions(int|Role $role, array $permissions): Role
    {
        return $this->roleRepository->syncPermissions($role, $permissions);
    }

    public function getPermissions(array $filters = []): Collection
    {
        return $this->permissionRepository->getAll($filters);
    }

    public function createPermission(array $data): Permission
    {
        return $this->permissionRepository->create($this->normalizeData($data));
    }

    public function updatePermission(int|Permission $permission, array $data): Permission
    {
        return $this->permissionRepository->update($permission, $this->normalizeData($data));
    }

    public function deletePermission(int|Permission $permission): bool
    {
        return $this->permissionRepository->delete($permission);
    }

    public function getPermissionsMatrix(): array
    {
        return [
            [
                'module' => 'Nurseries',
                'module_ar' => 'قطاع وتراخيص الحضانات',
                'icon' => 'bx-buildings',
                'screens' => [
                    [
                        'key' => 'nursery',
                        'name' => 'Nurseries',
                        'name_ar' => 'إدارة الحضانات',
                        'icon' => 'bx-buildings',
                        'view' => 'nursery.view',
                        'create' => 'nursery.create',
                        'edit' => 'nursery.edit',
                        'delete' => 'nursery.delete',
                        'custom' => [
                            ['name' => 'nursery.toggle-status', 'label' => 'تغيير الحالة', 'label_en' => 'Toggle Status'],
                        ],
                    ],
                    [
                        'key' => 'nursery_branches',
                        'name' => 'Nursery Branches',
                        'name_ar' => 'فروع الحضانات',
                        'icon' => 'bx-git-branch',
                        'view' => 'nursery.branches.view',
                        'create' => 'nursery.branches.create',
                        'edit' => 'nursery.branches.edit',
                        'delete' => 'nursery.branches.delete',
                        'custom' => [
                            ['name' => 'nursery.branches.toggle-status', 'label' => 'تغيير الحالة', 'label_en' => 'Toggle Status'],
                        ],
                    ],
                ],
            ],
            [
                'module' => 'Human Resources',
                'module_ar' => 'الموارد البشرية وشؤون الموظفين',
                'icon' => 'bx-user-pin',
                'screens' => [
                    [
                        'key' => 'hr_dashboard',
                        'name' => 'HR Dashboard',
                        'name_ar' => 'لوحة معلومات الموارد البشرية',
                        'icon' => 'bx-home-circle',
                        'view' => 'hr.dashboard.view',
                    ],
                    [
                        'key' => 'hr_employees',
                        'name' => 'Employees Directory',
                        'name_ar' => 'بيانات وملفات الموظفين',
                        'icon' => 'bx-id-card',
                        'view' => 'hr.employees.view',
                        'create' => 'hr.employees.create',
                        'edit' => 'hr.employees.edit',
                        'delete' => 'hr.employees.delete',
                        'custom' => [
                            ['name' => 'hr.employees.export', 'label' => 'تصدير إكسيل', 'label_en' => 'Export'],
                        ],
                    ],
                    [
                        'key' => 'hr_attendances',
                        'name' => 'Time & Attendance',
                        'name_ar' => 'الحضور والانصراف والبصمة',
                        'icon' => 'bx-time-five',
                        'view' => 'hr.attendances.view',
                        'create' => 'hr.attendances.create',
                        'edit' => 'hr.attendances.edit',
                        'delete' => 'hr.attendances.delete',
                        'custom' => [
                            ['name' => 'hr.attendances.import', 'label' => 'استيراد بصمات', 'label_en' => 'Import'],
                            ['name' => 'hr.attendances.export', 'label' => 'تصدير كشف', 'label_en' => 'Export'],
                        ],
                    ],
                    [
                        'key' => 'hr_leaves',
                        'name' => 'Leave Requests',
                        'name_ar' => 'طلبات ورصيد الإجازات',
                        'icon' => 'bx-calendar-event',
                        'view' => 'hr.leaves.view',
                        'create' => 'hr.leaves.create',
                        'edit' => 'hr.leaves.edit',
                        'delete' => 'hr.leaves.delete',
                        'custom' => [
                            ['name' => 'hr.leaves.approve', 'label' => 'اعتماد', 'label_en' => 'Approve'],
                            ['name' => 'hr.leaves.reject', 'label' => 'رفض', 'label_en' => 'Reject'],
                        ],
                    ],
                    [
                        'key' => 'hr_permissions',
                        'name' => 'Short Leaves & Permissions',
                        'name_ar' => 'أذونات الخروج والاستئذان',
                        'icon' => 'bx-door-open',
                        'view' => 'hr.permissions.view',
                        'create' => 'hr.permissions.create',
                        'edit' => 'hr.permissions.edit',
                        'delete' => 'hr.permissions.delete',
                        'custom' => [
                            ['name' => 'hr.permissions.approve', 'label' => 'اعتماد', 'label_en' => 'Approve'],
                            ['name' => 'hr.permissions.reject', 'label' => 'رفض', 'label_en' => 'Reject'],
                        ],
                    ],
                    [
                        'key' => 'hr_payrolls',
                        'name' => 'Payroll & Wages',
                        'name_ar' => 'مسيرات الرواتب والأجور',
                        'icon' => 'bx-money',
                        'view' => 'hr.payrolls.view',
                        'create' => 'hr.payrolls.create',
                        'edit' => 'hr.payrolls.edit',
                        'delete' => 'hr.payrolls.delete',
                        'custom' => [
                            ['name' => 'hr.payrolls.recalculate', 'label' => 'إعادة احتساب', 'label_en' => 'Recalculate'],
                            ['name' => 'hr.payrolls.approve', 'label' => 'اعتماد', 'label_en' => 'Approve'],
                            ['name' => 'hr.payrolls.disburse', 'label' => 'صرف', 'label_en' => 'Disburse'],
                            ['name' => 'hr.payrolls.cancel', 'label' => 'إلغاء', 'label_en' => 'Cancel'],
                            ['name' => 'hr.payrolls.export-wps', 'label' => 'ملف WPS', 'label_en' => 'Export WPS'],
                        ],
                    ],
                    [
                        'key' => 'hr_loans',
                        'name' => 'Loans & Advances',
                        'name_ar' => 'السلف والقروض الشخصية',
                        'icon' => 'bx-credit-card',
                        'view' => 'hr.loans.view',
                        'create' => 'hr.loans.create',
                        'edit' => 'hr.loans.edit',
                        'delete' => 'hr.loans.delete',
                        'custom' => [
                            ['name' => 'hr.loans.approve', 'label' => 'اعتماد', 'label_en' => 'Approve'],
                            ['name' => 'hr.loans.reject', 'label' => 'رفض', 'label_en' => 'Reject'],
                            ['name' => 'hr.loans.disburse', 'label' => 'صرف', 'label_en' => 'Disburse'],
                            ['name' => 'hr.loans.postpone', 'label' => 'تأجيل قسط', 'label_en' => 'Postpone'],
                            ['name' => 'hr.loans.pay-installment', 'label' => 'سداد قسط', 'label_en' => 'Pay Installment'],
                        ],
                    ],
                    [
                        'key' => 'hr_rewards',
                        'name' => 'Rewards & Incentives',
                        'name_ar' => 'المكافآت والحوافز التقديرية',
                        'icon' => 'bx-gift',
                        'view' => 'hr.rewards.view',
                        'create' => 'hr.rewards.create',
                        'edit' => 'hr.rewards.edit',
                        'delete' => 'hr.rewards.delete',
                        'custom' => [
                            ['name' => 'hr.rewards.approve', 'label' => 'اعتماد', 'label_en' => 'Approve'],
                            ['name' => 'hr.rewards.reject', 'label' => 'رفض', 'label_en' => 'Reject'],
                            ['name' => 'hr.rewards.pay', 'label' => 'صرف', 'label_en' => 'Pay'],
                        ],
                    ],
                    [
                        'key' => 'hr_penalties',
                        'name' => 'Penalties & Deductions',
                        'name_ar' => 'الجزاءات والخصومات الإدارية',
                        'icon' => 'bx-error-circle',
                        'view' => 'hr.penalties.view',
                        'create' => 'hr.penalties.create',
                        'edit' => 'hr.penalties.edit',
                        'delete' => 'hr.penalties.delete',
                        'custom' => [
                            ['name' => 'hr.penalties.approve', 'label' => 'اعتماد', 'label_en' => 'Approve'],
                            ['name' => 'hr.penalties.deduct', 'label' => 'تنفيذ الخصم', 'label_en' => 'Deduct'],
                            ['name' => 'hr.penalties.cancel', 'label' => 'إلغاء', 'label_en' => 'Cancel'],
                        ],
                    ],
                    [
                        'key' => 'hr_workforce_plans',
                        'name' => 'Workforce Plans',
                        'name_ar' => 'خطط القوى العاملة والتعيين',
                        'icon' => 'bx-line-chart',
                        'view' => 'hr.workforce-plans.view',
                        'create' => 'hr.workforce-plans.create',
                        'edit' => 'hr.workforce-plans.edit',
                        'delete' => 'hr.workforce-plans.delete',
                        'custom' => [
                            ['name' => 'hr.workforce-plans.submit', 'label' => 'إرسال', 'label_en' => 'Submit'],
                            ['name' => 'hr.workforce-plans.approve', 'label' => 'اعتماد', 'label_en' => 'Approve'],
                            ['name' => 'hr.workforce-plans.activate', 'label' => 'تفعيل', 'label_en' => 'Activate'],
                            ['name' => 'hr.workforce-plans.close', 'label' => 'إغلاق', 'label_en' => 'Close'],
                            ['name' => 'hr.workforce-plans.recalculate', 'label' => 'إعادة حساب', 'label_en' => 'Recalculate'],
                        ],
                    ],
                    [
                        'key' => 'hr_job_requisitions',
                        'name' => 'Job Requisitions',
                        'name_ar' => 'طلبات الاحتياج الوظيفي',
                        'icon' => 'bx-file',
                        'view' => 'hr.job-requisitions.view',
                        'create' => 'hr.job-requisitions.create',
                        'edit' => 'hr.job-requisitions.edit',
                        'delete' => 'hr.job-requisitions.delete',
                        'custom' => [
                            ['name' => 'hr.job-requisitions.submit', 'label' => 'إرسال', 'label_en' => 'Submit'],
                            ['name' => 'hr.job-requisitions.approve', 'label' => 'اعتماد', 'label_en' => 'Approve'],
                            ['name' => 'hr.job-requisitions.reject', 'label' => 'رفض', 'label_en' => 'Reject'],
                            ['name' => 'hr.job-requisitions.start-recruitment', 'label' => 'بدء الاستقطاب', 'label_en' => 'Start Recruitment'],
                            ['name' => 'hr.job-requisitions.cancel', 'label' => 'إلغاء', 'label_en' => 'Cancel'],
                        ],
                    ],
                    [
                        'key' => 'hr_job_openings',
                        'name' => 'Job Openings',
                        'name_ar' => 'إعلانات الوظائف الشاغرة',
                        'icon' => 'bx-briefcase',
                        'view' => 'hr.job-openings.view',
                        'create' => 'hr.job-openings.create',
                        'edit' => 'hr.job-openings.edit',
                        'delete' => 'hr.job-openings.delete',
                        'custom' => [
                            ['name' => 'hr.job-openings.publish', 'label' => 'نشر', 'label_en' => 'Publish'],
                            ['name' => 'hr.job-openings.close', 'label' => 'إغلاق التقديم', 'label_en' => 'Close'],
                        ],
                    ],
                    [
                        'key' => 'hr_job_applications',
                        'name' => 'Candidate Pipeline',
                        'name_ar' => 'طلبات التوظيف والمرشحين',
                        'icon' => 'bx-user-check',
                        'view' => 'hr.job-applications.view',
                        'create' => 'hr.job-applications.create',
                        'edit' => 'hr.job-applications.edit',
                        'delete' => 'hr.job-applications.delete',
                        'custom' => [
                            ['name' => 'hr.job-applications.update-stage', 'label' => 'تغيير المرحلة', 'label_en' => 'Update Stage'],
                            ['name' => 'hr.job-applications.reject', 'label' => 'رفض', 'label_en' => 'Reject'],
                            ['name' => 'hr.job-applications.reopen', 'label' => 'إعادة فتح', 'label_en' => 'Reopen'],
                            ['name' => 'hr.job-applications.download-resume', 'label' => 'تحميل السيرة', 'label_en' => 'Download CV'],
                        ],
                    ],
                    [
                        'key' => 'hr_interviews',
                        'name' => 'Interviews & Evaluations',
                        'name_ar' => 'المقابلات وتقييم المرشحين',
                        'icon' => 'bx-conversation',
                        'view' => 'hr.interviews.view',
                        'create' => 'hr.interviews.create',
                        'edit' => 'hr.interviews.edit',
                        'delete' => 'hr.interviews.delete',
                        'custom' => [
                            ['name' => 'hr.interviews.evaluate', 'label' => 'تقييم', 'label_en' => 'Evaluate'],
                            ['name' => 'hr.interviews.cancel', 'label' => 'إلغاء', 'label_en' => 'Cancel'],
                        ],
                    ],
                    [
                        'key' => 'hr_job_offers',
                        'name' => 'Job Offers',
                        'name_ar' => 'عروض العمل والتعاقد',
                        'icon' => 'bx-badge-check',
                        'view' => 'hr.job-offers.view',
                        'create' => 'hr.job-offers.create',
                        'edit' => 'hr.job-offers.edit',
                        'delete' => 'hr.job-offers.delete',
                        'custom' => [
                            ['name' => 'hr.job-offers.submit', 'label' => 'إرسال للاعتماد', 'label_en' => 'Submit'],
                            ['name' => 'hr.job-offers.approve', 'label' => 'اعتماد', 'label_en' => 'Approve'],
                            ['name' => 'hr.job-offers.send', 'label' => 'إرسال للمرشح', 'label_en' => 'Send'],
                            ['name' => 'hr.job-offers.accept', 'label' => 'قبول', 'label_en' => 'Accept'],
                            ['name' => 'hr.job-offers.decline', 'label' => 'رفض', 'label_en' => 'Decline'],
                            ['name' => 'hr.job-offers.cancel', 'label' => 'إلغاء', 'label_en' => 'Cancel'],
                        ],
                    ],
                    [
                        'key' => 'hr_onboardings',
                        'name' => 'Onboardings & Hired',
                        'name_ar' => 'التهيئة ومباشرة العمل',
                        'icon' => 'bx-user-plus',
                        'view' => 'hr.onboardings.view',
                        'create' => 'hr.onboardings.create',
                        'edit' => 'hr.onboardings.edit',
                        'delete' => 'hr.onboardings.delete',
                        'custom' => [
                            ['name' => 'hr.onboardings.tasks', 'label' => 'إدارة المهام', 'label_en' => 'Tasks'],
                            ['name' => 'hr.onboardings.convert', 'label' => 'تحويل لموظف', 'label_en' => 'Convert'],
                        ],
                    ],
                    [
                        'key' => 'hr_reports_attendance',
                        'name' => 'Attendance & Time Reports',
                        'name_ar' => 'تقارير الحضور والدوام',
                        'icon' => 'bx-bar-chart-alt-2',
                        'view' => 'hr.reports.attendance.view',
                        'custom' => [
                            ['name' => 'hr.reports.attendance.export', 'label' => 'تصدير إكسيل', 'label_en' => 'Export'],
                            ['name' => 'hr.reports.attendance.print', 'label' => 'طباعة التقرير', 'label_en' => 'Print'],
                        ],
                    ],
                ],
            ],
            [
                'module' => 'HR Settings',
                'module_ar' => 'إعدادات الموارد البشرية',
                'icon' => 'bx-slider-alt',
                'screens' => [
                    [
                        'key' => 'hr_settings_administrations',
                        'name' => 'Administrations',
                        'name_ar' => 'الإدارات العامة',
                        'icon' => 'bx-building',
                        'view' => 'hr.settings.administrations.view',
                        'create' => 'hr.settings.administrations.create',
                        'edit' => 'hr.settings.administrations.edit',
                        'delete' => 'hr.settings.administrations.delete',
                    ],
                    [
                        'key' => 'hr_settings_departments',
                        'name' => 'Departments',
                        'name_ar' => 'الأقسام الإدارية',
                        'icon' => 'bx-sitemap',
                        'view' => 'hr.settings.departments.view',
                        'create' => 'hr.settings.departments.create',
                        'edit' => 'hr.settings.departments.edit',
                        'delete' => 'hr.settings.departments.delete',
                    ],
                    [
                        'key' => 'hr_settings_job_titles',
                        'name' => 'Job Titles',
                        'name_ar' => 'المسميات الوظيفية',
                        'icon' => 'bx-id-card',
                        'view' => 'hr.settings.job-titles.view',
                        'create' => 'hr.settings.job-titles.create',
                        'edit' => 'hr.settings.job-titles.edit',
                        'delete' => 'hr.settings.job-titles.delete',
                    ],
                    [
                        'key' => 'hr_settings_allowance_types',
                        'name' => 'Allowance Types',
                        'name_ar' => 'أنواع البدلات والاستحقاقات',
                        'icon' => 'bx-plus-circle',
                        'view' => 'hr.settings.allowance-types.view',
                        'create' => 'hr.settings.allowance-types.create',
                        'edit' => 'hr.settings.allowance-types.edit',
                        'delete' => 'hr.settings.allowance-types.delete',
                    ],
                    [
                        'key' => 'hr_settings_document_types',
                        'name' => 'Document Types',
                        'name_ar' => 'أنواع الوثائق والمستندات',
                        'icon' => 'bx-folder',
                        'view' => 'hr.settings.document-types.view',
                        'create' => 'hr.settings.document-types.create',
                        'edit' => 'hr.settings.document-types.edit',
                        'delete' => 'hr.settings.document-types.delete',
                    ],
                    [
                        'key' => 'hr_settings_reward_types',
                        'name' => 'Reward Types',
                        'name_ar' => 'أنواع المكافآت والحوافز',
                        'icon' => 'bx-gift',
                        'view' => 'hr.settings.reward-types.view',
                        'create' => 'hr.settings.reward-types.create',
                        'edit' => 'hr.settings.reward-types.edit',
                        'delete' => 'hr.settings.reward-types.delete',
                    ],
                    [
                        'key' => 'hr_settings_penalty_types',
                        'name' => 'Penalty Types',
                        'name_ar' => 'أنواع الجزاءات والخصومات',
                        'icon' => 'bx-error',
                        'view' => 'hr.settings.penalty-types.view',
                        'create' => 'hr.settings.penalty-types.create',
                        'edit' => 'hr.settings.penalty-types.edit',
                        'delete' => 'hr.settings.penalty-types.delete',
                    ],
                    [
                        'key' => 'hr_settings_shifts',
                        'name' => 'Shifts',
                        'name_ar' => 'ورديات وفترات العمل',
                        'icon' => 'bx-time',
                        'view' => 'hr.settings.shifts.view',
                        'create' => 'hr.settings.shifts.create',
                        'edit' => 'hr.settings.shifts.edit',
                        'delete' => 'hr.settings.shifts.delete',
                    ],
                    [
                        'key' => 'hr_settings_work_schedules',
                        'name' => 'Work Schedules',
                        'name_ar' => 'جداول العمل الأسبوعية',
                        'icon' => 'bx-calendar',
                        'view' => 'hr.settings.work-schedules.view',
                        'create' => 'hr.settings.work-schedules.create',
                        'edit' => 'hr.settings.work-schedules.edit',
                        'delete' => 'hr.settings.work-schedules.delete',
                    ],
                    [
                        'key' => 'hr_settings_official_holidays',
                        'name' => 'Official Holidays',
                        'name_ar' => 'العطلات والإجازات الرسمية',
                        'icon' => 'bx-calendar-star',
                        'view' => 'hr.settings.official-holidays.view',
                        'create' => 'hr.settings.official-holidays.create',
                        'edit' => 'hr.settings.official-holidays.edit',
                        'delete' => 'hr.settings.official-holidays.delete',
                    ],
                    [
                        'key' => 'hr_settings_policies',
                        'name' => 'HR Policies',
                        'name_ar' => 'سياسات وقوانين العمل',
                        'icon' => 'bx-check-shield',
                        'view' => 'hr.settings.policies.view',
                        'edit' => 'hr.settings.policies.edit',
                        'custom' => [
                            ['name' => 'hr.settings.policies.reset', 'label' => 'إعادة تعيين', 'label_en' => 'Reset'],
                        ],
                    ],
                ],
            ],
            [
                'module' => 'System Settings & Access Control',
                'module_ar' => 'إعدادات النظام وإدارة الوصول',
                'icon' => 'bx-cog',
                'screens' => [
                    [
                        'key' => 'users',
                        'name' => 'User Management',
                        'name_ar' => 'إدارة المستخدمين',
                        'icon' => 'bx-user',
                        'view' => 'users.view',
                        'create' => 'users.create',
                        'edit' => 'users.edit',
                        'delete' => 'users.delete',
                        'custom' => [
                            ['name' => 'users.toggle-status', 'label' => 'تغيير الحالة', 'label_en' => 'Toggle Status'],
                        ],
                    ],
                    [
                        'key' => 'profile',
                        'name' => 'My Profile',
                        'name_ar' => 'الملف الشخصي',
                        'icon' => 'bx-user-circle',
                        'view' => 'profile.view',
                        'edit' => 'profile.edit',
                    ],
                    [
                        'key' => 'settings_company',
                        'name' => 'Company Settings',
                        'name_ar' => 'إعدادات الشركة وبياناتها',
                        'icon' => 'bx-building',
                        'view' => 'settings.company.view',
                        'edit' => 'settings.company.edit',
                    ],
                    [
                        'key' => 'settings_roles',
                        'name' => 'Roles & Permissions',
                        'name_ar' => 'الأدوار والصلاحيات',
                        'icon' => 'bx-shield-quarter',
                        'view' => 'settings.roles.view',
                        'create' => 'settings.roles.create',
                        'edit' => 'settings.roles.edit',
                        'delete' => 'settings.roles.delete',
                        'custom' => [
                            ['name' => 'settings.roles.assign-permissions', 'label' => 'إسناد الصلاحيات', 'label_en' => 'Assign Permissions'],
                        ],
                    ],
                    [
                        'key' => 'settings_translations',
                        'name' => 'Translation Manager',
                        'name_ar' => 'إدارة اللغات والترجمة',
                        'icon' => 'bx-globe',
                        'view' => 'settings.translations.view',
                        'create' => 'settings.translations.create',
                        'edit' => 'settings.translations.edit',
                        'delete' => 'settings.translations.delete',
                    ],
                    [
                        'key' => 'settings_print_templates',
                        'name' => 'Print Settings',
                        'name_ar' => 'إعدادات الطباعة',
                        'icon' => 'bx-printer',
                        'view' => 'settings.print-templates.view',
                        'create' => 'settings.print-templates.create',
                        'edit' => 'settings.print-templates.edit',
                        'delete' => 'settings.print-templates.delete',
                    ],
                    [
                        'key' => 'settings_general_governments',
                        'name' => 'Governments',
                        'name_ar' => 'المحافظات والمناطق',
                        'icon' => 'bx-map-pin',
                        'view' => 'settings.general.governments.view',
                        'create' => 'settings.general.governments.create',
                        'edit' => 'settings.general.governments.edit',
                        'delete' => 'settings.general.governments.delete',
                    ],
                    [
                        'key' => 'settings_general_nationalities',
                        'name' => 'Nationalities',
                        'name_ar' => 'الجنسيات',
                        'icon' => 'bx-flag',
                        'view' => 'settings.general.nationalities.view',
                        'create' => 'settings.general.nationalities.create',
                        'edit' => 'settings.general.nationalities.edit',
                        'delete' => 'settings.general.nationalities.delete',
                    ],
                    [
                        'key' => 'settings_general_religions',
                        'name' => 'Religions',
                        'name_ar' => 'الديانات',
                        'icon' => 'bx-book-open',
                        'view' => 'settings.general.religions.view',
                        'create' => 'settings.general.religions.create',
                        'edit' => 'settings.general.religions.edit',
                        'delete' => 'settings.general.religions.delete',
                    ],
                    [
                        'key' => 'settings_general_marital_statuses',
                        'name' => 'Marital Statuses',
                        'name_ar' => 'الحالات الاجتماعية',
                        'icon' => 'bx-group',
                        'view' => 'settings.general.marital-statuses.view',
                        'create' => 'settings.general.marital-statuses.create',
                        'edit' => 'settings.general.marital-statuses.edit',
                        'delete' => 'settings.general.marital-statuses.delete',
                    ],
                    [
                        'key' => 'settings_general_banks',
                        'name' => 'Banks',
                        'name_ar' => 'البنوك والمصارف',
                        'icon' => 'bx-bank',
                        'view' => 'settings.general.banks.view',
                        'create' => 'settings.general.banks.create',
                        'edit' => 'settings.general.banks.edit',
                        'delete' => 'settings.general.banks.delete',
                    ],
                    [
                        'key' => 'settings_general_bank_branches',
                        'name' => 'Bank Branches',
                        'name_ar' => 'فروع البنوك',
                        'icon' => 'bx-git-branch',
                        'view' => 'settings.general.bank-branches.view',
                        'create' => 'settings.general.bank-branches.create',
                        'edit' => 'settings.general.bank-branches.edit',
                        'delete' => 'settings.general.bank-branches.delete',
                    ],
                ],
            ],
            [
                'module' => 'Audit & System Activity Logs',
                'module_ar' => 'سجل الأنشطة وتدقيق العمليات',
                'icon' => 'bx-history',
                'screens' => [
                    [
                        'key' => 'audit_logs',
                        'name' => 'Audit Logs Activity',
                        'name_ar' => 'سجل الأنشطة والعمليات',
                        'icon' => 'bx-history',
                        'view' => 'audit.view',
                        'custom' => [
                            ['name' => 'audit.export', 'label' => 'تصدير السجلات', 'label_en' => 'Export Logs'],
                        ],
                    ],
                ],
            ],
            [
                'module' => 'Accounting & Finance',
                'module_ar' => 'المحاسبة والإدارة المالية',
                'icon' => 'bx-calculator',
                'screens' => [
                    [
                        'key' => 'accounting_dashboard',
                        'name' => 'Accounting Dashboard',
                        'name_ar' => 'لوحة المؤشرات والقيادة المالية',
                        'icon' => 'bx-line-chart',
                        'view' => 'accounting.dashboard.view',
                    ],
                    [
                        'key' => 'accounting_entities',
                        'name' => 'Accounting Entities',
                        'name_ar' => 'الكيانات والشركات المحاسبية',
                        'icon' => 'bx-buildings',
                        'view' => 'accounting.entities.view',
                        'create' => 'accounting.entities.create',
                        'edit' => 'accounting.entities.edit',
                        'delete' => 'accounting.entities.delete',
                    ],
                    [
                        'key' => 'accounting_fiscal_years',
                        'name' => 'Fiscal Years',
                        'name_ar' => 'السنوات المالية',
                        'icon' => 'bx-calendar',
                        'view' => 'accounting.fiscal-years.view',
                        'create' => 'accounting.fiscal-years.create',
                        'edit' => 'accounting.fiscal-years.edit',
                        'delete' => 'accounting.fiscal-years.delete',
                        'custom' => [
                            ['name' => 'accounting.fiscal-years.toggle-status', 'label' => 'إقفال/تغيير الحالة', 'label_en' => 'Close / Toggle Status'],
                        ],
                    ],
                    [
                        'key' => 'accounting_lock_dates',
                        'name' => 'Lock Dates & Exception',
                        'name_ar' => 'تواريخ الإقفال وحماية الفترات',
                        'icon' => 'bx-lock-alt',
                        'view' => 'accounting.lock-dates.view',
                        'edit' => 'accounting.lock-dates.edit',
                        'custom' => [
                            ['name' => 'accounting.lock-dates.exceptions.create', 'label' => 'منح استثناء مؤقت', 'label_en' => 'Grant Exception'],
                            ['name' => 'accounting.lock-dates.exceptions.revoke', 'label' => 'إلغاء الاستثناء', 'label_en' => 'Revoke Exception'],
                        ],
                    ],
                    [
                        'key' => 'accounting_cost_centers',
                        'name' => 'Cost Centers Tree',
                        'name_ar' => 'شجرة مراكز التكلفة',
                        'icon' => 'bx-sitemap',
                        'view' => 'accounting.cost-centers.view',
                        'create' => 'accounting.cost-centers.create',
                        'edit' => 'accounting.cost-centers.edit',
                        'delete' => 'accounting.cost-centers.delete',
                        'custom' => [
                            ['name' => 'accounting.cost-centers.toggle-status', 'label' => 'تفعيل/تعطيل', 'label_en' => 'Toggle Status'],
                        ],
                    ],
                    [
                        'key' => 'accounting_journals',
                        'name' => 'Journals',
                        'name_ar' => 'دفاتر اليومية',
                        'icon' => 'bx-book',
                        'view' => 'accounting.journals.view',
                        'create' => 'accounting.journals.create',
                        'edit' => 'accounting.journals.edit',
                        'delete' => 'accounting.journals.delete',
                        'custom' => [
                            ['name' => 'accounting.journals.toggle-status', 'label' => 'تفعيل/تعطيل', 'label_en' => 'Toggle Status'],
                        ],
                    ],
                    [
                        'key' => 'accounting_cash_accounts',
                        'name' => 'Cash & Bank Accounts',
                        'name_ar' => 'الخزائن والحسابات البنكية',
                        'icon' => 'bx-wallet',
                        'view' => 'accounting.cash-accounts.view',
                        'create' => 'accounting.cash-accounts.create',
                        'edit' => 'accounting.cash-accounts.edit',
                        'delete' => 'accounting.cash-accounts.delete',
                        'custom' => [
                            ['name' => 'accounting.cash-accounts.toggle-status', 'label' => 'تفعيل/تعطيل', 'label_en' => 'Toggle Status'],
                        ],
                    ],
                    [
                        'key' => 'accounting_accounts',
                        'name' => 'Chart of Accounts',
                        'name_ar' => 'دليل وشجرة الحسابات',
                        'icon' => 'bx-folder',
                        'view' => 'accounting.accounts.view',
                        'create' => 'accounting.accounts.create',
                        'edit' => 'accounting.accounts.edit',
                        'delete' => 'accounting.accounts.delete',
                        'custom' => [
                            ['name' => 'accounting.accounts.toggle-status', 'label' => 'تفعيل/تعطيل', 'label_en' => 'Toggle Status'],
                            ['name' => 'accounting.accounts.export', 'label' => 'تصدير Excel', 'label_en' => 'Export'],
                            ['name' => 'accounting.accounts.import', 'label' => 'استيراد دليل', 'label_en' => 'Import'],
                        ],
                    ],
                    [
                        'key' => 'accounting_journal_entries',
                        'name' => 'Journal Entries',
                        'name_ar' => 'قيود اليومية العامة',
                        'icon' => 'bx-receipt',
                        'view' => 'accounting.journal-entries.view',
                        'create' => 'accounting.journal-entries.create',
                        'edit' => 'accounting.journal-entries.edit',
                        'delete' => 'accounting.journal-entries.delete',
                        'custom' => [
                            ['name' => 'accounting.journal-entries.post', 'label' => 'اعتماد وترحيل', 'label_en' => 'Post Entry'],
                            ['name' => 'accounting.journal-entries.cancel', 'label' => 'إلغاء القيد', 'label_en' => 'Cancel Entry'],
                            ['name' => 'accounting.journal-entries.reverse', 'label' => 'عكس القيد المرحل', 'label_en' => 'Reverse Entry'],
                        ],
                    ],
                    [
                        'key' => 'accounting_opening_balances',
                        'name' => 'Opening Balances',
                        'name_ar' => 'الأرصدة الافتتاحية',
                        'icon' => 'bx-hourglass',
                        'view' => 'accounting.opening-balances.view',
                        'create' => 'accounting.opening-balances.create',
                        'custom' => [
                            ['name' => 'accounting.opening-balances.reopen', 'label' => 'إعادة فتح', 'label_en' => 'Reopen'],
                            ['name' => 'accounting.opening-balances.print', 'label' => 'طباعة', 'label_en' => 'Print'],
                        ],
                    ],
                    [
                        'key' => 'accounting_bank_reconciliations',
                        'name' => 'Bank Reconciliations',
                        'name_ar' => 'التسويات والمطابقات البنكية',
                        'icon' => 'bx-check-double',
                        'view' => 'accounting.bank-reconciliations.view',
                        'create' => 'accounting.bank-reconciliations.create',
                        'edit' => 'accounting.bank-reconciliations.edit',
                        'delete' => 'accounting.bank-reconciliations.delete',
                        'custom' => [
                            ['name' => 'accounting.bank-reconciliations.reopen', 'label' => 'إعادة فتح', 'label_en' => 'Reopen'],
                            ['name' => 'accounting.bank-reconciliations.print', 'label' => 'طباعة مذكرة', 'label_en' => 'Print'],
                        ],
                    ],
                    [
                        'key' => 'accounting_payment_matchings',
                        'name' => 'Payment Allocations',
                        'name_ar' => 'مطابقة وتخصيص المدفوعات',
                        'icon' => 'bx-link',
                        'view' => 'accounting.payment-matchings.view',
                        'create' => 'accounting.payment-matchings.create',
                        'delete' => 'accounting.payment-matchings.delete',
                    ],
                    [
                        'key' => 'accounting_reports',
                        'name' => 'Financial Reports',
                        'name_ar' => 'التقارير والقوائم المالية',
                        'icon' => 'bx-bar-chart-alt-2',
                        'view' => 'accounting.reports.view',
                        'custom' => [
                            ['name' => 'accounting.reports.general-ledger', 'label' => 'دفتر الأستاذ', 'label_en' => 'General Ledger'],
                            ['name' => 'accounting.reports.trial-balance', 'label' => 'ميزان المراجعة', 'label_en' => 'Trial Balance'],
                            ['name' => 'accounting.reports.income-statement', 'label' => 'قائمة الدخل', 'label_en' => 'Income Statement'],
                            ['name' => 'accounting.reports.balance-sheet', 'label' => 'الميزانية العمومية', 'label_en' => 'Balance Sheet'],
                            ['name' => 'accounting.reports.cash-flow', 'label' => 'التدفقات النقدية', 'label_en' => 'Cash Flow'],
                            ['name' => 'accounting.reports.cost-center', 'label' => 'أداء مراكز التكلفة', 'label_en' => 'Cost Center Report'],
                            ['name' => 'accounting.reports.export', 'label' => 'تصدير Excel', 'label_en' => 'Export'],
                            ['name' => 'accounting.reports.print', 'label' => 'طباعة تقارير', 'label_en' => 'Print'],
                        ],
                    ],
                    [
                        'key' => 'accounting_audit_logs',
                        'name' => 'Audit Trail & Activity',
                        'name_ar' => 'سجل النشاط والرقابة المالية',
                        'icon' => 'bx-shield-quarter',
                        'view' => 'accounting.audit-logs.view',
                    ],
                    [
                        'key' => 'accounting_ai_assistant',
                        'name' => 'AI Financial Assistant',
                        'name_ar' => 'المساعد المالي والمرشد الذكي',
                        'icon' => 'bx-bot',
                        'view' => 'accounting.ai-assistant.view',
                        'custom' => [
                            ['name' => 'accounting.ai-assistant.ask', 'label' => 'طرح أسئلة وتحليلات', 'label_en' => 'Ask Queries'],
                        ],
                    ],
                    [
                        'key' => 'accounting_guide',
                        'name' => 'User Guide',
                        'name_ar' => 'دليل النظام المحاسبي',
                        'icon' => 'bx-help-circle',
                        'view' => 'accounting.guide.view',
                    ],
                ],
            ],
            [
                'module' => 'Inventory',
                'module_ar' => 'إدارة المخزون والمستودعات',
                'icon' => 'bx-package',
                'screens' => [
                    [
                        'key' => 'inventory_items',
                        'name' => 'Items & Products',
                        'name_ar' => 'الأصناف والمنتجات',
                        'icon' => 'bx-box',
                        'view' => 'inventory.items.view',
                        'create' => 'inventory.items.create',
                        'edit' => 'inventory.items.edit',
                        'delete' => 'inventory.items.delete',
                        'custom' => [
                            ['name' => 'inventory.items.toggle-status', 'label' => 'تفعيل/تعطيل', 'label_en' => 'Toggle Status'],
                        ],
                    ],
                    [
                        'key' => 'inventory_categories',
                        'name' => 'Item Categories',
                        'name_ar' => 'تصنيفات الأصناف',
                        'icon' => 'bx-category',
                        'view' => 'inventory.categories.view',
                        'create' => 'inventory.categories.create',
                        'edit' => 'inventory.categories.edit',
                        'delete' => 'inventory.categories.delete',
                        'custom' => [
                            ['name' => 'inventory.categories.toggle-status', 'label' => 'تفعيل/تعطيل', 'label_en' => 'Toggle Status'],
                        ],
                    ],
                    [
                        'key' => 'inventory_units',
                        'name' => 'Units of Measure',
                        'name_ar' => 'وحدات القياس',
                        'icon' => 'bx-ruler',
                        'view' => 'inventory.units.view',
                        'create' => 'inventory.units.create',
                        'edit' => 'inventory.units.edit',
                        'delete' => 'inventory.units.delete',
                        'custom' => [
                            ['name' => 'inventory.units.toggle-status', 'label' => 'تفعيل/تعطيل', 'label_en' => 'Toggle Status'],
                        ],
                    ],
                    [
                        'key' => 'inventory_warehouses',
                        'name' => 'Warehouses',
                        'name_ar' => 'المستودعات والمخازن',
                        'icon' => 'bx-package',
                        'view' => 'inventory.warehouses.view',
                        'create' => 'inventory.warehouses.create',
                        'edit' => 'inventory.warehouses.edit',
                        'delete' => 'inventory.warehouses.delete',
                        'custom' => [
                            ['name' => 'inventory.warehouses.toggle-status', 'label' => 'تفعيل/تعطيل', 'label_en' => 'Toggle Status'],
                        ],
                    ],
                ],
            ],
            [
                'module' => 'CRM',
                'module_ar' => 'إدارة علاقات العملاء (CRM)',
                'icon' => 'bx-group',
                'screens' => [
                    [
                        'key' => 'crm_leads',
                        'name' => 'Leads',
                        'name_ar' => 'العملاء المحتملين',
                        'icon' => 'bx-user-plus',
                        'view' => 'crm.leads.view',
                        'create' => 'crm.leads.create',
                        'edit' => 'crm.leads.edit',
                        'delete' => 'crm.leads.delete',
                        'custom' => [
                            ['name' => 'crm.leads.export', 'label' => 'تصدير إكسيل', 'label_en' => 'Export'],
                        ],
                    ],
                    [
                        'key' => 'crm_service_requests',
                        'name' => 'Service Requests',
                        'name_ar' => 'طلبات الخدمة والشكاوى',
                        'icon' => 'bx-conversation',
                        'view' => 'crm.service-requests.view',
                        'create' => 'crm.service-requests.create',
                        'edit' => 'crm.service-requests.edit',
                        'delete' => 'crm.service-requests.delete',
                        'custom' => [
                            ['name' => 'crm.service-requests.export', 'label' => 'تصدير إكسيل', 'label_en' => 'Export'],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function normalizeData(array $data): array
    {
        return [
            'name' => trim((string) ($data['name'] ?? '')),
            'guard_name' => $data['guard_name'] ?? config('auth.defaults.guard', 'web'),
        ];
    }
}
