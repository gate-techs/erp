<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Modules\HR\Enums\HrSettingType;
use Modules\HR\Enums\PenaltyCalculationType;
use Modules\HR\Enums\PenaltyDeductionMethod;
use Modules\HR\Enums\PenaltyStatus;
use Modules\HR\Http\Requests\StorePenaltyRequest;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;
use Modules\HR\Models\HrSetting;
use Modules\HR\Models\Penalty;
use Modules\HR\Services\PenaltyService;
use Yajra\DataTables\Facades\DataTables;

class PenaltyController extends Controller
{
    public function __construct(
        protected PenaltyService $penaltyService
    ) {}

    /**
     * Display the Penalties & Disciplinary Actions management index page.
     */
    public function index(): View
    {
        abort_if(! auth()->user()->can('hr.penalties.view'), 403);

        $kpis             = $this->penaltyService->getKPIs();
        $employees        = Employee::with('translations')
            ->active()
            ->select('id', 'employee_code', 'department_id', 'basic_salary')
            ->get();
        $penaltyTypes     = HrSetting::where('type', HrSettingType::PENALTY_TYPES->value)
            ->active()
            ->with('translations')
            ->get();
        $departments      = Department::with('translations')->active()->get();
        $calculationTypes = PenaltyCalculationType::cases();
        $deductionMethods = PenaltyDeductionMethod::cases();
        $statuses         = PenaltyStatus::cases();

        return view('hr::penalties.index', compact(
            'kpis',
            'employees',
            'penaltyTypes',
            'departments',
            'calculationTypes',
            'deductionMethods',
            'statuses'
        ));
    }

