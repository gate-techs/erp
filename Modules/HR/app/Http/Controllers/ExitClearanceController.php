<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\HR\Enums\ClearanceApprovalStatus;
use Modules\HR\Enums\ExitClearanceStatus;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;
use Modules\HR\Models\ExitClearance;
use Modules\HR\Services\ExitClearanceService;
use Modules\Nursery\Models\NurseryBranch;
use Yajra\DataTables\Facades\DataTables;

class ExitClearanceController extends Controller
{
    public function __construct(
        protected ExitClearanceService $service
    ) {}

    /**
     * Display a listing of the exit clearance requests.
     */
    public function index(Request $request): View
    {
        $kpis = $this->service->getKPIs();
        $branches = class_exists(NurseryBranch::class) ? NurseryBranch::with('translations')->latest('id')->get() : collect();
        $departments = Department::with('translations')->get();
        $employees = Employee::with('translations')->where('status', 'active')->get();
        $statuses = ExitClearanceStatus::cases();

        return view('hr::exit_clearances.index', compact('kpis', 'branches', 'departments', 'employees', 'statuses'));
    }

    /**
     * Server-side DataTables endpoint for Exit Clearances.
     */
    public function data(Request $request): JsonResponse
    {
        $query = $this->service->getDataTableQuery($request->all());

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('clearance_code_badge', function (ExitClearance $item) {
                return '<a href="' . route('hr.exit-clearances.show', $item->id) . '" class="fw-bold text-primary">' . e($item->clearance_code) . '</a>';
            })
            ->addColumn('employee_info', function (ExitClearance $item) {
                $emp = $item->employee;
                if (!$emp) {
                    return '<span class="text-muted">' . __('Unknown Employee') . '</span>';
                }

                $avatarUrl = $emp->avatar ? asset('storage/' . $emp->avatar) : asset('assets/img/avatars/1.png');
                $deptName  = $emp->department?->name ?? '-';
                $code      = $emp->employee_code ?? '-';

                return '
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-sm flex-shrink-0">
                            <img src="' . $avatarUrl . '" alt="Avatar" class="rounded-circle">
                        </div>
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-heading text-truncate">' . e($emp->name) . '</span>
                            <small class="text-muted">' . e($code) . ' • ' . e($deptName) . '</small>
                        </div>
                    </div>
                ';
            })
            ->addColumn('dates_info', function (ExitClearance $item) {
                $lastDay = $item->last_working_day ? $item->last_working_day->format('Y-m-d') : '-';
                $resDate = $item->resignation_date ? $item->resignation_date->format('Y-m-d') : '-';

                return '<div class="d-flex flex-column small">
                            <span class="text-danger fw-bold"><i class="bx bx-calendar-x font-14 me-1"></i>' . $lastDay . '</span>
                            <span class="text-muted">' . __('Notice') . ': ' . $resDate . '</span>
                        </div>';
            })
            ->addColumn('reason_badge', function (ExitClearance $item) {
                return '<span class="badge bg-label-secondary fw-semibold">' . __(ucfirst($item->leaving_reason)) . '</span>';
            })
            ->addColumn('progress_bar', function (ExitClearance $item) {
                $totalApprovals = $item->approvals->count();
                $clearedApprovals = $item->approvals->where('status', ClearanceApprovalStatus::CLEARED)->count();
                $pct = $totalApprovals > 0 ? round(($clearedApprovals / $totalApprovals) * 100) : 0;
                $color = $pct == 100 ? 'success' : 'primary';

                return '
                    <div class="d-flex align-items-center gap-2" style="min-width: 130px;">
                        <div class="progress w-100" style="height: 6px;">
                            <div class="progress-bar bg-' . $color . '" role="progressbar" style="width: ' . $pct . '%" aria-valuenow="' . $pct . '" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <span class="fw-bold small text-heading">' . $clearedApprovals . '/' . $totalApprovals . '</span>
                    </div>
                ';
            })
            ->addColumn('status_badge', function (ExitClearance $item) {
                $status = $item->status;
                if (!$status) {
                    return '<span class="badge bg-label-secondary">-</span>';
                }

                return '<span class="badge ' . $status->badgeClass() . ' d-inline-flex align-items-center gap-1">
                            <i class="' . $status->icon() . ' font-12"></i>
                            <span>' . $status->label() . '</span>
                        </span>';
            })
            ->addColumn('actions', function (ExitClearance $item) {
                $showUrl  = route('hr.exit-clearances.show', $item->id);
                $printUrl = route('hr.exit-clearances.print', $item->id);

                return '
                    <div class="d-inline-block text-nowrap">
                        <a href="' . $showUrl . '" class="btn btn-sm btn-icon btn-text-secondary rounded-pill" title="' . __('View Checklists') . '">
                            <i class="bx bx-show font-18 text-primary"></i>
                        </a>
                        <a href="' . $printUrl . '" target="_blank" class="btn btn-sm btn-icon btn-text-secondary rounded-pill" title="' . __('Print Form') . '">
                            <i class="bx bx-printer font-18 text-info"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-icon btn-text-danger rounded-pill btn-delete-clearance" data-id="' . $item->id . '" title="' . __('Delete') . '">
                            <i class="bx bx-trash font-18 text-danger"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['clearance_code_badge', 'employee_info', 'dates_info', 'reason_badge', 'progress_bar', 'status_badge', 'actions'])
            ->make(true);
    }

    /**
     * Store a newly created exit clearance request.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'employee_id'           => 'required|exists:employees,id',
            'resignation_date'      => 'nullable|date',
            'last_working_day'      => 'required|date',
            'leaving_reason'        => 'required|string',
            'reason_details'        => 'nullable|string',
            'leave_balance_days'    => 'nullable|numeric|min:0',
            'end_of_service_amount' => 'nullable|numeric|min:0',
        ]);

        try {
            $clearance = $this->service->createClearance($validated, auth()->id());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Exit clearance request created successfully.'),
                    'data'    => $clearance,
                ]);
            }

            return redirect()->route('hr.exit-clearances.show', $clearance->id)
                ->with('success', __('Exit clearance request created successfully.'));
        } catch (Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified exit clearance request details.
     */
    public function show(int $id): View
    {
        $clearance = $this->service->findById($id);
        abort_if(!$clearance, 404);

        return view('hr::exit_clearances.show', compact('clearance'));
    }

    /**
     * Update department approval sign-off.
     */
    public function updateApproval(Request $request, int $approvalId): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'status'      => 'required|in:cleared,has_dues,rejected',
            'notes'       => 'nullable|string',
            'dues_amount' => 'nullable|numeric|min:0',
        ]);

        try {
            $this->service->updateDepartmentApproval($approvalId, $validated, auth()->id());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Department clearance updated successfully.'),
                ]);
            }

            return back()->with('success', __('Department clearance updated successfully.'));
        } catch (Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Finalize and sign-off exit clearance by HR.
     */
    public function finalize(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'final_notes'    => 'nullable|string',
            'force_override' => 'nullable|boolean',
        ]);

        try {
            $this->service->finalizeClearance($id, $validated, auth()->id());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Exit clearance finalized successfully. Employee record updated.'),
                ]);
            }

            return back()->with('success', __('Exit clearance finalized successfully.'));
        } catch (Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Print printable Exit Clearance Form.
     */
    public function print(int $id): View
    {
        $clearance = $this->service->findById($id);
        abort_if(!$clearance, 404);

        return view('hr::exit_clearances.print', compact('clearance'));
    }

    /**
     * Delete an exit clearance request.
     */
    public function destroy(int $id): JsonResponse|RedirectResponse
    {
        try {
            $this->service->deleteClearance($id);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Exit clearance request deleted successfully.'),
                ]);
            }

            return redirect()->route('hr.exit-clearances.index')
                ->with('success', __('Exit clearance request deleted successfully.'));
        } catch (Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->with('error', $e->getMessage());
        }
    }
}
