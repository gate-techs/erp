<?php

namespace Modules\Tasks\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Tasks\Enums\TaskStatus;
use Modules\Tasks\Models\Task;
use Modules\Tasks\Models\TaskAttachment;
use Modules\Tasks\Models\TaskNote;
use Modules\Tasks\Models\TaskStatusHistory;
use Modules\Tasks\Repositories\Contracts\TaskRepositoryInterface;

class TaskRepository implements TaskRepositoryInterface
{
    //-----------------------------------------
    public function getAll(array $filters = []): Collection
    {
        return $this->buildQuery($filters)->get();
    }

    //-----------------------------------------
    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->buildQuery($filters);
    }

    //-----------------------------------------
    public function findById(int $id): ?Task
    {
        return Task::query()
            ->with([
                'taskGroup',
                'employee.user',
                'creator',
                'attachments',
                'translations',
                'statusHistories.user',
                'notes.user',
            ])
            ->find($id);
    }

    //-----------------------------------------
    public function create(array $data): Task
    {
        return DB::transaction(function () use ($data) {
            $task = Task::create($data);

            // Record initial status in history
            TaskStatusHistory::create([
                'task_id'    => $task->id,
                'old_status' => null,
                'new_status' => $task->task_status instanceof TaskStatus ? $task->task_status->value : ($task->task_status ?? 'pending'),
                'changed_by' => Auth::id() ?? $task->created_by,
                'comment'    => __('Initial task creation'),
            ]);

            return $task;
        });
    }

    //-----------------------------------------
    public function update(int|Task $task, array $data): Task
    {
        $taskModel = $task instanceof Task ? $task : Task::findOrFail($task);

        return DB::transaction(function () use ($taskModel, $data) {
            $oldStatus = $taskModel->task_status instanceof TaskStatus ? $taskModel->task_status->value : $taskModel->task_status;
            
            $taskModel->update($data);

            $newStatus = $taskModel->task_status instanceof TaskStatus ? $taskModel->task_status->value : $taskModel->task_status;

            if ($oldStatus !== $newStatus) {
                TaskStatusHistory::create([
                    'task_id'    => $taskModel->id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'changed_by' => Auth::id(),
                    'comment'    => __('Task details updated'),
                ]);
            }

            return $taskModel->fresh(['taskGroup', 'employee.user', 'creator', 'attachments', 'translations', 'statusHistories.user', 'notes.user']);
        });
    }

    //-----------------------------------------
    public function delete(int|Task $task): bool
    {
        $taskModel = $task instanceof Task ? $task : Task::findOrFail($task);

        return DB::transaction(function () use ($taskModel) {
            foreach ($taskModel->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
                $attachment->delete();
            }

            return (bool) $taskModel->delete();
        });
    }

    //-----------------------------------------
    public function updateStatus(int|Task $task, string $status, ?string $comment = null): bool
    {
        $taskModel = $task instanceof Task ? $task : Task::findOrFail($task);

        return DB::transaction(function () use ($taskModel, $status, $comment) {
            $oldStatus = $taskModel->task_status instanceof TaskStatus ? $taskModel->task_status->value : $taskModel->task_status;
            
            $taskModel->task_status = $status;
            if ($status === TaskStatus::COMPLETED->value) {
                $taskModel->completed_at = now();
            } else {
                $taskModel->completed_at = null;
            }

            $saved = (bool) $taskModel->save();

            if ($saved && $oldStatus !== $status) {
                TaskStatusHistory::create([
                    'task_id'    => $taskModel->id,
                    'old_status' => $oldStatus,
                    'new_status' => $status,
                    'changed_by' => Auth::id(),
                    'comment'    => $comment,
                ]);
            }

            return $saved;
        });
    }

    //-----------------------------------------
    public function addAttachment(Task $task, array $fileData): TaskAttachment
    {
        return $task->attachments()->create($fileData);
    }

    //-----------------------------------------
    public function deleteAttachment(int $attachmentId): bool
    {
        $attachment = TaskAttachment::find($attachmentId);
        if (!$attachment) {
            return false;
        }

        Storage::disk('public')->delete($attachment->file_path);

        return (bool) $attachment->delete();
    }

    //-----------------------------------------
    public function addNote(Task $task, int $userId, string $note): TaskNote
    {
        return $task->notes()->create([
            'user_id' => $userId,
            'note'    => $note,
        ]);
    }

    //-----------------------------------------
    protected function buildQuery(array $filters = []): Builder
    {
        $query = Task::query()
            ->with(['taskGroup', 'employee', 'creator', 'translations', 'attachments'])
            ->latest('id');

        // Apply Role-Based Scoping
        $this->applyRoleScope($query);

        if (!empty($filters['task_group_id'])) {
            $query->where('task_group_id', $filters['task_group_id']);
        }

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['department_id'])) {
            $query->where(function (Builder $q) use ($filters) {
                $q->whereHas('employee', fn (Builder $empQ) => $empQ->where('department_id', $filters['department_id']))
                  ->orWhereHas('taskGroup', fn (Builder $grpQ) => $grpQ->where('department_id', $filters['department_id']));
            });
        }

        if (!empty($filters['branch_id'])) {
            $query->whereHas('employee', function (Builder $empQ) use ($filters) {
                $empQ->where('primary_branch_id', $filters['branch_id']);
            });
        }

        if (!empty($filters['is_overdue'])) {
            $query->where('completion_date', '<', now()->startOfDay())
                  ->where('task_status', '!=', TaskStatus::COMPLETED);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (!empty($filters['task_status'])) {
            $query->where('task_status', $filters['task_status']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('completion_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('completion_date', '<=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $search = is_array($filters['search'])
                ? ($filters['search']['value'] ?? null)
                : $filters['search'];

            if (is_string($search) && $search !== '') {
                $query->where(function (Builder $builder) use ($search) {
                    $builder->whereTranslationLike('title', "%{$search}%")
                        ->orWhereTranslationLike('body', "%{$search}%")
                        ->orWhereHas('employee', function (Builder $empQuery) use ($search) {
                            $empQuery->whereTranslationLike('name', "%{$search}%");
                        })
                        ->orWhereHas('taskGroup', function (Builder $groupQuery) use ($search) {
                            $groupQuery->whereTranslationLike('name', "%{$search}%");
                        });
                });
            }
        }

        return $query;
    }

    //-----------------------------------------
    /**
     * Apply Role Scoping:
     * - Admin / TaskManager: Sees all tasks
     * - DeptManager: Sees tasks of their department
     * - Employee: Sees only assigned tasks
     */
    protected function applyRoleScope(Builder $query): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        // 1. Super Admin, Admin, Developer, TaskManager, or users with full permission
        if ($this->isTaskManagerOrAdmin($user)) {
            return; // Can view everything
        }

        // 2. Department Manager
        if ($this->isDepartmentManager($user)) {
            $deptId = $user->employee?->department_id;
            if ($deptId) {
                $query->where(function (Builder $q) use ($deptId, $user) {
                    $q->whereHas('employee', fn (Builder $empQ) => $empQ->where('department_id', $deptId))
                      ->orWhereHas('taskGroup', fn (Builder $grpQ) => $grpQ->where('department_id', $deptId))
                      ->orWhere('created_by', $user->id);
                });
                return;
            }
        }

        // 3. Regular Employee
        $employeeId = $user->employee?->id;
        if ($employeeId) {
            $query->where(function (Builder $q) use ($employeeId, $user) {
                $q->where('employee_id', $employeeId)
                  ->orWhere('created_by', $user->id);
            });
        } else {
            $query->where('created_by', $user->id);
        }
    }

    //-----------------------------------------
    protected function isTaskManagerOrAdmin(User $user): bool
    {
        if ($user->isDeveloper() || $user->hasRole(['super_admin', 'admin', 'TaskManager', 'task_manager'])) {
            return true;
        }

        if ($user->can('tasks.view_all')) {
            return true;
        }

        return false;
    }

    //-----------------------------------------
    protected function isDepartmentManager(User $user): bool
    {
        return $user->hasRole(['DeptManager', 'dept_manager', 'department_manager', 'DepartmentManager']);
    }
}
