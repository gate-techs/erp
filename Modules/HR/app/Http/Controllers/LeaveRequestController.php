<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Modules\HR\Enums\LeaveRequestStatus;
use Modules\HR\Enums\LeaveType;
use Modules\HR\Http\Requests\StoreLeaveRequestRequest;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;
use Modules\HR\Models\LeaveRequest;
use Modules\HR\Services\LeaveRequestService;
use Modules\Nursery\Models\NurseryBranch;
use Yajra\DataTables\Facades\DataTables;

class LeaveRequestController extends Controller
{
    //======================
    public function __construct(
        protected LeaveRequestService $leaveService
    ) {}

    //======================
    /**
     * Display the Leave Requests index screen with KPI cards and filters.
     */
    public function index(Request $request): View
    {
        abort_if(! auth()->user()->can('hr.leaves.view'), 403);

        $kpis        = $this->leaveService->getKPIs();
        $employees   = Employee::with('translations')->where('status', 'active')->orderBy('id')->get();
        $branches    = NurseryBranch::with('translations')->latest('id')->get();
        $departments = Department::with('translations')->whereNotNull('parent_id')->get();
        $leaveTypes  = LeaveType::cases();
        $statuses    = LeaveRequestStatus::cases();

        return view('hr::leave_requests.index', compact(
            'kpis',
            'employees',
            'branches',
            'departments',
            'leaveTypes',
            'statuses'
        ));
    }

