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
use Yajra\DataTables\Facades\DataTables;

class ServiceRequestController extends Controller
{
    public function __construct(
        protected ServiceRequestService $service
    ) {}

    public function index(): View
    {
        return view('crm::service_requests.index', [
            'types' => ServiceRequestType::cases(),
            'statuses' => ServiceRequestStatus::cases(),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        return DataTables::eloquent($this->service->getDataTableQuery($request->all()))
            ->addIndexColumn()
            ->editColumn('type', fn (ServiceRequest $item) => $item->type?->label() ?? '-')
            ->editColumn('status', fn (ServiceRequest $item) => $item->status?->label() ?? '-')
            ->addColumn('branch_name', fn (ServiceRequest $item) => $item->branch?->name ?? '-')
            ->addColumn('department_name', fn (ServiceRequest $item) => $item->department?->name ?? '-')
            ->editColumn('created_at', fn (ServiceRequest $item) => $item->created_at?->format('Y-m-d') ?? '-')
            ->rawColumns([])
            ->make(true);
    }

    public function store(Request $request): RedirectResponse
    {
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
