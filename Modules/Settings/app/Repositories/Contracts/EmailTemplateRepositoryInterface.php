<?php

namespace Modules\Settings\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Modules\Settings\Enums\EmailTemplateKey;
use Modules\Settings\Models\EmailTemplate;

interface EmailTemplateRepositoryInterface
{
    /**
     * Get all email templates optionally filtered by module.
     */
    public function getAll(?string $module = null): Collection;

    /**
     * Find a template by its primary key ID.
     */
    public function findById(int $id): ?EmailTemplate;

    /**
     * Find a template by its enum key or code string.
     */
    public function findByKey(string|EmailTemplateKey $key): ?EmailTemplate;

    /**
     * Update an email template subject, body, from_name, and active state.
     */
    public function update(int|EmailTemplate $template, array $data): EmailTemplate;

    /**
     * Toggle active state.
     */
    public function toggleStatus(int|EmailTemplate $template): bool;

    /**
     * Sync and seed templates from the EmailTemplateKey Enum.
     */
    public function syncFromEnum(): void;

    /**
     * Get counts and stats for all modules.
     */
    public function getStats(): array;
}
