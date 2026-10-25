<?php

namespace Modules\Settings\Services;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\Settings\Enums\EmailTemplateKey;
use Modules\Settings\Models\EmailTemplate;
use Modules\Settings\Repositories\Contracts\EmailTemplateRepositoryInterface;
use Modules\Settings\Services\Contracts\EmailTemplateServiceInterface;

class EmailTemplateService implements EmailTemplateServiceInterface
{
    public function __construct(
        protected EmailTemplateRepositoryInterface $repository
    ) {}

    /**
     * Get all email templates grouped or with stats.
     */
    public function getAllTemplates(?string $module = null): Collection
    {
        return $this->repository->getAll($module);
    }

    /**
     * Get stats by module and total.
     */
    public function getModuleStats(): array
    {
        return $this->repository->getStats();
    }

    /**
     * Get template by ID.
     */
    public function getTemplateById(int $id): ?EmailTemplate
    {
        return $this->repository->findById($id);
    }

    /**
     * Update an email template.
     */
    public function updateTemplate(int $id, array $data): ?EmailTemplate
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus(int $id): bool
    {
        return $this->repository->toggleStatus($id);
    }

    /**
     * Sync templates with Enum.
     */
    public function syncTemplates(): void
    {
        $this->repository->syncFromEnum();
    }

