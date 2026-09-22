<?php

namespace Modules\Settings\Services;

use Illuminate\Database\Eloquent\Collection;
use Modules\Settings\Models\PrintTemplate;
use Modules\Settings\Repositories\Contracts\PrintTemplateRepositoryInterface;

class PrintTemplateService
{
    public function __construct(
        private readonly PrintTemplateRepositoryInterface $repository
    ) {}

    public function getAllTemplates(array $filters = []): Collection
    {
        return $this->repository->getAll($filters);
    }

    public function getTemplateById(int $id): ?PrintTemplate
    {
        return $this->repository->getById($id);
    }

    public function getTemplateByCode(string $code): ?PrintTemplate
    {
        return $this->repository->getByCode($code);
    }

    public function getEffectiveTemplate(?string $code = 'general_report'): ?PrintTemplate
    {
        if ($code) {
            $template = $this->repository->getByCode($code);
            if ($template && $template->is_active) {
                return $template;
            }
        }

        return PrintTemplate::where('is_active', true)->where('is_default', true)->first()
            ?? PrintTemplate::where('is_active', true)->first();
    }

    public function updateTemplate(int $id, array $data): PrintTemplate
    {
        return $this->repository->update($id, $data);
    }

    public function toggleStatus(int $id): bool
    {
        return $this->repository->toggleStatus($id);
    }
}
