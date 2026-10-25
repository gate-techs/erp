<?php

namespace Modules\Settings\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\Settings\Enums\EmailTemplateKey;
use Modules\Settings\Models\EmailTemplate;
use Modules\Settings\Repositories\Contracts\EmailTemplateRepositoryInterface;

class EmailTemplateRepository implements EmailTemplateRepositoryInterface
{
    /**
     * Get all email templates optionally filtered by module.
     */
    public function getAll(?string $module = null): Collection
    {
        $query = EmailTemplate::query();

        if ($module && $module !== 'all') {
            $query->where('module', $module);
        }

        return $query->orderBy('module')->orderBy('id')->get();
    }

    /**
     * Find a template by its primary key ID.
     */
    public function findById(int $id): ?EmailTemplate
    {
        return EmailTemplate::find($id);
    }

    /**
     * Find a template by its enum key or code string.
     */
    public function findByKey(string|EmailTemplateKey $key): ?EmailTemplate
    {
        $code = $key instanceof EmailTemplateKey ? $key->value : $key;

        return EmailTemplate::where('code', $code)->first();
    }

    /**
     * Update an email template.
     */
    public function update(int|EmailTemplate $template, array $data): EmailTemplate
    {
        $model = $template instanceof EmailTemplate ? $template : $this->findById($template);

        if (!$model) {
            throw new \InvalidArgumentException("Email template not found.");
        }

        $model->update($data);

        return $model;
    }

    /**
     * Toggle active state.
     */
    public function toggleStatus(int|EmailTemplate $template): bool
    {
        $model = $template instanceof EmailTemplate ? $template : $this->findById($template);

        if (!$model) {
            return false;
        }

        $model->is_active = !$model->is_active;
        $model->save();

        return (bool) $model->is_active;
    }

    /**
     * Sync and seed templates from the EmailTemplateKey Enum.
     */
    public function syncFromEnum(): void
    {
        foreach (EmailTemplateKey::cases() as $case) {
            $default = $case->defaultContent();

            EmailTemplate::firstOrCreate(
                ['code' => $case->value],
                [
                    'module'      => $case->module(),
                    'name'        => $case->label(),
                    'name_ar'     => $case->label(),
                    'subject_ar'  => $default['subject_ar'],
                    'subject_en'  => $default['subject_en'],
                    'body_ar'     => $default['body_ar'],
                    'body_en'     => $default['body_en'],
                    'is_active'   => true,
                    'is_system'   => true,
                ]
            );
        }
    }

    /**
     * Get counts and stats for all modules.
     */
    public function getStats(): array
    {
        $all = EmailTemplate::all();

        return [
            'total'       => $all->count(),
            'active'      => $all->where('is_active', true)->count(),
            'inactive'    => $all->where('is_active', false)->count(),
            'by_module'   => $all->groupBy('module')->map->count()->toArray(),
        ];
    }
}
