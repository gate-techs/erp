<?php

namespace Modules\Settings\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Modules\Settings\Models\PrintTemplate;

interface PrintTemplateRepositoryInterface
{
    /**
     * Get all print templates.
     */
    public function getAll(array $filters = []): Collection;

    /**
     * Find a template by ID.
     */
    public function getById(int $id): ?PrintTemplate;

    /**
     * Find a template by code slug.
     */
    public function getByCode(string $code): ?PrintTemplate;

    /**
     * Create a new template.
     */
    public function create(array $data): PrintTemplate;

    /**
     * Update an existing template.
     */
    public function update(int $id, array $data): PrintTemplate;

    /**
     * Delete a template.
     */
    public function delete(int $id): bool;

    /**
     * Toggle active status.
     */
    public function toggleStatus(int $id): bool;
}
