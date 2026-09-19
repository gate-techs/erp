<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Modules\HR\Enums\EmploymentType;
use Modules\HR\Enums\PlanItemPriority;
use Modules\HR\Enums\PlanQuarter;
use Modules\HR\Enums\WorkforcePlanStatus;
use Modules\HR\Http\Requests\StoreWorkforcePlanRequest;
use Modules\HR\Http\Requests\UpdateWorkforcePlanRequest;
use Modules\HR\Models\Department;
use Modules\HR\Models\JobTitle;
use Modules\HR\Models\WorkforcePlan;
use Modules\HR\Repositories\Contracts\WorkforcePlanRepositoryInterface;
use Modules\HR\Services\WorkforcePlanService;
use Modules\Nursery\Models\NurseryBranch;
use Yajra\DataTables\Facades\DataTables;

class WorkforcePlanController extends Controller
{
    public function __construct(
        protected WorkforcePlanService $planService,
        protected WorkforcePlanRepositoryInterface $planRepository
    ) {}

    /**
     * Display the Workforce Plans index view.
     */
    public function index(Request $request): View
    {
        $kpis        = $this->planService->getKPIs();
        $years       = WorkforcePlan::select('year')->distinct()->orderByDesc('year')->pluck('year');
        if ($years->isEmpty()) {
            $years = collect([now()->year]);
        }
        $statuses    = WorkforcePlanStatus::cases();

        return view('hr::workforce_plans.index', compact('kpis', 'years', 'statuses'));
    }

    /**
     * Server-side DataTables endpoint for Workforce Plans.
     */
    public function data(Request $request): JsonResponse
    {
        $query = $this->planRepository->getDataTableQuery($request->all());

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('plan_info', function (WorkforcePlan $plan) {
                return '
                    <div class="d-flex flex-column">
                        <a href="' . route('hr.workforce-plans.show', $plan->id) . '" class="fw-bold text-heading text-truncate text-decoration-none">' . e($plan->title) . '</a>
                        <small class="text-muted font-monospace">' . e($plan->plan_code) . ' • ' . e($plan->year) . '</small>
                    </div>
                ';
            })
            ->addColumn('duration', function (WorkforcePlan $plan) {
                $start = $plan->start_date ? $plan->start_date->format('Y-m-d') : '-';
                $end   = $plan->end_date ? $plan->end_date->format('Y-m-d') : '-';
                return '<div class="small text-muted font-monospace">' . $start . ' &rarr; ' . $end . '</div>';
            })
            ->addColumn('target_headcount', function (WorkforcePlan $plan) {
                return '<span class="fw-bold text-dark">' . number_format($plan->total_target_headcount) . ' ' . __('Positions') . '</span>';
            })
            ->addColumn('budgeted_cost', function (WorkforcePlan $plan) {
                return '<span class="fw-bold text-success font-monospace">' . number_format($plan->total_budgeted_cost, 3) . ' ' . __('KWD') . '</span>';
            })
            ->addColumn('status_badge', function (WorkforcePlan $plan) {
                $status = $plan->status;
                if (!$status) {
                    return '<span class="badge bg-label-secondary">-</span>';
                }
                return '<span class="badge ' . $status->badgeClass() . ' d-inline-flex align-items-center gap-1">
                            <i class="icon-base ' . $status->icon() . ' font-12"></i>
                            <span>' . $status->label() . '</span>
                        </span>';
            })
            ->addColumn('fulfillment', function (WorkforcePlan $plan) {
                $pct = $plan->fulfillment_percentage;
                $color = $pct >= 80 ? 'bg-success' : ($pct >= 40 ? 'bg-primary' : 'bg-warning');
                return '
                    <div class="d-flex align-items-center gap-2" style="min-width: 110px;">
                        <div class="progress w-100" style="height: 6px;">
                            <div class="progress-bar ' . $color . '" style="width: ' . min(100, $pct) . '%;"></div>
                        </div>
                        <small class="fw-bold">' . $pct . '%</small>
                    </div>
                ';
            })
            ->addColumn('actions', function (WorkforcePlan $plan) {
                $btnShow = '<a href="' . route('hr.workforce-plans.show', $plan->id) . '" class="btn btn-sm btn-icon btn-outline-info" title="' . __('View Plan') . '">
                                <i class="bx bx-show font-16"></i>
                            </a>';

                $btnEdit = '';
                if ($plan->status === WorkforcePlanStatus::DRAFT) {
                    $btnEdit = '<a href="' . route('hr.workforce-plans.edit', $plan->id) . '" class="btn btn-sm btn-icon btn-outline-primary" title="' . __('Edit Plan') . '">
                                    <i class="bx bx-edit font-16"></i>
                                </a>';
                }

                $btnDelete = '';
                if ($plan->status === WorkforcePlanStatus::DRAFT) {
                    $btnDelete = '<button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-delete-plan" data-id="' . $plan->id . '" data-title="' . e($plan->title) . '" title="' . __('Delete') . '">
                                    <i class="bx bx-trash font-16"></i>
                                  </button>';
                }

                return '<div class="d-flex align-items-center gap-1">' . $btnShow . $btnEdit . $btnDelete . '</div>';
            })
            ->rawColumns(['plan_info', 'duration', 'target_headcount', 'budgeted_cost', 'status_badge', 'fulfillment', 'actions'])
            ->make(true);
    }

