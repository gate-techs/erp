<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Modules\HR\Enums\PermissionRequestStatus;
use Modules\HR\Enums\PermissionType;
use Modules\HR\Http\Requests\StorePermissionRequest;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;
use Modules\HR\Models\PermissionRequest;
use Modules\HR\Services\PermissionRequestService;
use Modules\Nursery\Models\NurseryBranch;
use Yajra\DataTables\Facades\DataTables;

class PermissionRequestController extends Controller
{
    public function __construct(
        protected PermissionRequestService $permissionService
    ) {}

    //======================
    /**
     * Display the Permission Requests management index page.
     */
    public function index(): View
    {
        $kpis            = $this->permissionService->getKPIs();
        $branches        = class_exists(NurseryBranch::class) ? NurseryBranch::with('translations')->latest('id')->get() : collect();
        $departments     = Department::with('translations')->active()->get();
        $employees       = Employee::with('translations')
            ->active()
            ->select('id', 'employee_code', 'department_id', 'primary_branch_id')
            ->get();
        $permissionTypes = PermissionType::cases();
        $statuses        = PermissionRequestStatus::cases();

        return view('hr::permission_requests.index', compact(
            'kpis',
            'branches',
            'departments',
            'employees',
            'permissionTypes',
            'statuses'
        ));
    }

