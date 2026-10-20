<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HR\Enums\PayrollStatus;
use Modules\HR\Http\Requests\GeneratePayrollRequest;
use Modules\HR\Http\Requests\UpdatePayrollItemRequest;
use Modules\HR\Models\Payroll;
use Modules\HR\Repositories\Contracts\PayrollRepositoryInterface;
use Modules\HR\Services\PayrollService;
use Modules\Nursery\Models\NurseryBranch;
use Yajra\DataTables\Facades\DataTables;

class PayrollController extends Controller
{
    public function __construct(
        protected PayrollRepositoryInterface $payrollRepo,
        protected PayrollService $payrollService
    ) {}

    /**
     * Display listing of payroll runs.
     */
    public function index()
    {
        abort_if(! auth()->user()->can('hr.payrolls.view'), 403);

        $kpis = $this->payrollRepo->getKpis();
        $branches = NurseryBranch::where('status', 'active')->get();
        $statuses = PayrollStatus::cases();

        return view('hr::payrolls.index', compact('kpis', 'branches', 'statuses'));
    }

    /**
     * Server-side DataTables endpoint for payroll runs.
     */
    public function data(Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.payrolls.view'), 403);

        $query = $this->payrollRepo->getQuery();

        // 1. Multi-Filters
        if ($request->filled('month')) {
            $query->where('payroll_month', $request->month);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('payroll_code', function (Payroll $payroll) {
                $url = route('hr.payrolls.show', $payroll->id);
                return '<a href="' . $url . '" class="fw-bold text-primary">' . e($payroll->payroll_number) . '</a>';
            })
            ->addColumn('month_info', function (Payroll $payroll) {
                return '<div>
                    <strong class="text-heading d-block">' . e($payroll->payroll_month) . '</strong>
                    <small class="text-muted">' . e($payroll->title ?: '-') . '</small>
                </div>';
            })
            ->addColumn('branch_info', function (Payroll $payroll) {
                if ($payroll->branch) {
                    return '<span class="fw-medium text-heading d-inline-flex align-items-center gap-1"><i class="icon-base bx bx-building font-14 text-muted"></i>' . e($payroll->branch->name) . '</span>';
                }
                return '<span class="text-muted d-inline-flex align-items-center gap-1"><i class="icon-base bx bx-globe font-14"></i>' . __('All Branches') . '</span>';
            })
            ->addColumn('employees_count_formatted', function (Payroll $payroll) {
                return '<span class="fw-semibold text-heading">' . (int) $payroll->employees_count . '</span> <small class="text-muted">' . __('Employees') . '</small>';
            })
            ->addColumn('gross_formatted', function (Payroll $payroll) {
                return '<span class="fw-semibold text-heading">' . number_format($payroll->total_gross, 2) . ' ' . __('KWD') . '</span>';
            })
            ->addColumn('deductions_formatted', function (Payroll $payroll) {
                return '<span class="fw-semibold text-danger">-' . number_format($payroll->total_deductions, 2) . ' ' . __('KWD') . '</span>';
            })
            ->addColumn('net_formatted', function (Payroll $payroll) {
                return '<strong class="fw-bold text-success fs-6">' . number_format($payroll->total_net, 2) . ' ' . __('KWD') . '</strong>';
            })
            ->addColumn('status_badge', function (Payroll $payroll) {
                $statusEnum = $payroll->status instanceof PayrollStatus ? $payroll->status : PayrollStatus::tryFrom($payroll->status);
                if (!$statusEnum) {
                    return '<span class="text-secondary">' . e($payroll->status) . '</span>';
                }
                return '<span class="fw-semibold ' . $statusEnum->textColor() . ' d-inline-flex align-items-center gap-1">
                    <i class="icon-base bx ' . $statusEnum->icon() . ' font-16"></i>
                    <span>' . $statusEnum->label() . '</span>
                </span>';
            })
            ->addColumn('actions', function (Payroll $payroll) {
                $user = auth()->user();
                $id = $payroll->id;
                $status = $payroll->status instanceof PayrollStatus ? $payroll->status->value : $payroll->status;
                $showUrl = route('hr.payrolls.show', $id);

                $buttons = '';

                // View Details Button
                if ($user->can('hr.payrolls.view')) {
                    $buttons .= '<a href="' . $showUrl . '" class="btn btn-icon btn-sm btn-outline-info" title="' . __('View Payroll Details') . '">
                        <i class="icon-base bx bx-show"></i>
                    </a>';
                }

                // Recalculate (Draft only)
                if ($status === PayrollStatus::DRAFT->value && $user->can('hr.payrolls.recalculate')) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-recalculate-payroll" data-id="' . $id . '" title="' . __('Recalculate Payroll') . '">
                        <i class="icon-base bx bx-refresh"></i>
                    </button>';
                }

                // Approve (Draft only)
                if ($status === PayrollStatus::DRAFT->value && $user->can('hr.payrolls.approve')) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-success btn-approve-payroll" data-id="' . $id . '" title="' . __('Approve Payroll') . '">
                        <i class="icon-base bx bx-check"></i>
                    </button>';
                }

                // Disburse / Pay (Draft or Approved)
                if (in_array($status, [PayrollStatus::DRAFT->value, PayrollStatus::APPROVED->value]) && $user->can('hr.payrolls.disburse')) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-disburse-payroll" data-id="' . $id . '" data-amount="' . number_format($payroll->total_net, 2) . ' ' . __('KWD') . '" title="' . __('Disburse / Mark as Paid') . '">
                        <i class="icon-base bx bx-wallet"></i>
                    </button>';
                }

                // Cancel (Draft or Approved)
                if ($status !== PayrollStatus::PAID->value && $status !== PayrollStatus::CANCELLED->value && $user->can('hr.payrolls.cancel')) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-cancel-payroll" data-id="' . $id . '" title="' . __('Cancel Payroll') . '">
                        <i class="icon-base bx bx-x"></i>
                    </button>';
                }