    /**
     * Dispatch an email based on a template key or enum instance.
     */
    public function send(string|EmailTemplateKey $key, string $toEmail, array $data = [], ?string $locale = null): bool
    {
        $template = $this->repository->findByKey($key);

        if (!$template || !$template->is_active) {
            Log::warning("EmailTemplateService: Template [{$key}] not found or inactive.");
            return false;
        }

        try {
            $locale = $locale ?: app()->getLocale();
            $renderedHtml = $template->renderFullHtml($data, $locale);
            $subject = $template->renderSubject($data, $locale);

            Mail::html($renderedHtml, function ($message) use ($toEmail, $subject, $template) {
                $message->to($toEmail)->subject($subject);
                if (!empty($template->from_name)) {
                    $message->from(config('mail.from.address'), $template->from_name);
                }
            });

            return true;
        } catch (Exception $e) {
            $codeStr = $key instanceof EmailTemplateKey ? $key->value : $key;
            Log::error("EmailTemplateService: Failed sending email [{$codeStr}] to [{$toEmail}]: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send test email for template editor.
     */
    public function sendTest(EmailTemplate $template, string $recipientEmail): bool
    {
        try {
            $sampleData = $this->generateSampleData($template);
            $locale = app()->getLocale();
            $renderedHtml = $template->renderFullHtml($sampleData, $locale);
            $subject = '[TEST / تجربة] ' . $template->renderSubject($sampleData, $locale);

            Mail::html($renderedHtml, function ($message) use ($recipientEmail, $subject) {
                $message->to($recipientEmail)->subject($subject);
            });

            return true;
        } catch (Exception $e) {
            Log::error("EmailTemplateService: Test email failed to [{$recipientEmail}]: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate sample placeholder values for live preview or testing.
     */
    public function generateSampleData(EmailTemplate $template): array
    {
        return [
            // Generic System
            '{app_name}'        => config('app.name', 'Gate ERP'),
            '{company_name}'    => config('app.name', 'Gate ERP'),
            '{app_url}'         => url('/'),
            '{system_url}'      => url('/'),
            '{current_year}'    => date('Y'),

            // HR & Recruitment
            '{employee_name}'   => 'م. أحمد خالد',
            '{manager_name}'    => 'د. فيصل السعيد (المدير الإداري)',
            '{job_title}'       => 'مهندس برمجيات أول (Senior Software Engineer)',
            '{department}'      => 'إدارة تقنية المعلومات (IT)',
            '{login_email}'     => 'ahmed.khaled@example.com',
            '{login_url}'       => url('/login'),
            '{leave_type}'      => 'إجازة سنوية اعتيادية (Annual Leave)',
            '{start_date}'      => date('Y-m-d', strtotime('+3 days')),
            '{end_date}'        => date('Y-m-d', strtotime('+17 days')),
            '{days_count}'      => '14',
            '{total_days}'      => '14',
            '{status}'          => 'معتمد (Approved)',
            '{rejection_reason}'=> '',
            '{permission_date}' => date('Y-m-d'),
            '{hours_count}'     => '2',
            '{amount}'          => '15,000.00 ر.س',
            '{month_year}'      => date('F Y'),
            '{basic_salary}'    => '14,000.00 ر.س',
            '{allowances}'      => '3,500.00 ر.س',
            '{deductions}'      => '700.00 ر.س',
            '{net_salary}'      => '16,800.00 ر.س',
            '{payslip_url}'     => url('/hr/payroll'),
            '{penalty_title}'   => 'لفت نظر إداري',
            '{penalty_reason}'  => 'التأخر عن موعد الحضور دون إذن مسبق',
            '{deduction_days}'  => 'خصم نصف يوم',
            '{reward_title}'    => 'مكافأة الإنجاز المتميز للربع الأول',
            '{reward_amount}'   => '2,500.00 ر.س',
            '{candidate_name}'  => 'سارة عبد الرحمن',
            '{interview_date}'  => date('Y-m-d', strtotime('+2 days')),
            '{interview_time}'  => '11:00 صباحاً (11:00 AM)',
            '{interview_type}'  => 'مقابلة عن بعد عبر Zoom (Online Video Call)',
            '{location_link}'   => 'https://meet.google.com/sample-interview-room',
            '{salary}'          => '18,500.00 ر.س شهرياً',
            '{expiry_date}'     => date('Y-m-d', strtotime('+7 days')),
            '{offer_url}'       => url('/careers/offers/preview'),

            // Accounting & Finance
            '{entity_name}'     => 'شركة الأفق للاستشارات والتقنية',
            '{receipt_number}'  => 'REC-2026-0089',
            '{payment_method}'  => 'تحويل بنكي - مصرف الراجحي',
            '{payment_date}'    => date('Y-m-d'),
            '{balance_due}'     => '42,600.00 ر.س',
            '{due_date}'        => date('Y-m-d', strtotime('+5 days')),
            '{pay_url}'         => url('/payments/pay-now'),
            '{receipt_url}'     => url('/accounting/receipts/preview'),
            '{approver_name}'   => 'أ. خالد التميمي (المدير المالي)',
            '{entry_number}'    => 'JV-2026-0412',
            '{entry_amount}'    => '125,000.00 ر.س',
            '{created_by}'      => 'سليمان الحربي (محاسب عام)',
            '{entry_url}'       => url('/accounting/journal-entries'),

            // CRM
            '{agent_name}'      => 'عمر الزهراني (مستشار مبيعات)',
            '{lead_name}'       => 'عبد الله الشمري',
            '{lead_phone}'      => '+966 50 123 4567',
            '{lead_source}'     => 'الموقع الإلكتروني (Website Form)',
            '{lead_url}'        => url('/crm/leads'),
            '{client_name}'     => 'مؤسسة الرياض الحديثة',
            '{ticket_number}'   => 'TKT-99482',
            '{ticket_title}'    => 'طلب ربط نقطة بيع إضافية',
            '{ticket_url}'      => url('/crm/service-requests'),

            // Tasks
            '{task_title}'      => 'إعداد وتجهيز التقرير المالي للربع السنوي',
            '{task_code}'       => 'TSK-2026-104',
            '{priority}'        => 'أولوية قصوى (High)',
            '{assigned_by}'     => 'إدارة العمليات',
            '{task_url}'        => url('/tasks/tasks'),

            // Inventory
            '{item_name}'       => 'ماسح باركود لاسلكي صناعي Zebra DS3678',
            '{item_code}'       => 'ITM-SKU-9921',
            '{warehouse_name}'  => 'المستودع المركزي - الرياض',
            '{current_stock}'   => '4 قطع فقط',
            '{reorder_level}'   => '15 قطعة',
            '{item_url}'        => url('/inventory/items'),

            // System
            '{user_name}'       => 'فهد الناصر',
            '{activation_url}'  => url('/setup-password?token=sample_demo_token'),
            '{reset_url}'       => url('/reset-password?token=sample_demo_token'),
            '{action_url}'      => url('/'),
        ];
    }
}
