<?php

namespace Modules\CRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Modules\CRM\Enums\ServiceRequestStatus;
use Modules\CRM\Enums\ServiceRequestType;
use Modules\CRM\Models\Lead;
use Modules\CRM\Models\ServiceRequest;
use Modules\HR\Models\Department;
use Modules\Nursery\Models\NurseryBranch;
use Modules\CRM\Services\LeadService;
use Yajra\DataTables\Facades\DataTables;

class LeadController extends Controller
{
    public function __construct(
        protected LeadService $leadService
    ) {}

    public function index(): View
    {
        return view('crm::leads.index');
    }

    public function statisticsPage(): View
    {
        return view('crm::leads.statistics');
    }

    public function data(Request $request): JsonResponse
    {
        return DataTables::eloquent($this->leadService->getDataTableQuery($request->all()))
            ->addIndexColumn()
            ->editColumn('email', fn (Lead $lead) => $lead->email ?: '-')
            ->editColumn('civil_id', fn (Lead $lead) => $lead->civil_id ?: '-')
            ->editColumn('job_title', fn (Lead $lead) => $lead->job_title ?: '-')
            ->editColumn('created_at', fn (Lead $lead) => $lead->created_at?->format('Y-m-d') ?? '-')
            ->addColumn('actions', function (Lead $lead) {
                $user = auth()->user();
                $canEdit = $user?->can('crm.leads.edit');
                $canDelete = $user?->can('crm.leads.delete');

                if (! $canEdit && ! $canDelete) {
                    return '<span class="text-muted">-</span>';
                }

                $buttons = '<div class="d-inline-flex gap-1">';
                if ($canEdit) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-lead"
                        data-id="' . $lead->id . '"
                        data-name="' . e($lead->name) . '"
                        data-phone="' . e($lead->phone) . '"
                        data-email="' . e($lead->email ?? '') . '"
                        data-civil-id="' . e($lead->civil_id ?? '') . '"
                        data-job-title="' . e($lead->job_title ?? '') . '"
                        data-address="' . e($lead->address ?? '') . '"
                        data-notes="' . e($lead->notes ?? '') . '"
                        title="' . __('Edit') . '">
                        <i class="icon-base bx bx-edit-alt"></i>
                    </button>';
                }
                if ($canDelete) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-lead"
                        data-id="' . $lead->id . '" data-name="' . e($lead->name) . '" title="' . __('Delete') . '">
                        <i class="icon-base bx bx-trash"></i>
                    </button>';
                }
                $buttons .= '</div>';

                return $buttons;
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function statistics(): JsonResponse
    {
        $now = now();
        $last30DaysStart = $now->copy()->subDays(29)->startOfDay();
        $previous30DaysStart = $now->copy()->subDays(59)->startOfDay();

        $total = ServiceRequest::query()->count();
        $last30Days = ServiceRequest::query()->where('created_at', '>=', $last30DaysStart)->count();
        $previous30Days = ServiceRequest::query()->whereBetween('created_at', [
            $previous30DaysStart,
            $last30DaysStart->copy()->subSecond(),
        ])->count();

        $monthly = collect(range(11, 0))->map(function (int $monthsAgo) use ($now) {
            $month = $now->copy()->subMonths($monthsAgo);

            return [
                'label' => $month->locale(app()->getLocale())->translatedFormat('M Y'),
                'value' => ServiceRequest::query()->whereBetween('created_at', [
                    $month->copy()->startOfMonth(),
                    $month->copy()->endOfMonth(),
                ])->count(),
            ];
        })->values();

        $typeCounts = collect(ServiceRequestType::cases())->map(fn (ServiceRequestType $type) => [
            'label' => $type->label(),
            'value' => ServiceRequest::query()->where('type', $type->value)->count(),
        ])->values();

        $statusCounts = collect(ServiceRequestStatus::cases())->map(fn (ServiceRequestStatus $status) => [
            'label' => $status->label(),
            'value' => ServiceRequest::query()->where('status', $status->value)->count(),
        ])->values();

        $branchIds = ServiceRequest::query()->select('branch_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('branch_id')->groupBy('branch_id')->orderByDesc('total')->limit(8)->get();
        $branches = NurseryBranch::query()->whereIn('id', $branchIds->pluck('branch_id'))->get()->keyBy('id');
        $branchCounts = $branchIds->map(fn ($item) => [
            'label' => $branches->get($item->branch_id)?->name ?? __('Unknown'),
            'value' => (int) $item->total,
        ])->values();

        $departmentIds = ServiceRequest::query()->select('department_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('department_id')->groupBy('department_id')->orderByDesc('total')->limit(8)->get();
        $departments = Department::query()->whereIn('id', $departmentIds->pluck('department_id'))->get()->keyBy('id');
        $departmentCounts = $departmentIds->map(fn ($item) => [
            'label' => $departments->get($item->department_id)?->name ?? __('Unknown'),
            'value' => (int) $item->total,
        ])->values();

        $growth = $previous30Days > 0
            ? round((($last30Days - $previous30Days) / $previous30Days) * 100, 1)
            : ($last30Days > 0 ? 100 : 0);

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'this_month' => ServiceRequest::query()->where('created_at', '>=', $now->copy()->startOfMonth())->count(),
                'last_30_days' => $last30Days,
                'growth' => $growth,
                'type_counts' => $typeCounts,
                'status_counts' => $statusCounts,
                'branch_counts' => $branchCounts,
                'department_counts' => $departmentCounts,
                'with_email' => ServiceRequest::query()->whereNotNull('email')->where('email', '<>', '')->count(),
                'with_attachments' => ServiceRequest::query()->whereHas('attachments')->count(),
                'unique_customers' => ServiceRequest::query()->whereNotNull('civil_id')->distinct('civil_id')->count('civil_id'),
                'monthly' => $monthly,
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->leadService->createLead($this->validatedData($request));

        return redirect()->route('crm.leads.index')->with('success', __('Lead created successfully.'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $lead = $this->leadService->getLeadById($id);

        if (!$lead) {
            return redirect()->route('crm.leads.index')->with('error', __('Lead not found.'));
        }

        $this->leadService->updateLead($lead, $this->validatedData($request));

        return redirect()->route('crm.leads.index')->with('success', __('Lead updated successfully.'));
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $lead = $this->leadService->getLeadById($id);

            if (!$lead) {
                return redirect()->route('crm.leads.index')->with('error', __('Lead not found.'));
            }

            $this->leadService->deleteLead($lead);

            return redirect()->route('crm.leads.index')->with('success', __('Lead deleted successfully.'));
        } catch (Exception $exception) {
            return redirect()->route('crm.leads.index')
                ->with('error', __('An error occurred while deleting the lead: ') . $exception->getMessage());
        }
    }

    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'phone'     => ['required', 'string', 'max:50'],
            'email'     => ['nullable', 'email', 'max:255'],
            'civil_id'  => ['nullable', 'string', 'max:100'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'address'   => ['nullable', 'string'],
            'notes'     => ['nullable', 'string'],
        ]);
    }
}