    //======================
    /**
     * Server-side DataTables endpoint for Leave Requests.
     */
    public function data(Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.leaves.view'), 403);

        $query = $this->leaveService->getDataTableQuery($request->all());

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('employee_info', function (LeaveRequest $item) {
                $emp = $item->employee;
                if (!$emp) {
                    return '<span class="text-muted">' . __('Unknown Employee') . '</span>';
                }

                $avatarUrl = $emp->avatar ? asset('storage/' . $emp->avatar) : asset('assets/img/avatars/1.png');
                $deptName  = $emp->department?->name ?? '-';
                $code      = $emp->code ?? '-';

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
            ->addColumn('leave_type_badge', function (LeaveRequest $item) {
                $type = $item->leave_type;
                if (!$type) {
                    return '<span class="badge bg-label-secondary">-</span>';
                }

                return '<span class="badge ' . $type->badgeClass() . ' d-inline-flex align-items-center gap-1">
                            <i class="icon-base ' . $type->icon() . ' font-12"></i>
                            <span>' . $type->label() . '</span>
                        </span>';
            })
            ->addColumn('period', function (LeaveRequest $item) {
                $start = $item->start_date ? $item->start_date->format('Y-m-d') : '-';
                $end   = $item->end_date ? $item->end_date->format('Y-m-d') : '-';

                return '<div class="d-flex flex-column small">
                            <span class="text-heading fw-semibold">' . $start . ' <span class="text-muted">&rarr;</span> ' . $end . '</span>
                            <span class="text-primary fw-bold">' . $item->days_count . ' ' . __('Days') . '</span>
                        </div>';
            })
            ->addColumn('replacement_info', function (LeaveRequest $item) {
                $rep = $item->replacementEmployee;
                if (!$rep) {
                    return '<span class="text-muted small">' . __('None') . '</span>';
                }
                return '<span class="small fw-medium text-heading">' . e($rep->name) . '</span>';
            })
            ->addColumn('status_badge', function (LeaveRequest $item) {
                $status = $item->status;
                if (!$status) {
                    return '<span class="badge bg-label-secondary">-</span>';
                }

                return '<span class="badge ' . $status->badgeClass() . ' d-inline-flex align-items-center gap-1">
                            <i class="icon-base ' . $status->icon() . ' font-12"></i>
                            <span>' . $status->label() . '</span>
                        </span>';
            })
            ->addColumn('attachment_btn', function (LeaveRequest $item) {
                if (!$item->attachment) {
                    return '<span class="text-muted small">-</span>';
                }

                return '<a href="' . asset('storage/' . $item->attachment) . '" target="_blank" class="btn btn-icon btn-sm btn-outline-secondary" title="' . __('View Attachment') . '">
                            <i class="icon-base bx bx-paperclip"></i>
                        </a>';
            })
            ->addColumn('actions', function (LeaveRequest $item) {
                $user      = auth()->user();
                $statusVal = $item->status?->value ?? '';
                $buttons   = '';

                // View Details button
                if ($user->can('hr.leaves.view')) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-view-leave" 
                                    data-id="' . $item->id . '" title="' . __('View Details') . '">
                                    <i class="icon-base bx bx-show"></i>
                                 </button>';
                }

                // Approve, Reject & Delete buttons for Pending or Manager Approved
                if ($statusVal === LeaveRequestStatus::PENDING->value || $statusVal === LeaveRequestStatus::MANAGER_APPROVED->value) {
                    if ($user->can('hr.leaves.approve')) {
                        $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-success btn-approve-leave" 
                                        data-id="' . $item->id . '" title="' . __('Approve Request') . '">
                                        <i class="icon-base bx bx-check"></i>
                                     </button>';
                    }

                    if ($user->can('hr.leaves.reject')) {
                        $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-warning btn-reject-leave" 
                                        data-id="' . $item->id . '" title="' . __('Reject Request') . '">
                                        <i class="icon-base bx bx-x"></i>
                                     </button>';
                    }

                    if ($user->can('hr.leaves.delete')) {
                        $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-leave" 
                                        data-id="' . $item->id . '" data-name="' . e($item->employee?->name ?? '') . '" title="' . __('Delete') . '">
                                        <i class="icon-base bx bx-trash"></i>
                                     </button>';
                    }
                }

                return $buttons ? '<div class="d-inline-flex gap-1">' . $buttons . '</div>' : '<span class="text-muted">---</span>';
            })
            ->rawColumns(['employee_info', 'leave_type_badge', 'period', 'replacement_info', 'status_badge', 'attachment_btn', 'actions'])
            ->make(true);
    }

    //======================
    /**
     * AJAX endpoint: Get employee live balance and calculate net days.
     */
    public function checkBalance(Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.leaves.create'), 403);

        try {
            $employeeId = (int) $request->get('employee_id');
            $leaveType  = (string) $request->get('leave_type', 'annual');
            $startDate  = $request->get('start_date');
            $endDate    = $request->get('end_date');

            if (!$employeeId) {
                return response()->json(['success' => false, 'message' => __('Please select an employee.')], 422);
            }

            $balance = $this->leaveService->getEmployeeLeaveBalance($employeeId, $leaveType, $startDate);

            $daysCalculation = null;
            if (!empty($startDate) && !empty($endDate)) {
                $daysCalculation = $this->leaveService->calculateLeaveDays($employeeId, $startDate, $endDate);
            }

            return response()->json([
                'success'     => true,
                'balance'     => $balance,
                'calculation' => $daysCalculation,
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
     * Store new leave request.
     */
    public function store(StoreLeaveRequestRequest $request): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.leaves.create'), 403);

        try {
            $data = $request->validated();
            if ($request->hasFile('attachment')) {
                $data['attachment'] = $request->file('attachment');
            }

            $leave = $this->leaveService->createLeaveRequest($data, Auth::id());

            return response()->json([
                'success' => true,
                'message' => __('Leave request submitted successfully.'),
                'data'    => $leave,
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
     * Show leave request details for viewing/modal.
     */
    public function show(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.leaves.view'), 403);

        try {
            $leave = $this->leaveService->getById($id);
            if (!$leave) {
                return response()->json(['success' => false, 'message' => __('Leave request not found.')], 404);
            }

            $emp     = $leave->employee;
            $rep     = $leave->replacementEmployee;
            $balance = $this->leaveService->getEmployeeLeaveBalance($leave->employee_id, $leave->leave_type->value, $leave->start_date->format('Y-m-d'));
            $calc    = $this->leaveService->calculateLeaveDays($leave->employee_id, $leave->start_date->format('Y-m-d'), $leave->end_date->format('Y-m-d'));

            $statusBadge = '<span class="badge ' . $leave->status->badgeClass() . ' d-inline-flex align-items-center gap-1">
                                <i class="icon-base ' . $leave->status->icon() . ' font-12"></i>
                                <span>' . $leave->status->label() . '</span>
                            </span>';

            $data = [
                'id'                      => $leave->id,
                'employee'                => [
                    'id'         => $emp?->id,
                    'name'       => $emp?->name ?? '-',
                    'code'       => $emp?->code ?? '-',
                    'department' => $emp?->department?->name ?? '-',
                ],
                'employee_name'           => $emp?->name ?? '-',
                'employee_code'           => $emp?->code ?? '-',
                'department'              => $emp?->department?->name ?? '-',
                'leave_type'              => $leave->leave_type->value,
                'leave_type_label'        => $leave->leave_type->label(),
                'leave_type_badge'        => $leave->leave_type->badgeClass(),
                'start_date'              => $leave->start_date->format('Y-m-d'),
                'end_date'                => $leave->end_date->format('Y-m-d'),
                'days_count'              => $leave->days_count,
                'calendar_days'           => $calc['calendar_days'] ?? $leave->days_count,
                'status'                  => $leave->status->label(),
                'status_badge'            => $statusBadge,
                'status_raw'              => $leave->status->value,
                'reason'                  => $leave->reason ?: __('No reason specified'),
                'rejection_reason'        => $leave->rejection_reason,
                'replacement_employee'    => $rep ? ['id' => $rep->id, 'name' => $rep->name] : null,
                'replacement_employee_name' => $rep?->name ?? __('None'),
                'emergency_contact_phone' => $leave->emergency_contact_phone ?? '-',
                'attachment_url'          => $leave->attachment ? asset('storage/' . $leave->attachment) : null,
                'created_at'              => $leave->created_at->format('Y-m-d H:i'),
                'manager_approver'        => $leave->managerApprover?->name,
                'manager_approved_at'     => $leave->manager_approved_at?->format('Y-m-d H:i'),
                'hr_approver'             => $leave->hrApprover?->name,
                'hr_approved_at'          => $leave->hr_approved_at?->format('Y-m-d H:i'),
            ];

            return response()->json([
                'success'      => true,
                'data'         => $data,
                'leave'        => $data,
                'days_details' => $calc['details'] ?? [],
                'balance'      => $balance,
                'calculation'  => $calc,
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
     * Approve leave request.
     */
    public function approve(Request $request, int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.leaves.approve'), 403);

        try {
            $role  = $request->get('role', 'hr');
            $leave = $this->leaveService->approveLeaveRequest($id, Auth::id() ?? 1, $role);

            return response()->json([
                'success' => true,
                'message' => __('Leave request approved successfully.'),
                'data'    => $leave,
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
     * Reject leave request.
     */
    public function reject(Request $request, int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.leaves.reject'), 403);

        try {
            $request->validate([
                'rejection_reason' => ['required', 'string', 'max:500'],
            ]);

            $leave = $this->leaveService->rejectLeaveRequest($id, Auth::id() ?? 1, $request->input('rejection_reason'));

            return response()->json([
                'success' => true,
                'message' => __('Leave request rejected successfully.'),
                'data'    => $leave,
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
     * Delete leave request.
     */
    public function destroy(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('hr.leaves.delete'), 403);

        try {
            $this->leaveService->deleteLeaveRequest($id);

            return response()->json([
                'success' => true,
                'message' => __('Leave request deleted successfully.'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