    /**
     * Server-side DataTables endpoint for Penalties.
     */
    public function data(Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.penalties.view'), 403);

        $query = $this->penaltyService->getDataTableQuery($request->all());

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->filter(function ($query) use ($request) {
                if ($search = $request->input('search.value')) {
                    $query->where(function ($q) use ($search) {
                        $q->where('penalty_number', 'like', "%{$search}%")
                            ->orWhere('reason', 'like', "%{$search}%")
                            ->orWhereHas('employee.translations', function ($empQ) use ($search) {
                                $empQ->where('name', 'like', "%{$search}%");
                            })
                            ->orWhereHas('employee', function ($empQ) use ($search) {
                                $empQ->where('employee_code', 'like', "%{$search}%");
                            })
                            ->orWhereHas('penaltyType.translations', function ($typeQ) use ($search) {
                                $typeQ->where('name', 'like', "%{$search}%");
                            });
                    });
                }
            })
            ->addColumn('penalty_number_badge', function (Penalty $item) {
                $penaltyDate = $item->penalty_date ? $item->penalty_date->format('Y-m-d') : '-';
                return '
                    <div class="d-flex flex-column">
                        <span class="fw-bold text-heading">' . e($item->penalty_number) . '</span>
                        <small class="text-muted d-flex align-items-center gap-1">
                            <i class="icon-base bx bx-calendar font-12"></i> ' . $penaltyDate . '
                        </small>
                    </div>
                ';
            })
            ->addColumn('employee_info', function (Penalty $item) {
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
            ->addColumn('penalty_type_badge', function (Penalty $item) {
                $typeName = $item->penaltyType?->name ?? __('General Penalty');
                return '<span class="fw-semibold text-danger d-inline-flex align-items-center gap-1">
                            <i class="icon-base bx bx-error-circle font-14"></i>
                            <span>' . e($typeName) . '</span>
                        </span>';
            })
            ->addColumn('calculation_info', function (Penalty $item) {
                $calcType = $item->calculation_type;
                if (!$calcType) {
                    return '<span class="text-muted">-</span>';
                }

                $textColor = $calcType->textColor();
                $icon      = $calcType->icon();
                $label     = $calcType->label();

                if ($calcType === PenaltyCalculationType::WARNING_ONLY) {
                    return '
                        <span class="fw-semibold text-secondary d-inline-flex align-items-center gap-1">
                            <i class="icon-base ' . $icon . ' font-14"></i>
                            <span>' . $label . '</span>
                        </span>
                    ';
                }

                $valText = '';
                if ($calcType === PenaltyCalculationType::DAYS_DEDUCTION) {
                    $valText = number_format($item->calculation_value, 1) . ' ' . __('Days');
                } elseif ($calcType === PenaltyCalculationType::FIXED_AMOUNT) {
                    $valText = number_format($item->calculation_value, 2) . ' ' . __('KWD');
                } elseif ($calcType === PenaltyCalculationType::PERCENTAGE) {
                    $valText = number_format($item->calculation_value, 0) . '%';
                }

                return '
                    <div class="d-flex flex-column">
                        <span class="fw-semibold ' . $textColor . ' d-inline-flex align-items-center gap-1">
                            <i class="icon-base ' . $icon . ' font-14"></i>
                            <span>' . $label . '</span>
                        </span>
                        <small class="text-muted fw-medium">' . $valText . '</small>
                    </div>
                ';
            })
            ->addColumn('amount_formatted', function (Penalty $item) {
                if ($item->calculation_type === PenaltyCalculationType::WARNING_ONLY || $item->amount <= 0) {
                    return '<span class="text-muted fw-medium">0.00 ' . __('KWD') . '</span>';
                }
                return '<strong class="text-danger fs-6">' . number_format($item->amount, 2) . '</strong> <small class="text-muted">' . __('KWD') . '</small>';
            })
            ->addColumn('deduction_info', function (Penalty $item) {
                $method = $item->deduction_method;
                if (!$method) {
                    return '<span class="text-muted">-</span>';
                }

                $sub = '';
                if ($method === PenaltyDeductionMethod::NEXT_PAYROLL && $item->payroll_month) {
                    $sub = '<small class="text-muted">' . e($item->payroll_month) . '</small>';
                }

                return '
                    <div class="d-flex flex-column">
                        <span class="fw-semibold ' . $method->textColor() . ' d-inline-flex align-items-center gap-1">
                            <i class="icon-base ' . $method->icon() . ' font-14"></i>
                            <span>' . $method->label() . '</span>
                        </span>
                        ' . $sub . '
                    </div>
                ';
            })
            ->addColumn('status_badge', function (Penalty $item) {
                $status = $item->status;
                if (!$status) {
                    return '<span class="text-muted">-</span>';
                }

                return '<span class="fw-semibold ' . $status->textColor() . ' d-inline-flex align-items-center gap-1">
                            <i class="icon-base ' . $status->icon() . ' font-14"></i>
                            <span>' . $status->label() . '</span>
                        </span>';
            })
            ->addColumn('actions', function (Penalty $item) {
                $user = auth()->user();
                $buttons = '';

                // View Details Button
                if ($user->can('hr.penalties.view')) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-info btn-view-penalty" 
                                    data-id="' . $item->id . '" title="' . __('View Details') . '">
                                    <i class="icon-base bx bx-show"></i>
                                 </button>';
                }

                // Approve Button (Pending)
                if ($item->status === PenaltyStatus::PENDING && $user->can('hr.penalties.approve')) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-success btn-approve-penalty" 
                                    data-id="' . $item->id . '" title="' . __('Approve Penalty') . '">
                                    <i class="icon-base bx bx-check"></i>
                                 </button>';
                }

                // Deduct / Apply Button (Pending or Approved)
                if (in_array($item->status, [PenaltyStatus::PENDING, PenaltyStatus::APPROVED]) && $user->can('hr.penalties.deduct')) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-deduct-penalty" 
                                    data-id="' . $item->id . '" 
                                    data-amount="' . number_format($item->amount, 2) . ' ' . __('KWD') . '" 
                                    title="' . __('Deduct Penalty') . '">
                                    <i class="icon-base bx bx-minus-circle"></i>
                                 </button>';
                }

                // Cancel / Waive Button (Pending or Approved)
                if (in_array($item->status, [PenaltyStatus::PENDING, PenaltyStatus::APPROVED]) && $user->can('hr.penalties.cancel')) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-cancel-penalty" 
                                    data-id="' . $item->id . '" title="' . __('Cancel / Waive Penalty') . '">
                                    <i class="icon-base bx bx-block"></i>
                                 </button>';
                }

                // Delete Button (if not deducted)
                if ($item->status !== PenaltyStatus::DEDUCTED && $user->can('hr.penalties.delete')) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-secondary btn-delete-penalty" 
                                    data-id="' . $item->id . '" title="' . __('Delete') . '">
                                    <i class="icon-base bx bx-trash"></i>
                                 </button>';
                }

