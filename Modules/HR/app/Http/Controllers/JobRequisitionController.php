<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Modules\HR\Enums\EmployeeStatus;
use Modules\HR\Enums\EmploymentType;
use Modules\HR\Enums\JobRequisitionStatus;
use Modules\HR\Enums\PlanItemPriority;
use Modules\HR\Enums\RequisitionType;
use Modules\HR\Http\Requests\RejectRequisitionRequest;
use Modules\HR\Http\Requests\StoreJobRequisitionRequest;
use Modules\HR\Http\Requests\UpdateJobRequisitionRequest;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;
use Modules\HR\Models\JobRequisition;
use Modules\HR\Models\JobTitle;
use Modules\HR\Repositories\Contracts\JobRequisitionRepositoryInterface;
use Modules\HR\Services\JobRequisitionService;
use Modules\Nursery\Models\NurseryBranch;
use Yajra\DataTables\Facades\DataTables;

class JobRequisitionController extends Controller
{
    public function __construct(
        protected JobRequisitionService $requisitionService,
        protected JobRequisitionRepositoryInterface $requisitionRepository
    ) {}

    /**
     * Display the Job Requisitions index view.
     */
    public function index(Request $request): View
    {
        $kpis        = $this->requisitionService->getKPIs();
        $departments = Department::with('translations')->orderBy('id')->get();
        $branches    = NurseryBranch::with('translations')->latest('id')->get();
        $statuses    = JobRequisitionStatus::cases();
        $types       = RequisitionType::cases();
        $priorities  = PlanItemPriority::cases();

        return view('hr::job_requisitions.index', compact('kpis', 'departments', 'branches', 'statuses', 'types', 'priorities'));
    }

    /**
     * Server-side DataTables endpoint for Job Requisitions.
     */
    public function data(Request $request): JsonResponse
    {
        $query = $this->requisitionRepository->getDataTableQuery($request->all());

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('requisition_info', function (JobRequisition $item) {
                $roleName = $item->jobTitle?->translate(app()->getLocale())?->name
                         ?? $item->jobTitle?->translate('ar')?->name
                         ?? '-';
                $deptName = $item->department?->translate(app()->getLocale())?->name
                         ?? $item->department?->translate('ar')?->name
                         ?? '-';

                $budgetBadge = $item->is_budgeted
                    ? '<span class="badge bg-label-success ms-1 font-10">' . __('Budgeted') . '</span>'
                    : '<span class="badge bg-label-warning ms-1 font-10">' . __('Unbudgeted') . '</span>';

                return '
                    <div class="d-flex flex-column">
                        <div class="d-flex align-items-center">
                            <a href="' . route('hr.job-requisitions.show', $item->id) . '" class="fw-bold text-heading text-truncate text-decoration-none">' . e($roleName) . '</a>
                            ' . $budgetBadge . '
                        </div>
                        <small class="text-muted font-monospace">' . e($item->requisition_number) . ' • ' . e($deptName) . '</small>
                    </div>
                ';
            })
            ->addColumn('type_badge', function (JobRequisition $item) {
                $type = $item->requisition_type;
                if (!$type) {
                    return '-';
                }
                return '<span class="badge ' . $type->badgeClass() . '">' . $type->label() . '</span>';
            })
            ->addColumn('headcount', function (JobRequisition $item) {
                return '<span class="fw-bold text-dark">' . $item->headcount_needed . ' ' . __('needed') . '</span>
                        <small class="text-muted d-block font-11">(' . $item->hired_count . ' ' . __('hired') . ')</small>';
            })
            ->addColumn('salary_range', function (JobRequisition $item) {
                if ($item->min_salary || $item->max_salary) {
                    $min = $item->min_salary ? number_format($item->min_salary, 3) : '—';
                    $max = $item->max_salary ? number_format($item->max_salary, 3) : '—';
                    return '<span class="font-monospace small">' . $min . ' - ' . $max . ' <small class="text-muted">KWD</small></span>';
                }
                return '<span class="text-muted">—</span>';
            })
            ->addColumn('status_badge', function (JobRequisition $item) {
                $status = $item->status;
                if (!$status) {
                    return '<span class="badge bg-label-secondary">-</span>';
                }
                return '<span class="badge ' . $status->badgeClass() . ' d-inline-flex align-items-center gap-1">
                            <i class="icon-base ' . $status->icon() . ' font-12"></i>
                            <span>' . $status->label() . '</span>
                        </span>';
            })
            ->addColumn('priority_badge', function (JobRequisition $item) {
                $priority = $item->priority;
                if (!$priority) {
                    return '-';
                }
                return '<span class="badge ' . $priority->badgeClass() . '">' . $priority->label() . '</span>';
            })
            ->addColumn('actions', function (JobRequisition $item) {
                $btnShow = '<a href="' . route('hr.job-requisitions.show', $item->id) . '" class="btn btn-sm btn-icon btn-outline-info" title="' . __('View Requisition') . '">
                                <i class="bx bx-show font-16"></i>
                            </a>';

                $btnEdit = '';
                if (in_array($item->status, [JobRequisitionStatus::DRAFT, JobRequisitionStatus::PENDING_APPROVAL])) {
                    $btnEdit = '<a href="' . route('hr.job-requisitions.edit', $item->id) . '" class="btn btn-sm btn-icon btn-outline-primary" title="' . __('Edit') . '">
                                    <i class="bx bx-edit font-16"></i>
                                </a>';
                }

                $btnDelete = '';
                if ($item->status === JobRequisitionStatus::DRAFT) {
                    $btnDelete = '<button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-delete-requisition" data-id="' . $item->id . '" data-num="' . e($item->requisition_number) . '" title="' . __('Delete') . '">
                                    <i class="bx bx-trash font-16"></i>
                                  </button>';
                }

                return '<div class="d-flex align-items-center gap-1">' . $btnShow . $btnEdit . $btnDelete . '</div>';
            })
            ->rawColumns(['requisition_info', 'type_badge', 'headcount', 'salary_range', 'status_badge', 'priority_badge', 'actions'])
            ->make(true);
    }

