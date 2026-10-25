<?php

namespace Modules\Settings\Services\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Modules\Settings\Enums\EmailTemplateKey;
use Modules\Settings\Models\EmailTemplate;

interface EmailTemplateServiceInterface
{
    /**
     * Get templates for view listing with optional module filtering.
     */
    public function getAllTemplates(?string $module = null): Collection;

    /**
     * Get statistics on template counts and activations.
     */
    public function getModuleStats(): array;

    /**
     * Get template by ID.
     */
    public function getTemplateById(int $id): ?EmailTemplate;

    /**
     * Update template content and status.
     */
    public function updateTemplate(int $id, array $data): ?EmailTemplate;

    /**
     * Toggle active status.
     */
    public function toggleStatus(int $id): bool;

    /**
     * Send test email to recipient with dummy preview data.
     */
    public function sendTest(EmailTemplate $template, string $recipientEmail): bool;

    /**
     * Send email dynamically using EmailTemplateKey or template code.
     *
     * @param string|EmailTemplateKey $key
     * @param string $toEmail
     * @param array $data Placeholders data ['employee_name' => 'Ahmed']
     * @param string|null $locale 'ar' or 'en'
     */
    public function send(string|EmailTemplateKey $key, string $toEmail, array $data = [], ?string $locale = null): bool;

    /**
     * Sync database records with defined enum cases.
     */
    public function syncTemplates(): void;
}
