<?php

namespace Modules\Tasks\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Tasks\Models\Task;
use Modules\Tasks\Models\TaskAttachment;

interface TaskRepositoryInterface
{
    //-----------------------------------------
    public function getAll(array $filters = []): Collection;

    //-----------------------------------------
    public function getDataTableQuery(array $filters = []): Builder;

    //-----------------------------------------
    public function findById(int $id): ?Task;

    //-----------------------------------------
    public function create(array $data): Task;

    //-----------------------------------------
    public function update(int|Task $task, array $data): Task;

    //-----------------------------------------
    public function delete(int|Task $task): bool;

    //-----------------------------------------
    public function updateStatus(int|Task $task, string $status): bool;

    //-----------------------------------------
    public function addAttachment(Task $task, array $fileData): TaskAttachment;

    //-----------------------------------------
    public function deleteAttachment(int $attachmentId): bool;

    //-----------------------------------------
    public function addNote(Task $task, int $userId, string $note): \Modules\Tasks\Models\TaskNote;
}
