<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Modules\HR\Enums\LoanStatus;
use Modules\HR\Enums\LoanType;
use Modules\HR\Http\Requests\StoreLoanRequest;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;
use Modules\HR\Models\Loan;
use Modules\HR\Services\LoanService;
use Modules\Nursery\Models\NurseryBranch;
use Yajra\DataTables\Facades\DataTables;

class LoanController extends Controller
{
    public function __construct(
        protected LoanService $loanService
    ) {}

    //======================
    /**
     * Display the Loans & Advances management index page.
     */
    public function index(): View
    {
        $kpis        = $this->loanService->getKPIs();
        $branches    = class_exists(NurseryBranch::class) ? NurseryBranch::with('translations')->latest('id')->get() : collect();
        $departments = Department::with('translations')->active()->get();
        $employees   = Employee::with('translations')
            ->active()
            ->select('id', 'employee_code', 'department_id', 'primary_branch_id', 'basic_salary')
            ->get();
        $loanTypes   = LoanType::cases();
        $statuses    = LoanStatus::cases();

        return view('hr::loans.index', compact(
            'kpis',
            'branches',
            'departments',
            'employees',
            'loanTypes',
            'statuses'
        ));
    }

    //======================
    /**
     * Server-side DataTables endpoint for Loans.
     */
    public function data(Request $request): JsonResponse
    {
        $query = $this->loanService->getDataTableQuery($request->all());

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('employee_info', function (Loan $item) {
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
            ->addColumn('type_badge', function (Loan $item) {
                $type = $item->type;
                if (!$type) {
                    return '<span class="badge bg-label-secondary">-</span>';
                }

                return '<span class="badge ' . $type->badgeClass() . ' d-inline-flex align-items-center gap-1">
                            <i class="icon-base ' . $type->icon() . ' font-12"></i>
                            <span>' . $type->label() . '</span>
                        </span>';
            })
            ->addColumn('amount_formatted', function (Loan $item) {
                return '<strong class="text-heading">' . number_format($item->amount, 2) . '</strong>';
            })
            ->addColumn('installments_info', function (Loan $item) {
                return '<div class="d-flex flex-column small">
                            <span class="fw-semibold text-heading">' . $item->installments_count . ' ' . __('Installments') . '</span>
                            <span class="text-muted">' . number_format($item->monthly_installment, 2) . ' / ' . __('mo') . '</span>
                        </div>';
            })
            ->addColumn('repayment_progress', function (Loan $item) {
                $progress = $item->progress_percentage;
                $barColor = $progress >= 100 ? 'bg-success' : ($progress >= 50 ? 'bg-primary' : 'bg-warning');

                return '
                    <div class="d-flex flex-column" style="min-width: 130px;">
                        <div class="d-flex justify-content-between align-items-center mb-1 small">
                            <span class="fw-bold text-heading">' . $progress . '%</span>
                            <span class="text-muted">' . number_format($item->total_paid, 1) . ' / ' . number_format($item->amount, 1) . '</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar ' . $barColor . '" role="progressbar" style="width: ' . $progress . '%" aria-valuenow="' . $progress . '" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                ';
            })
            ->addColumn('status_badge', function (Loan $item) {
                $status = $item->status;
                if (!$status) {
                    return '<span class="badge bg-label-secondary">-</span>';
                }

                return '<span class="badge ' . $status->badgeClass() . ' d-inline-flex align-items-center gap-1">
                            <i class="icon-base ' . $status->icon() . ' font-12"></i>
                            <span>' . $status->label() . '</span>
                        </span>';
            })
            ->addColumn('actions', function (Loan $item) {
                $buttons = '';

                // 1. View Details Button
                $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-info btn-view-loan" 
                                data-id="' . $item->id . '" title="' . __('View Details & Installments') . '">
                                <i class="icon-base bx bx-show"></i>
                             </button>';

                // 2. Approve Button (Pending)
                if ($item->status === LoanStatus::PENDING) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-success btn-approve-loan" 
                                    data-id="' . $item->id . '" title="' . __('Approve Loan') . '">
                                    <i class="icon-base bx bx-check"></i>
                                 </button>';
                }

                // 3. Disburse Button (Approved)
                if ($item->status === LoanStatus::APPROVED) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-disburse-loan" 
                                    data-id="' . $item->id . '" data-amount="' . $item->amount . '" title="' . __('Disburse Funds') . '">
                                    <i class="icon-base bx bx-dollar-circle"></i>
                                 </button>';
                }

                // 4. Reject Button (Pending or Approved)
                if (in_array($item->status, [LoanStatus::PENDING, LoanStatus::APPROVED])) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-warning btn-reject-loan" 
                                    data-id="' . $item->id . '" title="' . __('Reject Loan') . '">
                                    <i class="icon-base bx bx-x"></i>
                                 </button>';
                }

                // 5. Delete Button (Pending or Rejected)
                if (in_array($item->status, [LoanStatus::PENDING, LoanStatus::REJECTED])) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-loan" 
                                    data-id="' . $item->id . '" data-name="' . e($item->employee?->name ?? '') . '" title="' . __('Delete') . '">
                                    <i class="icon-base bx bx-trash"></i>
                                 </button>';
                }

                return '<div class="d-inline-flex gap-1">' . $buttons . '</div>';
            })
            ->rawColumns(['employee_info', 'type_badge', 'amount_formatted', 'installments_info', 'repayment_progress', 'status_badge', 'actions'])
            ->make(true);
    }

    //======================
    /**
     * AJAX endpoint: Check employee loan eligibility and limits.
     */
    public function checkEligibility(Request $request): JsonResponse
    {
        try {
            $employeeId        = (int) $request->input('employee_id');
            $amount            = (float) $request->input('amount', 0);
            $installmentsCount = (int) $request->input('installments_count', 1);
            $type              = $request->input('type');

            if (!$employeeId) {
                return response()->json(['success' => false, 'message' => __('Please select an employee.')]);
            }

            $eligibility = $this->loanService->checkEligibility($employeeId, $amount, $installmentsCount, $type);

            return response()->json([
                'success' => true,
                'data'    => $eligibility,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    //======================
    /**
     * AJAX endpoint: Live preview of installment schedule.
     */
    public function previewSchedule(Request $request): JsonResponse
    {
        try {
            $amount            = (float) $request->input('amount', 0);
            $installmentsCount = (int) $request->input('installments_count', 1);
            $startDate         = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());

            if ($amount <= 0 || $installmentsCount <= 0) {
                return response()->json(['success' => false, 'schedule' => []]);
            }

            $schedule = $this->loanService->generateSchedulePreview($amount, $installmentsCount, $startDate);

            return response()->json([
                'success'  => true,
                'schedule' => $schedule,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    //======================
    /**
     * Store a newly created loan request.
     */
    public function store(StoreLoanRequest $request): JsonResponse
    {
        try {
            $loan = $this->loanService->createLoan(
                $request->validated(),
                $request->file('attachment'),
                Auth::id() ?? 1
            );

            return response()->json([
                'success' => true,
                'message' => __('Loan request submitted successfully.'),
                'data'    => $loan,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    //======================
    /**
     * Display full loan details and its installments.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $loan = $this->loanService->getById($id, [
                'employee.translations',
                'employee.department.translations',
                'creator',
                'installments',
                'managerApprover',
                'hrApprover',
            ]);

            if (!$loan) {
                return response()->json(['success' => false, 'message' => __('Loan request not found.')], 404);
            }

            $attachmentUrl = $loan->attachment ? asset('storage/' . $loan->attachment) : null;

            return response()->json([
                'success' => true,
                'data'    => [
                    'id'                     => $loan->id,
                    'employee_name'          => $loan->employee?->name,
                    'employee_code'          => $loan->employee?->employee_code,
                    'department'             => $loan->employee?->department?->name ?? '-',
                    'type_label'             => $loan->type?->label(),
                    'type_badge'             => '<span class="badge ' . $loan->type?->badgeClass() . '">' . $loan->type?->label() . '</span>',
                    'amount'                 => number_format($loan->amount, 2),
                    'total_paid'             => number_format($loan->total_paid, 2),
                    'remaining_amount'       => number_format($loan->remaining_amount, 2),
                    'progress_percentage'    => $loan->progress_percentage,
                    'installments_count'     => $loan->installments_count,
                    'monthly_installment'    => number_format($loan->monthly_installment, 2),
                    'start_date'             => $loan->start_date?->format('Y-m-d'),
                    'status_label'           => $loan->status?->label(),
                    'status_badge'           => '<span class="badge ' . $loan->status?->badgeClass() . '">' . $loan->status?->label() . '</span>',
                    'disbursed_at'           => $loan->disbursed_at ? $loan->disbursed_at->format('Y-m-d H:i') : null,
                    'disbursement_method'    => $loan->disbursement_method,
                    'disbursement_reference' => $loan->disbursement_reference,
                    'reason'                 => $loan->reason,
                    'rejection_reason'       => $loan->rejection_reason,
                    'attachment_url'         => $attachmentUrl,
                    'creator_name'           => $loan->creator?->name ?? '-',
                    'installments'           => $loan->installments->map(function ($inst) {
                        return [
                            'id'                 => $inst->id,
                            'number'             => $inst->installment_number,
                            'due_date'           => $inst->due_date?->format('Y-m-d'),
                            'amount'             => number_format($inst->amount, 2),
                            'paid_amount'        => number_format($inst->paid_amount, 2),
                            'status_value'       => $inst->status?->value,
                            'status_label'       => $inst->status?->label(),
                            'status_badge'       => '<span class="badge ' . $inst->status?->badgeClass() . '">' . $inst->status?->label() . '</span>',
                            'paid_at'            => $inst->paid_at?->format('Y-m-d'),
                            'postponed_from'     => $inst->postponed_from?->format('Y-m-d'),
                            'notes'              => $inst->notes,
                        ];
                    }),
                ],
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    //======================
    /**
     * Approve a loan request.
     */
    public function approve(Request $request, int $id): JsonResponse
    {
        try {
            $loan = $this->loanService->approveLoan($id, Auth::id() ?? 1);

            return response()->json([
                'success' => true,
                'message' => __('Loan request approved successfully. Awaiting finance disbursement.'),
                'data'    => $loan,
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    //======================
    /**
     * Disburse an approved loan.
     */
    public function disburse(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'disbursement_method'    => 'required|string',
                'disbursement_reference' => 'nullable|string|max:255',
                'disbursed_at'           => 'nullable|date',
            ]);

            $loan = $this->loanService->disburseLoan($id, $validated, Auth::id() ?? 1);

            return response()->json([
                'success' => true,
                'message' => __('Loan disbursed successfully. Repayment schedule activated.'),
                'data'    => $loan,
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    //======================
    /**
     * Reject a loan request.
     */
    public function reject(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'rejection_reason' => 'required|string|max:1000',
            ]);

            $loan = $this->loanService->rejectLoan($id, $validated['rejection_reason'], Auth::id() ?? 1);

            return response()->json([
                'success' => true,
                'message' => __('Loan request rejected.'),
                'data'    => $loan,
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    //======================
    /**
     * Postpone an installment.
     */
    public function postponeInstallment(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'new_due_date' => 'required|date|after:today',
                'reason'       => 'nullable|string|max:500',
            ]);

            $installment = $this->loanService->postponeInstallment($id, $validated['new_due_date'], $validated['reason'] ?? null);

            return response()->json([
                'success' => true,
                'message' => __('Installment postponed successfully.'),
                'data'    => $installment,
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    //======================
    /**
     * Record payment for an installment.
     */
    public function payInstallment(Request $request, int $id): JsonResponse
    {
        try {
            $paidAmount = $request->filled('paid_amount') ? (float) $request->input('paid_amount') : null;
            $notes      = $request->input('notes');

            $installment = $this->loanService->payInstallment($id, $paidAmount, $notes);

            return response()->json([
                'success' => true,
                'message' => __('Installment payment recorded successfully.'),
                'data'    => $installment,
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    //======================
    /**
     * Delete a loan request.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->loanService->deleteLoan($id);

            return response()->json([
                'success' => true,
                'message' => __('Loan request deleted successfully.'),
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
