<?php

namespace Modules\Tasks\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\HR\Models\Employee;
use Modules\Tasks\Enums\TaskPriority;
use Modules\Tasks\Enums\TaskStatus;
use Modules\Tasks\Http\Requests\StoreTaskRequest;
use Modules\Tasks\Http\Requests\UpdateTaskRequest;
use Modules\Tasks\Models\Task;
use Modules\Tasks\Models\TaskGroup;
use Modules\Tasks\Services\TaskService;
use Yajra\DataTables\Facades\DataTables;

class TaskController extends Controller
{
    //-----------------------------------------
    public function __construct(
        protected TaskService $taskService
    ) {}

    //-----------------------------------------
    public function index(): View
    {
        abort_if(! auth()->user()->can('tasks.view'), 403);

        $taskGroups = TaskGroup::query()->active()->get();
        $employees  = Employee::query()->active()->get();
        $priorities = TaskPriority::cases();
        $statuses   = TaskStatus::cases();

        return view('tasks::tasks.index', compact('taskGroups', 'employees', 'priorities', 'statuses'));
    }

    //-----------------------------------------
    public function data(Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('tasks.view'), 403);

        $query = $this->taskService->getDataTableQuery($request->all());

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->editColumn('title', function (Task $task) {
                return '<div class="d-flex flex-column">
                    <span class="fw-semibold text-heading">' . e($task->title) . '</span>
                    <small class="text-muted text-truncate" style="max-width: 250px;">' . e($task->body) . '</small>
                </div>';
            })
            ->addColumn('task_group_name', function (Task $task) {
                return $task->taskGroup?->name
                    ? '<span class="badge bg-label-info">' . e($task->taskGroup->name) . '</span>'
                    : '<span class="text-muted">-</span>';
            })
            ->addColumn('employee_name', function (Task $task) {
                return $task->employee?->name
                    ? '<div class="d-flex align-items-center gap-2">
                        <i class="icon-base bx bx-user text-muted font-18"></i>
                        <span class="fw-medium">' . e($task->employee->name) . '</span>
                    </div>'
                    : '<span class="text-muted">' . __('Unassigned') . '</span>';
            })
            ->editColumn('priority', function (Task $task) {
                $priority = $task->priority instanceof TaskPriority ? $task->priority : TaskPriority::tryFrom($task->priority);
                if (!$priority) return '-';

                return '<span class="badge ' . $priority->badgeClass() . '">' . $priority->label() . '</span>';
            })
            ->editColumn('task_status', function (Task $task) {
                $status = $task->task_status instanceof TaskStatus ? $task->task_status : TaskStatus::tryFrom($task->task_status);
                if (!$status) return '-';

                return '<span class="badge ' . $status->badgeClass() . '">' . $status->label() . '</span>';
            })
            ->editColumn('completion_date', function (Task $task) {
                return $task->completion_date?->format('Y-m-d') ?? '<span class="text-muted">-</span>';
            })
            ->addColumn('remaining_days', function (Task $task) {
                return $task->remaining_days_badge;
            })
            ->addColumn('attachments_count', function (Task $task) {
                $count = $task->attachments->count();
                if ($count === 0) {
                    return '<span class="text-muted">0</span>';
                }
                return '<span class="badge bg-label-secondary d-inline-flex align-items-center gap-1">
                    <i class="icon-base bx bx-paperclip font-14"></i> ' . $count . '
                </span>';
            })
            ->addColumn('actions', function (Task $task) {
                $user = auth()->user();
                $buttons = '';

                if ($user->can('tasks.view')) {
                    $buttons .= '<a href="' . route('tasks.tasks.show', $task->id) . '" class="btn btn-icon btn-sm btn-outline-info" title="' . __('View Details') . '">
                        <i class="icon-base bx bx-show"></i>
                    </a> ';
                }

                if ($user->can('tasks.edit')) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-task"
                        data-id="' . $task->id . '"
                        title="' . __('Edit') . '">
                        <i class="icon-base bx bx-edit-alt"></i>
                    </button> ';
                }

                if ($user->can('tasks.delete')) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-task"
                        data-id="' . $task->id . '"
                        data-title="' . e($task->title) . '"
                        title="' . __('Delete') . '">
                        <i class="icon-base bx bx-trash"></i>
                    </button>';
                }

