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
            ->addColumn('employee_name', function (ExitClearance $item) {
                $emp = $item->employee;
                if (!$emp) {
                    return '<span class="text-muted">' . __('Unknown Employee') . '</span>';
                }

                $avatarUrl = $emp->avatar ? asset('storage/' . $emp->avatar) : asset('assets/img/avatars/1.png');
                $code      = $emp->employee_code ?? '';

                return '
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-sm flex-shrink-0">
                            <img src="' . $avatarUrl . '" alt="Avatar" class="rounded-circle">
                        </div>
                        <div class="d-inline-flex align-items-center gap-2">
                            <span class="fw-bold text-heading">' . e($emp->name) . '</span>
                            ' . ($code ? '<span class="badge bg-label-secondary font-11 py-0 px-1">' . e($code) . '</span>' : '') . '
                        </div>
                    </div>
                ';
            })
            ->addColumn('department_name', function (ExitClearance $item) {
                $deptName = $item->department?->name ?? $item->employee?->department?->name ?? '-';
                return '<span class="text-heading">' . e($deptName) . '</span>';
            })
            ->addColumn('last_working_day_col', function (ExitClearance $item) {
                $lastDay = $item->last_working_day ? $item->last_working_day->format('Y-m-d') : '-';
                return '<span class="text-danger fw-bold"><i class="icon-base bx bx-calendar-x font-14 me-1"></i>' . $lastDay . '</span>';
            })
            ->addColumn('notice_date_col', function (ExitClearance $item) {
                $resDate = $item->resignation_date ? $item->resignation_date->format('Y-m-d') : '-';
                return '<span class="text-muted"><i class="icon-base bx bx-calendar font-14 me-1"></i>' . $resDate . '</span>';
            })
            ->addColumn('reason_badge', function (ExitClearance $item) {
                $reasonsMap = [
                    'resignation'     => __('Resignation'),
                    'contract_expiry' => __('Contract Expiry'),
                    'termination'     => __('Employer Termination'),
                    'retirement'      => __('Retirement'),
                    'other'           => __('Other'),
                ];
                $label = $reasonsMap[$item->leaving_reason] ?? __($item->leaving_reason);
                return '<span class="badge bg-label-secondary fw-semibold">' . e($label) . '</span>';
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
                    <div class="d-inline-flex gap-1">
                        <a href="' . $showUrl . '" class="btn btn-icon btn-sm btn-outline-info" title="' . __('View Details') . '" data-bs-toggle="tooltip">
                            <i class="icon-base bx bx-show font-16"></i>
                        </a>
                        <a href="' . $printUrl . '" target="_blank" class="btn btn-icon btn-sm btn-outline-secondary" title="' . __('Print Form') . '" data-bs-toggle="tooltip">
                            <i class="icon-base bx bx-printer font-16"></i>
                        </a>
                        <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-clearance" data-id="' . $item->id . '" title="' . __('Delete') . '" data-bs-toggle="tooltip">
                            <i class="icon-base bx bx-trash font-16"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['clearance_code_badge', 'employee_name', 'department_name', 'last_working_day_col', 'notice_date_col', 'reason_badge', 'status_badge', 'actions'])
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