                // Delete (not paid)
                if ($status !== PayrollStatus::PAID->value && $user->can('hr.payrolls.delete')) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-secondary btn-delete-payroll" data-id="' . $id . '" title="' . __('Delete') . '">
                        <i class="icon-base bx bx-trash"></i>
                    </button>';
                }

                return $buttons ? '<div class="d-inline-flex gap-1">' . $buttons . '</div>' : '<span class="text-muted">---</span>';
            })
            ->rawColumns(['payroll_code', 'month_info', 'branch_info', 'employees_count_formatted', 'gross_formatted', 'deductions_formatted', 'net_formatted', 'status_badge', 'actions'])
            ->make(true);
    }

    /**
     * Generate a new monthly payroll run.
     */
    public function store(GeneratePayrollRequest $request): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.payrolls.create'), 403);

        try {
            $payroll = $this->payrollService->generatePayroll(
                month: $request->payroll_month,
                branchId: $request->branch_id ? (int) $request->branch_id : null,
                title: $request->title,
                notes: $request->notes,
                userId: auth()->id() ?? 1
            );

            return response()->json([
                'success' => true,
                'message' => __('Payroll generated successfully with :count employees processed.', ['count' => $payroll->employees_count]),
                'data'    => [
                    'id'             => $payroll->id,
                    'payroll_number' => $payroll->payroll_number,
                    'url'            => route('hr.payrolls.show', $payroll->id),
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Display detailed payroll run with employees items table.
     */
    public function show(int $id)
    {
        abort_if(! auth()->user()->can('hr.payrolls.view'), 403);

        $payroll = $this->payrollRepo->findWithDetails($id);
        if (!$payroll) {
            abort(404, __('Payroll run not found.'));
        }

        return view('hr::payrolls.show', compact('payroll'));
    }

    /**
     * Server-side DataTables endpoint for items within a specific payroll run.
     */
    public function itemsData(int $id, Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.payrolls.view'), 403);

        $payroll = $this->payrollRepo->find($id);
        if (!$payroll) {
            return response()->json(['error' => __('Payroll run not found.')], 404);
        }

        $query = $payroll->items()->with(['employee.translations', 'employee.jobTitle', 'employee.department', 'employee.primaryBranch']);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->filter(function ($q) use ($request) {
                if ($search = $request->input('search.value')) {
                    $q->where(function ($sub) use ($search) {
                        $sub->whereHas('employee.translations', function ($empQ) use ($search) {
                            $empQ->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('employee', function ($empQ) use ($search) {
                            $empQ->where('employee_code', 'like', "%{$search}%");
                        });
                    });
                }
            })
            ->addColumn('employee_info', function ($item) {
                $emp = $item->employee;
                if (!$emp) return '-';

                $avatarUrl = $emp->avatar_url;
                $name = e($emp->name);
                $code = e($emp->employee_code);
                $job = $emp->jobTitle ? e($emp->jobTitle->name) : '';
                $dept = $emp->department ? e($emp->department->name) : '';

                return '<div class="d-flex align-items-center gap-2">
                    <img src="' . $avatarUrl . '" alt="' . $name . '" class="rounded-circle" width="36" height="36">
                    <div>
                        <strong class="text-heading d-block">' . $name . '</strong>
                        <small class="text-muted">' . $code . ($job ? ' • ' . $job : '') . ($dept ? ' • ' . $dept : '') . '</small>
                    </div>
                </div>';
            })
            ->addColumn('basic_salary_formatted', fn($item) => number_format($item->basic_salary, 2) . ' ' . __('KWD'))
            ->addColumn('allowances_formatted', fn($item) => number_format($item->allowances_amount, 2) . ' ' . __('KWD'))
            ->addColumn('overtime_formatted', function ($item) {
                if ($item->overtime_hours > 0) {
                    return '<span class="text-heading fw-medium">' . number_format($item->overtime_amount, 2) . ' ' . __('KWD') . '</span><br><small class="text-muted">(' . $item->overtime_hours . ' ' . __('hrs') . ')</small>';
                }
                return '<span class="text-muted">-</span>';
            })
            ->addColumn('rewards_formatted', function ($item) {
                if ($item->rewards_amount > 0) {
                    return '<span class="text-success fw-semibold">+' . number_format($item->rewards_amount, 2) . ' ' . __('KWD') . '</span>';
                }
                return '<span class="text-muted">-</span>';
            })
            ->addColumn('gross_formatted', fn($item) => '<strong class="text-heading">' . number_format($item->gross_salary, 2) . ' ' . __('KWD') . '</strong>')
            ->addColumn('deductions_breakdown', function ($item) {
                $html = '<div class="small">';
                if ($item->absence_deduction > 0) {
                    $html .= '<span class="d-block text-danger">' . __('Absence') . ': -' . number_format($item->absence_deduction, 2) . '</span>';
                }
                if ($item->late_deduction > 0) {
                    $html .= '<span class="d-block text-danger">' . __('Late') . ': -' . number_format($item->late_deduction, 2) . '</span>';
                }
                if ($item->loans_deduction > 0) {
                    $html .= '<span class="d-block text-warning">' . __('Loan') . ': -' . number_format($item->loans_deduction, 2) . '</span>';
                }
                if ($item->penalties_deduction > 0) {
                    $html .= '<span class="d-block text-danger">' . __('Penalty') . ': -' . number_format($item->penalties_deduction, 2) . '</span>';
                }
                if ($item->insurance_deduction > 0) {
                    $html .= '<span class="d-block text-secondary">' . __('PIFSS') . ': -' . number_format($item->insurance_deduction, 2) . '</span>';
                }
                if ($item->total_deductions == 0) {
                    $html .= '<span class="text-muted">-</span>';
                } else {
                    $html .= '<strong class="d-block text-danger border-top pt-1 mt-1">' . __('Total') . ': -' . number_format($item->total_deductions, 2) . ' ' . __('KWD') . '</strong>';
                }
                $html .= '</div>';
                return $html;
            })
            ->addColumn('net_salary_formatted', fn($item) => '<strong class="text-success fw-bold fs-6">' . number_format($item->net_salary, 2) . ' ' . __('KWD') . '</strong>')
            ->addColumn('payment_status_badge', function ($item) {
                $statusEnum = $item->payment_status;
                $color = $statusEnum?->textColor() ?? 'text-warning';
                $icon = $statusEnum?->icon() ?? 'bx-time-five';
                $label = $statusEnum?->label() ?? __('Pending Payment');

                return '<span class="fw-semibold ' . $color . ' d-inline-flex align-items-center gap-1">
                    <i class="icon-base bx ' . $icon . ' font-14"></i>
                    <span>' . $label . '</span>
                </span>';
            })
            ->addColumn('actions', function ($item) use ($payroll) {
                $user = auth()->user();
                $itemId = $item->id;
                $payrollId = $payroll->id;
                $isDraft = ($payroll->status === PayrollStatus::DRAFT);

                $buttons = '';

                // View Payslip (Outline Info)
                if ($user->can('hr.payrolls.view')) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-info btn-view-payslip" data-payroll-id="' . $payrollId . '" data-item-id="' . $itemId . '" title="' . __('View Payslip') . '">
                        <i class="icon-base bx bx-show"></i>
                    </button>';
                }

                // Edit Item (Draft only - Outline Warning)
                if ($isDraft && $user->can('hr.payrolls.edit')) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-warning btn-edit-item" data-payroll-id="' . $payrollId . '" data-item-id="' . $itemId . '" title="' . __('Adjust Item') . '">
                        <i class="icon-base bx bx-edit"></i>
                    </button>';
                }

                return $buttons ? '<div class="d-inline-flex gap-1">' . $buttons . '</div>' : '<span class="text-muted">---</span>';
            })
            ->rawColumns(['employee_info', 'overtime_formatted', 'rewards_formatted', 'gross_formatted', 'deductions_breakdown', 'net_salary_formatted', 'payment_status_badge', 'actions'])
            ->make(true);
    }

    /**
     * Recalculate draft payroll.
     */
    public function recalculate(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.payrolls.recalculate'), 403);

        $payroll = $this->payrollRepo->find($id);
        if (!$payroll) {
            return response()->json(['success' => false, 'message' => __('Payroll not found.')], 404);
        }

        try {
            $updated = $this->payrollService->recalculate($payroll);
            return response()->json([
                'success' => true,
                'message' => __('Payroll recalculated successfully for :count employees.', ['count' => $updated->employees_count]),
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Approve draft payroll.
     */
    public function approve(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.payrolls.approve'), 403);

        $payroll = $this->payrollRepo->find($id);
        if (!$payroll) {
            return response()->json(['success' => false, 'message' => __('Payroll not found.')], 404);
        }

        try {
            $this->payrollService->approve($payroll, auth()->id() ?? 1);
            return response()->json([
                'success' => true,
                'message' => __('Payroll approved successfully.'),
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Disburse / Mark as Paid and synchronize all connected modules.
     */
    public function disburse(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.payrolls.disburse'), 403);

        $payroll = $this->payrollRepo->find($id);
        if (!$payroll) {
            return response()->json(['success' => false, 'message' => __('Payroll not found.')], 404);
        }

        try {
            $this->payrollService->disburse($payroll, auth()->id() ?? 1);
            return response()->json([
                'success' => true,
                'message' => __('Payroll marked as disbursed/paid. All loan installments, rewards, and penalties have been synchronized.'),
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Cancel payroll run.
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.payrolls.cancel'), 403);

        $request->validate(['reason' => 'required|string|max:1000']);

        $payroll = $this->payrollRepo->find($id);
        if (!$payroll) {
            return response()->json(['success' => false, 'message' => __('Payroll not found.')], 404);
        }

        try {
            $this->payrollService->cancel($payroll, $request->reason, auth()->id() ?? 1);
            return response()->json([
                'success' => true,
                'message' => __('Payroll cancelled successfully.'),
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Delete payroll run.
     */
    public function destroy(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.payrolls.delete'), 403);

        $payroll = $this->payrollRepo->find($id);
        if (!$payroll) {
            return response()->json(['success' => false, 'message' => __('Payroll not found.')], 404);
        }

        try {
            $this->payrollService->delete($payroll);
            return response()->json([
                'success' => true,
                'message' => __('Payroll deleted successfully.'),
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Get single item data for edit modal.
     */
    public function getItem(int $id, int $itemId): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.payrolls.view'), 403);

        $item = $this->payrollRepo->findItem($itemId);
        if (!$item || $item->payroll_id != $id) {
            return response()->json(['success' => false, 'message' => __('Item not found.')], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                  => $item->id,
                'employee_name'       => $item->employee?->name,
                'basic_salary'        => $item->basic_salary,
                'allowances_amount'   => $item->allowances_amount,
                'overtime_amount'     => $item->overtime_amount,
                'rewards_amount'      => $item->rewards_amount,
                'other_earnings'      => $item->other_earnings,
                'gross_salary'        => $item->gross_salary,
                'absence_deduction'   => $item->absence_deduction,
                'late_deduction'      => $item->late_deduction,
                'loans_deduction'     => $item->loans_deduction,
                'penalties_deduction' => $item->penalties_deduction,
                'insurance_deduction' => $item->insurance_deduction,
                'other_deductions'    => $item->other_deductions,
                'total_deductions'    => $item->total_deductions,
                'net_salary'          => $item->net_salary,
                'notes'               => $item->notes,
            ],
        ]);
    }

    /**
     * Update individual payroll item.
     */
    public function updateItem(UpdatePayrollItemRequest $request, int $id, int $itemId): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.payrolls.edit'), 403);

        $item = $this->payrollRepo->findItem($itemId);
        if (!$item || $item->payroll_id != $id) {
            return response()->json(['success' => false, 'message' => __('Item not found.')], 404);
        }

        try {
            $updated = $this->payrollService->updateItem($item, $request->validated());
            return response()->json([
                'success' => true,
                'message' => __('Payroll item updated successfully.'),
                'data'    => $updated,
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Detailed employee payslip modal data.
     */
    public function payslip(int $id, int $itemId): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.payrolls.view'), 403);

        $item = $this->payrollRepo->findItem($itemId);
        if (!$item || $item->payroll_id != $id) {
            return response()->json(['success' => false, 'message' => __('Item not found.')], 404);
        }

        $emp = $item->employee;
        $payroll = $item->payroll;

        return response()->json([
            'success' => true,
            'data'    => [
                'payroll_number'      => $payroll->payroll_number,
                'payroll_month'       => $payroll->payroll_month,
                'employee'            => [
                    'name'            => $emp?->name,
                    'code'            => $emp?->employee_code,
                    'civil_id'        => $emp?->national_id,
                    'avatar'          => $emp?->avatar_url,
                    'job_title'       => $emp?->jobTitle?->name ?? '-',
                    'department'      => $emp?->department?->name ?? '-',
                    'branch'          => $emp?->primaryBranch?->name ?? '-',
                    'bank_name'       => $item->bank_name ?: ($emp?->bank?->name ?? '-'),
                    'iban'            => $item->iban ?: ($emp?->iban ?? '-'),
                ],
                'earnings'            => [
                    'basic_salary'    => number_format($item->basic_salary, 2),
                    'allowances'      => number_format($item->allowances_amount, 2),
                    'allowances_list' => $item->allowances_breakdown ?: [],
                    'overtime_hours'  => $item->overtime_hours,
                    'overtime_amount' => number_format($item->overtime_amount, 2),
                    'rewards_amount'  => number_format($item->rewards_amount, 2),
                    'other_earnings'  => number_format($item->other_earnings, 2),
                    'gross_salary'    => number_format($item->gross_salary, 2),
                ],
                'deductions'          => [
                    'absence_days'       => $item->absence_days,
                    'absence_deduction'  => number_format($item->absence_deduction, 2),
                    'late_minutes'       => $item->late_minutes,
                    'late_deduction'     => number_format($item->late_deduction, 2),
                    'loans_deduction'    => number_format($item->loans_deduction, 2),
                    'penalties_deduction'=> number_format($item->penalties_deduction, 2),
                    'insurance_deduction'=> number_format($item->insurance_deduction, 2),
                    'other_deductions'   => number_format($item->other_deductions, 2),
                    'total_deductions'   => number_format($item->total_deductions, 2),
                ],
                'net_salary'          => number_format($item->net_salary, 2),
                'payment_status'      => $item->payment_status?->label() ?? __('Pending Payment'),
                'payment_color'       => $item->payment_status?->textColor() ?? 'text-warning',
            ],
        ]);
    }

    /**
     * Export WPS / Bank salary transfer file (CSV formatted with UTF-8 BOM for Excel).
     */
    public function exportWps(int $id)
    {
        abort_if(! auth()->user()->can('hr.payrolls.export-wps'), 403);

        $payroll = $this->payrollRepo->findWithDetails($id);
        if (!$payroll) {
            abort(404, __('Payroll run not found.'));
        }

        $filename = 'WPS_' . $payroll->payroll_month . '_' . $payroll->payroll_number . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($payroll) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for Arabic characters in Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // CSV Header Row
            fputcsv($file, [
                __('Employee Code'),
                __('Employee Name'),
                __('Civil ID'),
                __('Bank Name'),
                __('IBAN'),
                __('Account Number'),
                __('Basic Salary (KWD)'),
                __('Allowances (KWD)'),
                __('Overtime (KWD)'),
                __('Rewards (KWD)'),
                __('Gross Salary (KWD)'),
                __('Deductions (KWD)'),
                __('Net Salary (KWD)'),
            ]);

            foreach ($payroll->items as $item) {
                $emp = $item->employee;
                fputcsv($file, [
                    $emp?->employee_code ?? '',
                    $emp?->name ?? '',
                    $emp?->national_id ?? '',
                    $item->bank_name ?? '',
                    $item->iban ?? '',
                    $item->account_number ?? '',
                    number_format($item->basic_salary, 2, '.', ''),
                    number_format($item->allowances_amount, 2, '.', ''),
                    number_format($item->overtime_amount, 2, '.', ''),
                    number_format($item->rewards_amount, 2, '.', ''),
                    number_format($item->gross_salary, 2, '.', ''),
                    number_format($item->total_deductions, 2, '.', ''),
                    number_format($item->net_salary, 2, '.', ''),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