    //======================
    /**
     * Server-side DataTables endpoint for Permission Requests.
     */
    public function data(Request $request): JsonResponse
    {
        $query = $this->permissionService->getDataTableQuery($request->all());

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('employee_info', function (PermissionRequest $item) {
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
            ->addColumn('type_badge', function (PermissionRequest $item) {
                $type = $item->type;
                if (!$type) {
                    return '<span class="badge bg-label-secondary">-</span>';
                }

                return '<span class="badge ' . $type->badgeClass() . ' d-inline-flex align-items-center gap-1">
                            <i class="icon-base ' . $type->icon() . ' font-12"></i>
                            <span>' . $type->label() . '</span>
                        </span>';
            })
            ->addColumn('date_time', function (PermissionRequest $item) {
                $date     = $item->date ? $item->date->format('Y-m-d') : '-';
                $fromTime = Carbon::createFromTimeString($item->from_time)->format('h:i A');
                $toTime   = Carbon::createFromTimeString($item->to_time)->format('h:i A');

                return '<div class="d-flex flex-column small">
                            <span class="text-heading fw-semibold">' . $date . '</span>
                            <span class="text-muted">' . $fromTime . ' &rarr; ' . $toTime . '</span>
                        </div>';
            })
            ->addColumn('time_period', function (PermissionRequest $item) {
                $date     = $item->date ? $item->date->format('Y-m-d') : '-';
                $fromTime = Carbon::createFromTimeString($item->from_time)->format('h:i A');
                $toTime   = Carbon::createFromTimeString($item->to_time)->format('h:i A');

                return '<div class="d-flex flex-column small">
                            <span class="text-heading fw-semibold">' . $date . '</span>
                            <span class="text-muted">' . $fromTime . ' &rarr; ' . $toTime . '</span>
                        </div>';
            })
            ->addColumn('hours_count', function (PermissionRequest $item) {
                return '<span class="badge bg-label-primary fw-bold">' . $item->formatted_duration . '</span>';
            })
            ->addColumn('duration', function (PermissionRequest $item) {
                return '<span class="badge bg-label-primary fw-bold">' . $item->formatted_duration . '</span>';
            })
            ->addColumn('status_badge', function (PermissionRequest $item) {
                $status = $item->status;
                if (!$status) {
                    return '<span class="badge bg-label-secondary">-</span>';
                }

                return '<span class="badge ' . $status->badgeClass() . ' d-inline-flex align-items-center gap-1">
                            <i class="icon-base ' . $status->icon() . ' font-12"></i>
                            <span>' . $status->label() . '</span>
                        </span>';
            })
            ->addColumn('attachment_btn', function (PermissionRequest $item) {
                if (!$item->attachment) {
                    return '<span class="text-muted small">-</span>';
                }

                return '<a href="' . asset('storage/' . $item->attachment) . '" target="_blank" class="btn btn-icon btn-sm btn-outline-secondary" title="' . __('View Attachment') . '">
                            <i class="icon-base bx bx-paperclip"></i>
                        </a>';
            })
            ->addColumn('actions', function (PermissionRequest $item) {
                $statusVal = $item->status?->value ?? '';
                $buttons   = '';

                // View Details button
                $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-view-permission" 
                                data-id="' . $item->id . '" title="' . __('View Details') . '">
                                <i class="icon-base bx bx-show"></i>
                             </button>';

                // Approve & Reject & Delete for pending / manager_approved
                if ($statusVal === PermissionRequestStatus::PENDING->value || $statusVal === PermissionRequestStatus::MANAGER_APPROVED->value) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-success btn-approve-permission" 
                                    data-id="' . $item->id . '" title="' . __('Approve Request') . '">
                                    <i class="icon-base bx bx-check"></i>
                                 </button>';

                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-warning btn-reject-permission" 
                                    data-id="' . $item->id . '" title="' . __('Reject Request') . '">
                                    <i class="icon-base bx bx-x"></i>
                                 </button>';

                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-permission" 
                                    data-id="' . $item->id . '" data-name="' . e($item->employee?->name ?? '') . '" title="' . __('Delete') . '">
                                    <i class="icon-base bx bx-trash"></i>
                                 </button>';
                }

                return '<div class="d-inline-flex gap-1">' . $buttons . '</div>';
            })
            ->rawColumns(['employee_info', 'type_badge', 'date_time', 'time_period', 'hours_count', 'duration', 'status_badge', 'attachment_btn', 'actions'])
            ->make(true);
    }

    //======================
    /**
     * AJAX endpoint: Get employee live balance & calculate duration.
     */
    public function checkBalance(Request $request): JsonResponse
    {
        try {
            $employeeId = (int) $request->input('employee_id');
            $date       = $request->input('date', Carbon::today()->toDateString());
            $fromTime   = $request->input('from_time');
            $toTime     = $request->input('to_time');

            if (!$employeeId) {
                return response()->json(['success' => false, 'message' => __('Employee is required.')], 422);
            }

            $balance = $this->permissionService->getEmployeeMonthlyBalance($employeeId, $date);

            $calculatedHours = 0;
            if ($fromTime && $toTime) {
                try {
                    $calculatedHours = $this->permissionService->calculateHours($fromTime, $toTime);
                } catch (Exception $e) {
                    // Ignored during partial typing
                }
            }

            return response()->json([
                'success'          => true,
                'balance'          => $balance,
                'calculated_hours' => $calculatedHours,
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
     * Store a newly created permission request.
     */
    public function store(StorePermissionRequest $request): JsonResponse
    {
        try {
            $permission = $this->permissionService->createPermissionRequest($request->validated(), Auth::id() ?? 1);

            return response()->json([
                'success' => true,
                'message' => __('Permission request submitted successfully.'),
                'data'    => $permission,
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
     * Show permission request details for modal viewing.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $permission = $this->permissionService->getById($id);
            if (!$permission) {
                return response()->json(['success' => false, 'message' => __('Permission request not found.')], 404);
            }

            $emp = $permission->employee;
            $fromTime = Carbon::createFromTimeString($permission->from_time)->format('h:i A');
            $toTime   = Carbon::createFromTimeString($permission->to_time)->format('h:i A');

            $statusBadge = '<span class="badge ' . $permission->status->badgeClass() . ' d-inline-flex align-items-center gap-1">
                                <i class="icon-base ' . $permission->status->icon() . ' font-12"></i>
                                <span>' . $permission->status->label() . '</span>
                            </span>';

            $typeBadge = '<span class="badge ' . $permission->type->badgeClass() . ' d-inline-flex align-items-center gap-1">
                            <i class="icon-base ' . $permission->type->icon() . ' font-12"></i>
                            <span>' . $permission->type->label() . '</span>
                          </span>';

            $data = [
                'id'                      => $permission->id,
                'employee_name'           => $emp?->name ?? '-',
                'employee_code'           => $emp?->employee_code ?? '-',
                'department'              => $emp?->department?->name ?? '-',
                'date'                    => $permission->date ? $permission->date->format('Y-m-d') : '-',
                'date_formatted'          => $permission->date ? $permission->date->translatedFormat('l, d F Y') : '-',
                'type'                    => $permission->type->value,
                'type_label'              => $permission->type->label(),
                'type_badge'              => $typeBadge,
                'from_time'               => $fromTime,
                'to_time'                 => $toTime,
                'hours_count'             => $permission->hours_count,
                'formatted_duration'      => $permission->formatted_duration,
                'is_emergency'            => $permission->is_emergency,
                'status'                  => $permission->status->label(),
                'status_badge'            => $statusBadge,
                'status_raw'              => $permission->status->value,
                'reason'                  => $permission->reason ?: __('No reason specified'),
                'rejection_reason'        => $permission->rejection_reason,
                'financial_policy'        => $permission->financial_policy,
                'attachment_url'          => $permission->attachment ? asset('storage/' . $permission->attachment) : null,
                'created_at'              => $permission->created_at->format('Y-m-d H:i'),
                'manager_approver'        => $permission->managerApprover?->name,
                'manager_approved_at'     => $permission->manager_approved_at?->format('Y-m-d H:i'),
                'hr_approver'             => $permission->hrApprover?->name,
                'hr_approved_at'          => $permission->hr_approved_at?->format('Y-m-d H:i'),
            ];

            return response()->json([
                'success'    => true,
                'data'       => $data,
                'permission' => $data,
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
     * Approve permission request.
     */
    public function approve(Request $request, int $id): JsonResponse
    {
        try {
            $role       = $request->get('role', 'hr');
            $permission = $this->permissionService->approvePermissionRequest($id, Auth::id() ?? 1, $role);

            return response()->json([
                'success' => true,
                'message' => __('Permission request approved successfully.'),
                'data'    => $permission,
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
     * Reject permission request.
     */
    public function reject(Request $request, int $id): JsonResponse
    {
        try {
            $reason = $request->input('rejection_reason');
            if (empty($reason)) {
                return response()->json(['success' => false, 'message' => __('Rejection reason is required.')], 422);
            }

            $permission = $this->permissionService->rejectPermissionRequest($id, $reason, Auth::id() ?? 1);

            return response()->json([
                'success' => true,
                'message' => __('Permission request rejected.'),
                'data'    => $permission,
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
     * Delete permission request.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->permissionService->deletePermissionRequest($id);

            return response()->json([
                'success' => true,
                'message' => __('Permission request deleted successfully.'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
