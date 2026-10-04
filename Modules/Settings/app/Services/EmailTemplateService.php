<?php

namespace Modules\Settings\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\Settings\Models\EmailTemplate;

class EmailTemplateService
{
    /**
     * Get all email templates grouped or with stats.
     */
    public function getAllTemplates(?string $module = null)
    {
        $query = EmailTemplate::query();
        if ($module && $module !== 'all') {
            $query->where('module', $module);
        }
        return $query->orderBy('module')->orderBy('name_ar')->get();
    }

    /**
     * Get template by ID.
     */
    public function getTemplateById(int $id): ?EmailTemplate
    {
        return EmailTemplate::find($id);
    }

    /**
     * Update an email template.
     */
    public function updateTemplate(int $id, array $data): ?EmailTemplate
    {
        $template = $this->getTemplateById($id);
        if ($template) {
            $template->update($data);
        }
        return $template;
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus(int $id): bool
    {
        $template = $this->getTemplateById($id);
        if (!$template) {
            return false;
        }

        $template->is_active = !$template->is_active;
        $template->save();

        return (bool) $template->is_active;
    }

    /**
     * Get unique modules list with template counts.
     */
    public function getModuleStats(): array
    {
        $all = EmailTemplate::all();
        $stats = [
            'total' => $all->count(),
            'active' => $all->where('is_active', true)->count(),
            'inactive' => $all->where('is_active', false)->count(),
            'modules' => $all->groupBy('module')->map->count()->toArray(),
        ];
        return $stats;
    }

    //-----------------------------------------
    /**
     * Get active template by its unique code.
     */
    public function getTemplate(string $code): ?EmailTemplate
    {
        return EmailTemplate::where('code', $code)->where('is_active', true)->first();
    }

    //-----------------------------------------
    /**
     * Render subject, body and complete HTML for an email event.
     */
    public function render(string $code, array $data = [], ?string $locale = null): ?array
    {
        $template = $this->getTemplate($code);
        if (!$template) {
            return null;
        }

        $locale = $locale ?: app()->getLocale();

        return [
            'template' => $template,
            'subject'  => $template->renderSubject($data, $locale),
            'body'     => $template->renderBody($data, $locale),
            'button'   => $template->renderButton($data, $locale),
            'html'     => $template->renderFullHtml($data, $locale),
        ];
    }

    //-----------------------------------------
    /**
     * Send email using the configured template.
     */
    public function send(string $toEmail, string $code, array $data = [], ?string $locale = null): bool
    {
        try {
            $rendered = $this->render($code, $data, $locale);
            if (!$rendered) {
                Log::info("EmailTemplateService: Template [{$code}] is inactive or not found.");
                return false;
            }

            Mail::html($rendered['html'], function ($message) use ($toEmail, $rendered) {
                $message->to($toEmail)
                    ->subject($rendered['subject']);
            });

            return true;
        } catch (Exception $e) {
            Log::error("EmailTemplateService: Failed to send email [{$code}] to [{$toEmail}]: " . $e->getMessage());
            return false;
        }
    }

    //-----------------------------------------
    /**
     * Send test email for template editor.
     */
    public function sendTest(EmailTemplate $template, string $recipientEmail): bool
    {
        try {
            // Generate sample data based on template variables
            $sampleData = $this->generateSampleData($template);
            $renderedHtml = $template->renderFullHtml($sampleData, app()->getLocale());
            $subject = '[TEST / تجربة] ' . $template->renderSubject($sampleData, app()->getLocale());

            Mail::html($renderedHtml, function ($message) use ($recipientEmail, $subject) {
                $message->to($recipientEmail)
                    ->subject($subject);
            });

            return true;
        } catch (Exception $e) {
            Log::error("EmailTemplateService: Test email failed to [{$recipientEmail}]: " . $e->getMessage());
            return false;
        }
    }

    //-----------------------------------------
    /**
     * Generate sample values for placeholders for live preview.
     */
    public function generateSampleData(EmailTemplate $template): array
    {
        return [
            '{employee_name}'   => 'محمد العتيبي',
            '{task_title}'      => 'تطوير تقرير الإيرادات والتحصيلات الربع سنوية',
            '{task_code}'       => 'TSK-2026-1042',
            '{priority}'        => 'عالية (High)',
            '{status}'          => 'قيد التنفيذ (In Progress)',
            '{due_date}'        => date('Y-m-d', strtotime('+3 days')),
            '{assigned_by}'     => 'م. أحمد خالد (مدير القسم)',
            '{task_url}'        => url('/tasks/tasks'),
            '{note_content}'    => 'يرجى التأكد من تدقيق الأرقام مع الإدارة المالية قبل الاعتماد النهائي.',
            '{note_author}'     => 'سارة القحطاني',
            '{days_overdue}'    => '3',
            '{month_year}'      => date('F Y'),
            '{basic_salary}'    => '12,500.00 ر.س',
            '{allowances}'      => '2,500.00 ر.س',
            '{deductions}'      => '500.00 ر.س',
            '{net_salary}'      => '14,500.00 ر.س',
            '{payslip_url}'     => url('/hr/payroll'),
            '{leave_type}'      => 'إجازة سنوية (Annual Leave)',
            '{start_date}'      => date('Y-m-d', strtotime('+7 days')),
            '{end_date}'        => date('Y-m-d', strtotime('+21 days')),
            '{total_days}'      => '14',
            '{company_name}'    => config('app.name', 'Gate ERP'),
            '{current_year}'    => date('Y'),
            '{system_url}'      => url('/'),
        ];
    }
}
