<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Modules\HR\Enums\HrSettingType;
use Modules\HR\Enums\RewardCalculationType;
use Modules\HR\Enums\RewardPaymentMethod;
use Modules\HR\Enums\RewardStatus;
use Modules\HR\Http\Requests\StoreRewardRequest;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;
use Modules\HR\Models\HrSetting;
use Modules\HR\Models\Reward;
use Modules\HR\Services\RewardService;
use Yajra\DataTables\Facades\DataTables;

class RewardController extends Controller
{
    public function __construct(
        protected RewardService $rewardService
    ) {}

    /**
     * Display the Rewards & Incentives management index page.
     */
    public function index(): View
    {
        abort_if(! auth()->user()->can('hr.rewards.view'), 403);

        $kpis             = $this->rewardService->getKPIs();
        $employees        = Employee::with('translations')
            ->active()
            ->select('id', 'employee_code', 'department_id', 'basic_salary')
            ->get();
        $rewardTypes      = HrSetting::where('type', HrSettingType::REWARD_TYPES->value)
            ->active()
            ->with('translations')
            ->get();
        $departments      = Department::with('translations')->active()->get();
        $calculationTypes = RewardCalculationType::cases();
        $paymentMethods   = RewardPaymentMethod::cases();
        $statuses         = RewardStatus::cases();

        return view('hr::rewards.index', compact(
            'kpis',
            'employees',
            'rewardTypes',
            'departments',
            'calculationTypes',
            'paymentMethods',
            'statuses'
        ));
    }

