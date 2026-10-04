<?php

namespace Modules\Tasks\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;
use Modules\Tasks\Enums\TaskPriority;
use Modules\Tasks\Enums\TaskStatus;
use Modules\Tasks\Http\Requests\StoreTaskGroupRequest;
use Modules\Tasks\Http\Requests\UpdateTaskGroupRequest;
use Modules\Tasks\Models\TaskGroup;
use Modules\Tasks\Services\TaskGroupService;
use Yajra\DataTables\Facades\DataTables;

class TaskGroupController extends Controller
{
    //-----------------------------------------
    public function __construct(
        protected TaskGroupService $taskGroupService
    ) {}

    //-----------------------------------------
    public function index(): View
    {
        $departments = Department::query()->active()->get();

        return view('tasks::task_groups.index', compact('departments'));
    }

    //-----------------------------------------
    public function data(Request $request): JsonResponse
    {
        $query = $this->taskGroupService->getDataTableQuery($request->all());

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->editColumn('name', function (TaskGroup $group) {
                return '<a href="' . route('tasks.task-groups.show', $group->id) . '" class="fw-semibold text-heading text-primary">' . e($group->name) . '</a>';
            })
            ->addColumn('department_name', function (TaskGroup $group) {
                return $group->department?->name ?? '<span class="text-muted">-</span>';
            })
            ->editColumn('start_date', function (TaskGroup $group) {
                return $group->start_date?->format('Y-m-d') ?? '<span class="text-muted">-</span>';
            })
            ->editColumn('end_date', function (TaskGroup $group) {
                return $group->end_date?->format('Y-m-d') ?? '<span class="text-muted">-</span>';
            })
            ->addColumn('remaining_days', function (TaskGroup $group) {
                return $group->remaining_days_badge;
            })
            ->editColumn('is_active', function (TaskGroup $group) {
                $checked = $group->is_active ? 'checked' : '';
                return '<div class="form-check form-switch d-inline-block">
                    <input class="form-check-input btn-toggle-status" type="checkbox" data-id="' . $group->id . '" ' . $checked . '>
                </div>';
            })
            ->addColumn('actions', function (TaskGroup $group) {
                $buttons = '<div class="d-inline-flex gap-1">';
                
                $buttons .= '<a href="' . route('tasks.task-groups.show', $group->id) . '" class="btn btn-icon btn-sm btn-outline-info" title="' . __('Tasks') . '">
                    <i class="icon-base bx bx-task"></i>
                </a>';

                $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-group"
                    data-id="' . $group->id . '"
                    data-name-ar="' . e($group->translateOrNew('ar')->name ?? '') . '"
                    data-name-en="' . e($group->translateOrNew('en')->name ?? '') . '"
                    data-desc-ar="' . e($group->translateOrNew('ar')->description ?? '') . '"
                    data-desc-en="' . e($group->translateOrNew('en')->description ?? '') . '"
                    data-department-id="' . ($group->department_id ?? '') . '"
                    data-start-date="' . ($group->start_date?->format('Y-m-d') ?? '') . '"
                    data-end-date="' . ($group->end_date?->format('Y-m-d') ?? '') . '"
                    data-is-active="' . ($group->is_active ? '1' : '0') . '"
                    title="' . __('Edit') . '">
                    <i class="icon-base bx bx-edit-alt"></i>
                </button>';

                $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-group"
                    data-id="' . $group->id . '"
                    data-name="' . e($group->name) . '"
                    title="' . __('Delete') . '">
                    <i class="icon-base bx bx-trash"></i>
                </button>';
                $buttons .= '</div>';

                return $buttons;
            })
            ->rawColumns(['name', 'department_name', 'start_date', 'end_date', 'remaining_days', 'is_active', 'actions'])
            ->make(true);
    }

    //-----------------------------------------
    public function store(StoreTaskGroupRequest $request): RedirectResponse|JsonResponse
    {
        $group = $this->taskGroupService->create($request->validated());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Task group created successfully.'),
                'data'    => $group,
            ]);
        }

        return redirect()->route('tasks.task-groups.index')
            ->with('success', __('Task group created successfully.'));
    }

    //-----------------------------------------
    public function show(int $id): View|JsonResponse
    {
        $group = $this->taskGroupService->getById($id);

        if (!$group) {
            abort(404, __('Task group not found.'));
        }

        $employees  = Employee::query()->active()->get();
        $priorities = TaskPriority::cases();
        $statuses   = TaskStatus::cases();

        return view('tasks::task_groups.show', compact('group', 'employees', 'priorities', 'statuses'));
    }

    //-----------------------------------------
    public function edit(int $id): JsonResponse
    {
        $group = $this->taskGroupService->getById($id);

        if (!$group) {
            return response()->json(['success' => false, 'message' => __('Task group not found.')], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'             => $group->id,
                'name_ar'        => $group->translateOrNew('ar')->name,
                'name_en'        => $group->translateOrNew('en')->name,
                'description_ar' => $group->translateOrNew('ar')->description,
                'description_en' => $group->translateOrNew('en')->description,
                'department_id'  => $group->department_id,
                'start_date'     => $group->start_date?->format('Y-m-d'),
                'end_date'       => $group->end_date?->format('Y-m-d'),
                'is_active'      => $group->is_active,
            ],
        ]);
    }

    //-----------------------------------------
    public function update(UpdateTaskGroupRequest $request, int $id): RedirectResponse|JsonResponse
    {
        $group = $this->taskGroupService->getById($id);

        if (!$group) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => __('Task group not found.')], 404);
            }
            return redirect()->route('tasks.task-groups.index')->with('error', __('Task group not found.'));
        }

        $this->taskGroupService->update($group, $request->validated());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Task group updated successfully.'),
            ]);
        }

        return redirect()->route('tasks.task-groups.index')
            ->with('success', __('Task group updated successfully.'));
    }

    //-----------------------------------------
    public function destroy(Request $request, int $id): RedirectResponse|JsonResponse
    {
        try {
            $group = $this->taskGroupService->getById($id);

            if (!$group) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => __('Task group not found.')], 404);
                }
                return redirect()->route('tasks.task-groups.index')->with('error', __('Task group not found.'));
            }

            $this->taskGroupService->delete($group);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Task group deleted successfully.'),
                ]);
            }

            return redirect()->route('tasks.task-groups.index')
                ->with('success', __('Task group deleted successfully.'));
        } catch (Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->route('tasks.task-groups.index')->with('error', $e->getMessage());
        }
    }

    //-----------------------------------------
    public function toggleStatus(int $id): JsonResponse
    {
        $group = $this->taskGroupService->getById($id);

        if (!$group) {
            return response()->json(['success' => false, 'message' => __('Task group not found.')], 404);
        }

        $this->taskGroupService->toggleStatus($group);

        return response()->json([
            'success' => true,
            'message' => __('Status updated successfully.'),
        ]);
    }
}