                return '<div class="d-inline-flex gap-1">' . ($buttons ?: '<span class="text-muted small">-</span>') . '</div>';
            })
            ->rawColumns(['title', 'task_group_name', 'employee_name', 'priority', 'task_status', 'completion_date', 'remaining_days', 'attachments_count', 'actions'])
            ->make(true);
    }

    //-----------------------------------------
    public function store(StoreTaskRequest $request): RedirectResponse|JsonResponse
    {
        abort_if(! auth()->user()->can('tasks.create'), 403);

        $attachments = $request->file('attachments', []);
        $task = $this->taskService->create($request->validated(), $attachments);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Task created successfully.'),
                'data'    => $task,
            ]);
        }

        return redirect()->route('tasks.tasks.index')->with('success', __('Task created successfully.'));
    }

    //-----------------------------------------
    public function show(int $id): View
    {
        abort_if(! auth()->user()->can('tasks.view'), 403);

        $task = $this->taskService->getById($id);

        if (!$task) {
            abort(404, __('Task not found.'));
        }

        $statuses = TaskStatus::cases();

        return view('tasks::tasks.show', compact('task', 'statuses'));
    }

    //-----------------------------------------
    public function edit(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('tasks.edit'), 403);

        $task = $this->taskService->getById($id);

        if (!$task) {
            return response()->json(['success' => false, 'message' => __('Task not found.')], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'              => $task->id,
                'title_ar'        => $task->translateOrNew('ar')->title,
                'title_en'        => $task->translateOrNew('en')->title,
                'body_ar'         => $task->translateOrNew('ar')->body,
                'body_en'         => $task->translateOrNew('en')->body,
                'task_group_id'   => $task->task_group_id,
                'employee_id'     => $task->employee_id,
                'priority'        => $task->priority instanceof TaskPriority ? $task->priority->value : $task->priority,
                'task_status'     => $task->task_status instanceof TaskStatus ? $task->task_status->value : $task->task_status,
                'start_date'      => $task->start_date?->format('Y-m-d'),
                'completion_date' => $task->completion_date?->format('Y-m-d'),
                'attachments'     => $task->attachments->map(fn ($att) => [
                    'id'            => $att->id,
                    'original_name' => $att->original_name,
                    'file_size'     => $att->formatted_size,
                    'url'           => $att->url,
                    'is_image'      => $att->is_image,
                    'is_pdf'        => $att->is_pdf,
                ]),
            ],
        ]);
    }

    //-----------------------------------------
    public function update(UpdateTaskRequest $request, int $id): RedirectResponse|JsonResponse
    {
        abort_if(! auth()->user()->can('tasks.edit'), 403);

        $task = $this->taskService->getById($id);

        if (!$task) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => __('Task not found.')], 404);
            }
            return redirect()->route('tasks.tasks.index')->with('error', __('Task not found.'));
        }

        $attachments = $request->file('attachments', []);
        $deleteAttachmentIds = $request->input('delete_attachments', []);

        $this->taskService->update($task, $request->validated(), $attachments, $deleteAttachmentIds);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Task updated successfully.'),
            ]);
        }

        return redirect()->route('tasks.tasks.index')->with('success', __('Task updated successfully.'));
    }

    //-----------------------------------------
    public function destroy(Request $request, int $id): RedirectResponse|JsonResponse
    {
        abort_if(! auth()->user()->can('tasks.delete'), 403);

        try {
            $task = $this->taskService->getById($id);

            if (!$task) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => __('Task not found.')], 404);
                }
                return redirect()->route('tasks.tasks.index')->with('error', __('Task not found.'));
            }

            $this->taskService->delete($task);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Task deleted successfully.'),
                ]);
            }

            return redirect()->route('tasks.tasks.index')->with('success', __('Task deleted successfully.'));
        } catch (Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->route('tasks.tasks.index')->with('error', $e->getMessage());
        }
    }

    //-----------------------------------------
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('tasks.toggle-status'), 403);

        $request->validate([
            'status'  => ['required', 'string', 'in:pending,in_progress,on_hold,completed,cancelled'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $task = $this->taskService->getById($id);

        if (!$task) {
            return response()->json(['success' => false, 'message' => __('Task not found.')], 404);
        }

        $this->taskService->updateStatus($task, $request->status, $request->comment);

        return response()->json([
            'success' => true,
            'message' => __('Task status updated successfully.'),
        ]);
    }

    //-----------------------------------------
    public function addNote(Request $request, int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('tasks.toggle-status'), 403);

        $request->validate([
            'note' => ['required', 'string', 'max:5000'],
        ]);

        $task = $this->taskService->getById($id);

        if (!$task) {
            return response()->json(['success' => false, 'message' => __('Task not found.')], 404);
        }

        $note = $this->taskService->addNote($task, $request->note);

        return response()->json([
            'success' => true,
            'message' => __('Note added successfully.'),
            'data'    => [
                'id'          => $note->id,
                'note'        => $note->note,
                'author_name' => $note->author_name,
                'avatar_url'  => $note->user?->avatar_url,
                'created_at'  => $note->created_at?->diffForHumans() ?? __('Just now'),
            ],
        ]);
    }

    //-----------------------------------------
    public function deleteAttachment(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('tasks.edit'), 403);

        $deleted = $this->taskService->deleteAttachment($id);

        return response()->json([
            'success' => $deleted,
            'message' => $deleted ? __('Attachment deleted successfully.') : __('Attachment not found.'),
        ]);
    }
}