    /**
     * Server-side DataTables endpoint for Rewards.
     */
    public function data(Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.rewards.view'), 403);

        $query = $this->rewardService->getDataTableQuery($request->all());

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->filter(function ($query) use ($request) {
                if ($search = $request->input('search.value')) {
                    $query->where(function ($q) use ($search) {
                        $q->where('reward_number', 'like', "%{$search}%")
                            ->orWhere('reason', 'like', "%{$search}%")
                            ->orWhereHas('employee.translations', function ($empQ) use ($search) {
                                $empQ->where('name', 'like', "%{$search}%");
                            })
                            ->orWhereHas('employee', function ($empQ) use ($search) {
                                $empQ->where('employee_code', 'like', "%{$search}%");
                            })
                            ->orWhereHas('rewardType.translations', function ($typeQ) use ($search) {
                                $typeQ->where('name', 'like', "%{$search}%");
                            });
                    });
                }
            })
            ->addColumn('reward_number_badge', function (Reward $item) {
                $awardDate = $item->award_date ? $item->award_date->format('Y-m-d') : '-';
                return '
                    <div class="d-flex flex-column">
                        <span class="fw-bold text-heading">' . e($item->reward_number) . '</span>
                        <small class="text-muted d-flex align-items-center gap-1">
                            <i class="icon-base bx bx-calendar font-12"></i> ' . $awardDate . '
                        </small>
                    </div>
                ';
            })
            ->addColumn('employee_info', function (Reward $item) {
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
            ->addColumn('reward_type_badge', function (Reward $item) {
                $typeName = $item->rewardType?->name ?? __('General Bonus');
                return '<span class="fw-semibold text-primary d-inline-flex align-items-center gap-1">
                            <i class="icon-base bx bx-gift font-14"></i>
                            <span>' . e($typeName) . '</span>
                        </span>';
            })
            ->addColumn('calculation_info', function (Reward $item) {
                $calcType = $item->calculation_type;
                if (!$calcType) {
                    return '<span class="text-muted">-</span>';
                }

                $textColor = $calcType->textColor();
                $icon      = $calcType->icon();
                $label     = $calcType->label();

                $valText = '';
                if ($calcType === RewardCalculationType::FIXED_AMOUNT) {
                    $valText = number_format($item->calculation_value, 2) . ' ' . __('KWD');
                } elseif ($calcType === RewardCalculationType::PERCENTAGE) {
                    $valText = number_format($item->calculation_value, 0) . '%';
                } else {
                    $valText = number_format($item->calculation_value, 0) . ' ' . __('Days');
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
            ->addColumn('amount_formatted', function (Reward $item) {
                return '<strong class="text-success fs-6">' . number_format($item->amount, 2) . '</strong> <small class="text-muted">' . __('KWD') . '</small>';
            })
            ->addColumn('payment_info', function (Reward $item) {
                $method = $item->payment_method;
                if (!$method) {
                    return '<span class="text-muted">-</span>';
                }

                $sub = '';
                if ($method === RewardPaymentMethod::NEXT_PAYROLL && $item->payroll_month) {
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
            ->addColumn('status_badge', function (Reward $item) {
                $status = $item->status;
                if (!$status) {
                    return '<span class="text-muted">-</span>';
                }

                return '<span class="fw-semibold ' . $status->textColor() . ' d-inline-flex align-items-center gap-1">
                            <i class="icon-base ' . $status->icon() . ' font-14"></i>
                            <span>' . $status->label() . '</span>
                        </span>';
            })
            ->addColumn('actions', function (Reward $item) {
                $user = auth()->user();
                $buttons = '';

                // View Details Button
                if ($user->can('hr.rewards.view')) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-info btn-view-reward" 
                                    data-id="' . $item->id . '" title="' . __('View Details') . '">
                                    <i class="icon-base bx bx-show"></i>
                                 </button>';
                }

                // Approve Button (Pending)
                if ($item->status === RewardStatus::PENDING && $user->can('hr.rewards.approve')) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-success btn-approve-reward" 
                                    data-id="' . $item->id . '" title="' . __('Approve Reward') . '">
                                    <i class="icon-base bx bx-check"></i>
                                 </button>';
                }

                // Pay Button (Pending or Approved)
                if (in_array($item->status, [RewardStatus::PENDING, RewardStatus::APPROVED]) && $user->can('hr.rewards.pay')) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-pay-reward" 
                                    data-id="' . $item->id . '" data-amount="' . $item->amount . '" title="' . __('Pay Reward') . '">
                                    <i class="icon-base bx bx-credit-card"></i>
                                 </button>';
                }

                // Reject Button (Pending or Approved)
                if (in_array($item->status, [RewardStatus::PENDING, RewardStatus::APPROVED]) && $user->can('hr.rewards.reject')) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-warning btn-reject-reward" 
                                    data-id="' . $item->id . '" title="' . __('Reject Reward') . '">
                                    <i class="icon-base bx bx-x"></i>
                                 </button>';
                }

                // Delete Button (Not Paid)
                if ($item->status !== RewardStatus::PAID && $user->can('hr.rewards.delete')) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-reward" 
                                    data-id="' . $item->id . '" data-name="' . e($item->reward_number) . '" title="' . __('Delete') . '">
                                    <i class="icon-base bx bx-trash"></i>
                                 </button>';
                }

                return $buttons ? '<div class="d-inline-flex gap-1">' . $buttons . '</div>' : '<span class="text-muted">---</span>';
            })
            ->rawColumns([
                'reward_number_badge',
                'employee_info',
                'reward_type_badge',
                'calculation_info',
                'amount_formatted',
                'payment_info',
                'status_badge',
                'actions',
            ])
            ->make(true);
    }

    /**
     * Store a newly created reward.
     */
    public function store(StoreRewardRequest $request): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.rewards.create'), 403);

        try {
            $reward = $this->rewardService->createReward(
                $request->validated(),
                $request->file('attachment'),
                Auth::id() ?? 1
            );

            return response()->json([
                'success' => true,
                'message' => __('Reward submitted successfully.'),
                'data'    => $reward,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to process reward: ') . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Live preview calculation AJAX endpoint.
     */
    public function calculate(Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.rewards.create'), 403);

        $request->validate([
            'employee_id'        => 'required|integer|exists:employees,id',
            'calculation_type'   => 'required|string',
            'calculation_value'  => 'required|numeric|min:0.01',
        ]);

        try {
            $result = $this->rewardService->calculateAmount(
                (int) $request->employee_id,
                $request->calculation_type,
                (float) $request->calculation_value
            );

            return response()->json([
                'success' => true,
                'data'    => $result,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Show reward details.
     */
    public function show(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.rewards.view'), 403);

        $reward = $this->rewardService->getById($id, [
            'employee.translations',
            'employee.department.translations',
            'employee.jobTitle.translations',
            'rewardType.translations',
            'creator',
            'approver',
            'payer',
            'rejecter',
        ]);

        if (!$reward) {
            return response()->json([
                'success' => false,
                'message' => __('Reward not found.'),
            ], 404);
        }

        $emp = $reward->employee;
        $attachmentUrl = $reward->attachment ? asset('storage/' . $reward->attachment) : null;

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                 => $reward->id,
                'reward_number'      => $reward->reward_number,
                'award_date'         => $reward->award_date ? $reward->award_date->format('Y-m-d') : '-',
                'status'             => $reward->status->value,
                'status_label'       => $reward->status->label(),
                'status_badge'       => $reward->status->badgeClass(),
                'status_color'       => $reward->status->textColor(),
                'status_icon'        => $reward->status->icon(),
                'reward_type'        => $reward->rewardType?->name ?? '-',
                'calculation_type'   => $reward->calculation_type->label(),
                'calculation_value'  => $reward->calculation_value,
                'amount'             => number_format($reward->amount, 2),
                'amount_raw'         => $reward->amount,
                'payment_method'     => $reward->payment_method->label(),
                'payroll_month'      => $reward->payroll_month ?? '-',
                'reason'             => $reward->reason,
                'notes'              => $reward->notes,
                'attachment_url'     => $attachmentUrl,
                'rejection_reason'   => $reward->rejection_reason,
                'employee'           => [
                    'id'           => $emp?->id,
                    'name'         => $emp?->name ?? '-',
                    'code'         => $emp?->employee_code ?? '-',
                    'department'   => $emp?->department?->name ?? '-',
                    'job_title'    => $emp?->jobTitle?->name ?? '-',
                    'basic_salary' => $emp?->basic_salary ? number_format($emp->basic_salary, 2) : '-',
                    'avatar'       => $emp?->avatar ? asset('storage/' . $emp->avatar) : asset('assets/img/avatars/1.png'),
                ],
                'created_by'         => $reward->creator?->name ?? '-',
                'created_at'         => $reward->created_at?->format('Y-m-d H:i') ?? '-',
                'approved_by'        => $reward->approver?->name ?? '-',
                'approved_at'        => $reward->approved_at?->format('Y-m-d H:i') ?? '-',
                'paid_by'            => $reward->payer?->name ?? '-',
                'paid_at'            => $reward->paid_at?->format('Y-m-d H:i') ?? '-',
                'rejected_by'        => $reward->rejecter?->name ?? '-',
                'rejected_at'        => $reward->rejected_at?->format('Y-m-d H:i') ?? '-',
            ],
        ]);
    }

    /**
     * Approve reward.
     */
    public function approve(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.rewards.approve'), 403);

        try {
            $reward = $this->rewardService->getById($id, []);
            if (!$reward) {
                return response()->json(['success' => false, 'message' => __('Reward not found.')], 404);
            }

            $this->rewardService->approveReward($reward, Auth::id() ?? 1);

            return response()->json([
                'success' => true,
                'message' => __('Reward approved successfully.'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Pay / disburse reward.
     */
    public function pay(int $id, Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.rewards.pay'), 403);

        try {
            $reward = $this->rewardService->getById($id, []);
            if (!$reward) {
                return response()->json(['success' => false, 'message' => __('Reward not found.')], 404);
            }

            $this->rewardService->payReward($reward, Auth::id() ?? 1, $request->input('notes'));

            return response()->json([
                'success' => true,
                'message' => __('Reward marked as paid successfully.'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Reject reward with reason.
     */
    public function reject(int $id, Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.rewards.reject'), 403);

        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        try {
            $reward = $this->rewardService->getById($id, []);
            if (!$reward) {
                return response()->json(['success' => false, 'message' => __('Reward not found.')], 404);
            }

            $this->rewardService->rejectReward($reward, Auth::id() ?? 1, $request->input('reason'));

            return response()->json([
                'success' => true,
                'message' => __('Reward rejected successfully.'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Delete a reward.
     */
    public function destroy(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.rewards.delete'), 403);

        try {
            $reward = $this->rewardService->getById($id, []);
            if (!$reward) {
                return response()->json(['success' => false, 'message' => __('Reward not found.')], 404);
            }

            $this->rewardService->deleteReward($reward);

            return response()->json([
                'success' => true,
                'message' => __('Reward deleted successfully.'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