                return $buttons ? '<div class="d-inline-flex gap-1">' . $buttons . '</div>' : '<span class="text-muted">---</span>';
            })
            ->rawColumns([
                'penalty_number_badge',
                'employee_info',
                'penalty_type_badge',
                'calculation_info',
                'amount_formatted',
                'deduction_info',
                'status_badge',
                'actions',
            ])
            ->make(true);
    }

    /**
     * Live preview calculation endpoint for frontend.
     */
    public function calculate(Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.penalties.create'), 403);

        $request->validate([
            'employee_id'       => 'required|integer|exists:employees,id',
            'calculation_type'  => 'required|string',
            'calculation_value' => 'nullable|numeric',
        ]);

        try {
            $data = $this->penaltyService->calculateAmount(
                (int) $request->input('employee_id'),
                (string) $request->input('calculation_type'),
                (float) ($request->input('calculation_value') ?? 0.0)
            );

            return response()->json([
                'success' => true,
                'data'    => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Store a newly created Penalty record.
     */
    public function store(StorePenaltyRequest $request): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.penalties.create'), 403);

        try {
            $penalty = $this->penaltyService->createPenalty(
                $request->validated(),
                $request->file('attachment'),
                Auth::id() ?? 1
            );

            return response()->json([
                'success' => true,
                'message' => __('Penalty registered successfully.'),
                'data'    => $penalty,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to process penalty: ') . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display detailed modal info for a single penalty.
     */
    public function show(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.penalties.view'), 403);

        $penalty = $this->penaltyService->getById($id);

        if (!$penalty) {
            return response()->json([
                'success' => false,
                'message' => __('Penalty not found.'),
            ], 404);
        }

        $emp = $penalty->employee;
        $attachmentUrl = $penalty->attachment ? asset('storage/' . $penalty->attachment) : null;

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                  => $penalty->id,
                'penalty_number'      => $penalty->penalty_number,
                'penalty_date'        => $penalty->penalty_date ? $penalty->penalty_date->format('Y-m-d') : '-',
                'status'              => $penalty->status->value,
                'status_label'        => $penalty->status->label(),
                'status_badge'        => $penalty->status->badgeClass(),
                'status_color'        => $penalty->status->textColor(),
                'status_icon'         => $penalty->status->icon(),
                'penalty_type'        => $penalty->penaltyType?->name ?? '-',
                'calculation_type'    => $penalty->calculation_type->label(),
                'calculation_value'   => $penalty->calculation_value,
                'amount'              => number_format($penalty->amount, 2),
                'amount_raw'          => $penalty->amount,
                'deduction_method'    => $penalty->deduction_method->label(),
                'payroll_month'       => $penalty->payroll_month ?? '-',
                'reason'              => $penalty->reason,
                'notes'               => $penalty->notes,
                'attachment_url'      => $attachmentUrl,
                'cancellation_reason' => $penalty->cancellation_reason,
                'employee'            => [
                    'id'           => $emp?->id,
                    'name'         => $emp?->name ?? '-',
                    'code'         => $emp?->employee_code ?? '-',
                    'department'   => $emp?->department?->name ?? '-',
                    'job_title'    => $emp?->jobTitle?->name ?? '-',
                    'basic_salary' => $emp?->basic_salary ? number_format($emp->basic_salary, 2) : '-',
                    'avatar'       => $emp?->avatar ? asset('storage/' . $emp->avatar) : asset('assets/img/avatars/1.png'),
                ],
                'created_by'          => $penalty->creator?->name ?? '-',
                'created_at'          => $penalty->created_at ? $penalty->created_at->format('Y-m-d H:i') : '-',
                'approved_by'         => $penalty->approver?->name ?? '-',
                'approved_at'         => $penalty->approved_at ? $penalty->approved_at->format('Y-m-d H:i') : '-',
                'deducted_by'         => $penalty->deductor?->name ?? '-',
                'deducted_at'         => $penalty->deducted_at ? $penalty->deducted_at->format('Y-m-d H:i') : '-',
                'cancelled_by'        => $penalty->canceller?->name ?? '-',
                'cancelled_at'        => $penalty->cancelled_at ? $penalty->cancelled_at->format('Y-m-d H:i') : '-',
            ],
        ]);
    }

    /**
     * Approve a penalty.
     */
    public function approve(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.penalties.approve'), 403);

        $penalty = $this->penaltyService->getById($id);

        if (!$penalty) {
            return response()->json([
                'success' => false,
                'message' => __('Penalty not found.'),
            ], 404);
        }

        try {
            $this->penaltyService->approvePenalty($penalty, Auth::id() ?? 1);

            return response()->json([
                'success' => true,
                'message' => __('Penalty approved successfully.'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Mark penalty as deducted.
     */
    public function deduct(Request $request, int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.penalties.deduct'), 403);

        $penalty = $this->penaltyService->getById($id);

        if (!$penalty) {
            return response()->json([
                'success' => false,
                'message' => __('Penalty not found.'),
            ], 404);
        }

        try {
            $notes = $request->input('notes');
            $this->penaltyService->deductPenalty($penalty, Auth::id() ?? 1, $notes);

            return response()->json([
                'success' => true,
                'message' => __('Penalty marked as deducted successfully.'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Cancel a penalty with justification.
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.penalties.cancel'), 403);

        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $penalty = $this->penaltyService->getById($id);

        if (!$penalty) {
            return response()->json([
                'success' => false,
                'message' => __('Penalty not found.'),
            ], 404);
        }

        try {
            $this->penaltyService->cancelPenalty($penalty, Auth::id() ?? 1, $request->input('reason'));

            return response()->json([
                'success' => true,
                'message' => __('Penalty cancelled successfully.'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Delete penalty.
     */
    public function destroy(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.penalties.delete'), 403);

        $penalty = $this->penaltyService->getById($id);

        if (!$penalty) {
            return response()->json([
                'success' => false,
                'message' => __('Penalty not found.'),
            ], 404);
        }

        try {
            $this->penaltyService->deletePenalty($penalty);

            return response()->json([
                'success' => true,
                'message' => __('Penalty deleted successfully.'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
