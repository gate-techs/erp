<?php

namespace Modules\Settings\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\Settings\Models\PrintTemplate;
use Modules\Settings\Repositories\Contracts\PrintTemplateRepositoryInterface;

class PrintTemplateRepository implements PrintTemplateRepositoryInterface
{
    public function getAll(array $filters = []): Collection
    {
        $query = PrintTemplate::query();

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool)$filters['is_active']);
        }

        return $query->orderBy('id', 'asc')->get();
    }

    public function getById(int $id): ?PrintTemplate
    {
        return PrintTemplate::find($id);
    }

    public function getByCode(string $code): ?PrintTemplate
    {
        return PrintTemplate::where('code', $code)->first();
    }

    public function create(array $data): PrintTemplate
    {
        return PrintTemplate::create($data);
    }

    public function update(int $id, array $data): PrintTemplate
    {
        $template = PrintTemplate::findOrFail($id);
        $template->update($data);
        return $template;
    }

    public function delete(int $id): bool
    {
        $template = PrintTemplate::findOrFail($id);
        return (bool) $template->delete();
    }

    public function toggleStatus(int $id): bool
    {
        $template = PrintTemplate::findOrFail($id);
        $template->is_active = !$template->is_active;
        $template->save();
        return $template->is_active;
    }
}
