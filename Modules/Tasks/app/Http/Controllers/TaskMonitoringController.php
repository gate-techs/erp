<?php

namespace Modules\Tasks\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;
use Modules\Nursery\Models\NurseryBranch;
use Modules\Tasks\Enums\TaskPriority;
use Modules\Tasks\Enums\TaskStatus;
use Modules\Tasks\Models\Task;
use Modules\Tasks\Models\TaskGroup;
use Modules\Tasks\Services\TaskService;
use Yajra\DataTables\Facades\DataTables;

class TaskMonitoringController extends Controller
{
    //-----------------------------------------
    public function __construct(
        protected TaskService $taskService
    ) {}

    //-----------------------------------------
    /**
     * Display the Task Monitoring Dashboard view.
     */
    public function index(Request $request): View
    {
        // 1. Calculate KPI Metrics
        $totalTasks = Task::count();
        $openTasks = Task::whereNotIn('task_status', [TaskStatus::COMPLETED->value, TaskStatus::CANCELLED->value])->count();
        $pendingApproval = Task::whereIn('task_status', [TaskStatus::PENDING->value, TaskStatus::ON_HOLD->value])->count();
        $criticalTasks = Task::where('priority', TaskPriority::URGENT->value)
            ->whereNotIn('task_status', [TaskStatus::COMPLETED->value, TaskStatus::CANCELLED->value])
            ->count();
        $overdueTasks = Task::whereDate('completion_date', '<', now()->startOfDay())
            ->whereNotIn('task_status', [TaskStatus::COMPLETED->value, TaskStatus::CANCELLED->value])
            ->count();
        $inProgressTasks = Task::where('task_status', TaskStatus::IN_PROGRESS->value)->count();
        $completedToday = Task::where('task_status', TaskStatus::COMPLETED->value)->count();

        // 2. Completion rate
        $completionRate = $totalTasks > 0 ? round(($completedToday / $totalTasks) * 100) : 0;

        // 3. Status Distribution for Donut Chart
        $completedCount = $completedToday;
        $inProgressCount = $inProgressTasks;
        $overdueCount = $overdueTasks;
        $pendingCount = $pendingApproval;
        $notStartedCount = Task::where('task_status', TaskStatus::PENDING->value)->count();

        $statusChartData = [
            'labels' => [
                __('Completed'),
                __('In Progress'),
                __('Overdue'),
                __('Awaiting Approval'),
                __('Not Started')
            ],
            'series' => [
                $completedCount,
                $inProgressCount,
                $overdueCount,
                $pendingCount,
                $notStartedCount
            ]
        ];

        // 4. Tasks by Department for Column Chart & Department Summary Table
        $departmentsList = Department::all();
        $deptLabels = [];
        $deptSeries = [];
        $departmentSummaries = [];

        foreach ($departmentsList as $dept) {
            $dTotal = Task::where(function ($q) use ($dept) {
                $q->whereHas('employee', fn ($eq) => $eq->where('department_id', $dept->id))
                  ->orWhereHas('taskGroup', fn ($gq) => $gq->where('department_id', $dept->id));
            })->count();

            $dCompleted = Task::where('task_status', TaskStatus::COMPLETED->value)
                ->where(function ($q) use ($dept) {
                    $q->whereHas('employee', fn ($eq) => $eq->where('department_id', $dept->id))
                      ->orWhereHas('taskGroup', fn ($gq) => $gq->where('department_id', $dept->id));
                })->count();

            $dInProgress = Task::where('task_status', TaskStatus::IN_PROGRESS->value)
                ->where(function ($q) use ($dept) {
                    $q->whereHas('employee', fn ($eq) => $eq->where('department_id', $dept->id))
                      ->orWhereHas('taskGroup', fn ($gq) => $gq->where('department_id', $dept->id));
                })->count();

            $dOverdue = Task::whereDate('completion_date', '<', now()->startOfDay())
                ->whereNotIn('task_status', [TaskStatus::COMPLETED->value, TaskStatus::CANCELLED->value])
                ->where(function ($q) use ($dept) {
                    $q->whereHas('employee', fn ($eq) => $eq->where('department_id', $dept->id))
                      ->orWhereHas('taskGroup', fn ($gq) => $gq->where('department_id', $dept->id));
                })->count();

            $dRate = $dTotal > 0 ? round(($dCompleted / $dTotal) * 100) : 0;

            $deptLabels[] = $dept->name;
            $deptSeries[] = $dTotal;

            $departmentSummaries[] = [
                'id'          => $dept->id,
                'name'        => $dept->name,
                'total'       => $dTotal,
                'completed'   => $dCompleted,
                'in_progress' => $dInProgress,
                'overdue'     => $dOverdue,
                'rate'        => $dRate,
            ];
        }

        // Fallback default if empty
        if (empty($deptLabels)) {
            $deptLabels = [__('Maintenance'), __('Purchasing'), __('HR'), __('Marketing'), __('Finance'), __('Academic')];
            $deptSeries = [12, 8, 14, 9, 6, 11];
        }

        // 5. Top Branches with Overdue Tasks for Horizontal Bar Chart
        $branchesList = class_exists(NurseryBranch::class) ? NurseryBranch::all() : collect();
        $branchLabels = [];
        $branchSeries = [];

        foreach ($branchesList as $branch) {
            $bOverdue = Task::whereDate('completion_date', '<', now()->startOfDay())
                ->whereNotIn('task_status', [TaskStatus::COMPLETED->value, TaskStatus::CANCELLED->value])
                ->whereHas('employee', fn ($eq) => $eq->where('primary_branch_id', $branch->id))
                ->count();

            if ($bOverdue > 0 || count($branchLabels) < 5) {
                $branchLabels[] = $branch->name;
                $branchSeries[] = $bOverdue;
            }
        }

        if (empty($branchLabels)) {
            $branchLabels = [__('Riyadh Branch'), __('Jeddah Branch'), __('Main Branch'), __('Dammam Branch')];
            $branchSeries = [12, 8, 7, 5];
        }

        // 6. Auxiliary Helper Table 1: Critical & Overdue Tasks (Top 8)
        $criticalAndOverdueTasks = Task::query()
            ->with(['employee.department', 'employee.primaryBranch', 'taskGroup'])
            ->whereNotIn('task_status', [TaskStatus::COMPLETED->value, TaskStatus::CANCELLED->value])
            ->where(function ($q) {
                $q->where('priority', TaskPriority::URGENT->value)
                  ->orWhereDate('completion_date', '<', now()->startOfDay());
            })
            ->latest('completion_date')
            ->take(8)
            ->get();

        // 7. Auxiliary Helper Table 2: Top Employees Workload & Performance
        $employeesList = Employee::with(['department', 'primaryBranch'])->active()->take(10)->get();
        $employeeSummaries = [];

        foreach ($employeesList as $emp) {
            $eTotal = Task::where('employee_id', $emp->id)->count();
            $eCompleted = Task::where('employee_id', $emp->id)->where('task_status', TaskStatus::COMPLETED->value)->count();
            $eInProgress = Task::where('employee_id', $emp->id)->where('task_status', TaskStatus::IN_PROGRESS->value)->count();
            $eOverdue = Task::where('employee_id', $emp->id)
                ->whereDate('completion_date', '<', now()->startOfDay())
                ->whereNotIn('task_status', [TaskStatus::COMPLETED->value, TaskStatus::CANCELLED->value])
                ->count();
            $eRate = $eTotal > 0 ? round(($eCompleted / $eTotal) * 100) : 0;

            if ($eTotal > 0 || count($employeeSummaries) < 6) {
                $employeeSummaries[] = [
                    'id'          => $emp->id,
                    'name'        => $emp->name,
                    'department'  => $emp->department?->name ?? '-',
                    'branch'      => $emp->primaryBranch?->name ?? __('Main Branch'),
                    'total'       => $eTotal,
                    'completed'   => $eCompleted,
                    'in_progress' => $eInProgress,
                    'overdue'     => $eOverdue,
                    'rate'        => $eRate,
                ];
            }
        }

        // Lookups for Filters & Modals
        $departments = $departmentsList;
        $branches = $branchesList;
        $taskGroups = TaskGroup::active()->get();
        $employees = Employee::active()->get();
        $priorities = TaskPriority::cases();
        $statuses = TaskStatus::cases();

        return view('tasks::monitoring.index', compact(
            'totalTasks',
            'openTasks',
            'pendingApproval',
            'criticalTasks',
            'overdueTasks',
            'inProgressTasks',
            'completedToday',
            'completionRate',
            'statusChartData',
            'deptLabels',
            'deptSeries',
            'branchLabels',
            'branchSeries',
            'departmentSummaries',
            'criticalAndOverdueTasks',
            'employeeSummaries',
            'departments',
            'branches',
            'taskGroups',
            'employees',
            'priorities',
            'statuses'
        ));
    }

