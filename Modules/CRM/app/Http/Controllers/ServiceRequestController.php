<?php

namespace Modules\CRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\CRM\Enums\ServiceRequestStatus;
use Modules\CRM\Enums\ServiceRequestType;
use Modules\CRM\Models\ServiceRequest;
use Modules\CRM\Services\ServiceRequestService;
use Modules\HR\Models\Department;
use Modules\Nursery\Models\NurseryBranch;
use Yajra\DataTables\Facades\DataTables;

class ServiceRequestController extends Controller
{
    public function __construct(
        protected ServiceRequestService $service
    ) {}

    public function index(): View
    {
        abort_if(! auth()->user()->can('crm.service-requests.view'), 403);

        $statusCounts = [
            'all' => ServiceRequest::count(),
            'pending' => ServiceRequest::where('status', ServiceRequestStatus::PENDING)->count(),
            'inprogress' => ServiceRequest::where('status', ServiceRequestStatus::IN_PROGRESS)->count(),
            'complete' => ServiceRequest::where('status', ServiceRequestStatus::COMPLETE)->count(),
        ];

        return view('crm::service_requests.index', [
            'types' => ServiceRequestType::cases(),
            'statuses' => ServiceRequestStatus::cases(),
            'statusCounts' => $statusCounts,
            'branches' => class_exists(NurseryBranch::class) ? NurseryBranch::with('translations')->latest('id')->get() : collect(),
            'departments' => class_exists(Department::class) ? Department::with('translations')->latest('id')->get() : collect(),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('crm.service-requests.view'), 403);

        $serviceRequest = $this->service->findById($id);

        if (!$serviceRequest) {
            return response()->json(['message' => __('Service request not found.')], 404);
        }

        return response()->json([
            'data' => $serviceRequest,
            'branch_name' => $serviceRequest->branch?->name ?? '-',
            'department_name' => $serviceRequest->department?->name ?? '-',
            'type_label' => $serviceRequest->type?->label() ?? '-',
            'status_label' => $serviceRequest->status?->label() ?? '-',
            'image_url' => $serviceRequest->image ? asset('storage/' . $serviceRequest->image) : null,
            'attachments' => $serviceRequest->attachments->map(fn ($a) => [
                'id' => $a->id,
                'name' => $a->original_name ?? basename($a->file_path),
                'url' => asset('storage/' . $a->file_path),
                'size' => $a->file_size ? round($a->file_size / 1024, 1) . ' KB' : '',
            ]),
        ]);
    }

    public function searchCivilId(Request $request): JsonResponse
    {
        abort_if(! (auth()->user()->can('crm.service-requests.view') || auth()->user()->can('crm.service-requests.create') || auth()->user()->can('crm.service-requests.edit')), 403);

        $term = trim((string) ($request->input('civil_id') ?? $request->input('term') ?? $request->input('q') ?? ''));

        if (empty($term) || strlen($term) < 2) {
            return response()->json([
                'found' => false,
                'count' => 0,
                'data' => [],
                'message' => __('Please provide at least 2 characters of civil ID'),
            ]);
        }

        $results = collect();
        $seen = [];

        // 1. Check Previous Service Requests
        $serviceRequests = ServiceRequest::where('civil_id', 'like', "%{$term}%")
            ->latest('id')
            ->limit(10)
            ->get();

        foreach ($serviceRequests as $req) {
            if (!empty($req->civil_id) && !isset($seen[$req->civil_id])) {
                $seen[$req->civil_id] = true;
                $results->push([
                    'civil_id' => $req->civil_id,
                    'name' => $req->name,
                    'email' => $req->email,
                    'branch_id' => $req->branch_id,
                    'department_id' => $req->department_id,
                    'source_label' => __('Previous Request'),
                ]);
            }
        }

        // 2. Check CRM Leads
        if (class_exists(\Modules\CRM\Models\Lead::class)) {
            $leads = \Modules\CRM\Models\Lead::where('civil_id', 'like', "%{$term}%")
                ->latest('id')
                ->limit(10)
                ->get();

            foreach ($leads as $lead) {
                if (!empty($lead->civil_id) && !isset($seen[$lead->civil_id])) {
                    $seen[$lead->civil_id] = true;
                    $results->push([
                        'civil_id' => $lead->civil_id,
                        'name' => $lead->name,
                        'email' => $lead->email,
                        'branch_id' => null,
                        'department_id' => null,
                        'source_label' => __('Lead Customer'),
                    ]);
                }
            }
        }

        // 3. Check Employees (by national_id or passport_number)
        if (class_exists(\Modules\HR\Models\Employee::class)) {
            $employees = \Modules\HR\Models\Employee::where('national_id', 'like', "%{$term}%")
                ->orWhere('passport_number', 'like', "%{$term}%")
                ->latest('id')
                ->limit(10)
                ->get();

            foreach ($employees as $emp) {
                $cid = $emp->national_id ?: $emp->passport_number;
                if (!empty($cid) && !isset($seen[$cid])) {
                    $seen[$cid] = true;
                    $results->push([
                        'civil_id' => $cid,
                        'name' => $emp->full_name ?? $emp->name ?? $emp->first_name ?? '',
                        'email' => $emp->work_email ?? $emp->personal_email ?? null,
                        'branch_id' => $emp->branch_id ?? null,
                        'department_id' => $emp->department_id ?? null,
                        'source_label' => __('Employee'),
                    ]);
                }
            }
        }

        $found = $results->isNotEmpty();

        return response()->json([
            'found' => $found,
            'count' => $results->count(),
            'data' => $results->values(),
            'message' => $found ? __('Matched records found') : __('No customer found with this civil ID'),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('crm.service-requests.view'), 403);

        return DataTables::eloquent($this->service->getDataTableQuery($request->all()))
            ->addIndexColumn()
            ->editColumn('name', fn (ServiceRequest $item) => '<span class="fw-semibold text-heading">' . e($item->name) . '</span>')
            ->editColumn('civil_id', fn (ServiceRequest $item) => '<span class="font-monospace text-secondary">' . e($item->civil_id) . '</span>')
            ->editColumn('email', fn (ServiceRequest $item) => $item->email ? '<span class="text-body font-monospace">' . e($item->email) . '</span>' : '<span class="text-muted">-</span>')
            ->editColumn('type', function (ServiceRequest $item) {
                $isComplaint = $item->type === ServiceRequestType::REGISTRATION_COMPLAINT;
                $badgeClass = $isComplaint ? 'bg-label-danger' : 'bg-label-primary';
                $icon = $isComplaint ? 'bx-error-alt' : 'bx-file';
                return '<span class="badge ' . $badgeClass . '"><i class="icon-base bx ' . $icon . ' me-1"></i>' . e($item->type?->label() ?? '-') . '</span>';
            })
            ->editColumn('status', function (ServiceRequest $item) {
                $badgeClass = match ($item->status) {
                    ServiceRequestStatus::PENDING => 'bg-label-warning',
                    ServiceRequestStatus::IN_PROGRESS => 'bg-label-info',
                    ServiceRequestStatus::COMPLETE => 'bg-label-success',
                    default => 'bg-label-secondary',
                };
                return '<span class="badge ' . $badgeClass . '">' . e($item->status?->label() ?? '-') . '</span>';
            })
            ->addColumn('branch_name', fn (ServiceRequest $item) => $item->branch?->name ?? '<span class="text-muted">-</span>')
            ->addColumn('department_name', fn (ServiceRequest $item) => $item->department?->name ?? '<span class="text-muted">-</span>')
            ->editColumn('call_recording_number', fn (ServiceRequest $item) => $item->call_recording_number ? '<span class="badge bg-label-secondary font-monospace"><i class="icon-base bx bx-phone-call me-1"></i>' . e($item->call_recording_number) . '</span>' : '<span class="text-muted">-</span>')
            ->editColumn('created_at', fn (ServiceRequest $item) => $item->created_at?->format('Y-m-d') ?? '-')
            ->addColumn('actions', function (ServiceRequest $item) {
                $attachmentsData = $item->attachments->map(fn ($a) => [
                    'id' => $a->id,
                    'name' => $a->original_name ?? basename($a->file_path),
                    'url' => asset('storage/' . $a->file_path),
                    'size' => $a->file_size ? round($a->file_size / 1024, 1) . ' KB' : '',
                ])->values();

                $statusClass = match ($item->status) {
                    ServiceRequestStatus::PENDING => 'bg-label-warning',
                    ServiceRequestStatus::IN_PROGRESS => 'bg-label-info',
                    ServiceRequestStatus::COMPLETE => 'bg-label-success',
                    default => 'bg-label-secondary',
                };
                $typeClass = $item->type === ServiceRequestType::REGISTRATION_COMPLAINT ? 'bg-label-danger' : 'bg-label-primary';

                $user = auth()->user();
                $canView = $user?->can('crm.service-requests.view') ?? true;
                $canEdit = $user?->can('crm.service-requests.edit') ?? false;
                $canDelete = $user?->can('crm.service-requests.delete') ?? false;

                if (! $canView && ! $canEdit && ! $canDelete) {
                    return '<span class="text-muted">-</span>';
                }

                $buttons = '<div class="d-inline-flex gap-1">';
                if ($canView) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-info btn-view-request"
                        data-id="' . $item->id . '"
                        data-civil-id="' . e($item->civil_id) . '"
                        data-name="' . e($item->name) . '"
                        data-email="' . e($item->email ?? '') . '"
                        data-type-label="' . e($item->type?->label() ?? '-') . '"
                        data-type-class="' . $typeClass . '"
                        data-status-label="' . e($item->status?->label() ?? '-') . '"
                        data-status-class="' . $statusClass . '"
                        data-branch-name="' . e($item->branch?->name ?? '-') . '"
                        data-department-name="' . e($item->department?->name ?? '-') . '"
                        data-call-recording-number="' . e($item->call_recording_number ?? '') . '"
                        data-notes="' . e($item->notes ?? '') . '"
                        data-image="' . ($item->image ? asset('storage/' . $item->image) : '') . '"
                        data-attachments=\'' . json_encode($attachmentsData, JSON_HEX_APOS | JSON_HEX_QUOT) . '\'
                        data-created-at="' . ($item->created_at?->format('Y-m-d H:i') ?? '-') . '"
                        title="' . __('View Details') . '">
                        <i class="icon-base bx bx-show"></i>
                    </button>';
                }
                if ($canEdit) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-request"
                        data-id="' . $item->id . '"
                        data-civil-id="' . e($item->civil_id) . '"
                        data-name="' . e($item->name) . '"
                        data-email="' . e($item->email ?? '') . '"
                        data-type="' . ($item->type?->value ?? '') . '"
                        data-status="' . ($item->status?->value ?? '') . '"
                        data-branch-id="' . ($item->branch_id ?? '') . '"
                        data-department-id="' . ($item->department_id ?? '') . '"
                        data-call-recording-number="' . e($item->call_recording_number ?? '') . '"
                        data-notes="' . e($item->notes ?? '') . '"
                        data-image="' . ($item->image ? asset('storage/' . $item->image) : '') . '"
                        title="' . __('Edit') . '">
                        <i class="icon-base bx bx-edit-alt"></i>
                    </button>';
                }
                if ($canDelete) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-request"
                        data-id="' . $item->id . '" data-name="' . e($item->name) . '" title="' . __('Delete') . '">
                        <i class="icon-base bx bx-trash"></i>
                    </button>';
                }
                $buttons .= '</div>';

                return $buttons;
            })
            ->rawColumns(['name', 'civil_id', 'email', 'type', 'status', 'branch_name', 'department_name', 'call_recording_number', 'actions'])
            ->with('statusCounts', [
                'all' => ServiceRequest::count(),
                'pending' => ServiceRequest::where('status', ServiceRequestStatus::PENDING)->count(),
                'inprogress' => ServiceRequest::where('status', ServiceRequestStatus::IN_PROGRESS)->count(),
                'complete' => ServiceRequest::where('status', ServiceRequestStatus::COMPLETE)->count(),
            ])
            ->make(true);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_if(! auth()->user()->can('crm.service-requests.create'), 403);

