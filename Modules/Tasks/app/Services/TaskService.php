<?php

namespace Modules\Tasks\Services;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Tasks\Models\Task;
use Modules\Tasks\Models\TaskNote;
use Modules\Tasks\Notifications\TaskAssignedNotification;
use Modules\Tasks\Notifications\TaskNoteAddedNotification;
use Modules\Tasks\Repositories\Contracts\TaskRepositoryInterface;

class TaskService
{
    //-----------------------------------------
    public function __construct(
        protected TaskRepositoryInterface $repository
    ) {}

    //-----------------------------------------
    public function getAll(array $filters = []): Collection
    {
        return $this->repository->getAll($filters);
    }

    //-----------------------------------------
    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->repository->getDataTableQuery($filters);
    }

    //-----------------------------------------
    public function getById(int $id): ?Task
    {
        return $this->repository->findById($id);
    }

    //-----------------------------------------
    public function create(array $validatedData, array $attachments = []): Task
    {
        return DB::transaction(function () use ($validatedData, $attachments) {
            $payload = $this->formatPayload($validatedData);
            $payload['created_by'] = Auth::id();

            $task = $this->repository->create($payload);

            $this->storeAttachments($task, $attachments);

            $freshTask = $task->fresh(['taskGroup', 'employee.user', 'creator', 'attachments', 'translations', 'statusHistories', 'notes']);

            // Send notification to assigned employee
            $this->notifyAssignedEmployee($freshTask);

            return $freshTask;
        });
    }

    //-----------------------------------------
    public function update(int|Task $task, array $validatedData, array $attachments = [], array $deleteAttachmentIds = []): Task
    {
        return DB::transaction(function () use ($task, $validatedData, $attachments, $deleteAttachmentIds) {
            $payload = $this->formatPayload($validatedData);

            $oldEmployeeId = $task instanceof Task ? $task->employee_id : null;

            $updatedTask = $this->repository->update($task, $payload);

            // Delete requested attachments
            foreach ($deleteAttachmentIds as $attachmentId) {
                $this->repository->deleteAttachment((int) $attachmentId);
            }

            // Upload new attachments
            $this->storeAttachments($updatedTask, $attachments);

            $freshTask = $updatedTask->fresh(['taskGroup', 'employee.user', 'creator', 'attachments', 'translations', 'statusHistories', 'notes']);

            // If newly assigned or reassigned to a different employee, send notification
            if ($oldEmployeeId !== $freshTask->employee_id && $freshTask->employee_id) {
                $this->notifyAssignedEmployee($freshTask);
            }

            return $freshTask;
        });
    }

    //-----------------------------------------
    public function delete(int|Task $task): bool
    {
        return $this->repository->delete($task);
    }

    //-----------------------------------------
    public function updateStatus(int|Task $task, string $status, ?string $comment = null): bool
    {
        return $this->repository->updateStatus($task, $status, $comment);
    }

    //-----------------------------------------
    public function addNote(Task $task, string $noteContent): TaskNote
    {
        $author = Auth::user();
        $userId = $author ? $author->id : $task->created_by;

        $note = $this->repository->addNote($task, (int) $userId, $noteContent);

        // Notify recipient (employee if author is creator/admin, or creator if author is employee)
        $this->notifyNoteAdded($task, $note);

        return $note;
    }

    //-----------------------------------------
    public function deleteAttachment(int $attachmentId): bool
    {
        return $this->repository->deleteAttachment($attachmentId);
    }

    //-----------------------------------------
    protected function formatPayload(array $data): array
    {
        return [
            'ar' => [
                'title' => $data['title_ar'] ?? null,
                'body'  => $data['body_ar'] ?? null,
            ],
            'en' => [
                'title' => $data['title_en'] ?? null,
                'body'  => $data['body_en'] ?? null,
            ],
            'task_group_id'   => $data['task_group_id'] ?? null,
            'employee_id'     => $data['employee_id'] ?? null,
            'priority'        => $data['priority'] ?? 'medium',
            'task_status'     => $data['task_status'] ?? 'pending',
            'start_date'      => $data['start_date'] ?? null,
            'completion_date' => $data['completion_date'] ?? null,
        ];
    }

    //-----------------------------------------
    protected function storeAttachments(Task $task, array $attachments): void
    {
        foreach ($attachments as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $filePath = $file->store('tasks/attachments', 'public');

                $this->repository->addAttachment($task, [
                    'file_path'     => $filePath,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type'     => $file->getClientMimeType(),
                    'file_size'     => $file->getSize(),
                ]);
            }
        }
    }

    //-----------------------------------------
    protected function notifyAssignedEmployee(Task $task): void
    {
        try {
            $user = $task->employee?->user;
            if ($user && $user->email) {
                $user->notify(new TaskAssignedNotification($task));
            }
        } catch (Exception $e) {
            Log::warning('Failed to send TaskAssignedNotification: ' . $e->getMessage());
        }
    }

    //-----------------------------------------
    protected function notifyNoteAdded(Task $task, TaskNote $note): void
    {
        try {
            $author = Auth::user();
            if (!$author) {
                return;
            }

            $recipients = collect();

            // 1. If assigned employee has a user account and is not the author
            if ($task->employee?->user && $task->employee->user->id !== $author->id) {
                $recipients->push($task->employee->user);
            }

            // 2. If creator is not the author
            if ($task->creator && $task->creator->id !== $author->id) {
                $recipients->push($task->creator);
            }

            foreach ($recipients->unique('id') as $recipient) {
                if ($recipient->email) {
                    $recipient->notify(new TaskNoteAddedNotification($task, $note, $author));
                }
            }
        } catch (Exception $e) {
            Log::warning('Failed to send TaskNoteAddedNotification: ' . $e->getMessage());
        }
    }
}