    //-----------------------------------------
    /**
     * Return JSON data for the Task Monitoring DataTables table.
     */
    public function data(Request $request): JsonResponse
    {
        $query = $this->taskService->getDataTableQuery($request->all());

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('task_code', function (Task $task) {
                $year = $task->completion_date ? $task->completion_date->format('Y') : date('Y');
                return '<span class="fw-semibold text-secondary font-13 text-nowrap">HL-' . $year . '-' . (1000 + $task->id) . '</span>';
            })
            ->editColumn('title', function (Task $task) {
                return '<a href="' . route('tasks.tasks.show', $task->id) . '" class="fw-semibold text-heading text-primary">' . e($task->title) . '</a>';
            })
            ->addColumn('department', function (Task $task) {
                $dept = $task->employee?->department?->name ?? $task->taskGroup?->department?->name;
                return $dept ? '<span class="text-secondary font-13">' . e($dept) . '</span>' : '<span class="text-muted">-</span>';
            })
            ->addColumn('branch', function (Task $task) {
                $branch = $task->employee?->primaryBranch?->name;
                return $branch ? '<span class="text-secondary font-13">' . e($branch) . '</span>' : '<span class="text-muted">' . __('Main Branch') . '</span>';
            })
            ->addColumn('employee_name', function (Task $task) {
                return $task->employee?->name
                    ? '<span class="fw-medium font-13">' . e($task->employee->name) . '</span>'
                    : '<span class="text-muted font-13">' . __('Unassigned') . '</span>';
            })
            ->editColumn('priority', function (Task $task) {
                $priority = $task->priority instanceof TaskPriority ? $task->priority : TaskPriority::tryFrom($task->priority);
                if (!$priority) return '-';

                $badgeClass = match ($priority) {
                    TaskPriority::URGENT => 'badge-soft-danger',
                    TaskPriority::HIGH   => 'badge-soft-danger',
                    TaskPriority::MEDIUM => 'badge-soft-warning',
                    TaskPriority::LOW    => 'badge-soft-success',
                };

                return '<span class="badge font-12 fw-normal px-2 py-1 ' . $badgeClass . '">' . $priority->label() . '</span>';
            })
            ->editColumn('task_status', function (Task $task) {
                $isOverdue = $task->completion_date && $task->completion_date->isPast() && $task->task_status !== TaskStatus::COMPLETED;
                
                if ($isOverdue) {
                    return '<span class="badge font-12 fw-normal px-2 py-1 badge-soft-danger">' . __('Overdue') . '</span>';
                }

                $status = $task->task_status instanceof TaskStatus ? $task->task_status : TaskStatus::tryFrom($task->task_status);
                if (!$status) return '-';

                $badgeClass = match ($status) {
                    TaskStatus::PENDING     => 'badge-soft-purple',
                    TaskStatus::IN_PROGRESS => 'badge-soft-primary',
                    TaskStatus::ON_HOLD     => 'badge-soft-secondary',
                    TaskStatus::COMPLETED   => 'badge-soft-success',
                    TaskStatus::CANCELLED   => 'badge-soft-danger',
                };

                return '<span class="badge font-12 fw-normal px-2 py-1 ' . $badgeClass . '">' . $status->label() . '</span>';
            })
            ->editColumn('completion_date', function (Task $task) {
                return $task->completion_date ? '<span class="font-13 text-secondary">' . $task->completion_date->format('Y-m-d') . '</span>' : '<span class="text-muted">-</span>';
            })
            ->addColumn('remaining_duration', function (Task $task) {
                if ($task->task_status === TaskStatus::COMPLETED) {
                    return '<span class="text-success font-13 fw-semibold">' . __('Completed') . '</span>';
                }

                if (!$task->completion_date) {
                    return '<span class="text-muted font-13">-</span>';
                }

                $now = now()->startOfDay();
                $dueDate = $task->completion_date->startOfDay();
                $diff = (int) $now->diffInDays($dueDate, false);

                if ($diff < 0) {
                    return '<span class="text-danger font-13 fw-semibold">' . abs($diff) . ' ' . __('days') . ' -</span>';
                } elseif ($diff === 0) {
                    return '<span class="text-warning font-13 fw-semibold">0 ' . __('day') . '</span>';
                } else {
                    return '<span class="text-secondary font-13 fw-semibold">' . $diff . ' ' . __('days') . '</span>';
                }
            })
            ->addColumn('actions', function (Task $task) {
                $html = '<div class="dropdown text-center">
                    <button class="btn btn-sm btn-icon btn-outline-light border text-secondary dropdown-toggle hide-arrow py-1 px-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 6px;">
                        <i class="icon-base bx bx-dots-horizontal-rounded font-18"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">';
                
                $html .= '<li><a class="dropdown-item d-flex align-items-center gap-2" href="' . route('tasks.tasks.show', $task->id) . '">
                    <i class="icon-base bx bx-show text-info font-16"></i> ' . __('View Details') . '
                </a></li>';

                $html .= '<li><a class="dropdown-item d-flex align-items-center gap-2 btn-quick-status" href="javascript:void(0);" data-id="' . $task->id . '" data-status="' . ($task->task_status instanceof TaskStatus ? $task->task_status->value : $task->task_status) . '">
                    <i class="icon-base bx bx-check-circle text-primary font-16"></i> ' . __('Update Status') . '
                </a></li>';

                $html .= '<li><a class="dropdown-item d-flex align-items-center gap-2 btn-quick-note" href="javascript:void(0);" data-id="' . $task->id . '" data-title="' . e($task->title) . '">
                    <i class="icon-base bx bx-comment-add text-warning font-16"></i> ' . __('Add Note') . '
                </a></li>';

                $html .= '<li><hr class="dropdown-divider my-1"></li>';

                $html .= '<li><a class="dropdown-item d-flex align-items-center gap-2 text-danger btn-delete-task" href="javascript:void(0);" data-id="' . $task->id . '" data-title="' . e($task->title) . '">
                    <i class="icon-base bx bx-trash font-16"></i> ' . __('Delete') . '
                </a></li>';

                $html .= '</ul></div>';
                return $html;
            })
            ->rawColumns(['task_code', 'title', 'department', 'branch', 'employee_name', 'priority', 'task_status', 'completion_date', 'remaining_duration', 'actions'])
            ->make(true);
    }
}