        $data = $this->validatedData($request);

        $this->service->create(
            $data,
            $request->file('image'),
            $request->file('attachments', [])
        );

        return redirect()->route('crm.service-requests.index')
            ->with('success', __('Service request created successfully.'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        abort_if(! auth()->user()->can('crm.service-requests.edit'), 403);

        $serviceRequest = $this->service->findById($id);

        if (!$serviceRequest) {
            return redirect()->route('crm.service-requests.index')
                ->with('error', __('Service request not found.'));
        }

        $this->service->update(
            $serviceRequest,
            $this->validatedData($request, true),
            $request->file('image'),
            $request->file('attachments', [])
        );

        return redirect()->route('crm.service-requests.index')
            ->with('success', __('Service request updated successfully.'));
    }

    public function destroy(int $id): RedirectResponse
    {
        abort_if(! auth()->user()->can('crm.service-requests.delete'), 403);

        try {
            $serviceRequest = $this->service->findById($id);

            if (!$serviceRequest) {
                return redirect()->route('crm.service-requests.index')
                    ->with('error', __('Service request not found.'));
            }

            $this->service->delete($serviceRequest);

            return redirect()->route('crm.service-requests.index')
                ->with('success', __('Service request deleted successfully.'));
        } catch (Exception $exception) {
            return redirect()->route('crm.service-requests.index')
                ->with('error', __('An error occurred while deleting the service request: ') . $exception->getMessage());
        }
    }

    protected function validatedData(Request $request, bool $isUpdate = false): array
    {
        return $request->validate([
            'civil_id' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'type' => ['required', 'in:' . implode(',', ServiceRequestType::values())],
            'branch_id' => ['nullable', 'integer', 'exists:nursery_branches,id'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'call_recording_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'status' => ['nullable', 'in:' . implode(',', ServiceRequestStatus::values())],
            'image' => ['nullable', 'image', 'max:5120'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:10240'],
        ]);
    }
}