    /**
     * Show create requisition form.
     */
    public function create(Request $request): View
    {
        $departments   = Department::with('translations')->orderBy('id')->get();
        $jobTitles     = JobTitle::with('translations')->orderBy('id')->get();
        $branches      = NurseryBranch::with('translations')->latest('id')->get();
        $planItems     = $this->requisitionService->getAvailablePlanItems();
        $employees     = Employee::with('translations')->whereIn('status', [EmployeeStatus::ACTIVE->value, EmployeeStatus::PROBATION->value])->get();
        $priorities    = PlanItemPriority::cases();
        $types         = RequisitionType::cases();
        $empTypes      = EmploymentType::cases();

        $selectedPlanItemId = $request->input('plan_item_id');

        return view('hr::job_requisitions.create', compact(
            'departments',
            'jobTitles',
            'branches',
            'planItems',
            'employees',
            'priorities',
            'types',
            'empTypes',
            'selectedPlanItemId'
        ));
    }

    /**
     * Store a newly created requisition.
     */
    public function store(StoreJobRequisitionRequest $request): RedirectResponse
    {
        try {
            $req = $this->requisitionService->createRequisition(
                $request->validated(),
                Auth::id()
            );

            return redirect()
                ->route('hr.job-requisitions.show', $req->id)
                ->with('success', __('Job Requisition created successfully.'));
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('An error occurred while creating requisition: ') . $e->getMessage());
        }
    }

    /**
     * Display the specified job requisition.
     */
    public function show(int $id): View
    {
        $requisition = $this->requisitionRepository->findById($id);

        if (!$requisition) {
            abort(404, __('Job Requisition not found.'));
        }

        return view('hr::job_requisitions.show', compact('requisition'));
    }

    /**
     * Show edit form.
     */
    public function edit(int $id): View|RedirectResponse
    {
        $requisition = $this->requisitionRepository->findById($id);

        if (!$requisition) {
            abort(404, __('Job Requisition not found.'));
        }

        if (!in_array($requisition->status, [JobRequisitionStatus::DRAFT, JobRequisitionStatus::PENDING_APPROVAL])) {
            return redirect()
                ->route('hr.job-requisitions.show', $requisition->id)
                ->with('error', __('This requisition cannot be modified in its current status.'));
        }

        $departments = Department::with('translations')->orderBy('id')->get();
        $jobTitles   = JobTitle::with('translations')->orderBy('id')->get();
        $branches    = NurseryBranch::with('translations')->latest('id')->get();
        $planItems   = $this->requisitionService->getAvailablePlanItems();
        $employees   = Employee::with('translations')->whereIn('status', [EmployeeStatus::ACTIVE->value, EmployeeStatus::PROBATION->value])->get();
        $priorities  = PlanItemPriority::cases();
        $types       = RequisitionType::cases();
        $empTypes    = EmploymentType::cases();

        return view('hr::job_requisitions.edit', compact(
            'requisition',
            'departments',
            'jobTitles',
            'branches',
            'planItems',
            'employees',
            'priorities',
            'types',
            'empTypes'
        ));
    }

    /**
     * Update existing requisition.
     */
    public function update(UpdateJobRequisitionRequest $request, int $id): RedirectResponse
    {
        try {
            $req = $this->requisitionService->updateRequisition($id, $request->validated());

            return redirect()
                ->route('hr.job-requisitions.show', $req->id)
                ->with('success', __('Job Requisition updated successfully.'));
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('An error occurred while updating requisition: ') . $e->getMessage());
        }
    }

    /**
     * Delete requisition.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->requisitionService->deleteRequisition($id);
            return response()->json(['success' => true, 'message' => __('Job Requisition deleted successfully.')]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Submit requisition for approval.
     */
    public function submit(int $id): RedirectResponse
    {
        try {
            $this->requisitionService->submitRequisition($id);
            return back()->with('success', __('Requisition submitted for approval successfully.'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Approve requisition.
     */
    public function approve(int $id): RedirectResponse
    {
        try {
            $this->requisitionService->approveRequisition($id, Auth::id());
            return back()->with('success', __('Requisition approved successfully.'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reject requisition.
     */
    public function reject(RejectRequisitionRequest $request, int $id): RedirectResponse
    {
        try {
            $this->requisitionService->rejectRequisition($id, $request->input('rejection_reason'), Auth::id());
            return back()->with('success', __('Requisition rejected.'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Start recruitment / sourcing phase.
     */
    public function startRecruitment(int $id): RedirectResponse
    {
        try {
            $this->requisitionService->startRecruitment($id);
            return back()->with('success', __('Recruitment phase started for this position.'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Cancel requisition.
     */
    public function cancel(int $id): RedirectResponse
    {
        try {
            $this->requisitionService->cancelRequisition($id);
            return back()->with('success', __('Requisition cancelled.'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * API helper to fetch available plan items for a department.
     */
    public function getPlanItems(Request $request): JsonResponse
    {
        $deptId = $request->filled('department_id') ? (int) $request->input('department_id') : null;
        $items  = $this->requisitionService->getAvailablePlanItems($deptId);

        $mapped = $items->map(function ($item) {
            $roleName = $item->jobTitle?->translate(app()->getLocale())?->name
                     ?? $item->jobTitle?->translate('ar')?->name
                     ?? '-';
            $planCode = $item->plan?->plan_code ?? '';

            return [
                'id'                       => $item->id,
                'job_title_id'             => $item->job_title_id,
                'job_title_name'           => $roleName,
                'department_id'            => $item->department_id,
                'branch_id'                => $item->branch_id,
                'employment_type'          => $item->employment_type->value,
                'vacancies_count'          => $item->vacancies_count,
                'target_headcount'         => $item->target_headcount,
                'current_headcount'        => $item->current_headcount,
                'estimated_monthly_salary' => $item->estimated_monthly_salary,
                'plan_code'                => $planCode,
            ];
        });

        return response()->json($mapped);
    }
}