    /**
     * Show create plan form.
     */
    public function create(): View
    {
        $departments = Department::with('translations')->orderBy('id')->get();
        $jobTitles   = JobTitle::with('translations')->orderBy('id')->get();
        $branches    = NurseryBranch::with('translations')->latest('id')->get();
        $priorities  = PlanItemPriority::cases();
        $quarters    = PlanQuarter::cases();
        $empTypes    = EmploymentType::cases();

        return view('hr::workforce_plans.create', compact(
            'departments',
            'jobTitles',
            'branches',
            'priorities',
            'quarters',
            'empTypes'
        ));
    }

    /**
     * Store a newly created plan.
     */
    public function store(StoreWorkforcePlanRequest $request): RedirectResponse
    {
        try {
            $plan = $this->planService->createPlan(
                $request->safe()->except('items'),
                $request->input('items', []),
                Auth::id()
            );

            return redirect()
                ->route('hr.workforce-plans.show', $plan->id)
                ->with('success', __('Workforce Plan created successfully.'));
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('An error occurred while creating workforce plan: ') . $e->getMessage());
        }
    }

    /**
     * Display the specified workforce plan.
     */
    public function show(int $id): View
    {
        $plan = $this->planRepository->findById($id);

        if (!$plan) {
            abort(404, __('Workforce Plan not found.'));
        }

        return view('hr::workforce_plans.show', compact('plan'));
    }

    /**
     * Show edit form.
     */
    public function edit(int $id): View|RedirectResponse
    {
        $plan = $this->planRepository->findById($id);

        if (!$plan) {
            abort(404, __('Workforce Plan not found.'));
        }

        if ($plan->status !== WorkforcePlanStatus::DRAFT) {
            return redirect()
                ->route('hr.workforce-plans.show', $plan->id)
                ->with('error', __('Only draft plans can be modified.'));
        }

        $departments = Department::with('translations')->orderBy('id')->get();
        $jobTitles   = JobTitle::with('translations')->orderBy('id')->get();
        $branches    = NurseryBranch::with('translations')->latest('id')->get();
        $priorities  = PlanItemPriority::cases();
        $quarters    = PlanQuarter::cases();
        $empTypes    = EmploymentType::cases();

        return view('hr::workforce_plans.edit', compact(
            'plan',
            'departments',
            'jobTitles',
            'branches',
            'priorities',
            'quarters',
            'empTypes'
        ));
    }

    /**
     * Update existing plan.
     */
    public function update(UpdateWorkforcePlanRequest $request, int $id): RedirectResponse
    {
        try {
            $plan = $this->planService->updatePlan(
                $id,
                $request->safe()->except('items'),
                $request->has('items') ? $request->input('items', []) : null
            );

            return redirect()
                ->route('hr.workforce-plans.show', $plan->id)
                ->with('success', __('Workforce Plan updated successfully.'));
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('An error occurred while updating workforce plan: ') . $e->getMessage());
        }
    }

    /**
     * Delete workforce plan.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->planService->deletePlan($id);
            return response()->json(['success' => true, 'message' => __('Workforce Plan deleted successfully.')]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Submit plan for approval.
     */
    public function submit(int $id): RedirectResponse
    {
        try {
            $this->planService->submitPlan($id);
            return back()->with('success', __('Workforce Plan submitted for approval successfully.'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Approve workforce plan.
     */
    public function approve(int $id): RedirectResponse
    {
        try {
            $this->planService->approvePlan($id, Auth::id());
            return back()->with('success', __('Workforce Plan approved successfully.'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Activate an approved plan.
     */
    public function activate(int $id): RedirectResponse
    {
        try {
            $this->planService->activatePlan($id);
            return back()->with('success', __('Workforce Plan activated successfully.'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Close workforce plan.
     */
    public function close(int $id): RedirectResponse
    {
        try {
            $this->planService->closePlan($id);
            return back()->with('success', __('Workforce Plan closed successfully.'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Recalculate live employee headcount for all items in this plan.
     */
    public function recalculateHeadcount(int $id): JsonResponse
    {
        try {
            $plan = $this->planRepository->findById($id);
            if (!$plan) {
                return response()->json(['success' => false, 'message' => __('Plan not found')], 404);
            }
            $this->planService->recalculateLiveHeadcount($plan);
            return response()->json(['success' => true, 'message' => __('Headcount recalculated successfully.')]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * API helper to fetch live headcount for a dept + job title + branch.
     */
    public function getLiveHeadcount(Request $request): JsonResponse
    {
        $deptId   = (int) $request->input('department_id');
        $jobId    = (int) $request->input('job_title_id');
        $branchId = $request->filled('branch_id') ? (int) $request->input('branch_id') : null;

        $count = $this->planService->countCurrentEmployees($deptId, $jobId, $branchId);

        return response()->json(['current_headcount' => $count]);
    }
}
